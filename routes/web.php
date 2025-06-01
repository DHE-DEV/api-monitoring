<?php
// routes/web.php - Erweitert mit Authentication

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApiMonitorController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect Root zur API Monitor Hauptseite oder Login
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('api-monitor.index');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Geschützte Routes (authentifizierte Benutzer)
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('permission:view-dashboard');

    Route::get('/dashboard/data', [DashboardController::class, 'data'])
        ->name('dashboard.data')
        ->middleware('permission:view-dashboard');

    Route::get('/dashboard/system', [DashboardController::class, 'systemStats'])
        ->name('dashboard.system')
        ->middleware('permission:manage-settings');

    Route::get('/dashboard/quick-actions', [DashboardController::class, 'quickActions'])
        ->name('dashboard.quick-actions')
        ->middleware('permission:view-dashboard');

    // User Management (später zu implementieren)
    Route::middleware('permission:view-users')->group(function () {
        Route::get('/users', function () {
            return view('users.index');
        })->name('users.index');
    });

    // Settings (später zu implementieren)
    Route::middleware('permission:view-settings')->group(function () {
        Route::get('/settings', function () {
            return view('settings.index');
        })->name('settings.index');
    });
});

// Bestehende API Monitor Routes (angepasst mit Berechtigungen)
Route::middleware('auth')->group(function () {
    Route::get('api-monitor', [ApiMonitorController::class, 'index'])
        ->name('api-monitor.index')
        ->middleware('permission:view-monitors');

    Route::get('api-monitor/create', [ApiMonitorController::class, 'create'])
        ->name('api-monitor.create')
        ->middleware('permission:create-monitors');

    Route::post('api-monitor', [ApiMonitorController::class, 'store'])
        ->name('api-monitor.store')
        ->middleware('permission:create-monitors');

    Route::get('api-monitor/{apiMonitor}', [ApiMonitorController::class, 'show'])
        ->name('api-monitor.show')
        ->middleware('permission:view-results');

    Route::get('api-monitor/{apiMonitor}/edit', [ApiMonitorController::class, 'edit'])
        ->name('api-monitor.edit')
        ->middleware('permission:edit-monitors');

    Route::put('api-monitor/{apiMonitor}', [ApiMonitorController::class, 'update'])
        ->name('api-monitor.update')
        ->middleware('permission:edit-monitors');

    Route::delete('api-monitor/{apiMonitor}', [ApiMonitorController::class, 'destroy'])
        ->name('api-monitor.destroy')
        ->middleware('permission:delete-monitors');

    Route::post('api-monitor/{apiMonitor}/test', [ApiMonitorController::class, 'test'])
        ->name('api-monitor.test')
        ->middleware('permission:test-monitors');

    Route::post('api-monitor/{apiMonitor}/toggle-email-alerts', [ApiMonitorController::class, 'toggleEmailAlerts'])
        ->name('api-monitor.toggle-email-alerts')
        ->middleware('permission:edit-monitors');

    Route::get('api-monitor/{apiMonitor}/export', [ApiMonitorController::class, 'export'])
        ->name('api-monitor.export')
        ->middleware('permission:export-results');
});
