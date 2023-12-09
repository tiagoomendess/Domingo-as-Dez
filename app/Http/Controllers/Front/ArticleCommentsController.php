<?php

namespace App\Http\Controllers\Front;

use App\ArticleComment;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;

class ArticleCommentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function comment(Request $request, int $article_id, string $article_comment_id = null)
    {
        $user = auth(null)->user();
        if (!$user)
            abort(403);

        $messages = new MessageBag();

        $request->validate([
            'comment' => 'required|max:255|string'
        ]);

        $lastComment = ArticleComment::where('user_id', $user->id)->orderBy('id', 'desc')->first();
        $now = Carbon::now();
        if ($lastComment && ($now->timestamp - $lastComment->created_at->timestamp) < 10) {
            $messages->add('error', 'Estás a tentar comentar muito rápido. Espera um pouco');
            Log::info("User $user->id tried to comment too fast");
            return redirect()->back()->with(['popup_message' => $messages]);
        }

        ArticleComment::create([
            'user_id' => $user->id,
            'article_id' => $article_id,
            'article_comment_id' => $article_comment_id,
            'content' => $request->input('comment')
        ]);

        Log::info("User $user->id commented on article $article_id");
        $messages->add('success', 'Comentário adicionado com sucesso');

        return redirect()->back()->with(['popup_message' => $messages]);
    }

    public function delete(int $comment_id)
    {
        $user = Auth::user();
        $comment = ArticleComment::findOrFail($comment_id);
        $messages = new MessageBag();

        if ($user) {

            $canDelete = has_permission('admin') || $user->id == $comment->user_id;

            if ($canDelete) {
                $comment->deleted = true;
                $comment->save();
                $messages->add('success', 'Comentário eliminado com sucesso');
                Log::info("User $user->id deleted comment $comment_id");

                return redirect()->back()->with(['popup_message' => $messages]);
            } else {
                Log::info("User $user->id tried to delete comment $comment_id without permission");

                $messages->add('error', 'Não podes apagar esse comentário');
            }
        } else {
            Log::info("Someone tried to delete comment $comment_id without being logged in");
            abort(403);
        }

        return redirect()->back()->with(['popup_message' => $messages]);
    }
}
