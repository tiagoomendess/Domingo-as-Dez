<?php

namespace App\Jobs;

use App\DeleteRequest;
use App\Notifications\GeneralBasicNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessDeleteRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $delete_request;
    protected $session;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $delete_requests = DeleteRequest::where('processed', false);

        foreach ($delete_requests as $delete_request) {

            Log::info('Processing delete request id ' . $this->delete_request->id);
            $user = $delete_request->user;

            if ($delete_request->cancelled) {
                $delete_request->processed = true;
                $delete_request->save();
                Log::info('Not deleting account ' . $user->id . ' because user cancelled the request (' . $delete_request->id . ').');
                continue;
            }

            $user->notify(new GeneralBasicNotification(trans('emails.account_deleted_subject'), trans('emails.account_deleted_p1')));

            DB::table('article_comments')->where('user_id', $user->id)->update(['content' => null]);
            DB::table('user_permissions')->where('user_id', $user->id)->delete();

            $profile = $user->profile;

            $profile->phone = null;
            $profile->bio = null;

            if (!is_null($profile->picture))
                unlink(public_path($profile->picture));

            $profile->picture = null;
            $profile->account_data_consent = null;
            $profile->analytics_cookies_consent = null;
            $profile->all_data_consent = null;
            $profile->save();

            $user->name = null;
            $user->email = null;
            $user->verified = false;
            $user->password = null;
            $user->email_token = null;
            $user->remember_token = null;

            $user->save();

            DB::table('social_providers')->where('user_id', $user->id)->delete();

            Log::info('Delete request id ' . $this->delete_request->id . ' finished!');

        }

    }
}
