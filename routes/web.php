<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/run-migrations', function () {
    return Artisan::call('migrate', ["--force" => true]);
});
Route::get('/refresh-migrations', function () {
    return Artisan::call('migrate:refresh', ["--force" => true, "--seed" => true]);
});
Route::get('/clear-cache', function () {
    return Artisan::call('cache:clear');
});
Route::get('/run-seeder', function () {
    return Artisan::call('db:seed');
});
