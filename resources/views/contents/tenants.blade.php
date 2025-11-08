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
        border-bottom: 2px solid #e2e8f0;
    }

    .tenants-table td {
        padding: 1rem;
        color: #4a5568;
        font-size: 0.875rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .tenants-table tbody tr:hover {
        background-color: #f7fafc;
    }

    .tenants-table tbody tr:last-child td {
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
    }

    .btn-edit, .btn-delete {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-edit {
        background-color: #e0f2fe;
        color: #0369a1;
    }

    .btn-edit:hover {
        background-color: #bae6fd;
    }

    .btn-delete {
        background-color: #fee2e2;
        color: #dc2626;
    }

    .btn-delete:hover {
        background-color: #fecaca;
    }

    .contact-info-label {
        font-size: 0.75rem;
        color: #718096;
        margin-bottom: 0.5rem;
        font-weight: 600;
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
                <span class="create-tenant-btn-icon">+</span>
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
    <div class="contact-info-label px-3 pt-3 mb-0">Contact Info (Phone/Email)</div>
    <table class="tenants-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Contact Info</th>
                <th>Emergency Contact</th>
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
                        {{ $tenant->contact_info }}
                    </td>
                    <td>
                        {{ $tenant->emer_contact_num ?? 'N/A' }}
                    </td>
                    <td>
                        <span class="status-badge {{ $tenant->status }}">
                            {{ ucfirst($tenant->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-edit" onclick="editTenant({{ $tenant->tenant_id }})">Edit</button>
                            <button class="btn-delete" onclick="deleteTenant({{ $tenant->tenant_id }})">Delete</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">No tenants found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
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
                    <button type="submit" class="btn btn-primary">Add Tenant</button>
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
