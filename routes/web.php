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
Route::get('/login/{provider}','Auth\LoginController@redirectToProvider')->where('provider','twitter|facebook|google');
Route::get('/login/{provider}/callback','Auth\LoginController@handleProviderCallback')->where('provider','twitter|facebook|google');

Route::get('/', 'Front\HomePageController@index')->name('homePage');

Route::get('/dashboard', 'Backoffice\DashboardController@index')->name('dashboard');

Route::get('/home', 'Front\HomePageController@home')->name('home');

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
]);

//Route::get('/users', 'Resources\UserController@index')->name('users.index');

Route::post('media_query', 'Resources\MediaController@mediaQuery')->name('mediaQuery');

Route::get('users/get_permissions_json/{id}', 'Resources\UserController@getPermissionsJson')->name('getPermissionsJson');
Route::post('users/add_permission', 'Resources\UserController@addPermission')->name('addPermission');
Route::post('users/remove_permission', 'Resources\UserController@removePermission')->name('removePermission');

