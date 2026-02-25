<?php

namespace App\Http\Controllers\Front;

use App\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class TransfersController extends Controller
{
    public function index(Request $request) {
        $page = $request->input('page', 1);
        $showLoginWall = !Auth::check() && $page > 1;

        $transfers = Transfer::where('visible', true)
            ->orderBy('date', 'desc')
            ->paginate(6);

        return view('front.pages.transfers', [
            'transfers' => $transfers,
            'showLoginWall' => $showLoginWall
        ]);
    }
}
