@extends('layouts.app')

@section('title', 'Activity Logs')

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

    .logs-table td {
        padding: 1rem;
        color: #4a5568;
        font-size: 0.875rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .logs-table tbody tr:hover {
        background-color: #f7fafc;
    }

    .logs-table tbody tr.clickable {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .logs-table tbody tr.clickable:hover {
        background-color: #edf2f7;
    }

    .logs-table tbody tr:last-child td {
        border-bottom: none;
    }

    .action-badge {
        display: inline-block;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .action-badge.created {
        background-color: #d1fae5;
        color: #065f46;
    }

    .action-badge.updated {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .action-badge.deleted,
    .action-badge.canceled,
    .action-badge.archived {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .action-badge.checked {
        background-color: #fef3c7;
        color: #92400e;
    }

    .action-badge.payment,
    .action-badge.generated {
        background-color: #e0e7ff;
        color: #3730a3;
    }

    .description-text {
        max-width: 500px;
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
        display: none !important; /* Hide mobile pagination */
    }

    .pagination-wrapper nav > div:last-child > div:first-child {
        display: none !important; /* Hide the "Showing X to Y" text div */
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
    <div class="logs-header d-flex justify-content-between align-items-center mb-4">
        <h1 class="logs-title">Activity Logs</h1>
    </div>

    <!-- Filters -->
    <div class="logs-filters">
        <form method="GET" action="{{ route('activity-logs') }}" id="filterForm" class="d-flex flex-wrap align-items-end gap-3">
            @if(auth()->check() && strtolower(auth()->user()->role) !== 'caretaker')
            <div class="filter-group">
                <label class="filter-label">User:</label>
                <select name="user_id" class="filter-select">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->user_id }}" {{ $selectedUserId == $user->user_id ? 'selected' : '' }}>
                            {{ $user->last_name }}, {{ $user->first_name }} ({{ ucfirst($user->role) }})
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="filter-group">
                <label class="filter-label">Action:</label>
                <select name="action" class="filter-select">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ $selectedAction == $action ? 'selected' : '' }}>
                            {{ $action }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Date From:</label>
                <input type="date" name="date_from" class="filter-input" value="{{ $dateFrom }}">
            </div>

            <div class="filter-group">
                <label class="filter-label">Date To:</label>
                <input type="date" name="date_to" class="filter-input" value="{{ $dateTo }}">
            </div>

            <div class="filter-group">
                <button type="submit" class="filter-btn">Apply Filters</button>
                <a href="{{ route('activity-logs') }}" class="filter-btn filter-btn-clear" style="text-decoration: none; display: inline-block;">Clear</a>
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="logs-table-container">
        @if($logs->count() > 0)
            <table class="logs-table">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Caretaker</th>
                        <th>Action</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr @if($log->resource_url) class="clickable" onclick="window.location.href='{{ $log->resource_url }}'" @endif>
                            <td>
                                <div>{{ $log->created_at->format('M d, Y') }}</div>
                                <div style="font-size: 0.75rem; color: #718096;">{{ $log->created_at->format('h:i A') }}</div>
                            </td>
                            <td>
                                <strong>{{ $log->caretaker_name }}</strong>
                            </td>
                            <td>
                                @php
                                    $actionClass = 'created';
                                    $actionLower = strtolower($log->action);
                                    if (str_contains($actionLower, 'update')) {
                                        $actionClass = 'updated';
                                    } elseif (str_contains($actionLower, 'delete') || str_contains($actionLower, 'cancel') || str_contains($actionLower, 'archive')) {
                                        $actionClass = 'deleted';
                                    } elseif (str_contains($actionLower, 'check')) {
                                        $actionClass = 'checked';
                                    } elseif (str_contains($actionLower, 'payment') || str_contains($actionLower, 'generate')) {
                                        $actionClass = 'payment';
                                    }
                                @endphp
                                <span class="action-badge {{ $actionClass }}">{{ $log->action }}</span>
                            </td>
                            <td>
                                <div class="description-text">{{ $log->description }}</div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                <div class="pagination-left">
                    <form method="GET" action="{{ route('activity-logs') }}" class="d-flex align-items-center gap-2">
                        <input type="hidden" name="user_id" value="{{ $selectedUserId }}">
                        <input type="hidden" name="action" value="{{ $selectedAction }}">
                        <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                        <input type="hidden" name="date_to" value="{{ $dateTo }}">
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
                        <span class="fw-semibold">{{ $logs->firstItem() ?? 0 }}</span>
                        to
                        <span class="fw-semibold">{{ $logs->lastItem() ?? 0 }}</span>
                        of
                        <span class="fw-semibold">{{ $logs->total() }}</span>
                        results
                    </p>
                </div>
                <div class="pagination-right">
                    {{ $logs->appends(['user_id' => $selectedUserId, 'action' => $selectedAction, 'date_from' => $dateFrom, 'date_to' => $dateTo, 'per_page' => $perPage])->links() }}
                </div>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-inbox"></i>
                </div>
                <h3>No activity logs found</h3>
                <p>There are no activity logs matching your filters.</p>
            </div>
        @endif
    </div>
</div>
@endsection


