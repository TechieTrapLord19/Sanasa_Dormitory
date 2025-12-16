<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use Illuminate\Validation\Rule;
use App\Models\Tenant;
use App\Traits\LogsActivity;
use App\Traits\ChecksRole;

class RoomController extends Controller
{
    use LogsActivity, ChecksRole;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tenants = Tenant::all();
        // Use fresh query to get latest data with proper relationships
        $rooms = Room::with([
            'activeBooking' => function($query) {
                $query->with(['tenant', 'secondaryTenant', 'rate']);
            },
            'assets'
        ])->get();

        $totalRooms = $rooms->count();
        $roomCounts = [
            'available' => Room::where('status', 'available')->count(),
            'occupied' => Room::where('status', 'occupied')->count(),
            'pending' => Room::where('status', 'pending')->count(),
            'maintenance' => Room::where('status', 'maintenance')->count(),
            'cleaning' => Room::where('status', 'cleaning')->count(),
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
        // Only owners can create rooms
        $this->requireOwner();

        $validatedData = $request->validate([
            'room_num' => 'required|string|unique:rooms',
            'floor' => 'required|string',
            'capacity' => 'required|integer',
            'status' => [
                'required',
                Rule::in(['available', 'pending', 'occupied', 'maintenance'])
            ]
        ]);

        $room = Room::create($validatedData);

        $this->logActivity(
            'Created Room',
            "Created room {$room->room_num} on floor {$room->floor} (Capacity: {$room->capacity}, Status: {$room->status})",
            $room
        );

        return redirect()->route('rooms.index')->with('success', 'Room created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $room = Room::with([
            'assets',
            'activeBooking' => function($query) {
                $query->with(['tenant', 'secondaryTenant', 'rate']);
            }
        ])->findOrFail($id);

        return view('contents.rooms-show', compact('room'));
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
        $room = Room::with('activeBooking')->findOrFail($id);
        $oldStatus = $room->status;

        // Prevent status changes when room has an active booking
        if ($room->activeBooking) {
            return redirect()->back()->with('error', 'Cannot change room status while occupied by an active booking. Please check out the tenant first.');
        }

        $validatedData = $request->validate([
            'status' => [
                'required',
                Rule::in(['available', 'pending', 'occupied', 'maintenance', 'cleaning'])
            ]
        ]);

        $room->update($validatedData);

        $this->logActivity(
            'Updated Room',
            "Updated room {$room->room_num} - Status changed from {$oldStatus} to {$room->status}",
            $room
        );

        return redirect()->back()->with('success', 'Room status updated successfully!');
    }

    /**
     * Mark a room as cleaned and set it back to available
     */
    public function markAsCleaned(string $id)
    {
        $room = Room::findOrFail($id);

        if ($room->status !== 'cleaning') {
            return redirect()->back()->with('error', 'This room is not in cleaning status.');
        }

        $room->update(['status' => 'available']);

        $this->logActivity(
            'Room Cleaned',
            "Marked room {$room->room_num} as cleaned and available",
            $room
        );

        return redirect()->back()->with('success', "Room {$room->room_num} has been marked as cleaned and is now available!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
