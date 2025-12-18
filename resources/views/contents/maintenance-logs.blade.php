@extends('layouts.app')

@section('title', 'Maintenance Logs')

@section('content')
<style>
    .logs-header {
        background-color: white;
        margin-bottom: 2rem;
    }
    .logs-title {
        font-size: 2rem;
        font-weight: 700;
        color: #03255b;
        margin: 0;
    }

    .add-log-btn {
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
        cursor: pointer;
    }

    .add-log-btn:hover {
        background-color: #021d47;
        color: white;
    }

    .modal-footer .btn-primary:hover {
        background-color: #021d47 !important;
        border-color: #021d47 !important;
    }

    /* Filter Styles */
    .logs-filters {
        background-color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
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
        padding: 0.45rem 1.5rem;
        border: none;
        background-color: #03255b;
        color: white;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 8px 20px rgba(3, 37, 91, 0.25);
    }

    .filter-btn:hover {
        background-color: #021d47;
    }

    .filter-btn-clear {
        background-color: white;
        color: #475569;
        border: 1px solid #cbd5e1;
        box-shadow: none;
    }

    .filter-btn-clear:hover {
        border-color: #94a3b8;
        color: #0f172a;
    }

    /* Table Styles */
    .logs-table-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        overflow: hidden;
    }

    .logs-table {
        width: 100%;
        border-collapse: collapse;
    }

    .logs-table thead {
        background-color: #f7fafc;
    }

    .logs-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #2d3748;
        font-size: 0.875rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .logs-table th.sortable {
        cursor: pointer;
        user-select: none;
        transition: all 0.2s ease;
    }

    .logs-table th.sortable:hover {
        background: #e2e8f0;
        color: #03255b;
    }

    .logs-table th.sortable .sort-icon {
        margin-left: 0.3rem;
        font-size: 0.7rem;
        opacity: 0.4;
    }

    .logs-table th.sortable.active .sort-icon {
        opacity: 1;
        color: #03255b;
    }

    .logs-table td {
        padding: 1rem;
        color: #4a5568;
        font-size: 0.875rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .logs-table tbody tr:hover {
        background-color: #f7fafc;
    }

    .logs-table tbody tr:last-child td {
        border-bottom: none;
    }

    .status-badge {
        display: inline-block;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-badge.Pending {
        background-color: #fef3c7;
        color: #92400e;
    }

    .status-badge.In-Progress {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .status-badge.Completed {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-badge.Cancelled {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .description-text {
        max-width: 400px;
        word-wrap: break-word;
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

    .btn-update-status {
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

    .btn-update-status:hover {
        background-color: #f7fafc;
        border-color: #cbd5e0;
        color: #03255b;
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

    <div class="logs-header d-flex justify-content-between align-items-center mb-4">
        <h1 class="logs-title">Maintenance Logs</h1>
        <button type="button" class="add-log-btn" data-bs-toggle="modal" data-bs-target="#logNewIssueModal">
            <i class="bi bi-plus-circle"></i> Log New Issue
        </button>
    </div>

    <!-- Filters -->
    <div class="logs-filters">
        <form method="GET" action="{{ route('maintenance-logs') }}" id="filterForm" class="d-flex flex-wrap align-items-end gap-3">
            <div class="filter-group">
                <label class="filter-label">Status:</label>
                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="Pending" {{ $selectedStatus == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="In Progress" {{ $selectedStatus == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="Completed" {{ $selectedStatus == 'Completed' ? 'selected' : '' }}>Completed</option>
                    <option value="Cancelled" {{ $selectedStatus == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Date:</label>
                <select name="date_filter" id="dateFilterSelect" class="filter-select" onchange="handleDateFilterChange(this)">
                    <option value="all" {{ $dateFilter == 'all' ? 'selected' : '' }}>All Time</option>
                    <option value="today" {{ $dateFilter == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="this_week" {{ $dateFilter == 'this_week' ? 'selected' : '' }}>This Week</option>
                    <option value="this_month" {{ $dateFilter == 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ $dateFilter == 'last_month' ? 'selected' : '' }}>Last Month</option>
                    <option value="this_year" {{ $dateFilter == 'this_year' ? 'selected' : '' }}>This Year</option>
                    <option value="custom" {{ $dateFilter == 'custom' ? 'selected' : '' }}>Custom Range</option>
                </select>
            </div>

            <div class="filter-group" id="customDateInputs" style="display: {{ $dateFilter == 'custom' ? 'flex' : 'none' }}; gap: 0.5rem;">
                <input type="date" name="date_from" class="filter-input" value="{{ $dateFrom }}" onchange="this.form.submit()">
                <span style="color: #64748b;">to</span>
                <input type="date" name="date_to" class="filter-input" value="{{ $dateTo }}" onchange="this.form.submit()">
            </div>

            <div class="filter-group">
                <a href="{{ route('maintenance-logs') }}?date_filter=this_month" class="filter-btn filter-btn-clear" style="text-decoration: none; display: inline-block;">Clear</a>
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="logs-table-container">
        @if($logs->count() > 0)
            <table class="logs-table">
                <thead>
                    <tr>
                        <th class="sortable {{ $sortBy === 'date_reported' ? 'active' : '' }}" onclick="sortTable('date_reported')">
                            Date Reported
                            @if($sortBy === 'date_reported')
                                <i class="bi bi-{{ $sortDir === 'asc' ? 'sort-up' : 'sort-down' }} sort-icon"></i>
                            @else
                                <i class="bi bi-arrow-down-up sort-icon"></i>
                            @endif
                        </th>
                        <th class="sortable {{ $sortBy === 'asset_id' ? 'active' : '' }}" onclick="sortTable('asset_id')">
                            Asset / Location
                            @if($sortBy === 'asset_id')
                                <i class="bi bi-{{ $sortDir === 'asc' ? 'sort-up' : 'sort-down' }} sort-icon"></i>
                            @else
                                <i class="bi bi-arrow-down-up sort-icon"></i>
                            @endif
                        </th>
                        <th>Issue Description</th>
                        <th>Reported By</th>
                        <th class="sortable {{ $sortBy === 'status' ? 'active' : '' }}" onclick="sortTable('status')">
                            Status
                            @if($sortBy === 'status')
                                <i class="bi bi-{{ $sortDir === 'asc' ? 'sort-up' : 'sort-down' }} sort-icon"></i>
                            @else
                                <i class="bi bi-arrow-down-up sort-icon"></i>
                            @endif
                        </th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr>
                            <td>
                                <div>{{ $log->date_reported->format('M d, Y') }}</div>
                            </td>
                            <td>
                                <strong>{{ $log->asset_location }}</strong>
                            </td>
                            <td>
                                <div class="description-text">{{ $log->description }}</div>
                            </td>
                            <td>
                                <strong>{{ $log->reporter_name }}</strong>
                            </td>
                            <td>
                                @php
                                    $statusClass = str_replace(' ', '-', $log->status);
                                @endphp
                                <span class="status-badge {{ $statusClass }}">{{ $log->status }}</span>
                            </td>
                            <td>
                                <button type="button"
                                        class="btn-update-status"
                                        data-bs-toggle="modal"
                                        data-bs-target="#updateStatusModal{{ $log->log_id }}">
                                    <i class="bi bi-pencil-square"></i> Update Status
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                <div class="pagination-left">
                    <form method="GET" action="{{ route('maintenance-logs') }}" class="d-flex align-items-center gap-2">
                        <input type="hidden" name="status" value="{{ $selectedStatus }}">
                        <input type="hidden" name="date_filter" value="{{ $dateFilter }}">
                        @if($dateFilter == 'custom')
                            <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                            <input type="hidden" name="date_to" value="{{ $dateTo }}">
                        @endif
                        <input type="hidden" name="asset_id" value="{{ $selectedAssetId }}">
                        <label for="perPage" class="text-muted small mb-0">Rows per page</label>
                        <select class="form-select form-select-sm" id="perPage" name="per_page" onchange="this.form.submit()">
                            @foreach([5, 10, 15, 20] as $option)
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
                        <span class="fw-semibold">{{ $logs->firstItem() ?? 0 }}</span>
                        to
                        <span class="fw-semibold">{{ $logs->lastItem() ?? 0 }}</span>
                        of
                        <span class="fw-semibold">{{ $logs->total() }}</span>
                        results
                    </p>
                </div>
                <div class="pagination-right">
                    {{ $logs->appends(['status' => $selectedStatus, 'date_filter' => $dateFilter, 'date_from' => $dateFrom, 'date_to' => $dateTo, 'asset_id' => $selectedAssetId, 'per_page' => $perPage, 'sort_by' => $sortBy, 'sort_dir' => $sortDir])->links() }}
                </div>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-inbox"></i>
                </div>
                <h3>No maintenance logs found</h3>
                <p>There are no maintenance logs matching your filters.</p>
            </div>
        @endif
    </div>
</div>

<!-- Log New Issue Modal -->
<div class="modal fade" id="logNewIssueModal" tabindex="-1" aria-labelledby="logNewIssueModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logNewIssueModalLabel">Log New Issue</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('maintenance-logs.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="asset_id" class="form-label">Asset</label>
                        <select class="form-select @error('asset_id') is-invalid @enderror"
                                id="asset_id"
                                name="asset_id">
                            <option value="">General Issue</option>
                            @foreach($assets as $asset)
                                <option value="{{ $asset->asset_id }}" {{ old('asset_id') == $asset->asset_id ? 'selected' : '' }}>
                                    {{ $asset->name }} - {{ $asset->location }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Select an asset or leave as "General Issue" for issues not tied to a specific asset</small>
                        @error('asset_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Issue Description <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description"
                                  name="description"
                                  rows="4"
                                  required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="date_reported" class="form-label">Date Reported</label>
                        <input type="date"
                               class="form-control @error('date_reported') is-invalid @enderror"
                               id="date_reported"
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
                    <button type="submit" class="btn btn-primary" style="background-color: #03255b; border-color: #03255b;">
                        <i class="bi bi-plus-circle"></i> Log Issue
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Status Modals -->
@foreach($logs as $log)
<div class="modal fade" id="updateStatusModal{{ $log->log_id }}" tabindex="-1" aria-labelledby="updateStatusModalLabel{{ $log->log_id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusModalLabel{{ $log->log_id }}">Update Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('maintenance-logs.update', $log->log_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Current Status:</label>
                        <input type="text"
                               class="form-control"
                               value="{{ $log->status }}"
                               readonly
                               style="background-color: #f8fafc;">
                    </div>

                    <div class="mb-3">
                        <label for="status{{ $log->log_id }}" class="form-label">New Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror"
                                id="status{{ $log->log_id }}"
                                name="status"
                                required>
                            <option value="Pending" {{ old('status', $log->status) === 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="In Progress" {{ old('status', $log->status) === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Completed" {{ old('status', $log->status) === 'Completed' ? 'selected' : '' }}>Completed</option>
                            <option value="Cancelled" {{ old('status', $log->status) === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="notes{{ $log->log_id }}" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                  id="notes{{ $log->log_id }}"
                                  name="notes"
                                  rows="3">{{ old('notes', $log->notes) }}</textarea>
                        <small class="text-muted">Optional: Add repair details or comments</small>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="date_completed{{ $log->log_id }}" class="form-label">Date Completed</label>
                        <input type="date"
                               class="form-control @error('date_completed') is-invalid @enderror"
                               id="date_completed{{ $log->log_id }}"
                               name="date_completed"
                               value="{{ old('date_completed', $log->date_completed ? $log->date_completed->format('Y-m-d') : '') }}">
                        <small class="text-muted">Auto-fills when status is set to "Completed"</small>
                        @error('date_completed')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" style="background-color: #03255b; border-color: #03255b;">
                        <i class="bi bi-check-circle"></i> Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script>
    // Auto-fill date_completed when status is set to "Completed" for all update status modals
    document.addEventListener('DOMContentLoaded', function() {
        // Find all status select elements in update status modals
        document.querySelectorAll('[id^="status"]').forEach(function(statusSelect) {
            if (statusSelect.name === 'status') {
                const logId = statusSelect.id.replace('status', '');
                const dateCompleted = document.getElementById('date_completed' + logId);

                if (dateCompleted) {
                    statusSelect.addEventListener('change', function() {
                        if (this.value === 'Completed' && !dateCompleted.value) {
                            const today = new Date().toISOString().split('T')[0];
                            dateCompleted.value = today;
                        } else if (this.value !== 'Completed') {
                            dateCompleted.value = '';
                        }
                    });
                }
            }
        });
    });

    // Sorting function
    function sortTable(column) {
        const url = new URL(window.location.href);
        const currentSort = url.searchParams.get('sort_by');
        const currentDir = url.searchParams.get('sort_dir') || 'asc';

        if (currentSort === column) {
            url.searchParams.set('sort_dir', currentDir === 'asc' ? 'desc' : 'asc');
        } else {
            url.searchParams.set('sort_by', column);
            url.searchParams.set('sort_dir', 'asc');
        }

        window.location.href = url.toString();
    }

    // Handle date filter dropdown change
    function handleDateFilterChange(select) {
        const customDateInputs = document.getElementById('customDateInputs');
        if (select.value === 'custom') {
            customDateInputs.style.display = 'flex';
        } else {
            customDateInputs.style.display = 'none';
            select.form.submit();
        }
    }
</script>
@endsection

