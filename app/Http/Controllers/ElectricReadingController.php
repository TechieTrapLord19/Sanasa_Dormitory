<?php

namespace App\Http\Controllers;

use App\Models\ElectricReading;
use App\Models\Room;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ElectricReadingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Load all rooms ordered by room number with active booking
        $rooms = Room::with(['activeBooking'])
            ->orderBy('room_num')
            ->get();

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

        // Get the electricity rate from database
        $electricityRate = Setting::get('electricity_rate_per_kwh', null);

        return view('contents.electric-readings', compact('roomsWithReadings', 'floors', 'electricityRate'));
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
        // Filter out empty readings from request before validation
        $readings = $request->input('readings', []);
        $filteredReadings = [];

        // Filter readings - only include those with valid meter values
        foreach ($readings as $index => $reading) {
            // Check if meter_value_kwh exists and is a valid positive number
            if (isset($reading['meter_value_kwh']) &&
                $reading['meter_value_kwh'] !== '' &&
                $reading['meter_value_kwh'] !== null &&
                is_numeric($reading['meter_value_kwh']) &&
                floatval($reading['meter_value_kwh']) > 0) {
                $filteredReadings[] = [
                    'room_id' => $reading['room_id'] ?? null,
                    'reading_date' => $reading['reading_date'] ?? null,
                    'meter_value_kwh' => $reading['meter_value_kwh'],
                ];
            }
        }

        // Require at least one reading
        if (empty($filteredReadings)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Please enter at least one reading.'], 422);
            }
            return redirect()->back()
                ->withErrors(['error' => 'Please enter at least one reading.']);
        }

        // Manually validate readings
        foreach ($filteredReadings as $index => $reading) {
            if (empty($reading['room_id'])) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => "Room ID is required for reading at index {$index}."], 422);
                }
                return redirect()->back()
                    ->withErrors(['error' => "Room ID is required for reading at index {$index}."]);
            }

            if (empty($reading['reading_date'])) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => "Reading date is required for reading at index {$index}."], 422);
                }
                return redirect()->back()
                    ->withErrors(['error' => "Reading date is required for reading at index {$index}."]);
            }

            // Validate room exists
            if (!\App\Models\Room::where('room_id', $reading['room_id'])->exists()) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => "Room ID {$reading['room_id']} does not exist."], 422);
                }
                return redirect()->back()
                    ->withErrors(['error' => "Room ID {$reading['room_id']} does not exist."]);
            }
        }

        DB::beginTransaction();
        try {
            $savedCount = 0;
            foreach ($filteredReadings as $reading) {
                ElectricReading::create([
                    'room_id' => $reading['room_id'],
                    'reading_date' => $reading['reading_date'],
                    'meter_value_kwh' => $reading['meter_value_kwh'],
                    'is_billed' => false,
                ]);
                $savedCount++;
            }

            DB::commit();

            $message = "Successfully recorded {$savedCount} electric reading(s).";

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

    /**
     * Store or update the electricity rate per kWh
     */
    public function storeRate(Request $request)
    {
        $validated = $request->validate([
            'electricity_rate_per_kwh' => 'required|numeric|min:0',
        ], [
            'electricity_rate_per_kwh.required' => 'Electricity rate is required.',
            'electricity_rate_per_kwh.numeric' => 'Electricity rate must be a number.',
            'electricity_rate_per_kwh.min' => 'Electricity rate must be at least 0.',
        ]);

        // Store in database for persistent use
        Setting::set('electricity_rate_per_kwh', $validated['electricity_rate_per_kwh']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Electricity rate updated to ₱{$validated['electricity_rate_per_kwh']}/kWh."
            ]);
        }

        return redirect()->back()
            ->with('success', "Electricity rate updated to ₱{$validated['electricity_rate_per_kwh']}/kWh.");
    }
}
