<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomePageController extends Controller
{
    //Landing page of the website
    public function index() {

        $user = Auth::user();

        return view('front.pages.homepage');
    }

    public function home() {
        return redirect(route('homePage'));
    }
}
