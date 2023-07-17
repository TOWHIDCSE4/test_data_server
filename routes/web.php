<?php

use App\Http\Controllers\WEB\ImagesController;
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


Route::get('/manager_images', [ImagesController::class, 'getData']);
Route::get('/manager_images_remove_last', [ImagesController::class, 'removeImage']);

