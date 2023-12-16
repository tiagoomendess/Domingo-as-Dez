<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\Page;
use App\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\View\View;

class PartnerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:partners')->only(['index', 'show']);
        $this->middleware('permission:partners.edit')->only(['edit', 'update']);
        $this->middleware('permission:partners.create')->only(['create', 'store', 'destroy']);
    }

    public function index()
    {
        $partners = Partner::orderBy('id', 'desc')->paginate(config('custom.results_per_page'));

        return view('backoffice.pages.partners', [
            'partners' => $partners
        ]);
    }

    public function create()
    {
        return view('backoffice.pages.create_partner');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50|string',
            'url' => 'required|max:255|string|url',
            'priority' => 'required|integer|min:1|max:100',
            'picture' => 'required|mimes:jpeg,jpg,png|max:20000',
            'visible' => 'required',
        ]);

        $picture = MediaController::storeImage(
            $request->file('picture'),
            str_replace(' ', ',', $request->input('name'))
        );

        $partner = Partner::create([
            'name' => $request->input('name'),
            'url' => $request->input('url'),
            'priority' => $request->input('priority'),
            'picture' => $picture,
            'visible' => $request->input('visible') == 'true',
        ]);

        return redirect(route('partners.show', ['partner' => $partner]));
    }

    /**
     * Display the specified resource.
     *
     * @return View
     */
    public function show(Partner $partner)
    {
        return view('backoffice.pages.partner', ['partner' => $partner]);
    }

    /**
     * Show the form for editing the specified resource.

     * @return View
     */
    public function edit(Partner $partner)
    {
        return view('backoffice.pages.edit_partner', ['partner' => $partner]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:50|string',
            'url' => 'required|max:255|string|url',
            'priority' => 'required|integer|min:1|max:100',
            'visible' => 'required',
        ]);

        $partner = Partner::findOrFail($id);
        $partner->name = $request->input('name');
        $partner->url = $request->input('url');
        $partner->priority = $request->input('priority');
        $partner->visible = $request->input('visible') == 'true';
        $partner->save();

        return redirect(route('partners.show', ['partner' => $partner]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     */
    public function destroy(Partner $partner)
    {
        $partner->delete();

        $message = new MessageBag();
        $message->add('success', trans('success.model_deleted', ['model_name' => trans('models.partner')]));

        return redirect(route('partners.index'))->with(['popup_message' => $message]);
    }
}