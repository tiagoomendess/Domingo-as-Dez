<?php


namespace App\Http\Controllers\Api;


use App\ArticleComment;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleCommentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only('comment');
    }

    public function get(int $articleId)
    {
        $comments = ArticleComment::where(['article_id' => $articleId, 'article_comment_id' => null])->get();

        $result = [];

        foreach ($comments as $comment) {
            $result[] = $this->buildComment($comment);
        }

        return JsonResponse::create($result);
    }

    public function comment(Request $request, int $articleId, string $comment)
    {
        dd([$request, $articleId, $comment]);
    }

    public function delete(int $commentId)
    {

    }

    private function buildComment(ArticleComment $comment)
    {
        $new = new \stdClass();
        $new->id = $comment->id;
        $new->article_id = $comment->article_id;
        $new->name = $comment->user->name;
        $new->picture = $comment->user->profile->getPicture();
        $new->content = $comment->content;

        foreach ($comment->child_comments as $child_comment) {
            $new->replies[] = $this->buildComment($child_comment);
        }

        return $new;
    }
}
