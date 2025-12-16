@extends('layouts.app')

@section('title', 'Room Details')

@section('content')
<style>
    .room-details-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
    }

    .details-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
        flex-shrink: 0;
        gap: 1rem;
    }

    .details-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #03255b;
        margin: 0;
    }

    .status-badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-badge.available {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-badge.occupied {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .status-badge.maintenance {
        background-color: #fef3c7;
        color: #92400e;
    }

    .status-badge.cleaning {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .status-badge.pending {
        background-color: #fef3c7;
        color: #92400e;
    }

    .info-section {
        margin-bottom: 1.5rem;
        flex-shrink: 0;
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }

    .info-section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.75rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-label {
        font-size: 0.75rem;
        color: #718096;
        font-weight: 600;
        margin-bottom: 0.2rem;
    }


    .info-value {
        font-size: 0.95rem;
        color: #2d3748;
        font-weight: 500;
    }

    .btn-action {
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }

    .btn-action i,
    .btn-edit-asset i,
    .btn i {
        font-size: 1rem;
    }

    .btn-action.btn-back {
        background-color: #f1f5f9;
        color: #475569;
    }

    .btn-action.btn-back:hover {
        background-color: #e2e8f0;
        color: #334155;
    }

    .btn-action.btn-booking {
        background-color: #f1f5f9;
        color: #475569;
    }

    .btn-action.btn-add-asset {
        background-color: #03255b;
        color: white;
    }

    .btn-action.btn-add-asset:hover {
        background-color: #021d47;
        color: white;
    }

    .assets-table-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        overflow: hidden;
    }

    .assets-table {
        width: 100%;
        border-collapse: collapse;
    }

    .assets-table thead {
        background-color: #f8fafc;
    }

    .assets-table th {
        padding: 0.75rem 1rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
    }

    .assets-table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #e2e8f0;
        font-size: 0.875rem;
        color: #2d3748;
    }

    .btn-edit-asset {
        padding: 0.375rem 0.75rem;
        background-color: #03255b;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-edit-asset:hover {
        background-color: #021d47;
        color: white;
    }

    .assets-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .assets-table tbody tr:last-child td {
        border-bottom: none;
    }

    .condition-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .condition-badge.Good {
        background-color: #d1fae5;
        color: #065f46;
    }

    .condition-badge.Needs-Repair {
        background-color: #fef3c7;
        color: #92400e;
    }

    .condition-badge.Broken {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .condition-badge.Missing {
        background-color: #e5e7eb;
        color: #4b5563;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .btn-edit-status {
        padding: 0.25rem 0.5rem;
        background-color: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-edit-status:hover {
        background-color: #e2e8f0;
        color: #03255b;
        border-color: #cbd5e1;
    }

    .btn-edit-status i {
        font-size: 0.875rem;
    }

    .btn-edit-status:disabled {
        background-color: #e5e7eb;
        color: #9ca3af;
        border-color: #d1d5db;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .btn-edit-status:disabled:hover {
        background-color: #e5e7eb;
        color: #9ca3af;
        border-color: #d1d5db;
    }


    .tenants-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 1.5rem;
    }

    .tenant-card {
        background-color: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1.25rem;
        transition: all 0.2s ease;
        cursor: pointer;
        text-decoration: none;
        display: block;
        color: inherit;
    }

    .tenant-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border-color: #cbd5e0;
        transform: translateY(-2px);
    }

    .tenant-header {
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .tenant-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #03255b;
        margin: 0;
    }

    .tenant-details {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .detail-row {
        display: flex;
        gap: 0.75rem;
        font-size: 0.9rem;
    }

    .detail-label {
        color: #718096;
        font-weight: 600;
        min-width: 120px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .detail-label i {
        color: #03255b;
        font-size: 0.9rem;
    }

    .detail-value {
        color: #2d3748;
        word-break: break-word;
    }
</style>

<div class="room-details-container">
    <div class="details-header">
        <div>
            <h1 class="details-title">Room {{ $room->room_num }}</h1>
        </div>
        <div class="action-buttons">
            <a href="{{ route('rooms.index') }}" class="btn-action btn-back">
                <i class="bi bi-arrow-left"></i> Back to Rooms
            </a>
            @if($room->activeBooking)
                <a href="{{ route('bookings.show', $room->activeBooking->booking_id) }}" class="btn-action btn-booking">
                    <i class="bi bi-file-earmark-text"></i> View Booking Details
                </a>
            @endif
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <h5 class="alert-heading">Please fix the following errors:</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

   <!-- Room Information -->
    <div class="info-section">
        <h2 class="info-section-title">Room Information</h2>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Room Number</span>
                <span class="info-value">{{ $room->room_num }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Floor</span>
                <span class="info-value">Floor {{ $room->floor }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Capacity</span>
                <span class="info-value">{{ $room->capacity }} person(s)</span>
            </div>
            <div class="info-item">
                <span class="info-label">Status</span>
                <span class="info-value" style="display: flex; align-items: center; gap: 0.5rem;">
                    <span class="status-badge {{ $room->status }}">{{ ucfirst($room->status) }}</span>
                    @if($room->status === 'cleaning')
                        <form action="{{ route('rooms.mark-cleaned', $room->room_id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Mark this room as cleaned and available?')">
                                <i class="bi bi-check-circle"></i> Mark as Cleaned
                            </button>
                        </form>
                    @else
                        <button type="button"
                                class="btn-edit-status"
                                data-bs-toggle="modal"
                                data-bs-target="#editStatusModal"
                                title="Edit Status">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                    @endif
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Current Rate</span>
                <span class="info-value">
                    @if($room->activeBooking && $room->activeBooking->rate)
                        @php
                            $rate = $room->activeBooking->rate;
                            $rateLabel = $rate->rate_name ?? $rate->duration_type;
                        @endphp
                        <strong>{{ $rateLabel }}</strong> &middot; ₱{{ number_format($rate->base_price, 2) }}
                    @else
                        <span style="color: #94a3b8;">No rate assigned</span>
                    @endif
                </span>
            </div>
        </div>
    </div>
    <!-- Current Tenant(s) -->
    <div class="info-section">
        <h2 class="info-section-title">Current Tenant(s)</h2>
        <div class="tenants-grid">
            @php
                $occupants = collect([$room->activeBooking?->tenant, $room->activeBooking?->secondaryTenant])->filter();
            @endphp
            @if($occupants->isNotEmpty())
                @foreach($occupants as $index => $occupant)
                    <a href="{{ route('tenants.show', $occupant->tenant_id) }}" class="tenant-card">
                        <div class="tenant-header">
                            <h4 class="tenant-name">{{ $occupant->full_name }}</h4>
                        </div>
                        <div class="tenant-details">
                            <div class="detail-row">
                                <span class="detail-label"><i class="bi bi-envelope"></i> Email:</span>
                                <span class="detail-value">{{ $occupant->email ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label"><i class="bi bi-telephone"></i> Contact:</span>
                                <span class="detail-value">{{ $occupant->contact_num ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label"><i class="bi bi-exclamation-circle"></i> Emergency:</span>
                                <span class="detail-value">{{ $occupant->emer_contact_num ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            @else
                <p class="text-muted">No tenant assigned</p>
            @endif
        </div>
    </div>


    <!-- Asset Inventory -->
    <div class="info-section">
        <div class="section-header">
            <h2 class="info-section-title mb-0">Asset Inventory</h2>
            <button type="button" class="btn-action btn-add-asset" data-bs-toggle="modal" data-bs-target="#assignAssetModal">
                <i class="bi bi-box-arrow-in-down"></i> Assign Asset
            </button>
        </div>

        <div class="assets-table-container">
            <table class="assets-table">
                <thead>
                    <tr>
                        <th>Asset Name</th>
                        <th>Condition</th>
                        <th>Date Acquired</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($room->assets as $asset)
                        <tr>
                            <td><strong>{{ $asset->name }}</strong></td>
                            <td>
                                <span class="condition-badge {{ str_replace(' ', '-', $asset->condition) }}">
                                    {{ $asset->condition }}
                                </span>
                            </td>
                            <td>
                                {{ $asset->date_acquired ? $asset->date_acquired->format('M d, Y') : 'N/A' }}
                            </td>
                            <td>
                                <button type="button"
                                        class="btn-edit-asset"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editAssetModal{{ $asset->asset_id }}">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No assets found for this room</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Assign Asset Modal -->
<div class="modal fade" id="assignAssetModal" tabindex="-1" aria-labelledby="assignAssetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignAssetModalLabel">Assign Asset to Room {{ $room->room_num }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('assets.assign') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="room_id" value="{{ $room->room_id }}">

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Select an asset from Storage or another room to assign to this room.
                    </div>

                    <div class="mb-3">
                        <label for="asset_id" class="form-label">Select Asset <span class="text-danger">*</span></label>
                        <select class="form-select @error('asset_id') is-invalid @enderror"
                                id="asset_id"
                                name="asset_id"
                                required>
                            <option value="">Choose an asset...</option>
                            @php
                                $allAssets = \App\Models\Asset::with('room')->orderBy('name')->get();
                                $storageAssets = $allAssets->whereNull('room_id');
                                $otherRoomAssets = $allAssets->where('room_id', '!=', $room->room_id)->whereNotNull('room_id');
                            @endphp

                            @if($storageAssets->isNotEmpty())
                                <optgroup label="📦 From Storage">
                                    @foreach($storageAssets as $asset)
                                        <option value="{{ $asset->asset_id }}">
                                            {{ $asset->name }} - {{ $asset->condition }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif

                            @if($otherRoomAssets->isNotEmpty())
                                <optgroup label="🚪 From Other Rooms">
                                    @foreach($otherRoomAssets as $asset)
                                        <option value="{{ $asset->asset_id }}">
                                            {{ $asset->name }} (Room {{ $asset->room->room_num }}) - {{ $asset->condition }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif

                            @if($storageAssets->isEmpty() && $otherRoomAssets->isEmpty())
                                <option value="" disabled>No available assets to assign</option>
                            @endif
                        </select>
                        @error('asset_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Assets currently assigned to this room are not shown.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-box-arrow-in-down"></i> Assign to Room
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Asset Modals -->
@foreach($room->assets as $asset)
<div class="modal fade" id="editAssetModal{{ $asset->asset_id }}" tabindex="-1" aria-labelledby="editAssetModalLabel{{ $asset->asset_id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAssetModalLabel{{ $asset->asset_id }}">Edit Asset: {{ $asset->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('assets.update', $asset->asset_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Asset Details Section -->
                    <div class="mb-3">
                        <label for="edit_asset_name{{ $asset->asset_id }}" class="form-label">Asset Name <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="edit_asset_name{{ $asset->asset_id }}"
                               name="name"
                               value="{{ old('name', $asset->name) }}"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="edit_asset_condition{{ $asset->asset_id }}" class="form-label">Condition <span class="text-danger">*</span></label>
                        <select class="form-select @error('condition') is-invalid @enderror"
                                id="edit_asset_condition{{ $asset->asset_id }}"
                                name="condition"
                                required>
                            <option value="">Select condition...</option>
                            <option value="Good" {{ old('condition', $asset->condition) === 'Good' ? 'selected' : '' }}>Good</option>
                            <option value="Needs Repair" {{ old('condition', $asset->condition) === 'Needs Repair' ? 'selected' : '' }}>Needs Repair</option>
                            <option value="Broken" {{ old('condition', $asset->condition) === 'Broken' ? 'selected' : '' }}>Broken</option>
                            <option value="Missing" {{ old('condition', $asset->condition) === 'Missing' ? 'selected' : '' }}>Missing</option>
                        </select>
                        @error('condition')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="edit_asset_date_acquired{{ $asset->asset_id }}" class="form-label">Date Acquired</label>
                        <input type="date"
                               class="form-control @error('date_acquired') is-invalid @enderror"
                               id="edit_asset_date_acquired{{ $asset->asset_id }}"
                               name="date_acquired"
                               value="{{ old('date_acquired', $asset->date_acquired ? $asset->date_acquired->format('Y-m-d') : '') }}">
                        @error('date_acquired')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">

                    <!-- Move/Transfer Section -->
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> <strong>Optional:</strong> Move this asset to another location
                    </div>

                    <div class="mb-3">
                        <label for="edit_room_id{{ $asset->asset_id }}" class="form-label">Location</label>
                        <select class="form-select @error('room_id') is-invalid @enderror"
                                id="edit_room_id{{ $asset->asset_id }}"
                                name="room_id">
                            <option value="{{ $room->room_id }}" selected>🚪 Keep in Room {{ $room->room_num }} (Current)</option>
                            <option value="">🏪 Move to Storage</option>
                            @php
                                $allRooms = \App\Models\Room::where('room_id', '!=', $room->room_id)
                                    ->orderBy('room_num')
                                    ->get();
                            @endphp
                            @foreach($allRooms as $otherRoom)
                                <option value="{{ $otherRoom->room_id }}">
                                    🚪 Move to Room {{ $otherRoom->room_num }} ({{ ucfirst($otherRoom->status) }})
                                </option>
                            @endforeach
                        </select>
                        @error('room_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Leave as current room to only update asset details, or select a new location to move it.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Update Asset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Edit Status Modal -->
<div class="modal fade" id="editStatusModal" tabindex="-1" aria-labelledby="editStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStatusModalLabel">Update Room Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('rooms.update', $room->room_id) }}" method="POST" id="editStatusForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    @if($room->activeBooking)
                        <div class="mb-3">
                            <label for="room_status" class="form-label">Room Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror"
                                    id="room_status"
                                    name="status"
                                    required>
                                <option value="occupied" {{ old('status', $room->status) === 'occupied' ? 'selected' : '' }}>Occupied</option>
                                <option value="maintenance" {{ old('status', $room->status) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Room is occupied. Only "Maintenance" status can be selected if room needs repair.</small>
                        </div>
                    @else
                        <div class="mb-3">
                            <label for="room_status" class="form-label">Room Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror"
                                    id="room_status"
                                    name="status"
                                    required>
                                <option value="available" {{ old('status', $room->status) === 'available' ? 'selected' : '' }}>Available</option>
                                <option value="occupied" {{ old('status', $room->status) === 'occupied' ? 'selected' : '' }}>Occupied</option>
                                <option value="cleaning" {{ old('status', $room->status) === 'cleaning' ? 'selected' : '' }}>Cleaning</option>
                                <option value="maintenance" {{ old('status', $room->status) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Select "Cleaning" if room needs cleaning, or "Maintenance" if it needs repair.</small>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


