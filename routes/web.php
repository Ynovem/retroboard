<?php

use Illuminate\Http\Request;

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
	$boards = \DB::table('boards')->select('board_id', 'board_name', 'board_password')->get();
    return view('index', [
    	'boards' => $boards
    ]);
});

Route::get('/display/{bid}', 'BoardController@displayBoard');
Route::post('/add', 'BoardController@add');
Route::post('/remove', 'BoardController@remove');
Route::post('/export', 'BoardController@export');;
