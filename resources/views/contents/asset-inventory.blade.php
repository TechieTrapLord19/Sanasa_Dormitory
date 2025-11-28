@extends('layouts.app')

@section('title', 'Asset Inventory')

@section('content')
<style>
    .assets-header {
        background-color: white;
        margin-bottom: 2rem;
    }
    .assets-title {
        font-size: 2rem;
        font-weight: 700;
        color: #03255b;
        margin: 0;
    }

    .add-asset-btn {
        background-color: #03255b;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: background-color 0.3s ease;
        text-decoration: none;
    }

    .add-asset-btn:hover {
        background-color: #021d47;
        color: white;
    }

    .modal-footer .btn-primary:hover {
        background-color: #021d47 !important;
        border-color: #021d47 !important;
    }

    /* Filter Styles */
    .assets-filters {
        background-color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .filter-label {
        font-weight: 600;
        color: #2d3748;
        font-size: 0.875rem;
        margin: 0;
        white-space: nowrap;
    }

    .filter-input,
    .filter-select {
        padding: 0.5rem 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.875rem;
        background-color: white;
        color: #4a5568;
        min-width: 150px;
    }

    .filter-input:focus,
    .filter-select:focus {
        outline: none;
        border-color: #03255b;
        box-shadow: 0 0 0 3px rgba(3, 37, 91, 0.1);
    }

    .filter-btn {
        padding: 0.5rem 1.5rem;
        border: none;
        background-color: #03255b;
        color: white;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .filter-btn:hover {
        background-color: #021d47;
    }

    .filter-btn-clear {
        background-color: #e2e8f0;
        color: #4a5568;
    }

    .filter-btn-clear:hover {
        background-color: #cbd5e0;
    }

    /* Table Styles */
    .assets-table-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .assets-table {
        width: 100%;
        border-collapse: collapse;
    }

    .assets-table thead {
        background-color: #f7fafc;
    }

    .assets-table th {
        padding: 1rem;
        text-align: center;
        font-weight: 600;
        color: #2d3748;
        font-size: 0.875rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .assets-table th:first-child {
        text-align: left;
    }

    .assets-table th:last-child {
        text-align: center;
        width: 1%;
        white-space: nowrap;
    }

    .assets-table td {
        padding: 1rem;
        color: #4a5568;
        font-size: 0.875rem;
        border-bottom: 1px solid #e2e8f0;
        text-align: center;
    }

    .assets-table td:first-child {
        text-align: left;
    }

    .assets-table td:last-child {
        text-align: center;
        width: 1%;
        white-space: nowrap;
    }

    .assets-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .assets-table tbody tr:last-child td {
        border-bottom: none;
    }

    .condition-badge {
        display: inline-block;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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

    .btn-edit-asset {
        padding: 0.375rem 0.875rem;
        border: 1px solid #e2e8f0;
        background-color: white;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        color: #4a5568;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .btn-edit-asset:hover {
        background-color: #f7fafc;
        border-color: #cbd5e0;
        color: #03255b;
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #718096;
    }

    .empty-state-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.25rem;
        background-color: #f8fafc;
        border-top: 1px solid #e2e8f0;
    }

    .pagination-wrapper .form-select {
        width: auto;
        border-radius: 999px;
        min-width: 70px;
    }

    .pagination-left {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .pagination-center {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .pagination-right {
        display: flex;
        align-items: center;
    }

    /* Fix pagination styling */
    .pagination-wrapper .pagination {
        margin: 0;
        display: flex;
        list-style: none;
        gap: 0.25rem;
    }

    .pagination-wrapper .pagination .page-item {
        margin: 0;
    }

    .pagination-wrapper .pagination .page-link {
        padding: 0.5rem 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        color: #475569;
        text-decoration: none;
        background-color: white;
        transition: all 0.2s ease;
    }

    .pagination-wrapper .pagination .page-link:hover {
        background-color: #f1f5f9;
        border-color: #cbd5e1;
        color: #03255b;
    }

    .pagination-wrapper .pagination .page-item.active .page-link {
        background-color: #03255b;
        border-color: #03255b;
        color: white;
        font-weight: 600;
    }

    .pagination-wrapper .pagination .page-item.disabled .page-link {
        background-color: #f8fafc;
        border-color: #e2e8f0;
        color: #94a3b8;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .pagination-wrapper .pagination .page-link:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(3, 37, 91, 0.1);
    }

    /* Hide the large chevron icons if they exist */
    .pagination-wrapper svg {
        display: none !important;
    }

    /* Hide the "Showing X to Y" text from Laravel pagination since we display it manually */
    .pagination-wrapper nav > div:first-child {
        display: none !important;
    }

    .pagination-wrapper nav > div:last-child > div:first-child {
        display: none !important;
    }

    /* Show only the pagination controls (ul.pagination) */
    .pagination-wrapper nav > div:last-child > div:last-child {
        display: block !important;
    }

    /* Style our custom "Showing X to Y" text */
    .pagination-center .small {
        font-size: 0.875rem;
        color: #64748b;
        margin: 0;
    }

    .pagination-center .fw-semibold {
        font-weight: 600;
        color: #0f172a;
    }
</style>

<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> Please fix the following errors:
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="assets-header d-flex justify-content-between align-items-center mb-4">
        <h1 class="assets-title">Asset Inventory</h1>
        <button type="button" class="add-asset-btn" data-bs-toggle="modal" data-bs-target="#addAssetModal">
            <i class="bi bi-plus-circle"></i> Add New Asset
        </button>
    </div>

    <!-- Filters -->
    <div class="assets-filters">
        <form method="GET" action="{{ route('asset-inventory') }}" id="filterForm" class="d-flex flex-wrap align-items-end gap-3">
            <div class="filter-group">
                <label class="filter-label">Asset Type:</label>
                <select name="asset_type" class="filter-select">
                    <option value="">All Asset Types</option>
                    @foreach($assetTypes as $assetType)
                        <option value="{{ $assetType }}" {{ $selectedAssetType == $assetType ? 'selected' : '' }}>
                            {{ $assetType }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Condition:</label>
                <select name="condition" class="filter-select">
                    <option value="">All Conditions</option>
                    <option value="Good" {{ $selectedCondition == 'Good' ? 'selected' : '' }}>Good</option>
                    <option value="Needs Repair" {{ $selectedCondition == 'Needs Repair' ? 'selected' : '' }}>Needs Repair</option>
                    <option value="Broken" {{ $selectedCondition == 'Broken' ? 'selected' : '' }}>Broken</option>
                    <option value="Missing" {{ $selectedCondition == 'Missing' ? 'selected' : '' }}>Missing</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Location:</label>
                <select name="location" class="filter-select">
                    <option value="all" {{ $selectedLocation == 'all' || $selectedLocation == '' ? 'selected' : '' }}>All Locations</option>
                    <option value="storage" {{ $selectedLocation == 'storage' ? 'selected' : '' }}>Storage</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->room_id }}" {{ $selectedLocation == $room->room_id ? 'selected' : '' }}>
                            Room {{ $room->room_num }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Search:</label>
                <input type="text"
                       name="search"
                       class="filter-input"
                       placeholder="Search by asset name..."
                       value="{{ $searchTerm }}">
            </div>

            <div class="filter-group">
                <button type="submit" class="filter-btn">Apply Filters</button>
                <a href="{{ route('asset-inventory') }}" class="filter-btn filter-btn-clear" style="text-decoration: none; display: inline-block;">Clear</a>
            </div>
        </form>
    </div>

    <!-- Assets Table -->
    <div class="assets-table-container">
        @if($assets->count() > 0)
            <table class="assets-table">
                <thead>
                    <tr>
                        <th>Asset Name</th>
                        <th>Location</th>
                        <th>Condition</th>
                        <th>Date Acquired</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assets as $asset)
                        <tr>
                            <td><strong>{{ $asset->name }}</strong></td>
                            <td>{{ $asset->location }}</td>
                            <td>
                                <span class="condition-badge {{ str_replace(' ', '-', $asset->condition) }}">
                                    {{ $asset->condition }}
                                </span>
                            </td>
                            <td>
                                {{ $asset->date_acquired ? $asset->date_acquired->format('M d, Y') : 'N/A' }}
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    <button type="button"
                                            class="btn-edit-asset"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editAssetModal{{ $asset->asset_id }}">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <button type="button"
                                            class="btn-edit-asset"
                                            data-bs-toggle="modal"
                                            data-bs-target="#logRepairModal{{ $asset->asset_id }}">
                                        <i class="bi bi-tools"></i> Log Repair
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                <div class="pagination-left">
                    <form method="GET" action="{{ route('asset-inventory') }}" class="d-flex align-items-center gap-2">
                        <input type="hidden" name="asset_type" value="{{ $selectedAssetType }}">
                        <input type="hidden" name="condition" value="{{ $selectedCondition }}">
                        <input type="hidden" name="location" value="{{ $selectedLocation }}">
                        <input type="hidden" name="search" value="{{ $searchTerm }}">
                        <label for="perPage" class="text-muted small mb-0">Rows per page</label>
                        <select class="form-select form-select-sm" id="perPage" name="per_page" onchange="this.form.submit()">
                            @foreach([10, 25, 50, 100] as $option)
                                <option value="{{ $option }}" {{ (int) $perPage === $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="pagination-center">
                    <p class="small text-muted mb-0">
                        Showing
                        <span class="fw-semibold">{{ $assets->firstItem() ?? 0 }}</span>
                        to
                        <span class="fw-semibold">{{ $assets->lastItem() ?? 0 }}</span>
                        of
                        <span class="fw-semibold">{{ $assets->total() }}</span>
                        results
                    </p>
                </div>
                <div class="pagination-right">
                    {{ $assets->appends(['asset_type' => $selectedAssetType, 'condition' => $selectedCondition, 'location' => $selectedLocation, 'search' => $searchTerm, 'per_page' => $perPage])->links() }}
                </div>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-inbox"></i>
                </div>
                <h3>No assets found</h3>
                <p>There are no assets matching your filters.</p>
            </div>
        @endif
    </div>
</div>

<!-- Add New Asset Modal -->
<div class="modal fade" id="addAssetModal" tabindex="-1" aria-labelledby="addAssetModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAssetModalLabel">Add New Asset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('assets.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="asset_name" class="form-label">Asset Name <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               id="asset_name"
                               name="name"
                               value="{{ old('name') }}"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="asset_location" class="form-label">Location <span class="text-danger">*</span></label>
                        <select class="form-select @error('room_id') is-invalid @enderror"
                                id="asset_location"
                                name="room_id">
                            <option value="">Storage</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->room_id }}" {{ old('room_id') == $room->room_id ? 'selected' : '' }}>
                                    Room {{ $room->room_num }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Select "Storage" or a specific room</small>
                        @error('room_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="asset_condition" class="form-label">Condition <span class="text-danger">*</span></label>
                        <select class="form-select @error('condition') is-invalid @enderror"
                                id="asset_condition"
                                name="condition"
                                required>
                            <option value="">Select condition...</option>
                            <option value="Good" {{ old('condition', 'Good') === 'Good' ? 'selected' : '' }}>Good</option>
                            <option value="Needs Repair" {{ old('condition') === 'Needs Repair' ? 'selected' : '' }}>Needs Repair</option>
                            <option value="Broken" {{ old('condition') === 'Broken' ? 'selected' : '' }}>Broken</option>
                            <option value="Missing" {{ old('condition') === 'Missing' ? 'selected' : '' }}>Missing</option>
                        </select>
                        @error('condition')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="asset_date_acquired" class="form-label">Date Acquired</label>
                        <input type="date"
                               class="form-control @error('date_acquired') is-invalid @enderror"
                               id="asset_date_acquired"
                               name="date_acquired"
                               value="{{ old('date_acquired', date('Y-m-d')) }}">
                        @error('date_acquired')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" style="background-color: #03255b; border-color: #03255b;">
                        <i class="bi bi-plus-circle"></i> Add Asset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Asset Modals -->
@foreach($assets as $asset)
<div class="modal fade" id="editAssetModal{{ $asset->asset_id }}" tabindex="-1" aria-labelledby="editAssetModalLabel{{ $asset->asset_id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAssetModalLabel{{ $asset->asset_id }}">Edit Asset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('assets.update', $asset->asset_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
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
                        <label for="edit_asset_location{{ $asset->asset_id }}" class="form-label">Location <span class="text-danger">*</span></label>
                        <select class="form-select @error('room_id') is-invalid @enderror"
                                id="edit_asset_location{{ $asset->asset_id }}"
                                name="room_id">
                            <option value="" {{ !$asset->room_id ? 'selected' : '' }}>Storage</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->room_id }}" {{ old('room_id', $asset->room_id) == $room->room_id ? 'selected' : '' }}>
                                    Room {{ $room->room_num }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Change location to move asset between rooms or storage</small>
                        @error('room_id')
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

<!-- Log Repair Modals -->
@foreach($assets as $asset)
<div class="modal fade" id="logRepairModal{{ $asset->asset_id }}" tabindex="-1" aria-labelledby="logRepairModalLabel{{ $asset->asset_id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logRepairModalLabel{{ $asset->asset_id }}">Log Repair for {{ $asset->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('maintenance-logs.store') }}" method="POST">
                @csrf
                <input type="hidden" name="asset_id" value="{{ $asset->asset_id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Asset:</label>
                        <input type="text"
                               class="form-control"
                               value="{{ $asset->name }} - {{ $asset->location }}"
                               readonly
                               style="background-color: #f8fafc;">
                    </div>

                    <div class="mb-3">
                        <label for="repair_description{{ $asset->asset_id }}" class="form-label">Issue Description <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="repair_description{{ $asset->asset_id }}"
                                  name="description"
                                  rows="4"
                                  required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="repair_date_reported{{ $asset->asset_id }}" class="form-label">Date Reported</label>
                        <input type="date"
                               class="form-control @error('date_reported') is-invalid @enderror"
                               id="repair_date_reported{{ $asset->asset_id }}"
                               name="date_reported"
                               value="{{ old('date_reported', date('Y-m-d')) }}">
                        <small class="text-muted">Defaults to today if not specified</small>
                        @error('date_reported')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-tools"></i> Log Repair
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
