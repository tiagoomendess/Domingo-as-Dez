<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Poll;
use App\PollAnswerVote;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Cookie;

class PollsController extends Controller
{
    public function __construct()
    {
        $this->middleware('cors');
    }

    public function show(Request $request, string $slug): View
    {
        $poll = Poll::where([
            'slug' => $slug,
            'visible' => true
        ])->first();

        if (empty($poll))
            abort(404);

        $answers = $poll->answers;

        /** @var User $user */
        $user = Auth::user();
        $timezone = empty($user) ? 'Europe/Lisbon' : $user->profile->timezone;

        $now = Carbon::now();
        $now->setTimezone($timezone);
        $showResultsAfter = Carbon::createFromFormat("Y-m-d H:i:s", $poll->show_results_after);
        $showResultsAfter->setTimezone($timezone);
        $closeAfter = Carbon::createFromFormat("Y-m-d H:i:s", $poll->close_after);
        $closeAfter->setTimezone($timezone);

        $votes = [];
        $totalVotes = 0;
        if ($now->timestamp > $showResultsAfter->timestamp) {
            foreach ($answers as $index => $answer) {
                $votes[$index] = PollAnswerVote::where('poll_answer_id', $answer->id)->count();
                $totalVotes += $votes[$index];
            }
        }

        $answerIds = [];
        foreach ($answers as $answer) {
            $answerIds[] = $answer->id;
        }

        // Track 2 kinds of votes
        $hasCookieVote = !empty($request->cookie('poll' . $poll->id));
        $hasUserVote = !empty($user)
            && !empty(PollAnswerVote::where('user_id', '=', $user->id)->whereIn('poll_answer_id', $answerIds)->first());

        $data = [
            'poll' => $poll,
            'answers' => $answers,
            'votes' => $votes,
            'totalVotes' => $totalVotes,
            'closeAfter' => $closeAfter,
            'showResultsAfter' => $showResultsAfter,
            'now' => $now,
            'hasVoted' => $hasCookieVote || $hasUserVote
        ];

        if ($request->input('mini') === "true") {
            return view('front.partial.minimal_poll', $data);
        }

        return view('front.pages.poll', $data);
    }

    public function vote(Request $request, string $slug): RedirectResponse
    {
        $poll = Poll::where('slug', $slug)->where('visible', true)->first();
        if (empty($poll)) {
            abort(404);
        }

        // Check if voting time is gone
        $now = Carbon::now();
        $closeAfter = Carbon::createFromFormat("Y-m-d H:i:s", $poll->close_after);
        if($now->timestamp > $closeAfter->timestamp) {
            return redirect()->back();
        }

        $idsArray = [];
        foreach ($poll->answers as $answer) {
            $idsArray[] = $answer->id;
        }

        $allIdsString = join(',', $idsArray);
        $rules = [
            'answer' => 'required|integer|in:' . $allIdsString,
            'ip' => 'required|ip'
        ];

        $user = Auth::user();
        $userVote = null;
        if (empty($user)) {
            $rules['g-recaptcha-response'] = 'required|recaptcha';
        } else {
            $userVote = PollAnswerVote::where('user_id', '=', $user->id)
                ->whereIn('poll_answer_id', $idsArray)
                ->first();
        }

        $this->validate($request, $rules);

        // Check if there is user vote in DB
        if(!empty($userVote)) {
            return redirect()
                ->back()
                ->cookie(cookie('poll' . $poll->id, $userVote->id, 576000, "/", url("/"), true, false));
        }

        // Check if user has vote registered in cookies
        $cookieVote = $request->cookie('poll' . $poll->id);
        if (!empty($cookieVote)) {
            return redirect()
                ->back()
                ->cookie($this->getCookie($poll->id, $cookieVote));
        }

        // Check if user has votes registered to IP address
        $ipVotes = PollAnswerVote::where('ip', $request->input('ip'))
            ->whereIn('poll_answer_id', $idsArray)
            ->get();

        if ($ipVotes->count() > 1 ) {
            return redirect()
                ->back()
                ->cookie($this->getCookie($poll->id, $ipVotes->first()->id));
        }

        $vote = PollAnswerVote::create([
            'user_id' => !empty($user) ? $user->id : null,
            'poll_answer_id' => $request->input('answer'),
            'ip' => $request->input('ip'),
        ]);

        return redirect()
            ->back()
            ->cookie($this->getCookie($poll->id, $vote->id));
    }

    private function getCookie(int $pollId, int $voteId): Cookie
    {
        return cookie(
            "poll$pollId",
            $voteId,
            576000,
            "",
            config("app.env") == "production" ? "domingoasdez.com" : "localhost",
            true,
            false
        );
    }
}
