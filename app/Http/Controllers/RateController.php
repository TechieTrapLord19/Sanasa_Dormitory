<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rate;
use App\Models\Room;

class RateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rates = Rate::all();
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
        $validatedData = $request->validate([
            'duration_type' => 'required|string|in:Daily,Weekly,Monthly',
            'base_price' => 'required|numeric|min:0',
            'inclusion' => 'required|string',
        ]);

        Rate::create($validatedData);

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
