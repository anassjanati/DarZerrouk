<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display activity logs
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50);
        $users = User::orderBy('name')->get();

        $actions = [
            'login' => 'Connexion',
            'logout' => 'Déconnexion',
            'create' => 'Création',
            'update' => 'Modification',
            'delete' => 'Suppression',
            'update_permissions' => 'Permissions',
        ];

        return view('admin.activity-logs.index', compact('logs', 'users', 'actions'));
    }

    /**
     * Show specific log details
     */
    public function show(ActivityLog $activityLog)
    {
        $activityLog->load('user');
        return view('admin.activity-logs.show', compact('activityLog'));
    }
}
