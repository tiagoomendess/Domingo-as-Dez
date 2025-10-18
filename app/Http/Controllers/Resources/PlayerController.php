<?php

namespace App\Http\Controllers\Resources;

use App\Audit;
use App\Media;
use App\Player;
use App\Transfer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\View\View;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;

class PlayerController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:players')->only(['index', 'show']);
        $this->middleware('permission:players.edit')->only(['edit', 'update']);
        $this->middleware('permission:players.create')->only(['create', 'store', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        if ($request->query->get('search')) {
            $players = Player::search($request->query->all());
        } else {
            $players = Player::orderBy('id', 'desc')->paginate(config('custom.results_per_page'));
        }

        return view('backoffice.pages.players', [
            'players' => $players,
            'searchFields' => Player::SEARCH_FIELDS,
            'queryParams' => $request->query->all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
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
            'picture' => 'nullable|file|mimes:png,jpg|max:2000',
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

        $name = ucwords(strtolower($request->input('name')));
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

            $media = Media::create([
                'user_id' => Auth::user()->id,
                'url' => $url,
                'media_type' => 'image',
                'tags' => $tags,
            ]);

            $media->generateThumbnail();
        }

        if($request->input('team_id') == null || $request->input('team_id') == 0)
            $team_id = null;
        else
            $team_id = $request->input('team_id');

        $player = DB::transaction(function () use ($name, $nickname, $url, $association_id, $phone, $email, $facebook_profile, $obs, $position, $visible, $birth_date, $team_id) {
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
                'birth_date' => $birth_date,
                'team_id' => $team_id,
            ]);

            // Create new transfer if user defined a team to that player
            if ($team_id) {
                $transfer = Transfer::create([
                    'player_id' => $player->id,
                    'team_id' => $team_id,
                    'date' => Carbon::now()->format('Y-m-d'),
                    'visible' => true,
                ]);

                Audit::add(Audit::ACTION_CREATE, 'Transfer', null, $transfer->toArray());
            }

            Audit::add(Audit::ACTION_CREATE, 'Player', null, $player->toArray());

            return $player;
        });

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
            'picture' => 'nullable|file|mimes:png,jpg|max:2000',
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

        $player = Player::findOrFail($id);
        $old_player = $player->toArray();

        if($request->input('visible') == 'true')
            $visible = true;
        else
            $visible = false;

        $name = ucwords(strtolower($request->input('name')));
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

        // Check if team changed
        if($request->input('team_id') == null || $request->input('team_id') == 0)
            $team_id = null;
        else
            $team_id = $request->input('team_id');

        $team_changed = $player->team_id != $team_id;

        DB::transaction(function () use ($player, $name, $association_id, $nickname, $phone, $email, $facebook_profile, $obs, $position, $visible, $birth_date, $team_id, $team_changed) {
            $player->name = $name;
            $player->association_id = $association_id;
            $player->nickname = $nickname;
            $player->phone = $phone;
            $player->email = $email;
            $player->facebook_profile = $facebook_profile;
            $player->obs = $obs;
            $player->position = $position;
            $player->visible = $visible;
            $player->birth_date = $birth_date;
            $player->team_id = $team_id;

            $player->save();

            // Create transfer if team changed
            if ($team_changed) {
                $transfer = Transfer::create([
                    'player_id' => $player->id,
                    'team_id' => $team_id,
                    'date' => Carbon::now()->format('Y-m-d'),
                    'visible' => true,
                ]);

                Audit::add(Audit::ACTION_CREATE, 'Transfer', null, $transfer->toArray());
            }
        });

        $messages = new MessageBag();
        $messages->add('success', trans('success.model_edited', ['model_name' => trans('models.player')]));

        Audit::add(Audit::ACTION_UPDATE, 'Player', $old_player, $player->toArray());

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
        $old_player = $player->toArray();
        $player->delete();

        $messages = new MessageBag();
        $messages->add('success', trans('success.model_deleted', ['model_name' => trans('models.player')]));

        Audit::add(Audit::ACTION_DELETE, 'Player', $old_player);

        return redirect(route('players.index'))->with(['popup_message' => $messages]);
    }

    /**
     * Search players for autocomplete
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function autocomplete(Request $request)
    {
        $request->validate([
            'term' => 'required|string|min:2|max:100'
        ]);

        $term = $request->get('term');
        
        $players = Player::where('name', 'LIKE', '%' . $term . '%')
            ->orWhere('nickname', 'LIKE', '%' . $term . '%')
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'nickname', 'picture']);

        $results = $players->map(function($player) {
            return [
                'id' => $player->id,
                'text' => $player->nickname ? $player->name . ' (' . $player->nickname . ')' : $player->name,
                'picture' => $player->picture ?: config('custom.default_profile_pic')
            ];
        });

        return response()->json($results);
    }
}
