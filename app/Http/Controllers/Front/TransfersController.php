<?php

namespace App\Http\Controllers\Front;

use App\Transfer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransfersController extends Controller
{
    public function index() {

        $transfers = Transfer::where('visible', true)->orderBy('date', 'desc')->paginate(config('custom.results_per_page'));

        return view('front.pages.transfers', ['transfers' => $transfers]);
    }
}
