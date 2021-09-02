<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestaurantController;
use Illuminate\Support\Facades\URL;
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
    $order = \App\Models\Order::latest()->where('order_status_id', 1)->first();

    $url = URL::temporarySignedRoute('order.recieved', now()->addMinutes(10), ['order' => $order]);


    return view('mail.restaurant.order.index', compact('order', 'url'));
});


Route::get('/order-recieved/{order}', [RestaurantController::class, 'orderRecieved'])->name('order.recieved')->middleware('signed');

Route::get('/unres', [ScheduleController::class, 'checkUnrespondsOrders']);


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
