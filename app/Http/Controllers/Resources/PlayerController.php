<?php

namespace App\Http\Controllers\Resources;

use App\Media;
use App\Player;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
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
        ]);

        $image = null;
        $name = $request->input('name');

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        if ($request->hasFile('picture')) {

            $image = Image::make($request->file('picture'));

            $image->fit(500, 500, function ($constraint) {
                $constraint->upsize();
            });

        } else if ($request->input('picture_url') != null) {

            $image = Image::make($request->input('picture_url'));

            $image->fit(500, 500, function ($constraint) {
                $constraint->upsize();
            });

        }

        $association_id = $request->input('association_id');
        $nickname = $request->input('nickname');
        $phone = $request->input('phone');
        $email = $request->input('email');
        $facebook_profile = $request->input('facebook_profile');
        $obs = $request->input('obs');
        $position = $request->input('position');


        if ($image) {

            $path = 'storage/media/images/';
            $filename = str_random(6) . '_' . str_replace(' ', '_', $name) . '.png';
            $public_url = '/storage/media/images/' . $filename;
            $image->save(public_path($path) . $filename);

            $tags = trans('models.player') . ',' . $name;

            if($nickname)
                $tags = $tags . ',' . $nickname;

            Media::create([
                'user_id' => Auth::user()->id,
                'url' => $public_url,
                'media_type' => 'image',
                'tags' => $tags,
            ]);

        } else {
            $public_url = null;
        }

        $player = Player::create([

            'name' => $name,
            'nickname' => $nickname,
            'picture' => $public_url,
            'association_id' => $association_id,
            'phone' => $phone,
            'email' => $email,
            'facebook_profile' => $facebook_profile,
            'obs' => $obs,
            'position' => $position,
            'visible' => $visible,

        ]);

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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
