<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

if (env('APP_ENV') === 'production') {
    URL::forceScheme('https');
}

// === Auth Routes =====================================================================================================
Auth::routes();
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
Route::get('/register/verify/{email}', 'Auth\RegisterController@verifyEmailPage')->name('verifyEmailPage');
Route::get('/register/verify/{email}/{token}', 'Auth\RegisterController@verifyEmail')->name('verifyEmail');
Route::get('/login/{provider}','Auth\LoginController@redirectToProvider')
    ->name('social.redirect')
    ->where('provider','twitter|facebook|google');
Route::get('/login/{provider}/callback','Auth\LoginController@handleProviderCallback')
    ->name('social.callback')
    ->where('provider','facebook|google');
// =====================================================================================================================

// === Front Routes =================================================================================================
Route::get('/', 'Front\HomePageController@index')->name('homePage');
Route::post('/vote', 'Front\MvpVotesController@vote')->name('mvp_vote');
Route::post('/game/{game}/generate_image', 'Front\MatchImageGeneratorController@generateImage')->name('generate_game_image');
Route::get('/p/{slug}', 'Front\PagesController@show')->name('page.show');
Route::get('/info', 'Front\InfoReportsController@create')->name('info.create');
Route::post('/info', 'Front\InfoReportsController@store')->name('info.store');
Route::post('/info/show', 'Front\InfoReportsController@show')->name('info.show');
Route::post('/info/delete', 'Front\InfoReportsController@delete')->name('info.delete');
Route::get('/score-reports/{game}', 'Front\ScoreReportsController@create')->name('score_reports.create');
Route::post('/score-reports/{game}', 'Front\ScoreReportsController@store')->name('score_reports.store');
Route::get('/home', 'Front\HomePageController@home')->name('home');
Route::get('/direto', 'Front\GamesController@liveMatches')->name('games.live');
Route::get('/hoje', 'Front\GamesController@today')->name('games.today');
Route::get('/hoje/edit', 'Front\GamesController@todayEdit')->name('games.today_edit');
Route::post('/hoje/edit/store', 'Front\GamesController@todayUpdateScore')->name('games.today_update_score');
Route::get('/noticias', 'Front\ArticlesController@index')->name('news.index');
Route::get('/noticias/{year}/{month}/{day}/{slug}', 'Front\ArticlesController@show')->name('news.show');
Route::get('/competicoes', 'Front\CompetitionsController@showAll')->name('competitions');
Route::get('/competicoes/{slug}', 'Front\CompetitionsController@show')->name('competition');
Route::get('/competicoes/{competition_slug}/{season_slug}/estatisticas', 'Front\CompetitionStatsController@show')
    ->name('competition.stats')
    ->where([
        'competition_slug' => '[a-z0-9\-]+',
        'season_slug' => '[0-9]{4}\-[0-9]{4}|[0-9]{4}'
    ]);
Route::get('/sondagens/{slug}', 'Front\PollsController@show')->name('polls.front.show')->where([
    'slug' => '[0-9a-z-]+'
]);
Route::put('/sondagens/{slug}', 'Front\PollsController@vote')->name('polls.front.vote')->where([
    'slug' => '[0-9a-z-]+'
]);
Route::get('/transferencias', 'Front\TransfersController@index')->name('transfers');
Route::get('/competicoes/{competition_slug}/{season_slug}/{group_slug}/{round}/{clubs_slug}', 'Front\GamesController@show')
    ->name('front.games.show')
    ->where([
        'competition_slug' => '[a-z0-9\-]+',
        'season_slug' => '[0-9]{4}\-[0-9]{4}|[0-9]{4}',
        'group_slug' => '[a-z0-9\-]+',
        'round' => '[0-9]+',
        'clubs_slug' => '[a-z0-9\-]+-vs-[a-z0-9\-]+',
    ]);
Route::get('/jogos/{game}/resultados-enviados', 'Front\GamesController@listScoreReports')->name('front.games.show_score_reports');
Route::put('/score-reports/{report}', 'Front\GamesController@updateIsFake')->name('score_reports.update_is_fake');
Route::get('/perfil/editar', 'Front\UserProfileController@edit')->name('front.userprofile.edit');
Route::post('/perfil/editar', 'Front\UserProfileController@updateProfileInfo')->name('front.userprofile.update');
Route::post('/perfil/foto/alterar', 'Front\UserProfileController@updateProfilePicture')->name('front.userprofilephoto.update');
Route::post('/perfil/password/change', 'Front\UserProfileController@changePassword')->name('front.change_password');
Route::get('/utilizador/perfil/download', 'Front\DefaultController@downloadUserInfo')->name('front.userprofile.download');
Route::get('/clubes/{club_slug}', 'Front\ClubController@show')->name('front.club.show');
Route::get('/jogadores/{id}/{name_slug}', 'Front\PlayersController@show')->name('front.player.show');
Route::get('/tecnicos/{id}/{name_slug}', 'Front\TeamAgentController@show')->name('front.team_agent.show');
Route::get('/arbitros/{id}/{name_slug}', 'Front\RefereesController@show')->name('front.referee.show');
Route::get('/politica-de-privacidade', 'Front\DefaultController@showPrivacyPolicyPage')->name('privacy_policy');
Route::get('/termos-e-condicoes', 'Front\DefaultController@showTermsPage')->name('terms_and_conditions');
Route::get('/rgpd', 'Front\DefaultController@showRGPDInfoPage')->name('rgpd_info');
Route::post('/rgpd', 'Front\DefaultController@setRGPDSettings')->name('rgpd_info.settings');
Route::get('/perfil/apagar', 'Resources\DeleteRequestsController@showDeletePage')->name('front.userprofile.delete.create');
Route::post('/perfil/apagar', 'Resources\DeleteRequestsController@storeDeleteRequest')->name('front.userprofile.delete.store');
Route::get('/perfil/apagar/verificar', 'Resources\DeleteRequestsController@showVerificationPage')->name('front.userprofile.delete.verify.show');
Route::post('/perfil/apagar/verificar', 'Resources\DeleteRequestsController@verifyCode')->name('front.userprofile.delete.verify.store');
Route::get('/perfil/apagar/cancelar', 'Resources\DeleteRequestsController@cancellationPage')->name('front.userprofile.delete.cancel.show');
Route::post('/perfil/apagar/cancelar', 'Resources\DeleteRequestsController@cancelDeleteRequest')->name('front.userprofile.delete.cancel.store');
Route::get('/parceiros/{partner}', 'Front\PartnersController@trackClick')->name('front.partners.track_click');
Route::get('/article_comments/{article_id}', 'Api\ArticleCommentsController@get')->name('api.article_comments.get')
    ->where(['article_id' => '[0-9]+']);
Route::post('/article_comments/{article_id}', 'Front\ArticleCommentsController@comment')->name('article_comments.comment')
    ->where(['article_id' => '[0-9]+']);
Route::post('/article_comments/{comment_id}/delete', 'Front\ArticleCommentsController@delete')->name('article_comments.delete')
    ->where(['id' => '[0-9]+']);
Route::get('/jogos', 'Front\GamesController@index')->name('front.games.index');
Route::get('/clubes', 'Front\ClubController@index')->name('front.clubs.index');
Route::get('/jogadores', 'Front\PlayersController@index')->name('front.players.index');
Route::get('/flash-interview/{uuid}', 'Front\GameCommentsController@edit')->name('front.game_comment');
Route::post('/flash-interview/{uuid}', 'Front\GameCommentsController@update')->name('front.game_comment_update');
Route::get('/flash-interview/{uuid}/pin', 'Front\GameCommentsController@pin')->name('front.game_comment_pin');
Route::get('/flash-interview/{uuid}/manage-notifications', 'Front\GameCommentsController@manageNotifications')->name('front.manage_notifications');
Route::post('/flash-interview/{uuid}/manage-notifications', 'Front\GameCommentsController@saveManageNotifications')->name('front.save_manage_notifications');
Route::get('/scores', 'Front\GamesController@scoresAggregator')->name('scores-aggregator');
// =====================================================================================================================

// === Backoffice Adhoc Routes =========================================================================================
Route::get('/dashboard', 'Backoffice\DashboardController@index')->name('dashboard');
Route::get('settings', 'SettingsController@index')->name('settings.index');
Route::get('/games/import', 'Resources\GameController@showImportPage')->name('games.show_import_page');
Route::post('/games/import', 'Resources\GameController@importGames')->name('games.import_games');
Route::post('/settings/change', 'SettingsController@changeSetting')->name('settings.change');
Route::post('/articles/{article}/post-on-facebook', 'Resources\ArticleController@postOnFacebook')
    ->name('articles.post_on_facebook');
Route::get('/audit', 'Backoffice\AuditController@index')->name('audit.index');
Route::post('/ckeditor/upload', 'Backoffice\CKEditorController@upload')->name('ckeditor.upload');
Route::get('/partners/{partner}/generate-image', 'Resources\PartnerController@showGenerateImage')
    ->name('partners.show_generate_image');
Route::post('/partners/{partner}/generate-image', 'Resources\PartnerController@doGenerateImage')
    ->name('partners.do_generate_image');
// =====================================================================================================================

// === Backoffice CRUD =================================================================================================
Route::resources([
    'media' => 'Resources\MediaController',
    'articles' => 'Resources\ArticleController',
    'permissions' => 'Resources\PermissionController',
    'users' => 'Resources\UserController',
    'users_profile' => 'Resources\UserProfileController',
    'user_bans' => 'Resources\UserBanController',
    'user_permissions' => 'Resources\UserPermissionsController',
    'competitions' => 'Resources\CompetitionController',
    'seasons' => 'Resources\SeasonController',
    'clubs' => 'Resources\ClubController',
    'teams' => 'Resources\TeamController',
    'players' => 'Resources\PlayerController',
    'transfers' => 'Resources\TransferController',
    'playgrounds' => 'Resources\PlaygroundController',
    'games' => 'Resources\GameController',
    'game_comments' => 'Resources\GameCommentController',
    'goals' => 'Resources\GoalController',
    'referees' => 'Resources\RefereeController',
    'gamegroups' => 'Resources\GameGroupController',
    'group_rules' => 'Resources\GroupRulesController',
    'pages' => 'Resources\PageController',
    'partners' => 'Resources\PartnerController',
    'info_reports' => 'Resources\InfoReportController',
    'polls' => 'Resources\PollController',
    'score_report_bans' => 'Resources\ScoreReportBanController',
    'team_agents' => 'Resources\TeamAgentController',
    'team_agent_history' => 'Resources\TeamAgentHistoryController',
    'player_update_requests' => 'Resources\PlayerUpdateRequestController',
]);

// === Player Update Request Custom Routes ========================================================================
Route::post('/player_update_requests/{id}/approve', 'Resources\PlayerUpdateRequestController@approve')
    ->name('player_update_requests.approve');
Route::post('/player_update_requests/{id}/deny', 'Resources\PlayerUpdateRequestController@deny')
    ->name('player_update_requests.deny');
// =====================================================================================================================

// === Backend Javascript Routes =================================================================================
Route::post('media_query', 'Resources\MediaController@mediaQuery')->name('mediaQuery');
Route::get('users/get_permissions_json/{id}', 'Resources\UserController@getPermissionsJson')->name('getPermissionsJson');
Route::post('users/add_permission', 'Resources\UserController@addPermission')->name('addPermission');
Route::post('users/remove_permission', 'Resources\UserController@removePermission')->name('removePermission');
Route::get('clubs/{id}/teams', 'Resources\ClubController@getTeams')->name('getClubTeams');
Route::get('competitions/{id}/seasons', 'Resources\CompetitionController@getSeasons')->name('getCompetitionsSeasons');
Route::get('gamegroups/{id}/games', 'Resources\GameGroupController@getGames')->name('getGameGroupGames');
Route::get('teams/{id}/current_players', 'Resources\TeamController@getCurrentPlayers')->name('getTeamCurrentPlayers');
Route::get('games/{id}/teams', 'Resources\GameController@getTeams')->name('getGameTeams');
Route::get('seasons/{id}/game_groups', 'Resources\SeasonController@getGameGroups')->name('getGameGroups');
Route::get('api/players/search', 'Resources\PlayerController@autocomplete')->name('api.players.search');
//======================================================================================================================

// Frontend Redirects ================
// Permanently redirect /competicoes/1a-divisao-afpb to competicoes/1a-divisao-agribar
Route::get('/competicoes/1a-divisao-afpb', function () {
    return redirect('/competicoes/1a-divisao-agribar', 301);
});

// HoneyPot Routes, POC, will think of better and more scalable solution later =========================================
$honeyPotRoutes = [
    "/phpma",
    "/phpmyadmin",
    "/admin",
    "/wp-login",
    "/wp-admin",
    "/wp-admin/css",
    "/badbottrap",
    "/site/wp-includes/wlwmanifest.xml",
    "/wp/wp-includes/wlwmanifest.xml",
    "/news/wp-includes/wlwmanifest.xml",
    "/wordpress/wp-includes/wlwmanifest.xml",
    "/wp-stad.php",
    "/wp-content/themes/twentystd/index.php",
    "/wp-content/themes/kernel-theme/style.css",
    "/wp-content/themes/satoshi/styles/functions.css",
    "/task.php",
    "/wp-cron.php",
    "/upload/server/php/",
    "/server/php/",
    "/fileupload/server/php/",
    "/admin/server/php/",
    "/assets/jquery-file-upload/server/php/",
    "/assets/global/plugins/jquery-file-upload/server/php/",
];

foreach ($honeyPotRoutes as $route) {
    Route::get($route, 'Front\HoneyPotController@Get');
}
