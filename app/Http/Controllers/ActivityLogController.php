<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Traits\ChecksRole;

class ActivityLogController extends Controller
{
    use ChecksRole;

    /**
     * Display a listing of activity logs.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');
        if (!in_array($sortDir, ['asc', 'desc'], true)) {
            $sortDir = 'desc';
        }

        // If caretaker, only show their own logs
        if ($this->isCaretaker()) {
            $query->where('user_id', Auth::id());
        } else {
            // Owners, admins, and developers can filter by any user
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
        }

        // Filter by action type
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

        // Apply sorting
        $allowedSortColumns = ['log_id', 'user_id', 'action', 'created_at'];
        if (in_array($sortBy, $allowedSortColumns, true)) {
            $query->orderBy($sortBy, $sortDir);
            if ($sortBy !== 'log_id') {
                $query->orderBy('log_id', $sortDir);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Pagination
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [5, 10, 15, 20], true)) {
            $perPage = 10;
        }

        $logs = $query->paginate($perPage)->withQueryString();

        // Get unique actions for filter dropdown
        $actions = ActivityLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        // Get all users for filter dropdown (for owners, admins, and developers)
        $users = collect();
        if (!$this->isCaretaker()) {
            $users = User::orderBy('last_name')
                ->orderBy('first_name')
                ->get();
        }

        // Get filter values
        $selectedUserId = $request->input('user_id', '');
        $selectedAction = $request->input('action', '');
        $dateFrom = $request->input('date_from', '');
        $dateTo = $request->input('date_to', '');

        return view('contents.payments', compact(
            'logs',
            'actions',
            'users',
            'selectedUserId',
            'selectedAction',
            'dateFrom',
            'dateTo',
            'perPage',
            'sortBy',
            'sortDir'
        ));
    }
}

