<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaintenanceLog;
use App\Models\Asset;
use Illuminate\Support\Facades\Auth;
use App\Traits\LogsActivity;

class MaintenanceLogController extends Controller
{
    use LogsActivity;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MaintenanceLog::with(['asset.room', 'loggedBy'])
            ->orderBy('date_reported', 'desc')
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('date_reported', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date_reported', '<=', $request->date_to);
        }

        // Filter by asset_id (optional)
        if ($request->filled('asset_id')) {
            $query->where('asset_id', $request->asset_id);
        }

        // Pagination
        $perPage = (int) $request->input('per_page', 25);
        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 25;
        }

        $logs = $query->paginate($perPage)->withQueryString();

        // Get all assets for filter dropdown (optional)
        $assets = Asset::with('room')->orderBy('name')->get();

        // Get filter values
        $selectedStatus = $request->input('status', '');
        $dateFrom = $request->input('date_from', '');
        $dateTo = $request->input('date_to', '');
        $selectedAssetId = $request->input('asset_id', '');

        return view('contents.maintenance-logs', compact(
            'logs',
            'assets',
            'selectedStatus',
            'dateFrom',
            'dateTo',
            'selectedAssetId',
            'perPage'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'asset_id' => 'nullable|exists:assets,asset_id',
            'description' => 'required|string|max:1000',
            'date_reported' => 'nullable|date',
        ]);

        // Set logged_by_user_id to current user
        $validatedData['logged_by_user_id'] = Auth::id();

        // Set status to 'Pending' by default
        $validatedData['status'] = 'Pending';

        // Set date_reported to today if not provided
        if (empty($validatedData['date_reported'])) {
            $validatedData['date_reported'] = now()->toDateString();
        }

        $maintenanceLog = MaintenanceLog::create($validatedData);

        // Log activity
        $assetInfo = $maintenanceLog->asset ? "{$maintenanceLog->asset->name} - {$maintenanceLog->asset->location}" : "General Issue";
        $this->logActivity(
            'Created Maintenance Log',
            "Created maintenance log for {$assetInfo}: {$validatedData['description']}",
            $maintenanceLog
        );

        return redirect()->route('maintenance-logs')
            ->with('success', 'Maintenance issue logged successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $maintenanceLog = MaintenanceLog::findOrFail($id);

        $validatedData = $request->validate([
            'status' => 'required|in:Pending,In Progress,Completed,Cancelled',
            'notes' => 'nullable|string|max:2000',
            'date_completed' => 'nullable|date',
        ]);

        // If status is 'Completed' and date_completed is empty, set to today
        if ($validatedData['status'] === 'Completed' && empty($validatedData['date_completed'])) {
            $validatedData['date_completed'] = now()->toDateString();
        } elseif ($validatedData['status'] !== 'Completed') {
            // Clear date_completed if status is not Completed
            $validatedData['date_completed'] = null;
        }

        $oldStatus = $maintenanceLog->status;
        $maintenanceLog->update($validatedData);

        // Log activity
        $assetInfo = $maintenanceLog->asset ? "{$maintenanceLog->asset->name} - {$maintenanceLog->asset->location}" : "General Issue";
        $statusChange = $oldStatus !== $validatedData['status'] ? " (Status changed from {$oldStatus} to {$validatedData['status']})" : "";
        $notesInfo = !empty($validatedData['notes']) ? " Notes: {$validatedData['notes']}" : "";
        $this->logActivity(
            'Updated Maintenance Log',
            "Updated maintenance log for {$assetInfo}{$statusChange}.{$notesInfo}",
            $maintenanceLog
        );

        return redirect()->route('maintenance-logs')
            ->with('success', 'Maintenance log updated successfully.');
    }
}
