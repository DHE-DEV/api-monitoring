<?php
// routes/web.php
use App\Http\Controllers\ApiMonitorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__.'/auth.php';

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show'); // ← HINZUFÜGEN
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // API Monitor Routes
    Route::resource('api-monitor', ApiMonitorController::class);
    Route::post('/api-monitor/{apiMonitor}/test', [ApiMonitorController::class, 'test'])
        ->name('api-monitor.test');
    Route::post('/api-monitor/{apiMonitor}/toggle-email-alerts', [ApiMonitorController::class, 'toggleEmailAlerts'])
        ->name('api-monitor.toggle-email-alerts');
    Route::get('/api-monitor/{apiMonitor}/export', [ApiMonitorController::class, 'export'])
        ->name('api-monitor.export');

    // User Management Routes
    Route::middleware('can:manage-users')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserManagementController::class);
        Route::post('/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])
            ->name('users.toggle-status');
    });
});
