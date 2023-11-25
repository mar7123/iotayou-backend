<?php

use App\Http\Controllers\AlarmController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\InstrumentController;
use App\Http\Controllers\ParameterController;
use App\Http\Controllers\PrinterController;
use App\Http\Controllers\RoleController;
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
        $result = $request->user()
            ->load([
                'user_permissions',
                'role.user_groups'
            ]);
        $result->role->setVisible(['user_groups', 'role_permissions']);
        return  $result;
    });
    Route::get('/logout', [UserController::class, 'logout']);

    // User CRUD
    Route::get('/users', [UserController::class, 'getUsers']);
    Route::get('/user/logs', [UserController::class, 'getUserLogs']);
    Route::post('/user/create', [UserController::class, 'createUser']);
    Route::post('/user/update', [UserController::class, 'updateUser']);
    Route::post('/user/delete', [UserController::class, 'deleteUser']);
    Route::post('/user/permission', [UserController::class, 'assignUserPermission']);

    // Role CRUD
    Route::post('/role/create', [RoleController::class, 'newRole']);
    Route::post('/role/update', [RoleController::class, 'updateRole']);
    Route::post('/role/delete', [RoleController::class, 'deleteRole']);
    Route::get('/clients', [RoleController::class, 'getClients']);
    Route::get('/customers', [RoleController::class, 'getCustomers']);

    // Site CRUD
    Route::post('/site/create', [SiteController::class, 'createSite']);
    Route::post('/site/update', [SiteController::class, 'updateSite']);
    Route::post('/site/delete', [SiteController::class, 'deleteSite']);
    Route::get('/sites', [SiteController::class, 'getSites']);

    // Printer CRUD
    Route::post('/printer/create', [PrinterController::class, 'createPrinter']);
    Route::post('/printer/update', [PrinterController::class, 'updatePrinter']);
    Route::post('/printer/delete', [PrinterController::class, 'deletePrinter']);
    Route::get('/user/devicelist', [PrinterController::class, 'deviceList']);

    // Instruments
    Route::get('/instruments', [InstrumentController::class, 'getInstruments']);
    Route::post('/instrument/create', [InstrumentController::class, 'createInstrument']);
    Route::post('/instrument/update', [InstrumentController::class, 'updateInstrument']);
    Route::post('/instrument/delete', [InstrumentController::class, 'deleteInstrument']);

    // Parameters
    Route::get('/parameters', [ParameterController::class, 'getParameters']);
    Route::get('/parameters/{ins_id}', [ParameterController::class, 'getParameterByInstrument']);
    Route::post('/parameter/create', [ParameterController::class, 'createParameter']);
    Route::post('/parameter/update', [ParameterController::class, 'updateParameter']);
    Route::post('/parameter/delete', [ParameterController::class, 'deleteParameter']);

    // Alarms
    Route::get('/alarms', [AlarmController::class, 'getAlarms']);
    Route::post('/alarm/create', [AlarmController::class, 'createAlarm']);
    Route::post('/alarm/update', [AlarmController::class, 'updateAlarm']);
    Route::post('/alarm/delete', [AlarmController::class, 'deleteAlarm']);

    // Alerts
    Route::get('/alerts', [AlertController::class, 'getAlerts']);
});
Route::post('auth/login', [UserController::class, 'loginUser']);
