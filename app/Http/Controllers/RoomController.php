<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use Illuminate\Validation\Rule;
use App\Models\Tenant;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   $tenants = Tenant::all();
        $rooms = Room::all();
        $rooms = Room::with(['tenant', 'rate', 'activeBooking.tenant','activeBooking.tenant'])->get();
        $totalRooms = $rooms->count();
        $roomCounts = [
            'available' => Room::where('status', 'available')->count(),
            'occupied' => Room::where('status', 'occupied')->count(),
            'maintenance' => Room::where('status', 'maintenance')->count(),
        ];
        $floors = Room::select('floor')->distinct()->orderBy('floor')->pluck('floor');

        return view('contents.rooms', compact('rooms', 'roomCounts', 'totalRooms', 'floors'));
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
        // 2. ADD THIS VALIDATION LOGIC
        $validatedData = $request->validate([
            'room_num' => 'required|string|unique:rooms',
            'floor' => 'required|string',
            'capacity' => 'required|integer',
            'status' => [
                'required',
                Rule::in(['available', 'occupied', 'maintenance']) // <-- This is the check
            ],
            'rate_id' => 'required|exists:rates,rate_id'

        ]);

        Room::create($validatedData);

        return redirect()->route('rooms.index')->with('success', 'Room created!');
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
        // // 3. ADD THIS VALIDATION LOGIC (for updating)
        // $validatedData = $request->validate([
        //     'room_num' => 'required|string|unique:rooms,room_num,' . $room->room_id . ',room_id',
        //     'floor' => 'required|string',
        //     'capacity' => 'required|integer',
        //     'status' => [
        //         'required',
        //         Rule::in(['available', 'occupied', 'maintenance']) // <-- This is the check
        //     ]
        // ]);

        // $room->update($validatedData);

        // return redirect()->route('rooms.index')->with('success', 'Room updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
