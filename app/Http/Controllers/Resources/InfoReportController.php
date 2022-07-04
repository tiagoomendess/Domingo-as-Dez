<?php

namespace App\Http\Controllers\Resources;

use App\InfoReport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InfoReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:info_reports.edit')->only(['edit', 'update']);
        $this->middleware('permission:info_reports.create')->only(['create', 'store', 'destroy']);
        $this->middleware('permission:info_reports');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->query->get('search')) {
            $infos = InfoReport::search($request->query->all());
        } else {
            $infos = InfoReport::whereIn('status', ['seen', 'sent'])
                ->orderBy('status', 'asc')
                ->orderBy('id', 'desc')
                ->paginate(config('custom.results_per_page'));
        }

        return view('backoffice.pages.info_reports', [
            'infos' => $infos,
            'searchFields' => InfoReport::SEARCH_FIELDS,
            'queryParams' => $request->query->all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     */
    public function show(InfoReport $info)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $info = InfoReport::findOrFail($id);

        if ($info->status === 'sent') {
            $info->status = 'seen';
            $info->save();
        }

        return view('backoffice.pages.edit_info_report', ['info' => $info]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:sent,seen,used,archived,deleted',
        ]);

        $info = InfoReport::findOrFail($id);
        $info->status = $request->input('status');
        $info->save();

        return redirect(route('info_reports.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
