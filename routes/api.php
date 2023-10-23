<?php

use App\Http\Controllers\PrinterController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', function (Request $request) {
        $expiry = new DateTime();
        $expiry->modify('+30 minutes');
        $request->user()->tokens()->update(['expires_at' => $expiry]);
        return $request->user();
    });
    Route::post('/user/update', [UserController::class, 'updateUser']);
    Route::get('/logout', [UserController::class, 'logout']);

    Route::post('/user/newreg', [UserController::class, 'newClientCust']);
    Route::post('/user/updatereg', [UserController::class, 'updateClientCust']);
    Route::post('/user/deletereg', [UserController::class, 'deleteClientCust']);
    
    // User Children
    Route::get('/clients', [UserController::class, 'getClients']);
    Route::get('/customers', [UserController::class, 'getCustomers']);

    // Sites
    Route::get('/sites', [SiteController::class, 'getSites']);

    // Devices
    Route::get('/user/devicelist', [PrinterController::class, 'deviceList']);
});
// Route::middleware('auth:sanctum')->post('/auth/register', [UserController::class, 'createUser']);
Route::post('auth/register', [UserController::class, 'createUser']);
Route::post('auth/login', [UserController::class, 'loginUser']);
