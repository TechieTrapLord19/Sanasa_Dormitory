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
        $query = MaintenanceLog::with(['asset.room', 'loggedBy']);

        // Sorting
        $sortBy = $request->input('sort_by', 'date_reported');
        $sortDir = $request->input('sort_dir', 'desc');
        if (!in_array($sortDir, ['asc', 'desc'], true)) {
            $sortDir = 'desc';
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date filter handling (preset options)
        $dateFilter = $request->input('date_filter', 'this_month');
        $dateFrom = '';
        $dateTo = '';

        switch ($dateFilter) {
            case 'today':
                $dateFrom = now()->toDateString();
                $dateTo = now()->toDateString();
                break;
            case 'this_week':
                $dateFrom = now()->startOfWeek()->toDateString();
                $dateTo = now()->endOfWeek()->toDateString();
                break;
            case 'this_month':
                $dateFrom = now()->startOfMonth()->toDateString();
                $dateTo = now()->endOfMonth()->toDateString();
                break;
            case 'last_month':
                $dateFrom = now()->subMonth()->startOfMonth()->toDateString();
                $dateTo = now()->subMonth()->endOfMonth()->toDateString();
                break;
            case 'this_year':
                $dateFrom = now()->startOfYear()->toDateString();
                $dateTo = now()->endOfYear()->toDateString();
                break;
            case 'custom':
                $dateFrom = $request->input('date_from', '');
                $dateTo = $request->input('date_to', '');
                break;
            case 'all':
            default:
                // No date filter
                $dateFilter = 'all';
                break;
        }

        // Apply date filters
        if ($dateFrom) {
            $query->whereDate('date_reported', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('date_reported', '<=', $dateTo);
        }

        // Filter by asset_id (optional)
        if ($request->filled('asset_id')) {
            $query->where('asset_id', $request->asset_id);
        }

        // Apply sorting
        $allowedSortColumns = ['log_id', 'asset_id', 'date_reported', 'date_resolved', 'status', 'created_at'];
        if (in_array($sortBy, $allowedSortColumns, true)) {
            $query->orderBy($sortBy, $sortDir);
            if ($sortBy !== 'log_id') {
                $query->orderBy('log_id', $sortDir);
            }
        } else {
            $query->orderBy('date_reported', 'desc')->orderBy('created_at', 'desc');
        }

        // Pagination
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [5, 10, 15, 20], true)) {
            $perPage = 10;
        }

        $logs = $query->paginate($perPage)->withQueryString();

        // Get all assets for filter dropdown (optional)
        $assets = Asset::with('room')->orderBy('name')->get();

        // Get filter values
        $selectedStatus = $request->input('status', '');
        $selectedAssetId = $request->input('asset_id', '');

        return view('contents.maintenance-logs', compact(
            'logs',
            'assets',
            'selectedStatus',
            'dateFilter',
            'dateFrom',
            'dateTo',
            'selectedAssetId',
            'perPage',
            'sortBy',
            'sortDir'
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

        // Automatically update asset condition to "Good" when maintenance is completed
        if ($validatedData['status'] === 'Completed' && $maintenanceLog->asset_id) {
            $asset = Asset::find($maintenanceLog->asset_id);
            if ($asset && in_array($asset->condition, ['Needs Repair', 'Broken'])) {
                $oldCondition = $asset->condition;
                $asset->condition = 'Good';
                $asset->save();

                // Log the asset condition update
                $location = $asset->room_id && $asset->room ? "room {$asset->room->room_num}" : "Storage";
                $this->logActivity(
                    'Updated Asset',
                    "Asset '{$asset->name}' in {$location} - Condition automatically changed from {$oldCondition} to Good (Maintenance completed)",
                    $asset
                );
            }
        }

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
