<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\SocialPoster;
use Illuminate\Support\Facades\Log;
use App\SocialMediaPost;

class PublishSocialMedia implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $poster = new SocialPoster();
        
        try {
            $this->preparePostsForPublish($poster);
        } catch (\Exception $e) {
            Log::error('Error preparing posts for publish', ['error' => $e->getMessage()]);
            return;
        }

        try {
            $this->publishPosts($poster);
        } catch (\Exception $e) {
            Log::error('Error publishing posts', ['error' => $e->getMessage()]);
            return;
        }
    }

    private function preparePostsForPublish(SocialPoster $poster)
    {
        $fifteenMinutesFromNow = now()->addMinutes(15);
        $aMinuteAgo = now()->subMinutes(1);

        $postRequests = SocialMediaPost::where('published', false)
            ->where('publish_at', '<=', $fifteenMinutesFromNow)
            ->where('publish_at', '>=', $aMinuteAgo)
            ->where('media_external_id', null)
            ->orderBy('publish_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // if no posts to publish, return
        if ($postRequests->count() === 0) {
            return;
        }

        Log::info('Preparing ' . $postRequests->count() . ' posts for publish');

        foreach ($postRequests as $postRequest) {
            switch ($postRequest->platform) {
                case SocialMediaPost::PLATFORM_FACEBOOK:
                    $this->prepareFacebookPosting($postRequest, $poster);
                    break;
                case SocialMediaPost::PLATFORM_INSTAGRAM:
                    $this->prepareInstagramPosting($postRequest, $poster);
                    break;
                default:
                    throw new \Exception('Invalid platform: ' . $postRequest->platform);
            }
        }

        Log::info('Finished preparing posts for publish');
    }

    private function prepareFacebookPosting(SocialMediaPost $postRequest, SocialPoster $poster)
    {
        switch ($postRequest->post_type) {
            case SocialMediaPost::POST_TYPE_POST:
                $this->prepareFacebookPost($postRequest, $poster);
                break;
            case SocialMediaPost::POST_TYPE_STORY:
                $this->prepareFacebookStory($postRequest, $poster);
                break;
            default:
                throw new \Exception('Invalid post type: ' . $postRequest->post_type);
        }
    }

    private function prepareInstagramPosting(SocialMediaPost $postRequest, SocialPoster $poster)
    {
        switch ($postRequest->post_type) {
            case SocialMediaPost::POST_TYPE_POST:
                $this->prepareInstagramPost($postRequest, $poster);
                break;
            case SocialMediaPost::POST_TYPE_STORY:
                $this->prepareInstagramStory($postRequest, $poster);
                break;
            default:
                throw new \Exception('Invalid post type: ' . $postRequest->post_type);
        }
    }

    private function prepareFacebookPost(SocialMediaPost $postRequest, SocialPoster $poster)
    {
        try {
            switch ($postRequest->post_content_type) {
                case SocialMediaPost::POST_CONTENT_TYPE_TEXT:
                case SocialMediaPost::POST_CONTENT_TYPE_IMAGE:
                case SocialMediaPost::POST_CONTENT_TYPE_VIDEO:
                    // No need to prepare anything, this one will be published directly when the time comes
                    return;
                default:
                    Log::error('Invalid post content type: ' . $postRequest->post_content_type);
                    return;
            }

            $postRequest->media_external_id = $external_id;
            $postRequest->save();
        } catch (\Exception $e) {
            Log::error('Error creating Facebook post media', ['error' => $e->getMessage()]);
            return;
        }
    }

    private function prepareFacebookStory(SocialMediaPost $postRequest, SocialPoster $poster)
    {
        try {
            $external_id = null;
            switch ($postRequest->post_content_type) {
                case SocialMediaPost::POST_CONTENT_TYPE_IMAGE:
                    $external_id = $poster->createFacebookStoryPhotoMedia($postRequest->media_path);
                    break;
                case SocialMediaPost::POST_CONTENT_TYPE_VIDEO:
                    $external_id = $poster->createFacebookStoryVideoMedia($postRequest->media_path);
                    break;
                default:
                    Log::error('Invalid post content type: ' . $postRequest->post_content_type);
                    return;
            }

            $postRequest->media_external_id = $external_id;
        } catch (\Exception $e) {
            Log::error('Error creating Facebook story photo media', ['error' => $e->getMessage()]);
            $postRequest->error_message = $e->getMessage();
        }

        $postRequest->save();
    }

    private function prepareInstagramPost(SocialMediaPost $postRequest, SocialPoster $poster)
    {
        try {
            switch ($postRequest->post_content_type) {
                case SocialMediaPost::POST_CONTENT_TYPE_IMAGE:
                    $external_id = $poster->createInstagramImageContainer($postRequest->media_path, $postRequest->text_content);
                    break;
                case SocialMediaPost::POST_CONTENT_TYPE_VIDEO:
                    $external_id = $poster->createInstagramReelContainer($postRequest->media_path, $postRequest->text_content);
                    break;
                default:
                    Log::error('Invalid post content type: ' . $postRequest->post_content_type);
                    return;
            }

            $postRequest->media_external_id = $external_id;
        } catch (\Exception $e) {
            Log::error('Error creating Instagram post media', ['error' => $e->getMessage()]);
            $postRequest->error_message = $e->getMessage();
        }

        $postRequest->save();
    }

    private function prepareInstagramStory(SocialMediaPost $postRequest, SocialPoster $poster)
    {
        try {
            switch ($postRequest->post_content_type) {
                case SocialMediaPost::POST_CONTENT_TYPE_IMAGE:
                    $external_id = $poster->createInstagramStoryPhotoContainer($postRequest->media_path);
                    break;
                case SocialMediaPost::POST_CONTENT_TYPE_VIDEO:
                    $external_id = $poster->createInstagramStoryVideoContainer($postRequest->media_path);
                    break;
                default:
                    Log::error('Invalid post content type: ' . $postRequest->post_content_type);
                    return;
            }

            $postRequest->media_external_id = $external_id;
            
        } catch (\Exception $e) {
            Log::error('Error creating Instagram story media', ['error' => $e->getMessage()]);
            $postRequest->error_message = $e->getMessage();
        }

        $postRequest->save();
    }

    private function publishPosts(SocialPoster $poster)
    {
        $posts = SocialMediaPost::where('published', false)
            ->where('publish_at', '<=', now())
            ->where('publish_at', '>=', now()->subMinutes(5)) // If we are 5 minutes past the publish time, we will skip the post
            ->orderBy('publish_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // if no posts to publish, return
        if ($posts->count() === 0) {
            return;
        }

        Log::info('Publishing ' . $posts->count() . ' posts');

        foreach ($posts as $post) {
            switch ($post->platform) {
                case SocialMediaPost::PLATFORM_INSTAGRAM:
                    $this->publishInstagram($post, $poster);
                    break;
                case SocialMediaPost::PLATFORM_FACEBOOK:
                    $this->publishFacebook($post, $poster);
                    break;
                default:
                    throw new \Exception('Invalid platform: ' . $post->platform);
            }
        }

        Log::info('Finished publishing posts');
    }

    private function publishInstagram(SocialMediaPost $postRequest, SocialPoster $poster) {
        try {
            $post_id = $poster->publishInstagramContainer($postRequest->media_external_id);
            $postRequest->post_id = $post_id;
            $postRequest->published = true;
        } catch (\Exception $e) {
            Log::error('Error publishing Instagram post', ['error' => $e->getMessage()]);
            $postRequest->error_message = $e->getMessage();
        }

        $postRequest->save();
    }

    private function publishFacebook(SocialMediaPost $postRequest, SocialPoster $poster) {
        try {
            $post_id = null;
            switch ($postRequest->post_type) {
                case SocialMediaPost::POST_TYPE_POST:
                    $post_id = $this->publishFacebookPost($postRequest, $poster);
                    break;
                case SocialMediaPost::POST_TYPE_STORY:
                    $post_id = $this->publishFacebookStory($postRequest, $poster);
                    break;
                default:
                    throw new \Exception('Invalid post type: ' . $postRequest->post_type);
            }

            $postRequest->post_id = $post_id;
            $postRequest->published = true;
        } catch (\Exception $e) {
            Log::error('Error publishing Facebook post', ['error' => $e->getMessage()]);
            $postRequest->error_message = $e->getMessage();
        }

        $postRequest->save();
    }

    private function publishFacebookPost(SocialMediaPost $postRequest, SocialPoster $poster) {
        try {
            $post_id = null;
            switch ($postRequest->post_content_type) {
                case SocialMediaPost::POST_CONTENT_TYPE_TEXT:
                    // Extract a link from the text content if it exists
                    $link = null;
                    if (preg_match('/https?:\/\/[^\s]+/', $postRequest->text_content, $matches)) {
                        $link = $matches[0];
                    }

                    // Remove the link from the text content
                    $text_content = preg_replace('/https?:\/\/[^\s]+/', '', $postRequest->text_content);

                    $post_id = $poster->postToFacebookText($text_content, $link);
                    break;
                case SocialMediaPost::POST_CONTENT_TYPE_IMAGE:
                    $post_id = $poster->postToFacebookPhoto($postRequest->media_path, $postRequest->text_content);
                    break;
                case SocialMediaPost::POST_CONTENT_TYPE_VIDEO:
                    // Not supported yet
                    return null;
                default:
                    throw new \Exception('Invalid post content type: ' . $postRequest->post_content_type);
            }

            return $post_id;

        } catch (\Exception $e) {
            Log::error('Error publishing Facebook post', ['error' => $e->getMessage()]);
            $postRequest->error_message = $e->getMessage();
        }

        $postRequest->save();

        return null;
    }

    private function publishFacebookStory(SocialMediaPost $postRequest, SocialPoster $poster) {
        try {
            $post_id = null;
            switch ($postRequest->post_content_type) {
                case SocialMediaPost::POST_CONTENT_TYPE_IMAGE:
                    $post_id = $poster->publishFacebookStoryPhoto($postRequest->media_external_id);
                    break;
                case SocialMediaPost::POST_CONTENT_TYPE_VIDEO:
                    $post_id = $poster->publishFacebookStoryVideo($postRequest->media_external_id);
                    break;
                default:
                    throw new \Exception('Invalid post content type: ' . $postRequest->post_content_type);
            }

            return $post_id;
        } catch (\Exception $e) {
            Log::error('Error publishing Facebook story', ['error' => $e->getMessage()]);
            $postRequest->error_message = $e->getMessage();
        }

        $postRequest->save();

        return null;
    }
}
