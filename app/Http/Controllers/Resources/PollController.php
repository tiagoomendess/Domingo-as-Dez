<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\Poll;
use App\PollAnswer;
use App\PollAnswerVote;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\View\View;

class PollController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:polls')->only(['index', 'show']);
        $this->middleware('permission:polls.edit')->only(['edit', 'update']);
        $this->middleware('permission:polls.create')->only(['create', 'store', 'destroy']);
    }

    public function index(): View
    {
        $polls = Poll::orderBy('id', 'desc')->paginate(config('custom.results_per_page'));

        return view('backoffice.pages.polls', [
            'polls' => $polls
        ]);
    }

    public function create(): View
    {
        $user = Auth::user();
        $now = Carbon::now($user->profile->timezone);
        $closeBy = Carbon::now()->addDays(3);
        $closeBy->setTimezone($user->profile->timezone);

        return view('backoffice.pages.create_poll', ['user' => $user, 'now' => $now, 'closeBy' => $closeBy]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:144',
            'show_results_after_date' => 'required|date',
            'show_results_after_time' => ["required", "string", "regex:/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/"],
            'publish_after_date' => 'required|date',
            'publish_after_time' => ["required", "string", "regex:/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/"],
            'close_after_date' => 'required|date',
            'close_after_time' => ["required", "string", "regex:/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/"],
            'timezone' => 'required|string|max:20',
            'answers' => 'array|min:2|max:40',
            'answers.*' => 'required|string|max:144|min:1',
        ]);

        $slug = str_slug($request->input('question'));
        $existingPoll = null;
        $newSlug = $slug;
        for ($i = 2; $i < 6; $i++) {
            $existingPoll = Poll::where('slug', $newSlug)->first();

            if(!empty($existingPoll)) {
                $newSlug = $slug . "-$i";
            }
        }

        if (!empty($existingPoll)) {
            $newSlug = $slug . "-" . strtolower(str_random(4));
        }

        // Show results after
        $showResultsAfter = new Carbon($request->input('show_results_after_date'), $request->input('timezone'));
        $showResultsAfterSplit = explode(':', $request->input('show_results_after_time'));
        $showResultsAfter->addHours($showResultsAfterSplit[0]);
        $showResultsAfter->addMinutes($showResultsAfterSplit[1]);

        // Publish after
        $publishAfter = new Carbon($request->input('publish_after_date'), $request->input('timezone'));
        $publishAfterSplit = explode(':', $request->input('publish_after_time'));
        $publishAfter->addHours($publishAfterSplit[0]);
        $publishAfter->addMinutes($publishAfterSplit[1]);

        // Close after
        $closeAfter = new Carbon($request->input('close_after_date'), $request->input('timezone'));
        $closeAfterSplit = explode(':', $request->input('close_after_time'));
        $closeAfter->addHours($closeAfterSplit[0]);
        $closeAfter->addMinutes($closeAfterSplit[1]);

        $now = Carbon::now();

        $poll = Poll::create([
            'question' => $request->input('question'),
            'slug' => $newSlug,
            'show_results_after' => Carbon::createFromTimestamp($showResultsAfter->timestamp)->format("Y-m-d H:i:s"),
            'publish_after' => Carbon::createFromTimestamp($publishAfter->timestamp)->format("Y-m-d H:i:s"),
            'close_after' => Carbon::createFromTimestamp($closeAfter->timestamp)->format("Y-m-d H:i:s"),
            'update_image' => true,
            'visible' => $now->timestamp > $publishAfter->timestamp,
        ]);

        $answers = $request->input('answers');
        foreach ($answers as $answer) {
            PollAnswer::create([
                'poll_id' => $poll->id,
                'answer' => $answer
            ]);
        }

        return redirect(route('polls.show', ['poll' => $poll]));
    }

    public function show($id): View
    {
        $poll = Poll::findOrFail($id);
        $user = Auth::user();

        return view('backoffice.pages.poll', ['poll' => $poll, 'user' => $user]);
    }

    public function edit($id): View
    {
        $poll = Poll::findOrFail($id);
        $user = Auth::user();
        $now = Carbon::now($user->profile->timezone);
        $publishAfterDate = Carbon::createFromFormat("Y-m-d H:i:s", $poll->publish_after);

        return view('backoffice.pages.edit_poll', [
            'poll' => $poll,
            'now' => $now,
            'user' => $user,
            'publishAfterDate' => $publishAfterDate
        ]);
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'show_results_after_date' => 'required|date',
            'show_results_after_time' => ["required", "string", "regex:/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/"],
            'publish_after_date' => 'required|date',
            'publish_after_time' => ["required", "string", "regex:/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/"],
            'close_after_date' => 'required|date',
            'close_after_time' => ["required", "string", "regex:/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/"],
            'timezone' => 'required|string|max:20',
        ]);

        $poll = Poll::findOrFail($id);
        $now = Carbon::now();

        // Publish after
        $publishAfter = new Carbon($request->input('publish_after_date'), $request->input('timezone'));
        $publishAfterSplit = explode(':', $request->input('publish_after_time'));
        $publishAfter->addHours($publishAfterSplit[0]);
        $publishAfter->addMinutes($publishAfterSplit[1]);

        // Close after
        $closeAfter = new Carbon($request->input('close_after_date'), $request->input('timezone'));
        $closeAfterSplit = explode(':', $request->input('close_after_time'));
        $closeAfter->addHours($closeAfterSplit[0]);
        $closeAfter->addMinutes($closeAfterSplit[1]);

        // Show results after
        $showResultsAfter = new Carbon($request->input('show_results_after_date'), $request->input('timezone'));
        $showResultsAfterSplit = explode(':', $request->input('show_results_after_time'));
        $showResultsAfter->addHours($showResultsAfterSplit[0]);
        $showResultsAfter->addMinutes($showResultsAfterSplit[1]);

        $poll->show_results_after = $showResultsAfter->format("Y-m-d H:i:s");
        $poll->publish_after = $publishAfter->format("Y-m-d H:i:s");
        $poll->close_after = $closeAfter->format("Y-m-d H:i:s");
        $poll->visible = $request->input('visible') == 'true';

        if ($poll->question != $request->input('question')) {
            $poll->question = $request->input('question');
            $poll->update_image = true;
        }

        $poll->visible = $now->timestamp > $publishAfter->timestamp;
        $poll->save();

        return redirect(route('polls.show', ['poll' => $poll]));
    }

    public function destroy($id): RedirectResponse
    {
        $poll = Poll::findOrFail($id);

        $answers = $poll->answers;
        foreach ($answers as $answer) {
            PollAnswerVote::where('poll_answer_id', $answer->id)->delete();
            $answer->delete();
        }

        $poll->delete();

        $messages = new MessageBag();
        $messages->add('success', trans('success.model_deleted', ['model_name' => 'Sondagem']));

        return redirect(route('polls.index'))->with(['popup_message' => $messages]);
    }
}
