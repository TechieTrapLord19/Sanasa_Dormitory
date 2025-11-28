<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\Room;
use App\Models\MaintenanceLog;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Traits\LogsActivity;

class AssetController extends Controller
{
    use LogsActivity;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Asset::with('room')->orderBy('name')->orderBy('asset_id');

        // Get filter values
        $selectedAssetType = $request->input('asset_type', '');
        $selectedCondition = $request->input('condition', '');
        $selectedLocation = $request->input('location', '');
        $searchTerm = trim($request->input('search', ''));

        // Filter by asset type/name
        if ($selectedAssetType) {
            $query->where('name', $selectedAssetType);
        }

        // Filter by condition
        if ($selectedCondition) {
            $query->where('condition', $selectedCondition);
        }

        // Filter by location
        if ($selectedLocation === 'storage') {
            $query->whereNull('room_id');
        } elseif ($selectedLocation && $selectedLocation !== 'all') {
            $query->where('room_id', $selectedLocation);
        }

        // Search by asset name
        if ($searchTerm) {
            $query->where('name', 'like', "%{$searchTerm}%");
        }

        // Pagination
        $perPage = (int) $request->input('per_page', 25);
        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 25;
        }

        $assets = $query->paginate($perPage)->withQueryString();

        // Get unique asset names for filter dropdown
        $assetTypes = Asset::select('name')
            ->distinct()
            ->orderBy('name')
            ->pluck('name');

        // Get all rooms for location filter
        $rooms = Room::orderBy('room_num')->get();

        return view('contents.asset-inventory', compact(
            'assets',
            'assetTypes',
            'rooms',
            'selectedAssetType',
            'selectedCondition',
            'selectedLocation',
            'searchTerm',
            'perPage'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'room_id' => 'nullable|exists:rooms,room_id',
            'name' => 'required|string|max:255',
            'condition' => [
                'required',
                Rule::in(['Good', 'Needs Repair', 'Broken', 'Missing'])
            ],
            'date_acquired' => 'nullable|date',
        ]);

        // Handle empty room_id as null (for storage)
        if (empty($validatedData['room_id'])) {
            $validatedData['room_id'] = null;
        }

        $asset = Asset::create($validatedData);

        // Log activity with appropriate location
        if ($asset->room_id) {
            $room = $asset->room;
            $location = "room {$room->room_num}";
        } else {
            $location = "Storage";
        }

        $description = "Added asset '{$asset->name}' to {$location} (Condition: {$asset->condition})";

        // Automatically create maintenance log if asset is created with "Needs Repair" or "Broken" condition
        if (in_array($asset->condition, ['Needs Repair', 'Broken'])) {
            $issueDescription = "New asset '{$asset->name}' added to {$location} with condition '{$asset->condition}'. ";

            // Add automatic description based on condition
            if ($asset->condition === 'Needs Repair') {
                $issueDescription .= "Requires maintenance and repair work.";
            } else if ($asset->condition === 'Broken') {
                $issueDescription .= "Asset is broken and needs immediate attention.";
            }

            MaintenanceLog::create([
                'asset_id' => $asset->asset_id,
                'description' => $issueDescription,
                'logged_by_user_id' => Auth::id(),
                'date_reported' => now(),
                'status' => 'Pending',
            ]);

            $description .= " - Maintenance log created automatically";
        }

        $this->logActivity('Created Asset', $description, $asset);

        return redirect()->back()->with('success', 'Asset added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $asset = Asset::findOrFail($id);

        $validatedData = $request->validate([
            'room_id' => 'nullable|exists:rooms,room_id',
            'name' => 'sometimes|required|string|max:255',
            'condition' => [
                'sometimes',
                'required',
                Rule::in(['Good', 'Needs Repair', 'Broken', 'Missing'])
            ],
            'date_acquired' => 'nullable|date',
        ]);

        // Handle empty room_id as null (for storage)
        if (isset($validatedData['room_id']) && empty($validatedData['room_id'])) {
            $validatedData['room_id'] = null;
        }

        $oldCondition = $asset->condition;
        $oldRoomId = $asset->room_id;
        $oldRoom = $asset->room; // Get old room before update

        $asset->update($validatedData);
        $asset->refresh()->load('room'); // Refresh and reload room relationship

        // Build description with location info
        $oldLocation = $oldRoomId && $oldRoom ? "room {$oldRoom->room_num}" : "Storage";
        $newLocation = $asset->room_id && $asset->room ? "room {$asset->room->room_num}" : "Storage";

        $description = "Updated asset '{$asset->name}'";

        // If location changed
        if (isset($validatedData['room_id']) && $oldRoomId != $asset->room_id) {
            $description .= " - Moved from {$oldLocation} to {$newLocation}";
        } else {
            $description .= " in {$newLocation}";
        }

        // If condition changed
        if (isset($validatedData['condition']) && $oldCondition !== $asset->condition) {
            $description .= " - Condition changed from {$oldCondition} to {$asset->condition}";

            // Automatically create maintenance log if condition changed to "Needs Repair" or "Broken"
            if (in_array($asset->condition, ['Needs Repair', 'Broken'])) {
                $issueDescription = "Asset condition changed from '{$oldCondition}' to '{$asset->condition}' in {$newLocation}. ";

                // Add automatic description based on condition
                if ($asset->condition === 'Needs Repair') {
                    $issueDescription .= "Requires maintenance and repair work.";
                } else if ($asset->condition === 'Broken') {
                    $issueDescription .= "Asset is broken and needs immediate attention.";
                }

                MaintenanceLog::create([
                    'asset_id' => $asset->asset_id,
                    'description' => $issueDescription,
                    'logged_by_user_id' => Auth::id(),
                    'date_reported' => now(),
                    'status' => 'Pending',
                ]);

                $description .= " - Maintenance log created automatically";
            }
        }

        $this->logActivity('Updated Asset', $description, $asset);

        return redirect()->back()->with('success', 'Asset updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Assign an existing asset to a room
     */
    public function assign(Request $request)
    {
        $validatedData = $request->validate([
            'asset_id' => 'required|exists:assets,asset_id',
            'room_id' => 'required|exists:rooms,room_id',
        ]);

        $asset = Asset::findOrFail($validatedData['asset_id']);
        $oldRoomId = $asset->room_id;
        $oldRoom = $asset->room;

        $asset->room_id = $validatedData['room_id'];
        $asset->save();
        $asset->refresh()->load('room');

        // Build activity description
        $oldLocation = $oldRoomId && $oldRoom ? "room {$oldRoom->room_num}" : "Storage";
        $newLocation = $asset->room->room_num;

        $description = "Assigned asset '{$asset->name}' from {$oldLocation} to room {$newLocation}";

        $this->logActivity('Assigned Asset', $description, $asset);

        return redirect()->back()->with('success', "Asset '{$asset->name}' has been assigned to Room {$newLocation}!");
    }

    /**
     * Move an asset to another room or storage
     */
    public function move(Request $request, string $id)
    {
        $asset = Asset::with('room')->findOrFail($id);

        $validatedData = $request->validate([
            'room_id' => 'nullable|exists:rooms,room_id',
        ]);

        $oldRoomId = $asset->room_id;
        $oldRoom = $asset->room;

        // Handle empty room_id as null (moving to storage)
        $newRoomId = $validatedData['room_id'] ?? null;

        $asset->room_id = $newRoomId;
        $asset->save();
        $asset->refresh()->load('room');

        // Build activity description
        $oldLocation = $oldRoomId && $oldRoom ? "room {$oldRoom->room_num}" : "Storage";
        $newLocation = $newRoomId && $asset->room ? "room {$asset->room->room_num}" : "Storage";

        $description = "Moved asset '{$asset->name}' from {$oldLocation} to {$newLocation}";

        $this->logActivity('Moved Asset', $description, $asset);

        return redirect()->back()->with('success', "Asset '{$asset->name}' has been moved to {$newLocation}!");
    }
}
