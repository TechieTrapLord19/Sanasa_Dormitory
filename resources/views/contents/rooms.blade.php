@extends('layouts.app')

@section('title', 'Rooms')

@section('content')
<style>
    .room-header {
        background-color: white;
    }
    .room-title {
        font-size: 2rem;
        font-weight: 700;
        color: #03255b;
        margin: 0;
    }
    .room-status-container {
        display: flex;
        flex-direction: column;
        align-items: end;
    }
    .room-status-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.80rem;
    }
    .status-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }
    .status-dot.available {
        background-color: #10b981;
    }
    .status-dot.occupied {
        background-color: #ef4444;
    }
    .status-dot.maintenance {
        background-color: #f59e0b;
    }
    .create-room-btn {
        background-color: #03255b;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: background-color 0.3s ease;
    }
    .create-room-btn:hover {
        background-color: #021d47;
        color: white;
    }
    .create-room-btn-icon {
        width: 24px;
        height: 24px;
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }
</style>

<div class="room-header">
    <div class="row align-items-center">
        <!-- Left: Title -->
        <div class="col-md-4 d-flex justify-content-start">
            <h1 class="room-title">Room Management</h1>
        </div>

        <!-- Center: Status Indicators -->

            <div class="col-md-4">
                <div class="room-status-container">
                    <div class="room-status-item">
                        <span class="status-dot available"></span>
                        <span>Available <strong>{{ $roomCounts['available'] ?? 0 }}/{{ $totalRooms ?? 0 }}</strong></span>
                    </div>
                    <div class="room-status-item">
                        <span class="status-dot occupied"></span>
                        <span>Occupied <strong>{{ $roomCounts['occupied'] ?? 0 }}/{{ $totalRooms ?? 0 }}</strong></span>
                    </div>
                    <div class="room-status-item">
                        <span class="status-dot maintenance"></span>
                        <span>Maintenance <strong>{{ $roomCounts['maintenance'] ?? 0 }}/{{ $totalRooms ?? 0 }}</strong></span>
                    </div>
                </div>
            </div>

        <!-- Right: Create Button -->
        <div class="col-md-4 d-flex justify-content-end">
            <button class="create-room-btn" data-bs-toggle="modal" data-bs-target="#createRoomModal">
                <span class="create-room-btn-icon">+</span>
                <span>Create New Room</span>
            </button>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- Room content will go here -->
</div>
<!-- Create Room Modal -->
<div class="modal fade" id="createRoomModal" tabindex="-1" aria-labelledby="createRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createRoomModalLabel">Create New Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('rooms.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="room_num" class="form-label">Room Number</label>
                        <input type="text" class="form-control @error('room_num') is-invalid @enderror"
                               id="room_num" name="room_num" required>
                        @error('room_num')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="floor" class="form-label">Floor</label>
                        <input type="text" class="form-control @error('floor') is-invalid @enderror"
                               id="floor" name="floor" required>
                        @error('floor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="capacity" class="form-label">Capacity</label>
                        <input type="number" class="form-control @error('capacity') is-invalid @enderror"
                               id="capacity" name="capacity" required value="{{ old('capacity', 2) }}">
                        @error('capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror"
                                id="status" name="status" required>
                            <option value="">Select status...</option>
                            <option value="available" selected>Available</option>
                            <option value="occupied">Occupied</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Room</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
