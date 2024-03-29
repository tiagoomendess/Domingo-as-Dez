<?php

namespace App\Http\Controllers\Backoffice;

use App\Audit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:admin')->only(['index']);
    }

    public function index(Request $request): View
    {
        if ($request->query->get('search')) {
            $audits = Audit::search($request->query->all());
        } else {
            $audits = Audit::orderBy('id', 'desc')->paginate(100);
        }

        return view('backoffice.pages.audits', [
            'audits' => $audits,
            'searchFields' => Audit::SEARCH_FIELDS,
            'queryParams' => $request->query->all()
        ]);
    }
}
