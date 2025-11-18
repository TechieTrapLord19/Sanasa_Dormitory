<?php

namespace App\Http\Controllers;

use App\Models\ElectricReading;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ElectricReadingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Load all rooms ordered by room number
        $rooms = Room::orderBy('room_num')->get();
        
        // Get distinct floors for filtering
        $floors = Room::select('floor')->distinct()->orderBy('floor')->pluck('floor');
        
        // For each room, get the latest reading
        $roomsWithReadings = $rooms->map(function($room) {
            $latestReading = ElectricReading::getLatestReading($room->room_id);
            return [
                'room' => $room,
                'latestReading' => $latestReading,
            ];
        });
        
        return view('contents.electric-readings', compact('roomsWithReadings', 'floors'));
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
     * Handles both single and bulk reading entry.
     */
    public function store(Request $request)
    {
        // Check if this is a bulk submission (array of readings)
        if ($request->has('readings') && is_array($request->readings)) {
            return $this->storeBulk($request);
        }
        
        // Single reading submission (backward compatibility)
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,room_id',
            'reading_date' => 'required|date',
            'meter_value_kwh' => 'required|numeric|min:0',
        ], [
            'room_id.required' => 'Room ID is required.',
            'room_id.exists' => 'The selected room does not exist.',
            'reading_date.required' => 'Reading date is required.',
            'reading_date.date' => 'Reading date must be a valid date.',
            'meter_value_kwh.required' => 'Meter value is required.',
            'meter_value_kwh.numeric' => 'Meter value must be a number.',
            'meter_value_kwh.min' => 'Meter value must be at least 0.',
        ]);

        DB::beginTransaction();
        try {
            ElectricReading::create([
                'room_id' => $validated['room_id'],
                'reading_date' => $validated['reading_date'],
                'meter_value_kwh' => $validated['meter_value_kwh'],
                'is_billed' => false,
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Electric reading recorded successfully.']);
            }

            return redirect()->back()
                ->with('success', 'Electric reading recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to record electric reading: ' . $e->getMessage()], 500);
            }
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to record electric reading: ' . $e->getMessage()]);
        }
    }

    /**
     * Store bulk readings for multiple rooms.
     */
    private function storeBulk(Request $request)
    {
        // Custom validation: only validate readings that have meter_value_kwh
        $readings = $request->input('readings', []);
        $validReadings = [];
        
        foreach ($readings as $index => $reading) {
            if (!empty($reading['meter_value_kwh']) && $reading['meter_value_kwh'] > 0) {
                $validReadings[$index] = $reading;
            }
        }

        if (empty($validReadings)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'No readings were saved. Please enter at least one reading.'], 422);
            }
            return redirect()->back()
                ->withErrors(['error' => 'No readings were saved. Please enter at least one reading.']);
        }

        $validated = $request->validate([
            'readings' => 'required|array',
            'readings.*.room_id' => 'required|exists:rooms,room_id',
            'readings.*.reading_date' => 'required|date',
            'readings.*.meter_value_kwh' => 'nullable|numeric|min:0',
        ], [
            'readings.required' => 'Readings data is required.',
            'readings.array' => 'Readings must be an array.',
            'readings.*.room_id.required' => 'Room ID is required for all readings.',
            'readings.*.room_id.exists' => 'One or more rooms do not exist.',
            'readings.*.reading_date.required' => 'Reading date is required for all readings.',
            'readings.*.reading_date.date' => 'Reading date must be a valid date.',
            'readings.*.meter_value_kwh.numeric' => 'Meter value must be a number.',
            'readings.*.meter_value_kwh.min' => 'Meter value must be at least 0.',
        ]);

        DB::beginTransaction();
        try {
            $savedCount = 0;
            foreach ($validated['readings'] as $reading) {
                // Only save if meter_value_kwh is provided and greater than 0
                if (!empty($reading['meter_value_kwh']) && $reading['meter_value_kwh'] > 0) {
                    ElectricReading::create([
                        'room_id' => $reading['room_id'],
                        'reading_date' => $reading['reading_date'],
                        'meter_value_kwh' => $reading['meter_value_kwh'],
                        'is_billed' => false,
                    ]);
                    $savedCount++;
                }
            }

            DB::commit();

            $message = $savedCount > 0 
                ? "Successfully recorded {$savedCount} electric reading(s)."
                : "No readings were saved. Please enter at least one reading.";

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }

            return redirect()->back()
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to record electric readings: ' . $e->getMessage()], 500);
            }
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to record electric readings: ' . $e->getMessage()]);
        }
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
