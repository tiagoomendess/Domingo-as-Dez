<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\App;

class SocialPoster
{
    protected GuzzleClient $client;
    protected string $graphApiVersion;
    protected string $pageId;
    protected string $pageToken;
    protected string $systemToken;
    protected string $igUserId;

    public function __construct()
    {
        $this->graphApiVersion = config('services.facebook.default_graph_version', 'v24.0');
        
        // Configure Guzzle client
        $clientConfig = [
            'base_uri' => "https://graph.facebook.com/{$this->graphApiVersion}/",
            'timeout'  => 30,
        ];

        // Only disable TLS verification when NOT in production
        if (!App::environment('production')) {
            $clientConfig['verify'] = false;
        }

        $this->client = new GuzzleClient($clientConfig);

        $this->pageId      = env('FB_PAGE_ID');
        $this->pageToken   = env('FB_PAGE_ACCESS_TOKEN'); // Page posting
        $this->systemToken = env('FB_SYSTEM_USER_TOKEN'); // IG posting
        $this->igUserId    = env('IG_USER_ID');
    }

    /**
     * Make a POST request to Facebook Graph API
     */
    protected function makeGraphApiPost(string $endpoint, array $params, string $accessToken, array $fileData = []): array
    {
        try {
            $params['access_token'] = $accessToken;
            
            // Log the request details
            $logData = [
                'endpoint' => $endpoint,
                'has_file' => !empty($fileData),
                'params' => array_keys($params),
            ];
            
            if (!empty($fileData)) {
                foreach ($fileData as $fieldName => $fileInfo) {
                    $logData['file_details'][$fieldName] = [
                        'filename' => $fileInfo['filename'] ?? 'file',
                        'size_bytes' => strlen($fileInfo['contents']),
                    ];
                }
            }
            
            Log::info('Facebook Graph API Request', $logData);
            
            // If there's file data, use multipart
            if (!empty($fileData)) {
                $multipart = [];
                
                // Add the file
                foreach ($fileData as $fieldName => $fileInfo) {
                    $multipart[] = [
                        'name' => $fieldName,
                        'contents' => $fileInfo['contents'],
                        'filename' => $fileInfo['filename'] ?? 'file',
                    ];
                }
                
                // Add other params
                foreach ($params as $name => $value) {
                    $multipart[] = [
                        'name' => $name,
                        'contents' => $value,
                    ];
                }
                
                $requestOptions = ['multipart' => $multipart];
            } else {
                $requestOptions = ['form_params' => $params];
            }
            
            $response = $this->client->post($endpoint, $requestOptions);
            
            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            
            Log::info('Facebook Graph API Response', [
                'endpoint' => $endpoint,
                'status_code' => $statusCode,
                'response_body' => $responseBody,
            ]);

            $body = json_decode($responseBody, true);
            
            if (isset($body['error'])) {
                Log::error('Facebook Graph API Error Response', [
                    'endpoint' => $endpoint,
                    'status_code' => $statusCode,
                    'error_code' => $body['error']['code'] ?? 'unknown',
                    'error_type' => $body['error']['type'] ?? 'unknown',
                    'error_message' => $body['error']['message'] ?? 'unknown',
                    'error_subcode' => $body['error']['error_subcode'] ?? null,
                    'full_error' => $body['error'],
                ]);
                throw new \RuntimeException($body['error']['message'] ?? 'Graph API error');
            }

            return $body;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $errorMessage = $e->getMessage();
            $statusCode = null;
            $responseBody = null;
            
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $responseBody = $e->getResponse()->getBody()->getContents();
                $errorBody = json_decode($responseBody, true);
                $errorMessage = $errorBody['error']['message'] ?? $errorMessage;
                
                Log::error('Facebook Graph API Request Exception', [
                    'endpoint' => $endpoint,
                    'status_code' => $statusCode,
                    'response_body' => $responseBody,
                    'error_decoded' => $errorBody ?? null,
                    'exception_message' => $e->getMessage(),
                ]);
            } else {
                Log::error('Facebook Graph API Request Exception (No Response)', [
                    'endpoint' => $endpoint,
                    'exception_message' => $e->getMessage(),
                    'exception_class' => get_class($e),
                ]);
            }
            
            throw new \RuntimeException("Graph API request failed: {$errorMessage}");
        }
    }

    /**
     * Download file content from URL
     */
    protected function getFileContentsFromUrl(string $url): string
    {
        try {
            Log::info('Downloading file from URL', ['url' => $url]);
            
            $response = $this->client->get($url);
            $contents = $response->getBody()->getContents();
            $size = strlen($contents);
            
            Log::info('File downloaded successfully', [
                'url' => $url,
                'size_bytes' => $size,
                'content_type' => $response->getHeader('Content-Type')[0] ?? 'unknown',
            ]);
            
            return $contents;
        } catch (\Exception $e) {
            Log::error('Failed to download file', [
                'url' => $url,
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
            ]);
            throw new \RuntimeException("Failed to download file from URL: {$e->getMessage()}");
        }
    }

    /** -------- Facebook -------- */

    // ========== Two-Phase Facebook Stories Publishing ==========

    /**
     * PHASE 1: Upload photo for Facebook Story (unpublished)
     * Call this 30 minutes before scheduled publish time
     * 
     * @return string Photo ID to save for later publishing
     */
    public function createFacebookStoryPhotoMedia(string $imageUrl): string
    {
        // Trim whitespace from the URL
        $imageUrl = trim($imageUrl);
        
        Log::info('Uploading photo for Facebook Story', [
            'image_url' => $imageUrl,
            'page_id' => $this->pageId,
        ]);
        
        // Upload photo as unpublished
        $uploadResponse = $this->makeGraphApiPost(
            "{$this->pageId}/photos",
            [
                'url' => $imageUrl,
                'published' => 'false', // Don't publish to feed
            ],
            $this->pageToken
        );
        
        $photoId = $uploadResponse['id'] ?? null;
        
        if (!$photoId) {
            Log::error('Failed to upload photo for Facebook Story', ['response' => $uploadResponse]);
            throw new \RuntimeException('Failed to upload photo for Facebook Story');
        }
        
        Log::info('Photo uploaded successfully for Facebook Story', [
            'photo_id' => $photoId,
        ]);
        
        return $photoId;
    }

    /**
     * PHASE 1: Upload video for Facebook Story
     * Call this 30 minutes before scheduled publish time
     * This handles the 2-step upload process (initialize + upload)
     * 
     * @return string Video ID to save for later publishing
     */
    public function createFacebookStoryVideoMedia(string $videoUrl): string
    {
        // Trim whitespace from the URL
        $videoUrl = trim($videoUrl);
        
        Log::info('Uploading video for Facebook Story', [
            'video_url' => $videoUrl,
            'page_id' => $this->pageId,
        ]);
        
        // Step 1: Initialize upload session
        Log::info('Step 1: Initializing video upload session');
        
        $initResponse = $this->makeGraphApiPost(
            "{$this->pageId}/video_stories",
            [
                'upload_phase' => 'start',
            ],
            $this->pageToken
        );
        
        $videoId = $initResponse['video_id'] ?? null;
        $uploadUrl = $initResponse['upload_url'] ?? null;
        
        if (!$videoId || !$uploadUrl) {
            Log::error('Failed to initialize video upload session', ['response' => $initResponse]);
            throw new \RuntimeException('Failed to initialize Facebook Video Story upload session');
        }
        
        Log::info('Upload session initialized', [
            'video_id' => $videoId,
            'upload_url' => $uploadUrl,
        ]);
        
        // Step 2: Upload the video to the upload URL
        Log::info('Step 2: Uploading video to Facebook servers', [
            'video_id' => $videoId,
            'video_url' => $videoUrl,
        ]);
        
        try {
            // Use local file upload method: download video and upload as binary
            // According to Facebook docs, upload with offset=0, file_size, and binary data
            
            Log::info('Downloading video file for binary upload');
            $videoContent = $this->getFileContentsFromUrl($videoUrl);
            $fileSize = strlen($videoContent);
            
            Log::info('Video downloaded, uploading as binary to Facebook', [
                'file_size' => $fileSize,
                'upload_url' => $uploadUrl,
            ]);
            
            // Upload as binary data with offset and file_size headers
            // curl -X POST "upload_url" -H "offset: 0" -H "file_size: bytes" --data-binary "@file"
            $response = $this->client->post($uploadUrl, [
                'headers' => [
                    'offset' => '0',
                    'file_size' => (string)$fileSize,
                ],
                'body' => $videoContent,
            ]);
            
            $responseBody = $response->getBody()->getContents();
            $uploadResult = json_decode($responseBody, true);
            
            Log::info('Video uploaded successfully', [
                'video_id' => $videoId,
                'status_code' => $response->getStatusCode(),
                'response_body' => $responseBody,
                'upload_response' => $uploadResult,
            ]);
            
            if (!isset($uploadResult['success']) || $uploadResult['success'] !== true) {
                throw new \RuntimeException('Video upload did not return success');
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $errorBody = null;
            $statusCode = null;
            
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $errorBody = $e->getResponse()->getBody()->getContents();
            }
            
            Log::error('Video upload failed', [
                'video_id' => $videoId,
                'status_code' => $statusCode,
                'error_body' => $errorBody,
                'error_message' => $e->getMessage(),
            ]);
            throw new \RuntimeException("Failed to upload video to Facebook: {$e->getMessage()}");
        } catch (\Exception $e) {
            Log::error('Video upload failed (general exception)', [
                'video_id' => $videoId,
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
            ]);
            throw new \RuntimeException("Failed to upload video to Facebook: {$e->getMessage()}");
        }
        
        Log::info('Video media created successfully for Facebook Story', [
            'video_id' => $videoId,
        ]);
        
        return $videoId;
    }

    /**
     * PHASE 2: Publish a Facebook Story Photo using previously uploaded photo
     * Call this at the scheduled publish time
     * 
     * @param string $photoId The photo ID from Phase 1
     * @return string The published post ID
     */
    public function publishFacebookStoryPhoto(string $photoId): string
    {
        Log::info('Publishing Facebook Story Photo', [
            'photo_id' => $photoId,
            'page_id' => $this->pageId,
        ]);
        
        $storyResponse = $this->makeGraphApiPost(
            "{$this->pageId}/photo_stories",
            [
                'photo_id' => $photoId,
            ],
            $this->pageToken
        );
        
        $postId = $storyResponse['post_id'] ?? '';
        
        if (!$postId) {
            throw new \RuntimeException('Failed to publish Facebook Story Photo');
        }
        
        Log::info('Facebook Story Photo published successfully', [
            'photo_id' => $photoId,
            'post_id' => $postId,
            'success' => $storyResponse['success'] ?? false,
        ]);
        
        return $postId;
    }

    /**
     * PHASE 2: Publish a Facebook Story Video using previously uploaded video
     * Call this at the scheduled publish time
     * 
     * @param string $videoId The video ID from Phase 1
     * @return string The published post ID
     */
    public function publishFacebookStoryVideo(string $videoId): string
    {
        Log::info('Publishing Facebook Story Video', [
            'video_id' => $videoId,
            'page_id' => $this->pageId,
        ]);
        
        $publishResponse = $this->makeGraphApiPost(
            "{$this->pageId}/video_stories",
            [
                'video_id' => $videoId,
                'upload_phase' => 'finish',
            ],
            $this->pageToken
        );
        
        $postId = $publishResponse['post_id'] ?? '';
        
        if (!$postId) {
            throw new \RuntimeException('Failed to publish Facebook Story Video');
        }
        
        Log::info('Facebook Story Video published successfully', [
            'video_id' => $videoId,
            'post_id' => $postId,
            'success' => $publishResponse['success'] ?? false,
        ]);
        
        return $postId;
    }

    // ========== Legacy Single-Step Methods (for backward compatibility) ==========

    public function postToFacebookText(?string $message, ?string $link = null): string
    {
        $params = array_filter(['message' => $message, 'link' => $link]);
        $response = $this->makeGraphApiPost("{$this->pageId}/feed", $params, $this->pageToken);
        return $response['id'] ?? '';
    }

    public function postToFacebookPhoto(string $imageUrl, ?string $caption = null): string
    {
        $params = array_filter(['url' => $imageUrl, 'caption' => $caption]);
        $response = $this->makeGraphApiPost("{$this->pageId}/photos", $params, $this->pageToken);
        return $response['post_id'] ?? '';
    }

    /**
     * Post to Facebook Story Photo (single-step, legacy method)
     * For immediate posting. For scheduled posts, use createFacebookStoryPhotoMedia() + publishFacebookStoryPhoto()
     */
    public function postToFacebookStoryPhoto(string $imageUrl): string
    {
        Log::info('Posting Facebook Story Photo (single-step)', [
            'image_url' => $imageUrl,
        ]);
        
        // Phase 1: Upload photo
        $photoId = $this->createFacebookStoryPhotoMedia($imageUrl);
        
        // Phase 2: Publish immediately
        return $this->publishFacebookStoryPhoto($photoId);
    }

    /**
     * Post to Facebook Story Video (single-step, legacy method)
     * For immediate posting. For scheduled posts, use createFacebookStoryVideoMedia() + publishFacebookStoryVideo()
     */
    public function postToFacebookStoryVideo(string $videoUrl): string
    {
        Log::info('Posting Facebook Story Video (single-step)', [
            'video_url' => $videoUrl,
        ]);
        
        // Phase 1: Upload video (initialize + upload)
        $videoId = $this->createFacebookStoryVideoMedia($videoUrl);
        
        // Phase 2: Publish immediately
        return $this->publishFacebookStoryVideo($videoId);
    }

    /** -------- Instagram -------- */

    // ========== Two-Phase Instagram Publishing ==========
    
    /**
     * PHASE 1: Create Instagram image container and upload media
     * Call this 30 minutes before scheduled publish time
     * 
     * @return string Container ID to save for later publishing
     */
    public function createInstagramImageContainer(string $imageUrl, ?string $caption = null): string
    {
        // Trim whitespace from the URL
        $imageUrl = trim($imageUrl);
        
        Log::info('Creating Instagram image container', [
            'image_url' => $imageUrl,
            'ig_user_id' => $this->igUserId,
        ]);
        
        if (empty($this->igUserId)) {
            throw new \RuntimeException('Instagram User ID (IG_USER_ID) is not configured');
        }
        
        $response = $this->makeGraphApiPost("{$this->igUserId}/media", [
            'image_url' => $imageUrl,
            'caption'   => $caption,
        ], $this->systemToken);

        $containerId = $response['id'] ?? null;
        if (!$containerId) {
            throw new \RuntimeException('Failed to create Instagram image container');
        }
        
        Log::info('Instagram image container created successfully', [
            'container_id' => $containerId,
        ]);

        return $containerId;
    }

    /**
     * PHASE 1: Create Instagram reel container and upload media
     * Call this 30 minutes before scheduled publish time
     * 
     * @return string Container ID to save for later publishing
     */
    public function createInstagramReelContainer(string $videoUrl, ?string $caption = null): string
    {
        // Trim whitespace from the URL
        $videoUrl = trim($videoUrl);
        
        Log::info('Creating Instagram reel container', [
            'video_url' => $videoUrl,
            'ig_user_id' => $this->igUserId,
        ]);
        
        if (empty($this->igUserId)) {
            throw new \RuntimeException('Instagram User ID (IG_USER_ID) is not configured');
        }
        
        $response = $this->makeGraphApiPost("{$this->igUserId}/media", [
            'media_type' => 'REELS',
            'video_url'  => $videoUrl,
            'caption'    => $caption,
        ], $this->systemToken);

        $containerId = $response['id'] ?? null;
        if (!$containerId) {
            throw new \RuntimeException('Failed to create Instagram reel container');
        }
        
        Log::info('Instagram reel container created successfully', [
            'container_id' => $containerId,
        ]);

        return $containerId;
    }

    /**
     * PHASE 1: Create Instagram story photo container and upload media
     * Call this 30 minutes before scheduled publish time
     * 
     * @return string Container ID to save for later publishing
     */
    public function createInstagramStoryPhotoContainer(string $imageUrl): string
    {
        // Trim whitespace from the URL
        $imageUrl = trim($imageUrl);
        
        Log::info('Creating Instagram story photo container', [
            'image_url' => $imageUrl,
            'ig_user_id' => $this->igUserId,
        ]);
        
        if (empty($this->igUserId)) {
            throw new \RuntimeException('Instagram User ID (IG_USER_ID) is not configured');
        }
        
        $response = $this->makeGraphApiPost("{$this->igUserId}/media", [
            'media_type' => 'STORIES',
            'image_url'  => $imageUrl,
        ], $this->systemToken);

        $containerId = $response['id'] ?? null;
        if (!$containerId) {
            throw new \RuntimeException('Failed to create Instagram story photo container');
        }
        
        Log::info('Instagram story photo container created successfully', [
            'container_id' => $containerId,
        ]);

        return $containerId;
    }

    /**
     * PHASE 1: Create Instagram story video container and upload media
     * Call this 30 minutes before scheduled publish time
     * 
     * @return string Container ID to save for later publishing
     */
    public function createInstagramStoryVideoContainer(string $videoUrl): string
    {
        // Trim whitespace from the URL
        $videoUrl = trim($videoUrl);
        
        Log::info('Creating Instagram story video container', [
            'video_url' => $videoUrl,
            'ig_user_id' => $this->igUserId,
        ]);
        
        if (empty($this->igUserId)) {
            throw new \RuntimeException('Instagram User ID (IG_USER_ID) is not configured');
        }
        
        $response = $this->makeGraphApiPost("{$this->igUserId}/media", [
            'media_type' => 'STORIES',
            'video_url'  => $videoUrl,
        ], $this->systemToken);

        $containerId = $response['id'] ?? null;
        if (!$containerId) {
            throw new \RuntimeException('Failed to create Instagram story video container');
        }
        
        Log::info('Instagram story video container created successfully', [
            'container_id' => $containerId,
        ]);

        return $containerId;
    }

    /**
     * PHASE 2: Publish a previously created Instagram container
     * Call this at the scheduled publish time
     * 
     * @param string $containerId The container ID from Phase 1
     * @return string The published post ID
     */
    public function publishInstagramContainer(string $containerId): string
    {
        Log::info('Publishing Instagram container', [
            'container_id' => $containerId,
            'ig_user_id' => $this->igUserId,
        ]);
        
        if (empty($this->igUserId)) {
            throw new \RuntimeException('Instagram User ID (IG_USER_ID) is not configured');
        }
        
        // Wait for container to be ready (should be quick if created 30 min ago)
        $this->waitForInstagramContainerReady($containerId);
        
        // Publish the container
        $publishResponse = $this->makeGraphApiPost("{$this->igUserId}/media_publish", [
            'creation_id' => $containerId,
        ], $this->systemToken);

        $postId = $publishResponse['id'] ?? '';
        
        if (!$postId) {
            throw new \RuntimeException('Failed to publish Instagram container');
        }
        
        Log::info('Instagram container published successfully', [
            'container_id' => $containerId,
            'post_id' => $postId,
        ]);

        return $postId;
    }

    /**
     * Get Instagram Business Account ID from the connected Facebook Page
     */
    public function getInstagramBusinessAccountId(): ?string
    {
        try {
            $response = $this->client->get("{$this->pageId}", [
                'query' => [
                    'fields' => 'instagram_business_account',
                    'access_token' => $this->pageToken,
                ],
            ]);
            
            $body = json_decode($response->getBody()->getContents(), true);
            $igAccountId = $body['instagram_business_account']['id'] ?? null;
            
            Log::info('Retrieved Instagram Business Account ID', [
                'page_id' => $this->pageId,
                'ig_account_id' => $igAccountId,
                'full_response' => $body,
            ]);
            
            return $igAccountId;
        } catch (\Exception $e) {
            Log::error('Failed to get Instagram Business Account ID', [
                'page_id' => $this->pageId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Wait for Instagram container to be ready for publishing
     * Instagram needs time to download and process the media
     */
    protected function waitForInstagramContainerReady(string $containerId, int $maxAttempts = 5, int $sleepSeconds = 2): void
    {
        Log::info('Waiting for Instagram container to be ready', [
            'container_id' => $containerId,
            'max_attempts' => $maxAttempts,
        ]);
        
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                // Check container status
                $response = $this->client->get($containerId, [
                    'query' => [
                        'fields' => 'status_code',
                        'access_token' => $this->systemToken,
                    ],
                ]);
                
                $body = json_decode($response->getBody()->getContents(), true);
                $statusCode = $body['status_code'] ?? null;
                
                Log::info('Container status check', [
                    'container_id' => $containerId,
                    'attempt' => $attempt,
                    'status_code' => $statusCode,
                ]);
                
                // Status codes: FINISHED, IN_PROGRESS, ERROR
                if ($statusCode === 'FINISHED') {
                    Log::info('Container is ready for publishing', ['container_id' => $containerId]);
                    return;
                }
                
                if ($statusCode === 'ERROR') {
                    throw new \RuntimeException('Instagram container processing failed');
                }
                
                // Still processing, wait before next attempt
                if ($attempt < $maxAttempts) {
                    sleep($sleepSeconds);
                }
            } catch (\Exception $e) {
                Log::warning('Error checking container status', [
                    'container_id' => $containerId,
                    'attempt' => $attempt,
                    'error' => $e->getMessage(),
                ]);
                
                if ($attempt < $maxAttempts) {
                    sleep($sleepSeconds);
                } else {
                    throw $e;
                }
            }
        }
        
        throw new \RuntimeException('Instagram container did not become ready within the timeout period');
    }

    /**
     * Post to Instagram Image (single-step, legacy method)
     * For immediate posting. For scheduled posts, use createInstagramImageContainer() + publishInstagramContainer()
     */
    public function postToInstagramImage(string $imageUrl, ?string $caption = null): string
    {
        Log::info('Posting to Instagram Image (single-step)', [
            'image_url' => $imageUrl,
        ]);
        
        // Phase 1: Create container
        $containerId = $this->createInstagramImageContainer($imageUrl, $caption);
        
        // Phase 2: Publish immediately
        return $this->publishInstagramContainer($containerId);
    }

    /**
     * Post to Instagram Reel (single-step, legacy method)
     * For immediate posting. For scheduled posts, use createInstagramReelContainer() + publishInstagramContainer()
     */
    public function postToInstagramReel(string $videoUrl, ?string $caption = null): string
    {
        Log::info('Posting Instagram Reel (single-step)', [
            'video_url' => $videoUrl,
        ]);
        
        // Phase 1: Create container
        $containerId = $this->createInstagramReelContainer($videoUrl, $caption);
        
        // Phase 2: Publish immediately
        return $this->publishInstagramContainer($containerId);
    }

    /**
     * Post to Instagram Story Photo (single-step, legacy method)
     * For immediate posting. For scheduled posts, use createInstagramStoryPhotoContainer() + publishInstagramContainer()
     */
    public function postToInstagramStoryPhoto(string $imageUrl): string
    {
        Log::info('Posting Instagram Story Photo (single-step)', [
            'image_url' => $imageUrl,
        ]);
        
        // Phase 1: Create container
        $containerId = $this->createInstagramStoryPhotoContainer($imageUrl);
        
        // Phase 2: Publish immediately
        return $this->publishInstagramContainer($containerId);
    }

    /**
     * Post to Instagram Story Video (single-step, legacy method)
     * For immediate posting. For scheduled posts, use createInstagramStoryVideoContainer() + publishInstagramContainer()
     */
    public function postToInstagramStoryVideo(string $videoUrl): string
    {
        Log::info('Posting Instagram Story Video (single-step)', [
            'video_url' => $videoUrl,
        ]);
        
        // Phase 1: Create container
        $containerId = $this->createInstagramStoryVideoContainer($videoUrl);
        
        // Phase 2: Publish immediately
        return $this->publishInstagramContainer($containerId);
    }
}
