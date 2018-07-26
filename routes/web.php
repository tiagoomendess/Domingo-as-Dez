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

Auth::routes();
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
Route::get('/register/verify', 'Auth\RegisterController@verifyEmailPage')->name('verifyEmailPage');
Route::get('/register/verify/{email}/{token}', 'Auth\RegisterController@verifyEmail')->name('verifyEmail');
Route::get('/login/{provider}','Auth\LoginController@redirectToProvider')->name('social.redirect')->where('provider','twitter|facebook|google');
Route::get('/login/{provider}/callback','Auth\LoginController@handleProviderCallback')->name('social.callback')->where('provider','twitter|facebook|google');

//Front
Route::get('/', 'Front\HomePageController@index')->name('homePage');

Route::get('/home', 'Front\HomePageController@home')->name('home');

//Backoffice
Route::get('/dashboard', 'Backoffice\DashboardController@index')->name('dashboard');
Route::get('settings', 'SettingsController@index')->name('settings.index');
Route::get('/games/import', 'Resources\GameController@showImportPage')->name('games.show_import_page');
Route::post('/games/import', 'Resources\GameController@importGames')->name('games.import_games');
Route::post('/settings/change', 'SettingsController@changeSetting')->name('settings.change');

Route::get('teste', function() {
    dd(\App\Club::find(2)->getFirstPlayground());
});

//Resources ---------------------
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
    'goals' => 'Resources\GoalController',
    'referees' => 'Resources\RefereeController',
    'gamegroups' => 'Resources\GameGroupController',
]);

//Routes to javascript in backend
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
//-----

//Front
Route::get('/direto', 'Front\GamesController@liveMatches')->name('games.live');
Route::get('/noticias', 'Front\ArticlesController@index')->name('news.index');
Route::get('/noticias/{year}/{month}/{day}/{slug}', 'Front\ArticlesController@show')->name('news.show');
Route::get('/competicoes/{slug}', 'Front\CompetitionsController@show')->name('competition');
Route::get('/competicoes/{slug}/classificacao-detalhada/', 'Front\CompetitionsController@showDetailedTable')->name('competition.detailed_table');
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

Route::get('/utilizador/perfil/editar', 'Front\UserProfileController@edit')->name('front.userprofile.edit');
Route::post('/utilizador/perfil/editar', 'Front\UserProfileController@updateProfileInfo')->name('front.userprofile.update');
Route::post('/utilizador/perfil/foto/alterar', 'Front\UserProfileController@updateProfilePicture')->name('front.userprofilephoto.update');
Route::get('/utilizador/perfil/download', 'Front\DefaultController@downloadUserInfo')->name('front.userprofile.download');
Route::get('/clubes/{club_slug}', 'Front\ClubController@show')->name('front.club.show');
Route::get('/jogadores/{id}/{name_slug}', 'Front\PlayersController@show')->name('front.player.show');
Route::get('/arbitros/{id}/{name_slug}', 'Front\RefereesController@show')->name('front.referee.show');

Route::get('/politica-de-privacidade', 'Front\DefaultController@showPrivacyPolicyPage')->name('privacy_policy');
Route::get('/termos-e-condicoes', 'Front\DefaultController@showTermsPage')->name('terms_and_conditions');
Route::get('/rgpd', 'Front\DefaultController@showRGPDInfoPage')->name('rgpd_info');
Route::post('/rgpd', 'Front\DefaultController@setRGPDSettings')->name('rgpd_info.settings');


