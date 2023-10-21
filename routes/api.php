<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController\{
    StaffAppController2,
    DriverAppController
};
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('orders', [StaffAppController2::class, 'orders']);
Route::post('login', [StaffAppController2::class, 'login']);
Route::post('addOrderComment', [StaffAppController2::class, 'addComment']);
Route::post('cashCollection', [StaffAppController2::class, 'cashCollection']);
Route::post('orderStatusUpdate', [StaffAppController2::class, 'orderStatusUpdate']);
Route::post('driverOrderStatusUpdate', [StaffAppController2::class, 'driverOrderStatusUpdate']);
Route::post('rescheduleOrder', [StaffAppController2::class, 'rescheduleOrder']);
Route::get('timeSlots', [StaffAppController2::class, 'timeSlots']);
Route::get('orderChat', [StaffAppController2::class, 'orderChat']);
Route::post('addOrderChat', [StaffAppController2::class, 'addOrderChat']);

// Driver app       

Route::get('driverOrders', [DriverAppController::class, 'orders']);
Route::post('driverLogin', [DriverAppController::class, 'login']);
Route::get('driverOrderStatusUpdate/{order}', [DriverAppController::class, 'orderDriverStatusUpdate']);