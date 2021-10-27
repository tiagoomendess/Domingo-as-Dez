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
Route::get('/games/live', 'Api\GamesController@getLiveMatches')->name('api.games.live');
Route::post('/games/live/update_match', 'Api\GamesController@updateScoreLiveMatch')->name('api.games.live.update_score');
Route::get('/games/is_live', 'Api\GamesController@isLive')->name('api.games.is_live');
Route::get('/games/next/{team_id}', 'Api\GamesController@getNextTeamGame')->name('api.games.next');

