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
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

Route::get('/', function () {
    return view('welcome');
});
Route::get('/mail', function () {
    $order = \App\Models\Order::latest()->first();

    return view('mail.restaurant.order.index', compact('order'));
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
