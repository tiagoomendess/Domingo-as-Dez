<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/competitions', 'Api\CompetitionsController@getCompetitions')->name('api.competitions');
Route::get('/competitions/{competition}/seasons', 'Api\CompetitionsController@getCompetitionSeasons')->name('api.competitions.seasons')->where(['competition' => '[0-9]+']);
Route::get('/seasons/{season}', 'Api\SeasonsController@show')->name('api.seasons.show')->where(['season' => '[0-9]+']);
Route::get('/seasons/{season}/games', 'Api\SeasonsController@getGames')->name('api.seasons.games')->where(['season' => '[0-9]+']);
Route::get('/games/{game}', 'Api\GamesController@show')->name('api.games.show')->where(['game' => '[0-9]+']);
Route::get('/games/today', 'Api\GamesController@todayMatches')->name('api.games.today');
Route::get('/games/live', 'Api\GamesController@getLiveMatches')->name('api.games.live');
Route::post('/games/live/update_match', 'Api\GamesController@updateScoreLiveMatch')->name('api.games.live.update_score');
Route::get('/games/is_live', 'Api\GamesController@isLive')->name('api.games.is_live');
Route::get('/games/next/{team_id}', 'Api\GamesController@getNextTeamGame')->name('api.games.next');
Route::put('/games/{game}/scoreboard-updated', 'Api\GamesController@scoreboardUpdated')->name('api.games.scoreboard_updated');
Route::post('/score-reports/{game}', 'Api\ScoreReportsController@store')->name('api.game_reports.store');
Route::get('/players', 'Api\PlayersController@index')->name('api.players.index');
Route::get('/clubs/search', 'Api\ClubsController@search')->name('api.clubs.search');
