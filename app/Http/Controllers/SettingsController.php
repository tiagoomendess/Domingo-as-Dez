<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:admin');
    }

    public function index() {
        return view('backoffice.pages.settings');
    }

    public function changeSetting(Request $request) {

        $request->validate([
            'setting_path' => 'required|string|max:100|min:2',
            'setting_value' => 'required',
        ]);

        $setting_path = $request->input('setting_path');
        $setting_value = $request->input('setting_value');

        config([$setting_path => $setting_value]);

        return response('Ok', 200);
    }

}
