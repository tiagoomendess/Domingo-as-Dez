<?php


namespace App\Http\Controllers\Api;


use App\ArticleComment;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleCommentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['comment', 'delete']);
    }

    public function get(int $articleId)
    {
        $comments = ArticleComment::where(['article_id' => $articleId, 'article_comment_id' => null, 'deleted' => false])
            ->orderBy('id', 'asc')
            ->get();

        $result = [];

        foreach ($comments as $comment) {
            $result[] = $this->buildComment($comment);
        }

        return JsonResponse::create($result);
    }

    public function comment(Request $request, int $article_id, string $article_comment_id = null)
    {
        dd("Ola");
        $user = auth(null)->user();
        if (!$user)
            abort(403);

        $request->validate([
            'comment' => 'required|max:255|string'
        ]);

        $newComment = ArticleComment::create([
            'user_id' => $user->id,
            'article_id' => $article_id,
            'article_comment_id' => $article_comment_id,
            'content' => $request->input('comment')
        ]);

        return JsonResponse::create($this->buildComment($newComment));
    }

    public function delete(int $commentId)
    {
        $user = Auth::user();

        if (!$user)
            abort(403);

        $comment = ArticleComment::findOrFail($commentId);

        $comment->deleted = true;
        $comment->save();

        return JsonResponse::create(['message' => 'Comentário apagado']);
    }

    private function buildComment(ArticleComment $comment)
    {
        $new = new \stdClass();
        $new->id = $comment->id;
        $new->article_id = $comment->article_id;
        $new->name = $comment->user->name;
        $new->picture = $comment->user->profile->getPicture();
        $new->content = $comment->content;
        $new->date = $comment->created_at->timezone('Europe/Lisbon')->format('d/m/Y \à\s H:i');
        $new->user_id = $comment->user_id;

        foreach ($comment->child_comments as $child_comment) {
            $new->replies[] = $this->buildComment($child_comment);
        }

        return $new;
    }
}
