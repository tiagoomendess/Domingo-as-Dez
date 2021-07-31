<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticleComment extends Model
{
    protected $fillable = ['article_id', 'article_comment_id', 'content', 'user_id', 'deleted'];

    protected $guarded = [];

    protected $hidden = [];

    public function article() {
        return $this->belongsTo(Article::class);
    }

    public function parent_comment() {
        return $this->belongsTo(ArticleComment::class);
    }

    public function child_comments() {
        return $this->hasMany(ArticleComment::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
