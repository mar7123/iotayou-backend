<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\TryoutPKGController;
use App\Http\Controllers\UserPKGController;
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
    Route::get('/user/admin/children', [UserController::class, 'getAdminChildren']);
    Route::get('/user/client/children', [UserController::class, 'getClientChildren']);
    Route::get('/user/customer/children', [UserController::class, 'getCustomerChildren']);
    Route::get('/logout', [UserController::class, 'logout']);
});
// Route::middleware('auth:sanctum')->post('/auth/register', [UserController::class, 'createUser']);
Route::post('auth/register', [UserController::class, 'createUser']);
Route::post('auth/login', [UserController::class, 'loginUser']);
