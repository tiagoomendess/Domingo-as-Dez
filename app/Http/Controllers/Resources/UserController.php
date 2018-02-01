<?php

namespace App\Http\Controllers\Resources;

use App\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\User;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:admin']);
    }

    public function index() {
        $users = User::where('verified', true)->orderBy('id', 'desc')->paginate(config('custom.results_per_page'));
        $permAdmin = Permission::where('name', 'admin')->first();
        $admins = $permAdmin->users;

        return view('backoffice.pages.users', ['users' => $users, 'admins' => $admins]);
    }

    public function create() {

        abort(404);
    }

    public function show($id) {
        $user = User::findOrFail($id);

        $profile = $user->profile;
        $permissions = $user->permissions;

        return view('backoffice.pages.user', ['user' => $user, 'profile' => $profile, 'permissions' => $permissions]);
    }

    public function edit($id) {

    }

    public function store(Request $request) {
        abort(404);
    }

    public function update($id) {

    }

}
