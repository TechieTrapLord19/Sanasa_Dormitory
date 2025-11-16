<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use Illuminate\Validation\Rule;
use App\Traits\LogsActivity;

class AssetController extends Controller
{
    use LogsActivity;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            'room_id' => 'required|exists:rooms,room_id',
            'name' => 'required|string|max:255',
            'condition' => [
                'required',
                Rule::in(['Good', 'Needs Repair', 'Broken', 'Missing'])
            ],
            'date_acquired' => 'nullable|date',
        ]);

        $asset = Asset::create($validatedData);
        $room = $asset->room;

        $this->logActivity(
            'Created Asset',
            "Added asset '{$asset->name}' to room {$room->room_num} (Condition: {$asset->condition})",
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
            'name' => 'sometimes|required|string|max:255',
            'condition' => [
                'sometimes',
                'required',
                Rule::in(['Good', 'Needs Repair', 'Broken', 'Missing'])
            ],
            'date_acquired' => 'nullable|date',
        ]);

        $oldCondition = $asset->condition;
        $asset->update($validatedData);
        $room = $asset->room;

        $description = "Updated asset '{$asset->name}' in room {$room->room_num}";
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
