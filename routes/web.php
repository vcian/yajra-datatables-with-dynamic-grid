<?php

use Illuminate\Support\Facades\Route;

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
Route::get('/', ['uses'=>'UserController@index', 'as'=>'users.index']);
Route::get('users', ['uses'=>'UserController@export', 'as'=>'users.export']);
Route::get('drags', ['uses'=>'UserController@drags', 'as'=>'users.drags']);
Route::get('get-column-list/{slug}', 'TableColumnsListController@getColumnList');
Route::post('post-column-list/{slug}', 'TableColumnsListController@updateColumns');
