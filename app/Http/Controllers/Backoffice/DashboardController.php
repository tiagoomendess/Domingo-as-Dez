<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:dashboard');
    }

    public function index() {

        $usersWithPermissions = DB::table('user_permissions')->join('users', 'user_permissions.id', '=', 'users.id')->select('users.id', 'users.name')->distinct('user_permissions.user_id')->get();
        //dd($usersWithPermissions);
        return view('backoffice.pages.dashboard')->with(['usersWithPermissions' => $usersWithPermissions]);
    }
}
