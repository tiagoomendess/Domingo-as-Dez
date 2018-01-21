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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::get('/register/verify/{email}/{token}', 'Auth\RegisterController@verifyEmail')->name('verifyEmail');
Route::get('/login/{provider}','Auth\LoginController@redirectToProvider')->where('provider','twitter|facebook|google');
Route::get('/login/{provider}/callback','Auth\LoginController@handleProviderCallback')->where('provider','twitter|facebook|google');

Route::get('/home', 'HomeController@index')->name('home');

Route::resource('article', 'ArticleController');
