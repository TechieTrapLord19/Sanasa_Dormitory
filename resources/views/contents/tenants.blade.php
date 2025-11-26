@extends('layouts.app')

@section('title', 'Tenants Management')

@section('content')
<style>
    .tenants-header {
        background-color: white;
        margin-bottom: 2rem;
    }
    .tenants-title {
        font-size: 2rem;
        font-weight: 700;
        color: #03255b;
        margin: 0;
    }
    .create-tenant-btn {
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
    .create-tenant-btn:hover {
        background-color: #021d47;
        color: white;
    }
    .create-tenant-btn-icon {
        width: 24px;
        height: 24px;
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    .modal-footer .btn-primary:hover {
        background-color: #021d47 !important;
        border-color: #021d47 !important;
    }

    /* Filter Styles */
    .tenants-filters {
        background-color: white;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .filter-label {
        font-weight: 600;
        color: #2d3748;
        font-size: 0.875rem;
        margin: 0;
    }

    .filter-input {
        padding: 0.5rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.875rem;
        background-color: white;
        color: #4a5568;
        min-width: 250px;
    }

    .filter-input:focus {
        outline: none;
        border-color: #03255b;
        box-shadow: 0 0 0 3px rgba(3, 37, 91, 0.1);
    }

    .filter-btn {
        padding: 0.5rem 1rem;
        border: 1px solid #e2e8f0;
        background-color: white;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        color: #4a5568;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .filter-btn:hover {
        background-color: #f7fafc;
        border-color: #cbd5e0;
    }

    .filter-btn.active {
        background-color: #03255b;
        color: white;
        border-color: #03255b;
    }

    /* Table Styles */
    .tenants-table-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .tenants-table {
        width: 100%;
        border-collapse: collapse;
    }

    .tenants-table thead {
        background-color: #f7fafc;
    }

    .tenants-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #2d3748;
        font-size: 0.875rem;
    }

    .tenants-table td {
        padding: 1rem;
        color: #4a5568;
        font-size: 0.875rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .tenants-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .tenants-table tbody tr:last-child td {
        border-bottom: none;
    }

    .tenants-table thead th {
        padding: 1rem;
        font-weight: 600;
        background-color: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }

    /* Age column - center */
    .tenants-table th:nth-child(4),
    .tenants-table td:nth-child(4) {
        text-align: center;
    }

    /* Status column - center */
    .tenants-table th:nth-child(5),
    .tenants-table td:nth-child(5) {
        text-align: center;
    }

    /* Actions column - center and fit content */
    .tenants-table th:nth-child(6),
    .tenants-table td:nth-child(6) {
        text-align: center;
        width: 1%;
        white-space: nowrap;
        padding: 1rem 0.75rem;
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

    .status-badge.active {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-badge.inactive {
        background-color: #e5e7eb;
        color: #4b5563;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
        align-items: center;
    }

    .btn-view, .btn-edit, .btn-archive, .btn-activate {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-view i, .btn-edit i, .btn-archive i, .btn-activate i,
    .action-buttons button i, .action-buttons a i {
        font-size: 1rem;
    }

    .btn-view {
        background-color: #e0f2fe;
        color: #0369a1;
    }

    .btn-view:hover {
        background-color: #bae6fd;
        color: #0369a1;
    }

    .btn-edit {
        background-color: #e0f2fe;
        color: #0369a1;
    }

    .btn-edit:hover {
        background-color: #bae6fd;
    }

    .btn-archive {
        background-color: #fef3c7;
        color: #92400e;
    }

    .btn-archive:hover {
        background-color: #fde68a;
    }

    .btn-activate {
        background-color: #d1fae5;
        color: #065f46;
    }

    .btn-activate:hover {
        background-color: #a7f3d0;
    }

    /* .contact-info-label {
        font-size: 0.75rem;
        color: #718096;
        margin-bottom: 0.5rem;
        font-weight: 600;
    } */

    /* Pagination Styles */
    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.25rem;
        background-color: #f8fafc;
        flex-wrap: wrap;
        gap: 1rem;
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
        font-size: 0.875rem;
        min-width: 38px;
        text-align: center;
        display: inline-block;
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
    .pagination-wrapper svg {
        display: none !important;
    }
    .pagination-wrapper nav > div:first-child {
        display: none !important;
    }
    .pagination-wrapper nav > div:last-child > div:first-child {
        display: none !important;
    }
    .pagination-wrapper nav > div:last-child > div:last-child {
        display: block !important;
    }
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

<div class="tenants-header">
    <div class="row align-items-center">
        <!-- Left: Title -->
        <div class="col-md-8 d-flex justify-content-start">
            <h1 class="tenants-title">Tenants Management</h1>
        </div>

        <!-- Right: Create Button -->
        <div class="col-md-4 d-flex justify-content-end">
            <button class="create-tenant-btn" data-bs-toggle="modal" data-bs-target="#createTenantModal">
                <i class="bi bi-plus-circle"></i>
                <span>Add New Tenant</span>
            </button>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="tenants-filters">
    <div class="filter-group">
        <p class="filter-label mb-0">Search Tenant:</p>
        <form method="GET" action="{{ route('tenants') }}" class="d-flex gap-2 flex-grow-1">
            <input type="text"
                   class="filter-input"
                   name="search"
                   id="searchInput"
                   placeholder="Search by name or contact..."
                   value="{{ request('search') }}">
            <input type="hidden" name="status" id="statusInput" value="{{ request('status', 'all') }}">
        </form>
    </div>
    <div class="filter-group mt-3">
        <p class="filter-label mb-0">Filter by Status:</p>
        <button class="filter-btn {{ request('status', 'all') === 'all' ? 'active' : '' }}"
                data-status="all"
                onclick="filterByStatus('all')">
            All ({{ $statusCounts['total'] ?? 0 }})
        </button>
        <button class="filter-btn {{ request('status') === 'active' ? 'active' : '' }}"
                data-status="active"
                onclick="filterByStatus('active')">
            Active ({{ $statusCounts['active'] ?? 0 }})
        </button>
        <button class="filter-btn {{ request('status') === 'inactive' ? 'active' : '' }}"
                data-status="inactive"
                onclick="filterByStatus('inactive')">
            Inactive ({{ $statusCounts['inactive'] ?? 0 }})
        </button>
    </div>
</div>

<!-- Tenants Table -->
<div class="tenants-table-container">
        {{-- <div class="contact-info-label px-3 pt-3 mb-0">Contact Info (Phone/Email)</div> --}}
    <table class="tenants-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Contact Number</th>
                <th>Emergency Contact</th>
                <th>Age</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tenants as $tenant)
                <tr>
                    <td>
                        <strong>{{ $tenant->full_name }}</strong>
                    </td>
                    <td>
                        {{ $tenant->contact_num ?? 'N/A' }}
                    </td>
                    <td>
                        {{ $tenant->emer_contact_num ?? 'N/A' }}
                    </td>
                    <td>
                        {{ $tenant->age ?? 'N/A' }}
                    </td>
                    <td>
                        <span class="status-badge {{ $tenant->status }}">
                            {{ ucfirst($tenant->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('tenants.show', $tenant->tenant_id) }}" class="btn-view">
                                <i class="bi bi-eye"></i> View
                            </a>
                            @if($tenant->status === 'active')
                                <form action="{{ route('tenants.archive', $tenant->tenant_id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn-archive" onclick="return confirm('Are you sure you want to archive this tenant?')">
                                        <i class="bi bi-archive"></i> Archive
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('tenants.activate', $tenant->tenant_id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn-activate">
                                        <i class="bi bi-check-circle"></i> Activate
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">No tenants found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination-wrapper">
        <div class="pagination-left">
            <form method="GET" action="{{ route('tenants') }}" class="d-flex align-items-center gap-2">
                <input type="hidden" name="search" value="{{ $searchTerm }}">
                <input type="hidden" name="status" value="{{ $activeStatus }}">
                <label for="perPage" class="text-muted small mb-0">Rows per page</label>
                <select class="form-select form-select-sm" id="perPage" name="per_page" onchange="this.form.submit()">
                    @foreach([10, 25, 50] as $option)
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
                <span class="fw-semibold">{{ $tenants->firstItem() ?? 0 }}</span>
                to
                <span class="fw-semibold">{{ $tenants->lastItem() ?? 0 }}</span>
                of
                <span class="fw-semibold">{{ $tenants->total() }}</span>
                results
            </p>
        </div>
        <div class="pagination-right">
            {{ $tenants->appends(['status' => $activeStatus, 'search' => $searchTerm, 'per_page' => $perPage])->links() }}
        </div>
    </div>
</div>

<!-- Create Tenant Modal -->
<div class="modal fade" id="createTenantModal" tabindex="-1" aria-labelledby="createTenantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createTenantModalLabel">Add New Tenant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('tenants.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                   id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="middle_name" class="form-label">Middle Name</label>
                            <input type="text" class="form-control @error('middle_name') is-invalid @enderror"
                                   id="middle_name" name="middle_name" value="{{ old('middle_name') }}">
                            @error('middle_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                   id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="contact_num" class="form-label">Contact Number</label>
                            <input type="text" class="form-control @error('contact_num') is-invalid @enderror"
                                   id="contact_num" name="contact_num" value="{{ old('contact_num') }}"
                                   placeholder="0912-345-6789">
                            @error('contact_num')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="emer_contact_num" class="form-label">Emergency Contact Number</label>
                            <input type="text" class="form-control @error('emer_contact_num') is-invalid @enderror"
                                   id="emer_contact_num" name="emer_contact_num" value="{{ old('emer_contact_num') }}"
                                   placeholder="0917-969-4567">
                            @error('emer_contact_num')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="birth_date" class="form-label">Birth Date</label>
                            <input type="date" class="form-control @error('birth_date') is-invalid @enderror"
                                   id="birth_date" name="birth_date" value="{{ old('birth_date') }}">
                            @error('birth_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror"
                                  id="address" name="address" rows="2">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_document" class="form-label">ID Document</label>
                            <input type="text" class="form-control @error('id_document') is-invalid @enderror"
                                   id="id_document" name="id_document" value="{{ old('id_document') }}"
                                   placeholder="e.g., Driver's License, Passport">
                            @error('id_document')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror"
                                    id="status" name="status" required>
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" style="background-color: #03255b; border-color: #03255b;">Add Tenant</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function filterByStatus(status) {
    const statusInput = document.getElementById('statusInput');
    const form = statusInput.closest('form');
    statusInput.value = status;

    // Update active button
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.getAttribute('data-status') === status) {
            btn.classList.add('active');
        }
    });

    form.submit();
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    let searchTimeout;

    // Auto-submit search after user stops typing (debounce)
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            if (searchInput.value.length > 0 || searchInput.value.length === 0) {
                searchInput.closest('form').submit();
            }
        }, 500);
    });

    // Submit on Enter key
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchInput.closest('form').submit();
        }
    });
});

function editTenant(tenantId) {
    // TODO: Implement edit functionality
    alert('Edit tenant ' + tenantId);
}

function deleteTenant(tenantId) {
    if (confirm('Are you sure you want to delete this tenant? This action cannot be undone.')) {
        // TODO: Implement delete functionality
        alert('Delete tenant ' + tenantId);
    }
}
</script>
@endsection
