<?php

namespace App\Http\Controllers\Front;

use App\Game;
use App\Http\Controllers\Controller;
use App\ScoreReport;
use App\ScoreReportBan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

class ScoreReportsController extends Controller
{
    public function create(Request $request, Game $game) {

        $backUrl = $request->input('returnTo', url()->previous($game->getPublicUrl()));
        if (!filter_var($backUrl, FILTER_VALIDATE_URL)) {
            $backUrl = $game->getPublicUrl();
        }

        $uuid = $request->cookie('uuid');
        if (empty($uuid)) {
            $uuid = Str::limit($this->guidv4(), 36, '');
            $request->cookies->add(['uuid' => $uuid]);
        }

        $uuid = Str::limit($uuid, 36, '');

        $ban = ScoreReportBan::findMatch(
            $uuid,
            empty($user) ? null : $user->id,
            null,
            $request->header('User-Agent')
        );

        $response = new Response(view('front.pages.score_report', [
            'game' => $game,
            'backUrl' => $backUrl,
            'ban' => $ban,
        ]));
        $response->withCookie(cookie('uuid', $uuid, 525948));

        return $response;
    }

    public function store(Request $request, Game $game) {

        $validatorRules = [
            'home_score' => 'required|integer|min:0|max:32',
            'away_score' => 'required|integer|min:0|max:32',
            'latitude' => 'numeric|nullable',
            'longitude' => 'numeric|nullable',
            'accuracy' => 'numeric|nullable',
            'ip' => 'string|max:155|nullable',
            'redirect_to' => 'string|max:255|nullable',
        ];

        $user = Auth::user();
        $user_id = empty($user) ? '0' : $user->id;

        // If not logged in require captcha
        if (empty($user)) {
            $validatorRules['g-recaptcha-response'] = 'required|recaptcha';
        }

        $validator = Validator::make($request->all(), $validatorRules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // get uuid from cookie
        $uuid = $request->cookie('uuid');
        if (empty($uuid)) {
            $uuid = Str::limit($this->guidv4(), 36, '');
            $request->cookies->add(['uuid' => $uuid]);
            Log::info("Error creating Score Report. UUID was missing, created new one: $uuid");

            // redirect back with cookie and error
            return redirect()
                ->back()
                ->withErrors(['uuid' => 'Ocorreu um erro, por favor tente de novo'])
                ->withInput()
                ->withCookie(cookie()->forever('uuid', $uuid));
        }

        if (Str::length($uuid) != 36) {
            $uuid = Str::limit($uuid, 36, '');
        }

        if (!$game->allowScoreReports()) {
            return redirect()
                ->back()
                ->withErrors(['game' => 'Este jogo não aceita resultados porque ainda não começou ou já terminou'])
                ->withInput()
                ->withCookie(cookie()->forever('uuid', $uuid));
        }

        $ban = ScoreReportBan::findMatch(
            $uuid,
            empty($user) ? null : $user->id,
            $request->input('ip'),
            $request->header('User-Agent')
        );
        if (!empty($ban)) {
            $expire = Carbon::createFromFormat("Y-m-d H:i:s", $ban->expires_at)->format("d/m/Y \à\s H:i");
            $reason = $ban->reason;
            return redirect()
                ->back()
                ->withErrors(['uuid' => "Você foi bloqueado temporariamente e não pode enviar resultados até $expire. Razão: $reason "])
                ->withInput()
                ->withCookie(cookie()->forever('uuid', $uuid));
        }

        $sameReportFromSameUser = DB::table('score_reports')
            ->whereRaw('game_id = ? AND home_score = ? AND away_score = ? AND (user_id = ? OR uuid = ?)', [
                $game->id,
                $request->input('home_score'),
                $request->input('away_score'),
                empty($user) ? null : $user->id,
                $uuid,
            ])->count();
        if ($sameReportFromSameUser > 0) {
            Log::info("User ($user_id) tried to send same score report twice for game " . $game->id . " - uuid($uuid)");

            return redirect()
                ->back()
                ->withErrors(['game' => 'Já enviou este resultado e não precisa de faze-lo novamente, obrigado pelo seu contributo'])
                ->withInput()
                ->withCookie(cookie()->forever('uuid', $uuid));
        }

        $now = Carbon::now();

        $recentReportByUserId = null;
        if (!empty($user)) {
            $recentReportByUserId = ScoreReport::where('user_id', $user->id)
                ->where('source', 'website')
                ->where('created_at', '>', $now->subMinutes(4))
                ->first();
        }

        $recentReportByUuid = ScoreReport::where('uuid', $uuid)
            ->where('source', 'website')
            ->where('created_at', '>', $now->subMinutes(4))
            ->first();

        // if got any report in the past 5 minutes return error
        if (!empty($recentReportByUserId) || !empty($recentReportByUuid)) {
            return redirect()
                ->back()
                ->withErrors(['uuid' => 'Já enviou um resultado recentemente, por favor tente de novo mais tarde'])
                ->withInput();
        }

        $recentTotalByIpAddress = ScoreReport::where('ip_address', $request->input('ip'))
            ->where('source', 'website')
            ->where('created_at', '>', $now->subMinutes(4))
            ->where('game_id', $game->id)
            ->count();

        // if total equal or greater than 3 return error
        if ($recentTotalByIpAddress >= 3) {
            Log::info("Score report blocked from ip " . $request->input('ip') . " for game " . $game->id . " because of too many reports");
            return redirect()
                ->back()
                ->withErrors(['ip' => 'Já temos muitos registos vindos da sua rede nos últimos minutos. Por favor tente de novo mais tarde'])
                ->withInput();
        }

        $location = $this->getMysqlPoint($request->input('latitude'), $request->input('longitude'));

        $home_score = $request->input('home_score');
        $away_score = $request->input('away_score');
        ScoreReport::create([
            'user_id' => empty($user) ? null : $user->id,
            'game_id' => $game->id,
            'home_score' => $home_score,
            'away_score' => $away_score,
            'source' => 'website',
            'ip_address' => Str::limit($request->input('ip'), 45, ''),
            'user_agent' => Str::limit($request->header('User-Agent'), 255, ''),
            'location' => $location,
            'location_accuracy' => $request->input('accuracy') ? (int) $request->input('accuracy') : null,
            'uuid' => Str::limit($uuid, 36, ''),
        ]);

        $successMessage = "Resultado de $home_score-$away_score enviado, obrigado pelo seu contributo. ";
        if (empty($location)) {
            $successMessage .= ' No entanto a localização não foi enviada junto com o resultado. Para a próxima considere enviar também a localização para a sua informação ter mais relevância. ';
        }
        $successMessage .= "O resultado do jogo pode demorar alguns minutos até ser atualizado no website.";

        $messages = new MessageBag();
        $messages->add('success', $successMessage);

        $url = $request->input('redirect_to', $game->getPublicUrl());

        $logMessage = "User ($user_id) created score report of $home_score-$away_score for game " . $game->id . " - uuid($uuid)";
        Log::info($logMessage);

        return redirect($url)
            ->with('popup_message', $messages);
    }

    private function getMysqlPoint($latitude, $longitude) {
        if (empty($latitude) || empty($longitude)) {
            return null;
        }

        return DB::raw("ST_GeomFromText('POINT(" . $latitude . " " . $longitude . ")')");
    }

    private function guidv4($data = null) {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
