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

            return redirect()->back()
                ->with('success', 'Electric reading recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to record electric reading: ' . $e->getMessage()]);
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
