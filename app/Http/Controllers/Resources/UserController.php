<?php

namespace App\Http\Controllers\Resources;

use App\Audit;
use App\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\User;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:users.edit')->only(['edit', 'update']);
        $this->middleware('permission:users.create')->only(['create', 'store', 'destroy']);
        $this->middleware('permission:users');
    }

    public function index(Request $request) {

        $permAdmin = Permission::where('name', 'admin')->first();
        $admins = $permAdmin->users;

        if ($request->query->get('search')) {
            $users = User::search($request->query->all());
        } else {
            $users = User::where('verified', 1)->orderBy('id', 'desc')->paginate(config('custom.results_per_page'));
        }

        return view('backoffice.pages.users', [
            'users' => $users,
            'admins' => $admins,
            'searchFields' => User::SEARCH_FIELDS,
            'queryParams' => $request->query->all()
        ]);
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

        $user = User::findOrFail($id);
        $permissions = $user->permissions;
        $profile = $user->profile;

        return view('backoffice.pages.edit_user', ['user' => $user, 'permissions' => $permissions, 'profile' => $profile]);
    }

    public function store(Request $request) {
        abort(404);
    }

    public function update($id) {
        abort(404);
    }

    public function addPermission(Request $request) {

        $request->validate([
            'user_id' => 'integer|required',
            'permission_id' => 'integer|required',
        ]);

        $permission = Permission::find($request->input('permission_id'));
        $user = User::find($request->input('user_id'));
        $old_user = $user->toArray();
        $old_user['permissions'] = $user->permissions->toArray();

        if (!$user || !$permission)
            return response(404);

        if (!$user->hasPermission($permission->name)) {

            $user->permissions()->attach($permission->id);
            $user->save();

            $new_user = $user->toArray();
            $new_user['permissions'] = $user->permissions->toArray();
            $new_user['permissions'][] = $permission->toArray();

            Audit::add(Audit::ACTION_UPDATE, 'User', $old_user,  $new_user);
        }

        return response(200);
    }

    public function removePermission(Request $request) {

        $request->validate([
            'user_id' => 'integer|required',
            'permission_id' => 'integer|required',
        ]);

        $permission = Permission::find($request->input('permission_id'));
        $user = User::find($request->input('user_id'));
        $old_user = $user->toArray();
        $old_user['permissions'] = $user->permissions->toArray();

        if (!$user || !$permission)
            return response(404);

        $user->permissions()->detach($permission->id);
        $user->save();

        $new_user = User::find($user->id);
        $new_perms = $new_user->permissions->toArray();
        $new_user = $new_user->toArray();
        $new_user['permissions'] = $new_perms;

        Audit::add(Audit::ACTION_UPDATE, 'User', $old_user,  $new_user);

        return response(200);
    }

    public function getPermissionsJson($id) {

        $user = User::findOrFail($id);

        return response()->json($user->permissions);
    }

}
