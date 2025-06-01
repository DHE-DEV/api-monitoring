<?php
// routes/web.php - Vollständige Version mit User Management

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ApiMonitorController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect Root zur Dashboard oder Login
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
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

    // User Management Routes
    Route::middleware('permission:view-users')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])
            ->name('users.index');

        Route::get('/users/data', [UserManagementController::class, 'getUserData'])
            ->name('users.data');

        Route::get('/users/{user}', [UserManagementController::class, 'show'])
            ->name('users.show');
    });

    Route::middleware('permission:create-users')->group(function () {
        Route::get('/users/create', [UserManagementController::class, 'create'])
            ->name('users.create');

        Route::post('/users', [UserManagementController::class, 'store'])
            ->name('users.store');
    });

    Route::middleware('permission:edit-users')->group(function () {
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])
            ->name('users.edit');

        Route::put('/users/{user}', [UserManagementController::class, 'update'])
            ->name('users.update');

        Route::post('/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])
            ->name('users.toggle-status');

        Route::post('/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])
            ->name('users.reset-password');

        Route::post('/users/bulk-action', [UserManagementController::class, 'bulkAction'])
            ->name('users.bulk-action');
    });

    Route::middleware('permission:delete-users')->group(function () {
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])
            ->name('users.destroy');
    });

    // Settings (für zukünftige Implementierung)
    Route::middleware('permission:view-settings')->group(function () {
        Route::get('/settings', function () {
            return view('settings.index');
        })->name('settings.index');
    });
});

// API Monitor Routes (bestehende, erweitert mit Berechtigungen)
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
