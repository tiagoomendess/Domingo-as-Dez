<?php

namespace App\Http\Controllers\Resources;

use App\Media;
use App\Player;
use App\Transfer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\MessageBag;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;

class PlayerController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:players');
        $this->middleware('permission:players.edit')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $players = Player::orderBy('id', 'desc')->paginate(config('custom.results_per_page'));
        return view('backoffice.pages.players', ['players' => $players]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backoffice.pages.create_player');
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
            'name' => 'required|string|max:155|min:3',
            'picture' => 'nullable|image',
            'picture_url' => 'nullable|string|max:280|url',
            'association_id' => 'string|max:155|nullable',
            'nickname' => 'nullable|string|max:155|min:2',
            'phone' => 'nullable|string|max:14|min:6',
            'email' => 'nullable|string|max:100|min:3|email',
            'facebook_profile' => 'nullable|string|max:280|url',
            'obs' => 'string|max:3000|min:6|nullable',
            'position' => 'required|string|min:3|max:10',
            'visible' => 'required',
            'team_id' => 'nullable|integer|exists:teams,id',
            'birth_date' => 'nullable|date',
        ]);


        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        $name = $request->input('name');
        $association_id = $request->input('association_id');
        $nickname = $request->input('nickname');
        $phone = $request->input('phone');
        $email = $request->input('email');
        $facebook_profile = $request->input('facebook_profile');
        $obs = $request->input('obs');
        $position = $request->input('position');
        $birth_date = $request->input('birth_date');

        $image = null;
        $url = null;

        if ($request->hasFile('picture'))
            $image = Image::make($request->file('picture'));
        else if ($request->input('picture_url') != null)
            $image = Image::make($request->input('picture_url'));

        if ($image) {

            $url = MediaController::storeSquareImage($image, $name);

            $tags = trans('models.player') . ',' . $name;
            if ($nickname)
                $tags = $tags . ',' . $nickname;

            Media::create([
                'user_id' => Auth::user()->id,
                'url' => $url,
                'media_type' => 'image',
                'tags' => $tags,
            ]);
        }

        $player = Player::create([

            'name' => $name,
            'nickname' => $nickname,
            'picture' => $url,
            'association_id' => $association_id,
            'phone' => $phone,
            'email' => $email,
            'facebook_profile' => $facebook_profile,
            'obs' => $obs,
            'position' => $position,
            'visible' => $visible,
            'birth_date' => $birth_date

        ]);


        //create new transfer if user defined a club to that player
        if ($request->input('team_id')) {

            Transfer::create([
                'player_id' => $player->id,
                'team_id' => $request->input('team_id'),
                'date' => Carbon::now()->format("Y-m-d"),
                'visible' => false,
            ]);

        }

        return redirect(route('players.show', ['player' => $player]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $player = Player::findOrFail($id);
        return view('backoffice.pages.player', ['player' => $player]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $player = Player::findOrFail($id);
        return view('backoffice.pages.edit_player', ['player' => $player]);
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
            'name' => 'required|string|max:155|min:3',
            'picture' => 'nullable|image',
            'picture_url' => 'nullable|string|max:280|url',
            'association_id' => 'string|max:155|nullable',
            'nickname' => 'nullable|string|max:155|min:2',
            'phone' => 'nullable|string|max:14|min:6',
            'email' => 'nullable|string|max:100|min:3|email',
            'facebook_profile' => 'nullable|string|max:280|url',
            'obs' => 'string|max:3000|min:6|nullable',
            'position' => 'required|string|min:3|max:10',
            'visible' => 'required',
            'birth_date' => 'nullable|date',
        ]);

        $player = Player::findOrFail($id);

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        $name = $request->input('name');
        $association_id = $request->input('association_id');
        $nickname = $request->input('nickname');
        $phone = $request->input('phone');
        $email = $request->input('email');
        $facebook_profile = $request->input('facebook_profile');
        $obs = $request->input('obs');
        $position = $request->input('position');
        $birth_date = $request->input('birth_date');

        $image = null;
        $url = null;

        if ($request->hasFile('picture'))
            $image = Image::make($request->file('picture'));
        else if ($request->input('picture_url') != null)
            $image = Image::make($request->input('picture_url'));

        if ($image) {

            $url = MediaController::storeSquareImage($image, $name);

            $tags = trans('models.player') . ',' . $name;
            if ($nickname)
                $tags = $tags . ',' . $nickname;

            Media::create([
                'user_id' => Auth::user()->id,
                'url' => $url,
                'media_type' => 'image',
                'tags' => $tags,
            ]);

            $player->picture = $url;
        }

        $player->name = $name;
        $player->association_id = $association_id;
        $player->nickname =  $nickname;
        $player->phone = $phone;
        $player->email = $email;
        $player->facebook_profile = $facebook_profile;
        $player->obs = $obs;
        $player->position = $position;
        $player->visible = $visible;
        $player->birth_date = $birth_date;

        $player->save();

        $messages = new MessageBag();
        $messages->add('success', trans('success.model_edited', ['model_name' => trans('models.player')]));

        return redirect(route('players.show', ['player' => $player]))->with(['popup_message' => $messages]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $player = Player::findOrFail($id);
        $player->delete();

        $messages = new MessageBag();
        $messages->add('success', trans('success.model_deleted', ['model_name' => trans('models.player')]));

        return redirect(route('players.index'))->with(['popup_message' => $messages]);
    }
}
