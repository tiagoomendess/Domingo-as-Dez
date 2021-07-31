<?php


namespace App\Http\Controllers\Front;


use App\Http\Controllers\Controller;

class ArticleCommentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

}