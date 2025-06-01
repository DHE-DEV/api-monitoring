<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Dashboard anzeigen
     */
    public function index()
    {
        // Basis-Statistiken
        $stats = [
            'users_count' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'monitors_count' => 0, // Wird später implementiert
            'active_monitors' => 0,
        ];

        return view('dashboard.index', compact('stats'));
    }
}
