<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiMonitorController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('api-monitor', ApiMonitorController::class);
Route::post('api-monitor/{apiMonitor}/test', [ApiMonitorController::class, 'test'])
    ->name('api-monitor.test');
Route::get('api-monitor/{apiMonitor}/export', [ApiMonitorController::class, 'export'])
    ->name('api-monitor.export');
