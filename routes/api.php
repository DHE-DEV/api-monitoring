<?php
// routes/api.php - Erweitert mit Authentication

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Öffentliche Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// Geschützte Routes (benötigen Sanctum Token)
Route::middleware('auth:sanctum')->group(function () {

    // Authentication Management
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
    });

    // Dashboard & Monitoring (später zu implementieren)
    Route::middleware('permission:view-dashboard')->group(function () {
        Route::get('/dashboard/stats', function () {
            return response()->json([
                'success' => true,
                'stats' => [
                    'monitors_count' => 0, // Wird später implementiert
                    'active_monitors' => 0,
                    'success_rate' => 0,
                    'avg_response_time' => 0,
                ]
            ]);
        });
    });

    // User Management Routes (später zu implementieren)
    Route::middleware('permission:view-users')->group(function () {
        Route::get('/users', function () {
            return response()->json([
                'success' => true,
                'users' => \App\Models\User::with('roles')->get()
            ]);
        });
    });

    // Test Route für Permissions
    Route::get('/test-permissions', function (Request $request) {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'user_id' => $user->id,
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'roles' => $user->roles->pluck('name'),
            'tests' => [
                'can_view_dashboard' => $user->can('view-dashboard'),
                'can_create_monitors' => $user->can('create-monitors'),
                'can_manage_users' => $user->can('view-users'),
                'is_super_admin' => $user->hasRole('Super Admin'),
                'is_manager' => $user->hasRole('Manager'),
            ]
        ]);
    });
});
