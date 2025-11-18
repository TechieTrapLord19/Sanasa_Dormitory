<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\Room;
use Illuminate\Validation\Rule;
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

        $this->logActivity(
            'Created Asset',
            "Added asset '{$asset->name}' to {$location} (Condition: {$asset->condition})",
            $asset
        );

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
}
