<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\PlayerUpdateRequest;
use App\ScoreReportBan;
use App\InfoReport;
use App\PartnerClick;
use App\Partner;
use App\MvpVotes;
use App\ScoreReport;
use App\GameComment;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:dashboard');
    }

    public function index()
    {

        /* I need to get the following metrics:
        - Amount of player update requests
        - Amount of blocked users currently (check expiration date)
        - Amount of info reports received that have not been viewed
        - Amount of clicks on partner ads, per partner
        - Amount of MVP votes in the last 24 hours
        - Amount of results sent in the last 24 hours
        - Total amount of registered users
        - usersWithPermissions I already have this
        */

        // define cache strategy otherwise this will be a performance bottleneck
        $playerUpdateRequestsCacheKey = 'dashboard_player_update_requests';
        $blockedUsersCacheKey = 'dashboard_blocked_users';
        $infoReportsCacheKey = 'dashboard_info_reports';
        $partnerClicksCacheKey = 'dashboard_partner_clicks';
        $mvpVotesCacheKey = 'dashboard_mvp_votes';
        $resultsSentCacheKey = 'dashboard_results_sent';
        $registeredUsersCacheKey = 'dashboard_registered_users';
        $usersWithPermissionsCacheKey = 'dashboard_users_with_permissions';
        $gameCommentsCacheKey = 'dashboard_game_comments';

        // get player update requests
        $playerUpdateRequestsCount = Cache::remember($playerUpdateRequestsCacheKey, 60, function () {
            return PlayerUpdateRequest::where('status', PlayerUpdateRequest::STATUS_PENDING)->count();
        });

        // get blocked users
        $blockedUsersCount = Cache::remember($blockedUsersCacheKey, 60, function () {
            return ScoreReportBan::where('expires_at', '>', now())->count();
        });

        // get info reports
        $infoReportsCount = Cache::remember($infoReportsCacheKey, 60, function () {
            return InfoReport::where('status', 'sent')->count();
        });

        // get partner clicks
        $partners = Cache::remember($partnerClicksCacheKey, 60, function () {
            $activePartners = Partner::where('visible', true)->get();

            foreach ($activePartners as $partner) {
                $partner->click_count = PartnerClick::where('partner_id', $partner->id)->count();
            }

            // sort list by click_count descending
            $activePartners = $activePartners->sortByDesc('click_count');

            return $activePartners;
        });

        $mvpVotesCount = Cache::remember($mvpVotesCacheKey, 60, function () {
            return MvpVotes::where('created_at', '>=', now()->subHours(24))->count();
        });

        $resultsSentCount = Cache::remember($resultsSentCacheKey, 60, function () {
            return ScoreReport::where('created_at', '>=', now()->subHours(24))->count();
        });

        $registeredUsersCount = Cache::remember($registeredUsersCacheKey, 60, function () {
            return User::where('verified', 1)->where('email', '!=', null)->count();
        });

        // Get the GameComments created in the past 24h that have the updated_at different from the created_at
        $gameCommentsCount = Cache::remember($gameCommentsCacheKey, 60, function () {
            return GameComment::where('created_at', '>=', now()->subHours(48))->where('updated_at', '>', 'created_at')->count();
        });

        $usersWithPermissions = DB::table('user_permissions')->selectRaw('DISTINCT user_permissions.user_id as id, users.name')->join('users', 'user_permissions.user_id', '=', 'users.id')->get();
        return view('backoffice.pages.dashboard')
            ->with([
                'playerUpdateRequestsCount' => $playerUpdateRequestsCount,
                'blockedUsersCount' => $blockedUsersCount,
                'infoReportsCount' => $infoReportsCount,
                'partners' => $partners,
                'mvpVotesCount' => $mvpVotesCount,
                'resultsSentCount' => $resultsSentCount,
                'registeredUsersCount' => $registeredUsersCount,
                'gameCommentsCount' => $gameCommentsCount,
                'usersWithPermissions' => $usersWithPermissions,
            ]);
    }
}
