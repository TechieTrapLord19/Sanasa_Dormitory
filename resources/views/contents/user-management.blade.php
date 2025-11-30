@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<style>
    .users-header {
        background-color: white;
        margin-bottom: 2rem;
    }
    .users-title {
        font-size: 2rem;
        font-weight: 700;
        color: #03255b;
        margin: 0;
    }
    .create-user-btn {
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
    .create-user-btn:hover {
        background-color: #021d47;
        color: white;
    }
    .create-user-btn-icon {
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
    .users-filters {
        background-color: white;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
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
        border: 1px solid #cbd5e1;
        padding: 0.45rem 1.1rem;
        border-radius: 999px;
        background-color: white;
        font-size: 0.85rem;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .filter-btn:hover {
        border-color: #94a3b8;
        color: #0f172a;
    }

    .filter-btn.active {
        background: #03255b;
        color: white;
        border-color: #03255b;
        box-shadow: 0 8px 20px rgba(3, 37, 91, 0.25);
    }

    /* Table Styles */
    .users-table-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        overflow: hidden;
    }

    .users-table {
        width: 100%;
        border-collapse: collapse;
    }

    .users-table thead {
        background-color: #f7fafc;
    }

    .users-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #2d3748;
        font-size: 0.875rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .users-table td {
        padding: 1rem;
        color: #4a5568;
        font-size: 0.875rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .users-table tbody tr:hover {
        background-color: #f7fafc;
    }

    .users-table tbody tr:last-child td {
        border-bottom: none;
    }

    .role-badge {
        display: inline-block;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .role-badge.owner {
        background-color: #e9d5ff;
        color: #6b21a8;
    }

    .role-badge.caretaker {
        background-color: #dbeafe;
        color: #1e40af;
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

    .status-badge.archived {
        background-color: #e5e7eb;
        color: #6b7280;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .btn-edit, .btn-archive, .btn-activate {
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

    .btn-edit i, .btn-archive i, .btn-activate i {
        font-size: 1rem;
    }

    .btn-edit {
        background-color: #e0f2fe;
        color: #0369a1;
    }

    .btn-edit:hover {
        background-color: #bae6fd;
        color: #0369a1;
    }

    .btn-archive {
        background-color: #fef3c7;
        color: #92400e;
    }

    .btn-archive:hover {
        background-color: #fde68a;
        color: #92400e;
    }

    .btn-activate {
        background-color: #d1fae5;
        color: #065f46;
    }

    .btn-activate:hover {
        background-color: #a7f3d0;
        color: #065f46;
    }

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

<div class="users-header">
    <div class="row align-items-center">
        <!-- Left: Title -->
        <div class="col-md-8 d-flex justify-content-start">
            <h1 class="users-title">User Management</h1>
        </div>

        <!-- Right: Create Button -->
        <div class="col-md-4 d-flex justify-content-end">
            <button class="create-user-btn" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="bi bi-plus-circle"></i>
                <span>Create Caretaker</span>
            </button>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="users-filters">
    <div class="filter-group">
        <p class="filter-label mb-0">Search User:</p>
        <form method="GET" action="{{ route('user-management') }}" class="d-flex gap-2 flex-grow-1">
            <input type="text"
                   class="filter-input"
                   name="search"
                   id="searchInput"
                   placeholder="Search by name or email..."
                   value="{{ $searchTerm }}">
            <input type="hidden" name="role" id="roleInput" value="{{ $selectedRole }}">
            <input type="hidden" name="status" id="statusInput" value="{{ $selectedStatus ?? 'all' }}">
        </form>
    </div>
    <div class="filter-group mt-3">
        <p class="filter-label mb-0">Filter by Role:</p>
        <button class="filter-btn {{ $selectedRole === 'all' ? 'active' : '' }}"
                data-role="all"
                onclick="filterByRole('all')">
            All ({{ $roleCounts['total'] ?? 0 }})
        </button>
        <button class="filter-btn {{ $selectedRole === 'owner' ? 'active' : '' }}"
                data-role="owner"
                onclick="filterByRole('owner')">
            Admin ({{ $roleCounts['owner'] ?? 0 }})
        </button>
        <button class="filter-btn {{ $selectedRole === 'caretaker' ? 'active' : '' }}"
                data-role="caretaker"
                onclick="filterByRole('caretaker')">
            Caretaker ({{ $roleCounts['caretaker'] ?? 0 }})
        </button>
    </div>
    <div class="filter-group mt-3">
        <p class="filter-label mb-0">Filter by Status:</p>
        <button class="filter-btn {{ ($selectedStatus ?? 'all') === 'all' ? 'active' : '' }}"
                data-status="all"
                onclick="filterByStatus('all')">
            All
        </button>
        <button class="filter-btn {{ ($selectedStatus ?? 'all') === 'active' ? 'active' : '' }}"
                data-status="active"
                onclick="filterByStatus('active')">
            Active ({{ $statusCounts['active'] ?? 0 }})
        </button>
        <button class="filter-btn {{ ($selectedStatus ?? 'all') === 'archived' ? 'active' : '' }}"
                data-status="archived"
                onclick="filterByStatus('archived')">
            Archived ({{ $statusCounts['archived'] ?? 0 }})
        </button>
    </div>
</div>

<!-- Users Table -->
<div class="users-table-container">
    <table class="users-table">
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Age</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>
                        <strong>{{ $user->full_name }}</strong>
                    </td>
                    <td>
                        {{ $user->email }}
                    </td>
                    <td>
                        <span class="role-badge {{ strtolower($user->role) }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td>
                        <span class="status-badge {{ $user->status ?? 'active' }}">
                            {{ ucfirst($user->status ?? 'active') }}
                        </span>
                    </td>
                    <td>
                        {{ $user->age ? $user->age . ' years old' : 'N/A' }}
                    </td>
                    <td>
                        {{ $user->address ?? 'N/A' }}
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button type="button" class="btn-edit" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->user_id }}">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            @if($user->user_id !== auth()->user()->user_id)
                                @if($user->status === 'active')
                                    <form action="{{ route('users.archive', $user->user_id) }}" method="POST" style="display: inline;" id="archiveUserForm{{ $user->user_id }}">
                                        @csrf
                                        <button type="button" class="btn-archive" onclick="confirmAction('Are you sure you want to archive this user?', function() { document.getElementById('archiveUserForm{{ $user->user_id }}').submit(); }, { title: 'Archive User', confirmText: 'Yes, Archive', type: 'warning' })">
                                            <i class="bi bi-archive"></i> Archive
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('users.activate', $user->user_id) }}" method="POST" style="display: inline;" id="activateUserForm{{ $user->user_id }}">
                                        @csrf
                                        <button type="button" class="btn-activate" onclick="confirmAction('Are you sure you want to activate this user?', function() { document.getElementById('activateUserForm{{ $user->user_id }}').submit(); }, { title: 'Activate User', confirmText: 'Yes, Activate', type: 'info' })">
                                            <i class="bi bi-check-circle"></i> Activate
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No users found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination-wrapper">
        <div class="pagination-left">
            <form method="GET" action="{{ route('user-management') }}" class="d-flex align-items-center gap-2">
                <input type="hidden" name="search" value="{{ $searchTerm }}">
                <input type="hidden" name="role" value="{{ $selectedRole }}">
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
                <span class="fw-semibold">{{ $users->firstItem() ?? 0 }}</span>
                to
                <span class="fw-semibold">{{ $users->lastItem() ?? 0 }}</span>
                of
                <span class="fw-semibold">{{ $users->total() }}</span>
                results
            </p>
        </div>
        <div class="pagination-right">
            {{ $users->appends(['role' => $selectedRole, 'search' => $searchTerm, 'per_page' => $perPage])->links() }}
        </div>
    </div>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createUserModalLabel">Create Caretaker</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
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
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
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
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror"
                                      id="address" name="address" rows="2">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                <option value="caretaker" {{ old('role', 'caretaker') === 'caretaker' ? 'selected' : '' }}>Caretaker</option>
                                <option value="owner" {{ old('role') === 'owner' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                   id="password_confirmation" name="password_confirmation" required>
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" style="background-color: #03255b; border-color: #03255b;">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modals -->
@foreach($users as $user)
<div class="modal fade" id="editUserModal{{ $user->user_id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $user->user_id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel{{ $user->user_id }}">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('users.update', $user->user_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    @if($errors->any() && old('_method') === 'PUT' && old('user_id') == $user->user_id)
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="first_name_{{ $user->user_id }}" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                   id="first_name_{{ $user->user_id }}" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="middle_name_{{ $user->user_id }}" class="form-label">Middle Name</label>
                            <input type="text" class="form-control @error('middle_name') is-invalid @enderror"
                                   id="middle_name_{{ $user->user_id }}" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}">
                            @error('middle_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="last_name_{{ $user->user_id }}" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                   id="last_name_{{ $user->user_id }}" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email_{{ $user->user_id }}" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email_{{ $user->user_id }}" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="birth_date_{{ $user->user_id }}" class="form-label">Birth Date</label>
                            <input type="date" class="form-control @error('birth_date') is-invalid @enderror"
                                   id="birth_date_{{ $user->user_id }}" name="birth_date" value="{{ old('birth_date', $user->birth_date ? $user->birth_date->format('Y-m-d') : '') }}">
                            @error('birth_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="address_{{ $user->user_id }}" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror"
                                      id="address_{{ $user->user_id }}" name="address" rows="2">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role_{{ $user->user_id }}" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role_{{ $user->user_id }}" name="role" required>
                                <option value="caretaker" {{ old('role', $user->role) === 'caretaker' ? 'selected' : '' }}>Caretaker</option>
                                <option value="owner" {{ old('role', $user->role) === 'owner' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password_{{ $user->user_id }}" class="form-label">New Password <small class="text-muted">(leave blank to keep current)</small></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password_{{ $user->user_id }}" name="password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation_{{ $user->user_id }}" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                   id="password_confirmation_{{ $user->user_id }}" name="password_confirmation">
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script>
function filterByRole(role) {
    document.getElementById('roleInput').value = role;
    document.querySelector('form[action="{{ route('user-management') }}"]').submit();
}

function filterByStatus(status) {
    document.getElementById('statusInput').value = status;
    document.querySelector('form[action="{{ route('user-management') }}"]').submit();
}
</script>
@endsection

