<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\ScoreReportBan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class ScoreReportBanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:score-report-bans')->only(['index', 'show']);
        $this->middleware('permission:score-report-bans.edit')->only(['edit', 'update']);
        $this->middleware('permission:score-report-bans.create')->only(['create', 'store', 'destroy']);
    }

    public function index()
    {
        $bans = ScoreReportBan::orderBy('id', 'desc')->paginate(config('custom.results_per_page'));

        return view(
            'backoffice.pages.score_report_bans',
            [
                'bans' => $bans,
            ]
        );
    }

    public function create()
    {
        return view('backoffice.pages.create_score_report_ban');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ban_days' => 'required|integer|min:1|max:120',
            'reason' => 'string|max:255|min:3|required',
            'uuid' => 'string|max:36|min:20|nullable',
            'user_id' => 'integer|nullable',
            'ip_address' => 'string|max:45|min:3|nullable|ip',
            'user_agent' => 'string|max:255|min:3|nullable',
            'shadow_ban' => 'required',
            'ip_ban' => 'required',
        ]);

        $uuid = $request->input('uuid');
        $ban_days = $request->input('ban_days');
        $reason = $request->input('reason');
        $user_id = $request->input('user_id');
        $user_agent = $request->input('user_agent');
        $shadow_ban = $request->input('shadow_ban') == 'true';
        $ip_ban = $request->input('ip_ban') == 'true';
        $ip_address = $request->input('ip_address');

        if (empty($uuid) && empty($user_id) && empty($ip_address)) {
            return redirect()->route('score_report_bans.create')
                ->withInput($request->all())
                ->withErrors(new MessageBag([
                    'one' => "Pelo menos um dos seguintes campos tem de ser preenchido: uuid, ip_address ou user_id",
                ]));
        }

        if ($ip_ban && empty($ip_address)) {
            return redirect()->route('score_report_bans.create')
                ->withInput($request->all())
                ->withErrors(new MessageBag([
                    'ip_address' => 'O campo endereço de IP é obrigatório quando o bloqueio global por IP está marcado',
                ]));
        }

        $expires_at = Carbon::now()->addDays($ban_days);

        $ban = new ScoreReportBan();
        $ban->uuid = $uuid;
        $ban->expires_at = $expires_at;
        $ban->reason = $reason;
        $ban->user_id = $user_id;
        $ban->user_agent = $user_agent;
        $ban->shadow_ban = $shadow_ban;
        $ban->ip_ban = $ip_ban;
        $ban->ip_address = $ip_address;

        $ban->save();

        return redirect()->route('score_report_bans.show', ['ban' => $ban->id])
            ->with('success', 'Ban created successfully');
    }

    public function show($id)
    {
        $ban = ScoreReportBan::findOrFail($id);

        return view(
            'backoffice.pages.score_report_ban',
            [
                'ban' => $ban,
            ]
        );
    }

    public function edit($id)
    {
        $ban = ScoreReportBan::findOrFail($id);

        return view('backoffice.pages.edit_score_report_ban', ['ban' => $ban]);
    }

    public function update(Request $request, $id)
    {
        $ban = ScoreReportBan::findOrFail($id);

        $request->validate([
            'expires_at_date' => 'required|date',
            'expires_at_time' => ["required", "string", "regex:/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/"],
            'reason' => 'string|max:255|min:3|required',
            'shadow_ban' => 'required',
            'ip_ban' => 'required',
        ]);

        $carbon = new Carbon($request->input('expires_at_date'), $request->input('timezone'));
        $splited = explode(':', $request->input('expires_at_time'));
        $carbon->addHours($splited[0]);
        $carbon->addMinutes($splited[1]);

        $reason = $request->input('reason');
        $shadow_ban = $request->input('shadow_ban') == 'true';
        $ip_ban = $request->input('ip_ban') == 'true';

        $ban->expires_at = $carbon;
        $ban->reason = $reason;
        $ban->shadow_ban = $shadow_ban;
        $ban->ip_ban = $ip_ban;
        $ban->save();

        return redirect()->route('score_report_bans.show', ['ban' => $ban->id])
            ->with('success', 'Ban updated successfully');
    }

    public function destroy($id)
    {
        $ban = ScoreReportBan::findOrFail($id);
        $ban->delete();

        $message = new MessageBag();
        $message->add('success', trans('success.model_deleted', ['model_name' => 'Bloqueio']));

        return redirect(route('score_report_bans.index'))->with(['popup_message' => $message]);
    }
}
