<?php

namespace App\Http\Controllers\Resources;

use App\Audit;
use App\Transfer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\MessageBag;

class TransferController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:transfers.edit')->only(['edit', 'update']);
        $this->middleware('permission:transfers.create')->only(['create', 'store', 'destroy']);
        $this->middleware('permission:transfers');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transfers = Transfer::orderBy('id', 'desc')->paginate(config('custom.results_per_page'));

        return view('backoffice.pages.transfers', ['transfers' => $transfers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backoffice.pages.create_transfer');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'player_id' => 'required|integer|exists:players,id',
            'date' => 'required|date',
            'team_id' => 'nullable|integer|exists:teams,id',
            'visible' => 'required',
        ]);

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        if($request->input('team_id') == null || $request->input('team_id') == 0)
            $team_id = null;
        else
            $team_id = $request->input('team_id');

        $date = $request->input('date');
        $player_id = $request->input('player_id');

        $transfer = Transfer::create([

            'player_id' => $player_id,
            'team_id' => $team_id,
            'date' => $date,
            'visible' => $visible,

        ]);

        Audit::add(Audit::ACTION_CREATE, 'Transfer', null, $transfer->toArray());

        return redirect(route('transfers.show', ['transfer' => $transfer]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $transfer = Transfer::findOrFail($id);
        return view('backoffice.pages.transfer', ['transfer' => $transfer]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $transfer = Transfer::findOrFail($id);
        return view('backoffice.pages.edit_transfer', ['transfer' => $transfer]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'player_id' => 'required|integer|exists:players,id',
            'date' => 'required|date',
            'team_id' => 'nullable|integer|exists:teams,id',
            'visible' => 'required',
        ]);

        $transfer = Transfer::findOrFail($id);
        $old_transfer = $transfer->toArray();

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        if($request->input('team_id') == null || $request->input('team_id') == 0)
            $team_id = null;
        else
            $team_id = $request->input('team_id');

        $date = $request->input('date');
        $player_id = $request->input('player_id');

        $transfer->player_id = $player_id;
        $transfer->team_id = $team_id;
        $transfer->date = $date;
        $transfer->visible = $visible;

        $transfer->save();

        $messages = new MessageBag();
        $messages->add('success', trans('success.model_edited', ['model_name' => trans('models.transfer')]));

        Audit::add(Audit::ACTION_UPDATE, 'Transfer', $old_transfer, $transfer->toArray());

        return redirect(route('transfers.show', ['transfer' => $transfer]))->with(['popup_message' => $messages]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $transfer = Transfer::findOrFail($id);
        $old_transfer = $transfer->toArray();
        $transfer->delete();

        $messages = new MessageBag();
        $messages->add('success', trans('success.model_deleted', ['model_name' => trans('models.transfer')]));

        Audit::add(Audit::ACTION_DELETE, 'Transfer', $old_transfer);
        return redirect(route('transfers.index'))->with(['popup_message' => $messages]);
    }
}
