<?php

namespace App\Http\Controllers\Resources;

use App\Article;
use App\Audit;
use App\Media;
use Facebook\Facebook;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpKernel\Log\Logger;
use App\Services\SocialPoster;

class ArticleController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:articles')->only(['index', 'show']);
        $this->middleware('permission:articles.edit')->only(['edit', 'update']);
        $this->middleware('permission:articles.create')->only(['create', 'store', 'destroy', 'postOnFacebook', 'generateSocialImage']);
    }

    /**
     * Display a listing of the resource.
     *
     *
     */
    public function index(Request $request)
    {
        if ($request->query->get('search')) {
            $articles = Article::search($request->query->all());
        } else {
            $articles = Article::orderBy('id', 'desc')->paginate(config('custom.results_per_page'));
        }

        return view('backoffice.pages.articles', [
            'articles' => $articles,
            'searchFields' => Article::SEARCH_FIELDS,
            'queryParams' => $request->query->all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        return view('backoffice.pages.create_article');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:155|string',
            'description' => 'nullable|max:280|string',
            'selected_media_id' => 'nullable|integer',
            'editor1' => 'required|max:65000|string',
            'date' => 'required|date',
            'tags' => 'nullable|string|max:280',
            'visible' => 'required',
        ]);

        $user = Auth::user();

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        $title = $request->input('title');
        if (str_ends_with($title, '.') || str_ends_with($title, '!') || str_ends_with($title, '?')) {
            $title = substr($title, 0, -1);
        }

        $description = $request->input('description');
        $media_id = $request->input('selected_media_id');
        $text = $request->input('editor1');
        $date = $request->input('date');
        $tags = str_replace(', ', ',', $request->input('tags') ?? '');

        $media = Media::where('id', $media_id)->where('visible', true)->get();

        if (count($media) != 1)
            $media_id = null;

        if (empty($description))
            $description = substr(strip_tags($text), 0, 270) . '...';

        $article = Article::create([
            'title' => $title,
            'description' => $description,
            'media_id' => $media_id,
            'text' => $text,
            'date' => $date,
            'tags' => $tags,
            'user_id' => $user->id,
            'visible' => $visible,
            'facebook_post_id' => null,
        ]);

        Audit::add(
            Audit::ACTION_CREATE,
            'Article',
            null,
            $article->toArray()
        );

        return redirect(route('articles.show', ['article' => $article]));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $article = Article::findOrFail($id);

        if ($article->media_id == null)
            $media = null;
        else
            $media = Media::find($article->media_id);

        return view('backoffice.pages.article', ['article' => $article, 'media' => $media]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     */
    public function edit($id)
    {
        $article = Article::findOrFail($id);

        return view('backoffice.pages.edit_article', ['article' => $article]);
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'nullable|max:155|string',
            'description' => 'nullable|max:280|string',
            'selected_media_id' => 'nullable|integer',
            'editor1' => 'required|max:65000|string',
            'tags' => 'nullable|string|max:280',
            'visible' => 'required',
        ]);

        $messages = new MessageBag();

        $article = Article::findOrFail($id);
        $old_article = $article->toArray();
        $user = Auth::user();
        if ($user->id != $article->user_id) {
            if(!$user->hasPermission('admin')) {

                $error = new MessageBag();
                $error->add('error', trans('errors.no_permission'));

                return redirect(route('articles.show', ['article' => $article]))->with(['popup_message' => $error]);
            }
        }

        $title = $request->input('title');
        if (str_ends_with($title, '.') || str_ends_with($title, '!') || str_ends_with($title, '?')) {
            $title = substr($title, 0, -1);
        }

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        $media_id = $request->input('selected_media_id');
        $media = Media::where('id', $media_id)->where('visible', true)->get();

        if (count($media) != 1)
            $media_id = null;

        if (!$visible) {
            $article->title = $title;
        }

        $article->description = $request->input('description');
        $article->media_id = $media_id;
        $article->text = $request->input('editor1');
        $article->visible = $visible;
        $article->tags = str_replace(', ', ',', $request->input('tags'));

        if (empty($article->description))
            $article->description = substr(strip_tags($article->text), 0, 270) . '...';

        $article->save();

        $messages->add('success', trans('success.model_edited', ['model_name' => trans('models.article')]));

        Audit::add(
            Audit::ACTION_UPDATE,
            'Article',
            $old_article,
            $article->toArray()
        );

        return redirect(route('articles.show', ['article' => $article]))->with(['popup_message' => $messages]);
    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $article = Article::findOrFail($id);
        $old_article = $article->toArray();

        if ($user->id != $article->user_id) {
            if(!$user->hasPermission('admin')) {

                $error = new MessageBag();
                $error->add('error', trans('errors.no_permission'));

                return redirect(route('articles.show', ['article' => $article]))->with(['popup_message' => $error]);
            }
        }

        $article->delete();
        $message = new MessageBag();
        $message->add('success', trans('success.model_deleted', ['model_name' => trans('models.article')]));

        Audit::add(Audit::ACTION_DELETE, 'Article', $old_article);

        return redirect(route('articles.index'))->with(['popup_message' => $message]);
    }

    public function postOnFacebook(Request $request, Article $article, ImageManager $manager, SocialPoster $socialPoster)
    {
        $messages = new MessageBag();

        if (!empty($article->facebook_post_id)) {
            $messages->add('error', 'Este artigo já foi publicado no facebook');
            return redirect(route('articles.index'))->with(['popup_message' => $messages]);
        }

        $request->validate([
            'message' => 'nullable|max:280|string',
        ]);

        try {
            // Step 1: Generate the social media image
            Log::info('Generating social media image for Facebook post', ['article_id' => $article->id]);
            $image = $this->createArticleSocialImage($article, $manager);
            
            // Step 2: Save image to tmp folder
            $tmpFolder = 'storage/tmp/';
            $tmpDir = public_path($tmpFolder);
            if (!is_dir($tmpDir)) {
                mkdir($tmpDir, 0755, true);
            }
            
            $filename = 'article_fb_post_' . $article->id . '_' . time() . '.jpg';
            $relativePath = $tmpFolder . $filename;
            $absolutePath = public_path($relativePath);
            
            $image->encode('jpg', 95)->save($absolutePath);
            Log::info('Image saved to tmp folder', ['path' => $relativePath]);
            
            // Step 3: Post image to Facebook with caption
            $caption = $request->input('message', '');
            if (empty($caption)) {
                $caption = $article->title . "\n\n🔗 Link nos comentários";
            } else {
                $caption .= "\n\n🔗 Link nos comentários";
            }
            
            Log::info('Posting image to Facebook', ['file_path' => $absolutePath, 'article_id' => $article->id]);
            
            // Use binary upload instead of URL (works for localhost and production)
            $postId = $socialPoster->postToFacebookPhotoFromFile($absolutePath, $caption);
            
            if (empty($postId)) {
                throw new \RuntimeException('Facebook did not return a post ID');
            }
            
            Log::info('Facebook post created successfully', ['post_id' => $postId]);
            
            // Step 4: Try to comment on the post with the article link (non-critical)
            $commentId = null;
            $commentFailed = false;
            try {
                $articleUrl = $article->getPublicUrl();
                $commentText = "📰 Lê o artigo completo aqui: " . $articleUrl;
                
                Log::info('Posting comment with article link', ['post_id' => $postId, 'article_url' => $articleUrl]);
                $commentId = $socialPoster->postCommentOnFacebookPost($postId, $commentText);
                
                Log::info('Comment posted successfully', ['comment_id' => $commentId]);
            } catch (\Exception $commentException) {
                // Comment failed, but don't fail the whole operation
                $commentFailed = true;
                Log::warning('Failed to post comment on Facebook post (post was still published)', [
                    'post_id' => $postId,
                    'error' => $commentException->getMessage(),
                ]);
            }
            
            // Step 5: Save the post ID in the article
            $article->facebook_post_id = $postId;
            $article->save();
            
            // Step 6: Clean up tmp file
            if (file_exists($absolutePath)) {
                unlink($absolutePath);
                Log::info('Tmp file cleaned up', ['path' => $absolutePath]);
            }
            
            // Success message
            if ($commentFailed) {
                $messages->add('success', "O Artigo foi publicado no Facebook com sucesso!");
                $messages->add('warning', "⚠️ Não foi possível adicionar o comentário automaticamente. Por favor, adicione o link manualmente nos comentários ou configure as permissões da App do Facebook.");
            } else {
                $messages->add('success', "O Artigo foi publicado no Facebook com sucesso! Link adicionado nos comentários.");
            }
            
            Audit::add(
                Audit::ACTION_CREATE,
                'FacebookPost',
                null,
                [
                    'article_id' => $article->id, 
                    'post_id' => $postId, 
                    'comment_id' => $commentId,
                    'comment_failed' => $commentFailed,
                ]
            );
            
        } catch (\Exception $e) {
            Log::error('Failed to post article to Facebook', [
                'article_id' => $article->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $messages->add('error', "Não foi possível publicar o artigo na página de Facebook: " . $e->getMessage());
            
            // Clean up tmp file if it exists
            if (isset($absolutePath) && file_exists($absolutePath)) {
                unlink($absolutePath);
            }
            
            return redirect(route('articles.index'))->with(['popup_message' => $messages]);
        }

        return redirect(route('articles.index'))->with(['popup_message' => $messages]);
    }

    public function generateSocialImage(Article $article, ImageManager $manager)
    {
        Log::info('Generating social media image for article ' . $article->id . ' by user ' . Auth::user()->name);

        $base = $this->createArticleSocialImage($article, $manager);

        // Generate filename
        $name = 'article-' . $article->id . '-social.jpg';
        $base = $base->encode('jpg', 95);

        $headers = [
            'Content-Type' => 'image/jpeg',
            'Content-Disposition' => 'attachment; filename=' . $name,
        ];

        Audit::add(Audit::ACTION_VIEW, "ArticleSocialImage", null, $article->toArray());
        Log::info("Social media image generated for article " . $article->id);

        return response()->stream(function () use ($base) {
            echo $base;
        }, 200, $headers);
    }

    /**
     * Create the article social media image (reusable for download and posting)
     */
    private function createArticleSocialImage(Article $article, ImageManager $manager)
    {
        // Base image dimensions
        $baseWidth = 1080;
        $baseHeight = 1350;

        // Load base image
        $base = $manager->make(public_path('/images/article_post_base.png'));

        // Title section (top part)
        $titleHeight = 300;
        $titleY = 370;

        // Wrap and center title text
        $this->drawWrappedText($base, $article->title, (int) ($baseWidth / 2), $titleY, $baseWidth - 100, 50, $manager);

        // Media section (16:9 aspect ratio)
        $mediaWidth = (int) ($baseWidth - 100); // 980px
        $mediaHeight = (int) (($mediaWidth * 9) / 16); // 551px
        
        // Position media so its bottom is 15% from the bottom
        $bottomMargin = (int) ($baseHeight * 0.15); // 135px from bottom
        $mediaBottom = $baseHeight - $bottomMargin; // 1215px
        $mediaY = (int) ($mediaBottom - $mediaHeight); // Top position of media

        // Get media image
        if ($article->media) {
            if ($article->media->media_type == 'image') {
                // Use full image
                $mediaPath = public_path($article->media->url);
            } else {
                // Use thumbnail for video or other types
                $mediaPath = public_path($article->getThumbnail());
            }
        } else {
            // Use placeholder
            $mediaPath = public_path(Media::getPlaceholder('16:9', $article->id));
        }

        // Insert and resize media
        $mediaImg = $manager->make($mediaPath);
        $mediaImg->fit($mediaWidth, $mediaHeight);
        $base->insert($mediaImg, 'top-left', (int) 50, $mediaY);

        return $base;
    }

    private function drawWrappedText($image, $text, $x, $y, $maxWidth, $fontSize, $manager)
    {
        $fontPath = public_path('Roboto-Black.ttf');
        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';

        // Word wrapping
        foreach ($words as $word) {
            $testLine = $currentLine . ($currentLine ? ' ' : '') . $word;
            $testImg = $manager->canvas(1, 1);
            $testImg->text($testLine, 0, 0, function($font) use ($fontPath, $fontSize) {
                $font->file($fontPath);
                $font->size($fontSize);
            });
            
            // Simple width estimation
            if (strlen($testLine) * ($fontSize * 0.6) <= $maxWidth) {
                $currentLine = $testLine;
            } else {
                if ($currentLine) {
                    $lines[] = $currentLine;
                }
                $currentLine = $word;
            }
        }
        
        if ($currentLine) {
            $lines[] = $currentLine;
        }

        // Draw lines centered
        $lineHeight = $fontSize + 10;
        $startY = (int) ($y - ((count($lines) - 1) * $lineHeight / 2));

        foreach ($lines as $index => $line) {
            $currentY = (int) ($startY + ($index * $lineHeight));
            
            // Shadow
            $image->text($line, $x - 3, $currentY + 3, function($font) use ($fontPath, $fontSize) {
                $font->file($fontPath);
                $font->size($fontSize);
                $font->color('#282828');
                $font->align('center');
                $font->valign('center');
            });
            
            // Main text
            $image->text($line, $x, $currentY, function($font) use ($fontPath, $fontSize) {
                $font->file($fontPath);
                $font->size($fontSize);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('center');
            });
        }
    }
}
