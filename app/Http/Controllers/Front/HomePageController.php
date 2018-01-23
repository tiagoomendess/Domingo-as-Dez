<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomePageController extends Controller
{
    //Landing page of the website
    public function index() {

        $user = Auth::user();

        return view('');
    }
}
