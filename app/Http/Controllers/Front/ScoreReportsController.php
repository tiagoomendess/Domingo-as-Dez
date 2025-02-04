<?php

namespace App\Http\Controllers\Front;

use App\Game;
use App\Http\Controllers\Controller;
use App\ScoreReport;
use App\ScoreReportBan;
use App\UserUuid;
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
    public function __construct() {
        $this->middleware('ensure-uuid')->only(['create', 'store']);
    }

    public function create(Request $request, Game $game) {

        $backUrl = $request->input('returnTo', url()->previous($game->getPublicUrl()));
        if (!filter_var($backUrl, FILTER_VALIDATE_URL)) {
            $backUrl = $game->getPublicUrl();
        }

        $ip_address = $request->getClientIp() ?? 'Unknown';
        $ip_country = Str::limit($request->header('CF-IPCountry', 'Unknown'), 155, '');
        $uuid = $request->cookie('uuid');
        if (empty($uuid) || Str::length($uuid) > 36) {
            $uuid = Str::limit(Str::uuid(), 36, '');
            $request->cookies->add(['uuid' => $uuid]);
        }

        Log::debug("Score Report page opened for game $game->id by ip $ip_address country $ip_country uuid $uuid");

        $ban = ScoreReportBan::findMatch(
            $uuid,
            empty($user) ? null : $user->id,
            null,
            $request->header('User-Agent')
        );

        $user_id = null;
        $user = Auth::user();
        if (!empty($user)) {
            $user_id = Auth::user()->id;
        }
        $already_sent = ScoreReport::GetAlreadySent($game->id, $uuid, $user_id);

        if (!empty($ban)) {
            Log::info("A Banned user($user_id) is trying to submit a score report from $ip_address in country $ip_country uuid $uuid");
        }

        // if passed 105 minutes from kickoff, can_finish is true
        $now = Carbon::now();
        $game_start_date = Carbon::createFromFormat("Y-m-d H:i:s", $game->date);
        $can_finish = $now->diffInMinutes($game_start_date) > 105;

        $response = new Response(view('front.pages.score_report', [
            'game' => $game,
            'backUrl' => $backUrl,
            'ban' => $ban,
            'already_sent' => $already_sent,
            'can_finish' => $can_finish,
        ]));
        $response->withCookie(cookie('uuid', $uuid, 525948));

        return $response;
    }

    public function store(Request $request, Game $game) {
        // get uuid from cookie
        $uuid = $request->cookie('uuid');
        if (empty($uuid) || Str::length($uuid) > 36) {
            $uuid = Str::limit($this->guidv4(), 36, '');
            Log::info("Error creating Score Report. UUID was missing, created new one: $uuid");

            // redirect back with cookie and error
            return redirect()
                ->back()
                ->withErrors(['uuid' => 'Ocorreu um erro, por favor tente de novo'])
                ->withInput()
                ->withCookie(cookie()->forever('uuid', $uuid));
        }

        $validatorRules = [
            'home_score' => 'required|integer|min:0|max:32',
            'away_score' => 'required|integer|min:0|max:32',
            'latitude' => 'numeric|nullable',
            'longitude' => 'numeric|nullable',
            'accuracy' => 'numeric|nullable',
            'ip' => 'string|max:155|nullable',
            'redirect_to' => 'string|max:255|nullable',
            'finished' => 'nullable|string|max:7|min:3',
        ];

        $user = Auth::user();
        $user_id = 0;
        // If not logged in require captcha
        if (empty($user)) {
            $validatorRules['g-recaptcha-response'] = 'required|recaptcha';
        } else {
            $user_id = $user->id;
            UserUuid::addIfNotExist($user_id, $uuid);
        }

        $validator = Validator::make($request->all(), $validatorRules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (!$game->allowScoreReports()) {
            return redirect()
                ->back()
                ->withErrors(['game' => 'Este jogo não aceita resultados porque ainda não começou ou já terminou'])
                ->withInput()
                ->withCookie(cookie()->forever('uuid', $uuid));
        }

        $messages = new MessageBag();
        $ip_country = Str::limit($request->header('CF-IPCountry'), 155, '');
        $url = $request->input('redirect_to', $game->getPublicUrl());
        $ip = $request->getClientIp();
        $finished = $request->input('finished', false) === 'true';
        if (empty($ip)) {
            $ip = $request->input('ip', '');
        }

        if (empty($ip)) {
            Log::warning("Could not get user ip address: uuid($uuid) user($user_id) game($game->id) score "
                . $request->input('home_score') . " - " . $request->input('away_score'));
        }

        $ban = ScoreReportBan::findMatch(
            $uuid,
            empty($user) ? null : $user->id,
            $ip,
            $request->header('User-Agent')
        );
        if (!empty($ban)) {
            if ($ban->shadow_ban) {
                Log::info("User ($user_id) and uuid($uuid) tried to send score report for game $game->id but is shadow banned ban($ban->id)");
                $messages->add('success', 'Obrigado por enviar o resultado, poderá levar alguns minutos até ser atualizado no website');
                return redirect($url)
                    ->with('popup_message', $messages);
            }

            $expire = Carbon::createFromFormat("Y-m-d H:i:s", $ban->expires_at)->format("d/m/Y \à\s H:i");
            $reason = $ban->reason;
            return redirect()
                ->back()
                ->withErrors(['uuid' => "Você foi bloqueado temporariamente e não pode enviar resultados até $expire. Razão: $reason "])
                ->withInput()
                ->withCookie(cookie()->forever('uuid', $uuid));
        }

        $sameReportFromSameUser = DB::table('score_reports')
            ->whereRaw('game_id = ? AND home_score = ? AND away_score = ? AND finished = ? AND (user_id = ? OR uuid = ?)', [
                $game->id,
                $request->input('home_score'),
                $request->input('away_score'),
                $finished,
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

        $recentTotalByIpAddress = ScoreReport::where('ip_address', $ip)
            ->where('source', 'website')
            ->where('created_at', '>', $now->subMinutes(4))
            ->where('game_id', $game->id)
            ->count();

        // if total equal or greater than 3 return error
        if ($recentTotalByIpAddress >= 3) {
            Log::info("Score report blocked from ip $ip for game " . $game->id . " because of too many reports");
            return redirect()
                ->back()
                ->withErrors(['ip' => 'Já temos muitos registos vindos da sua rede nos últimos minutos. Por favor tente de novo mais tarde'])
                ->withInput();
        }

        $location = $this->getMysqlPoint($request->input('latitude'), $request->input('longitude'));

        $home_score = $request->input('home_score');
        $away_score = $request->input('away_score');
        $ip = Str::limit($ip, 45, '');

        // If score is ridiculous, ban user
        if ($home_score > 20 || $away_score > 20) {
            Log::info("Score report of $home_score-$away_score blocked for game $game->id");
            ScoreReportBan::create([
                'uuid' => $uuid,
                'ip_address' => $ip,
                'user_agent' => Str::limit($request->header('User-Agent'), 255, ''),
                'reason' => "Envio de resultados falsos no jogo " . $game->home_team->club->name . " vs " . $game->away_team->club->name,
                'expires_at' => Carbon::now()->addDays(2),
            ]);

            Log::info("User with uuid $uuid and ip $ip was banned for sending invalid score report");

            return redirect()
                ->back();
        }

        $location_accuracy = $request->input('accuracy');
        if (!empty($location_accuracy)) {
            // max accuracy is 1000 meters
            $location_accuracy = min($location_accuracy, 1000);
        }

        ScoreReport::create([
            'user_id' => empty($user) ? null : $user->id,
            'game_id' => $game->id,
            'home_score' => $home_score,
            'away_score' => $away_score,
            'source' => 'website',
            'ip_address' => $ip,
            'ip_country' => $ip_country,
            'user_agent' => Str::limit($request->header('User-Agent'), 255, ''),
            'location' => $location,
            'location_accuracy' => $location_accuracy,
            'uuid' => Str::limit($uuid, 36, ''),
            'finished' => $finished,
        ]);

        $successMessage = "Resultado de $home_score-$away_score enviado, obrigado pelo seu contributo. ";
        if (empty($location)) {
            $successMessage .= ' No entanto a localização não foi enviada junto com o resultado. Para a próxima considere enviar também a localização para a sua informação ter mais relevância. ';
        }
        $successMessage .= "O resultado do jogo pode demorar alguns minutos até ser atualizado no website.";
        $messages->add('success', $successMessage);
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
