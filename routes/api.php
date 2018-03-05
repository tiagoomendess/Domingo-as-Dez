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

Route::get('/competicao/{slug}/season/{season}/round/{round}/games', 'Front\CompetitionsController@getGames')->name('getTable')->where(['season' => '[0-9]+', 'round' => '[0-9]+']);
Route::get('/competicao/{slug}/season/{season}/round/{round}/table', 'Front\CompetitionsController@getTable')->name('getGames')->where(['season' => '[0-9]+', 'round' => '[0-9]+']);
Route::get('/season/{season}', 'Front\SeasonsController@show')->name('getSeason')->where(['season' => '[0-9]+']);
