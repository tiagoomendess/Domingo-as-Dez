<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Page;
use Illuminate\Support\Str;

class PagesController extends Controller
{
    public function __construct()
    {

    }

    public function show(string $slug)
    {
        $page = Page::where('slug', $slug)->first();

        if (!$page)
            abort(404);

        $description = Str::limit(strip_tags($page->body), 155);

        return view('front.pages.page', ['page' => $page, 'description' => $description]);
    }
}
