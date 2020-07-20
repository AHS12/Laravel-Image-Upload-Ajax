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
    return view('index');
});

//upload Route
Route::post('image/upload','image\ImageController@imageUploadAjax');
Route::post('image/delete','image\ImageController@imageDeleteAjax');
