<?php

namespace App\Http\Controllers\Backoffice;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {

        $user = Auth::user();
        $permissions = $user->permissions;

        return view('backoffice.pages.dashboard')->with(['user' => $user, 'permissions' => $permissions]);
    }
}
