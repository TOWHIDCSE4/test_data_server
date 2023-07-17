<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\MultipleUploadController;

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

Route::post('image-upload', 'App\Http\Controllers\API\MultipleUploadController@uploadCustomFile');
Route::post('image-upload-new', 'App\Http\Controllers\API\MultipleUploadController@uploadCustomFile');

Route::post('video-upload', 'App\Http\Controllers\API\MultipleUploadVideoController@upload');

Route::get('SHVideos/{slug?}', 'App\Http\Controllers\API\AccessVideoController@show')->where('slug', '.*')->name('k3');

Route::get('SHImages/{slug?}', 'App\Http\Controllers\API\AccessImageController@showNew')->where('slug', '.*')->name('k1');

Route::get('SHImages2/{slug?}', 'App\Http\Controllers\API\AccessImageController@showNew')->where('slug', '.*')->name('k2');
