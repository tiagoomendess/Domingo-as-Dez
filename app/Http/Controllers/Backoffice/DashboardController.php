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

    public function index()
    {
        $usersWithPermissions = DB::table('user_permissions')->selectRaw('DISTINCT users.id, users.name')->join('users', 'user_permissions.id', '=', 'users.id')->get();
        return view('backoffice.pages.dashboard')->with(['usersWithPermissions' => $usersWithPermissions]);
    }
}
