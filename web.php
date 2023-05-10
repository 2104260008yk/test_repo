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

// トップページ
Route::get('pics','ProductionController@index'); 

// ログインページ
Route::get('login','ProductionController@login');
Route::post('login','ProductionController@login_send');

// 新規登録
Route::get('new_account','ProductionController@new_account');
Route::post('new_account','ProductionController@register');

// マイページ
Route::get('mypage','ProductionController@mypage');

// 投稿
Route::get('post','ProductionController@post');
Route::post('post','ProductionController@posted');

// 会員情報
Route::get('check','ProductionController@check');

// ログアウト
Route::get('logout','ProductionController@logout');

// ユーザ名変更
Route::get('change_name','ProductionController@change_name');
Route::post('change_name','ProductionController@changed_name');

// パスワード変更
Route::get('change_passwd','ProductionController@change_passwd');
Route::post('change_passwd','ProductionController@changed_passwd');

//　アカウント削除
Route::get('delete','Productioncontroller@delete');
Route::post('delete','Productioncontroller@deleted');