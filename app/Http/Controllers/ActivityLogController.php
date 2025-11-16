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
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        // If caretaker, only show their own logs
        if ($this->isCaretaker()) {
            $query->where('user_id', Auth::id());
        } else {
            // Owners can filter by caretaker
            if ($request->filled('caretaker')) {
                $query->where('user_id', $request->caretaker);
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

        // Pagination
        $perPage = (int) $request->input('per_page', 25);
        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 25;
        }

        $logs = $query->paginate($perPage)->withQueryString();

        // Get unique actions for filter dropdown
        $actions = ActivityLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        // Get all caretakers for filter dropdown (only for owners)
        $caretakers = collect();
        if ($this->isOwner()) {
            $caretakers = User::where('role', 'caretaker')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get();
        }

        // Get filter values
        $selectedCaretaker = $request->input('caretaker', '');
        $selectedAction = $request->input('action', '');
        $dateFrom = $request->input('date_from', '');
        $dateTo = $request->input('date_to', '');

        return view('contents.payments', compact(
            'logs',
            'actions',
            'caretakers',
            'selectedCaretaker',
            'selectedAction',
            'dateFrom',
            'dateTo',
            'perPage'
        ));
    }
}

