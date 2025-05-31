<?php

use App\Http\Controllers\ApiMonitorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // API Monitor (mit direkter Middleware-Überprüfung)
    Route::group([
        'middleware' => function ($request, $next) {
            $user = auth()->user();
            $hasPermission = $user->hasPermission('view_monitors');

            Log::debug('Route Middleware: view_monitors', [
                'user_id' => $user?->id,
                'has_permission' => $hasPermission,
            ]);

            if (!$hasPermission) {
                abort(403, 'Keine Berechtigung (Middleware)');
            }

            return $next($request);
        }
    ], function () {
        Route::resource('api-monitor', ApiMonitorController::class);
        Route::post('/api-monitor/{apiMonitor}/test', [ApiMonitorController::class, 'test'])->name('api-monitor.test');
        Route::post('/api-monitor/{apiMonitor}/toggle-email-alerts', [ApiMonitorController::class, 'toggleEmailAlerts'])->name('api-monitor.toggle-email-alerts');
        Route::get('/api-monitor/{apiMonitor}/export', [ApiMonitorController::class, 'export'])->name('api-monitor.export');
    });

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {

        // User Management
        Route::middleware('can:manage-users')->group(function () {
            Route::resource('users', UserManagementController::class);
            Route::post('/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
        });

        // Group Management
        Route::middleware('can:view-groups')->group(function () {
            Route::resource('groups', GroupController::class);
            Route::post('/groups/{group}/members', [GroupController::class, 'addMember'])->name('groups.add-member')->middleware('can:manage-groups');
            Route::delete('/groups/{group}/members/{user}', [GroupController::class, 'removeMember'])->name('groups.remove-member')->middleware('can:manage-groups');
            Route::post('/groups/{group}/monitors', [GroupController::class, 'addMonitor'])->name('groups.add-monitor')->middleware('can:manage-groups');
            Route::delete('/groups/{group}/monitors/{monitor}', [GroupController::class, 'removeMonitor'])->name('groups.remove-monitor')->middleware('can:manage-groups');
        });
    });
});
