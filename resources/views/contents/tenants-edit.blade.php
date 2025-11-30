@extends('layouts.app')

@section('title', 'Edit Tenant')

@section('content')
<style>
    .tenant-edit-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        padding: 1.5rem;
        max-width: 900px;
        margin: 0 auto;
    }

    .edit-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .edit-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #03255b;
        margin: 0;
    }

    .btn-back {
        background-color: #e0f2fe;
        color: #0369a1;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 500;
        display: inline-block;
    }

    .btn-back:hover {
        background-color: #bae6fd;
        color: #0369a1;
    }
</style>

<div class="tenant-edit-container">
    <div class="edit-header">
        <h1 class="edit-title">Edit Tenant</h1>
        <a href="{{ route('tenants.show', $tenant->tenant_id) }}" class="btn-back">Back to Details</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <h5 class="alert-heading">Please fix the following errors:</h5>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('tenants.update', $tenant->tenant_id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                       id="first_name" name="first_name" value="{{ old('first_name', $tenant->first_name) }}" required>
                @error('first_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="middle_name" class="form-label">Middle Name</label>
                <input type="text" class="form-control @error('middle_name') is-invalid @enderror"
                       id="middle_name" name="middle_name" value="{{ old('middle_name', $tenant->middle_name) }}">
                @error('middle_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                       id="last_name" name="last_name" value="{{ old('last_name', $tenant->last_name) }}" required>
                @error('last_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                       id="email" name="email" value="{{ old('email', $tenant->email) }}">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="contact_num" class="form-label">Contact Number</label>
                <input type="text" class="form-control @error('contact_num') is-invalid @enderror"
                       id="contact_num" name="contact_num" value="{{ old('contact_num', $tenant->contact_num) }}">
                @error('contact_num')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="emer_contact_num" class="form-label">Emergency Contact Number</label>
                <input type="text" class="form-control @error('emer_contact_num') is-invalid @enderror"
                       id="emer_contact_num" name="emer_contact_num" value="{{ old('emer_contact_num', $tenant->emer_contact_num) }}">
                @error('emer_contact_num')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="birth_date" class="form-label">Birth Date</label>
                <input type="date" class="form-control @error('birth_date') is-invalid @enderror"
                       id="birth_date" name="birth_date" value="{{ old('birth_date', $tenant->birth_date ? $tenant->birth_date->format('Y-m-d') : '') }}">
                @error('birth_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea class="form-control @error('address') is-invalid @enderror"
                      id="address" name="address" rows="2">{{ old('address', $tenant->address) }}</textarea>
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="id_document" class="form-label">ID Document</label>
                <input type="text" class="form-control @error('id_document') is-invalid @enderror"
                       id="id_document" name="id_document" value="{{ old('id_document', $tenant->id_document) }}">
                @error('id_document')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                @php
                    $hasActiveBooking = $tenant->bookings()->where('status', 'Active')->exists();
                @endphp
                <select class="form-select @error('status') is-invalid @enderror"
                        id="status" name="status" required
                        @if($hasActiveBooking && $tenant->status === 'active') disabled @endif>
                    <option value="active" {{ old('status', $tenant->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $tenant->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @if($hasActiveBooking && $tenant->status === 'active')
                    <input type="hidden" name="status" value="active">
                    <small class="text-warning d-block mt-1">
                        <i class="bi bi-exclamation-triangle"></i> Cannot change status while tenant has an active booking
                    </small>
                @endif
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('tenants.show', $tenant->tenant_id) }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Tenant</button>
        </div>
    </form>
</div>
@endsection


