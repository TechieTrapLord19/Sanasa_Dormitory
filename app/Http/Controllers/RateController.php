<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rate;
use App\Models\Room;
use App\Models\Utility;
use App\Models\Booking;
use App\Traits\LogsActivity;
use App\Traits\ChecksRole;

class RateController extends Controller
{
    use LogsActivity, ChecksRole;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rates = Rate::with('utilities')->get();
        // Only show occupied rooms in the active room rates section
        $occupiedRooms = Room::where('status', 'occupied')->get();
        $floors = Room::select('floor')->distinct()->orderBy('floor')->pluck('floor');
        
        return view('contents.rates', compact('rates', 'occupiedRooms', 'floors'));
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
        // Only owners can create rates
        $this->requireOwner();

        $validatedData = $request->validate([
            'rate_name' => 'nullable|string|max:255',
            'duration_type' => 'required|string|in:Daily,Weekly,Monthly',
            'base_price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'utilities' => 'nullable|array',
            'utilities.*.name' => 'required_with:utilities|string|max:255',
            'utilities.*.price' => 'required_with:utilities|numeric|min:0',
        ]);

        $rate = Rate::create([
            'rate_name' => $validatedData['rate_name'] ?? null,
            'duration_type' => $validatedData['duration_type'],
            'base_price' => $validatedData['base_price'],
            'description' => $validatedData['description'],
        ]);

        // Create utilities if provided
        if (isset($validatedData['utilities']) && is_array($validatedData['utilities'])) {
            foreach ($validatedData['utilities'] as $utilityData) {
                if (!empty($utilityData['name'])) {
                    Utility::create([
                        'rate_id' => $rate->rate_id,
                        'name' => $utilityData['name'],
                        'price' => $utilityData['price'] ?? 0.00,
                    ]);
                }
            }
        }

        $rateName = $rate->rate_name ?? $rate->duration_type . ' Rate';
        $this->logActivity(
            'Created Rate',
            "Created {$rateName} - Base Price: ₱" . number_format($rate->base_price, 2) . " (Description: {$rate->description})",
            $rate
        );

        return redirect()->route('rates.index')->with('success', 'Rate created successfully!');
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
        $rate = Rate::with('utilities')->findOrFail($id);
        return response()->json($rate);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Only owners can update rates
        $this->requireOwner();

        $rate = Rate::findOrFail($id);

        $validatedData = $request->validate([
            'rate_name' => 'nullable|string|max:255',
            'duration_type' => 'required|string|in:Daily,Weekly,Monthly',
            'base_price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'utilities' => 'nullable|array',
            'utilities.*.name' => 'required_with:utilities|string|max:255',
            'utilities.*.price' => 'required_with:utilities|numeric|min:0',
        ]);

        $rate->update([
            'rate_name' => $validatedData['rate_name'] ?? null,
            'duration_type' => $validatedData['duration_type'],
            'base_price' => $validatedData['base_price'],
            'description' => $validatedData['description'],
        ]);

        // Delete existing utilities and create new ones
        $rate->utilities()->delete();
        if (isset($validatedData['utilities']) && is_array($validatedData['utilities'])) {
            foreach ($validatedData['utilities'] as $utilityData) {
                if (!empty($utilityData['name'])) {
                    Utility::create([
                        'rate_id' => $rate->rate_id,
                        'name' => $utilityData['name'],
                        'price' => $utilityData['price'] ?? 0.00,
                    ]);
                }
            }
        }

        $rateName = $rate->rate_name ?? $rate->duration_type . ' Rate';
        $this->logActivity(
            'Updated Rate',
            "Updated {$rateName} - Base Price: ₱" . number_format($rate->base_price, 2) . " (Description: {$rate->description})",
            $rate
        );

        return redirect()->route('rates.index')->with('success', 'Rate updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Only owners can delete rates
        $this->requireOwner();

        $rate = Rate::findOrFail($id);
        $rateName = $rate->rate_name ?? $rate->duration_type . ' Rate';

        // Check if there are any bookings using this rate
        $bookingsCount = Booking::where('rate_id', $rate->rate_id)->count();
        
        if ($bookingsCount > 0) {
            return redirect()->route('rates.index')
                ->with('error', "Cannot delete {$rateName}. This rate is currently being used by {$bookingsCount} booking(s). Please remove or update the bookings first.");
        }

        // Delete associated utilities
        $rate->utilities()->delete();
        
        // Delete the rate
        $rate->delete();

        $this->logActivity(
            'Deleted Rate',
            "Deleted {$rateName}",
            null
        );

        return redirect()->route('rates.index')->with('success', 'Rate deleted successfully!');
    }
}
