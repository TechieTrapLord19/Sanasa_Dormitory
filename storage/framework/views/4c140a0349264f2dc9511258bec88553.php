<?php $__env->startSection('title', 'Tenants Management'); ?>

<?php $__env->startSection('content'); ?>
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
    .tenants-table-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
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
    .tenants-table th.sortable {
        cursor: pointer;
        user-select: none;
        transition: all 0.2s ease;
    }
    .tenants-table th.sortable:hover {
        background: #e2e8f0;
        color: #03255b;
    }
    .tenants-table th.sortable .sort-icon {
        margin-left: 0.3rem;
        font-size: 0.7rem;
        opacity: 0.4;
    }
    .tenants-table th.sortable.active .sort-icon {
        opacity: 1;
        color: #03255b;
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

    /* Room column - center */
    .tenants-table th:nth-child(2),
    .tenants-table td:nth-child(2) {
        text-align: center;
    }

    /* Age column - center */
    .tenants-table th:nth-child(5),
    .tenants-table td:nth-child(5) {
        text-align: center;
    }

    /* Status column - center */
    .tenants-table th:nth-child(6),
    .tenants-table td:nth-child(6) {
        text-align: center;
    }

    /* Actions column - center and fit content */
    .tenants-table th:nth-child(7),
    .tenants-table td:nth-child(7) {
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
        <p class="filter-label mb-0">Search:</p>
        <form method="GET" action="<?php echo e(route('tenants')); ?>" class="d-flex gap-2 flex-grow-1">
            <input type="text"
                   class="filter-input"
                   name="search"
                   id="searchInput"
                   placeholder="Search by name or contact..."
                   value="<?php echo e(request('search')); ?>">
            <input type="hidden" name="status" id="statusInput" value="<?php echo e(request('status', 'all')); ?>">
        </form>
    </div>
    <div class="filter-group mt-3">
        <p class="filter-label mb-0">Filter by Status:</p>
        <button class="filter-btn <?php echo e(request('status', 'all') === 'all' ? 'active' : ''); ?>"
                data-status="all"
                onclick="filterByStatus('all')">
            All (<?php echo e($statusCounts['total'] ?? 0); ?>)
        </button>
        <button class="filter-btn <?php echo e(request('status') === 'active' ? 'active' : ''); ?>"
                data-status="active"
                onclick="filterByStatus('active')">
            Active (<?php echo e($statusCounts['active'] ?? 0); ?>)
        </button>
        <button class="filter-btn <?php echo e(request('status') === 'inactive' ? 'active' : ''); ?>"
                data-status="inactive"
                onclick="filterByStatus('inactive')">
            Inactive (<?php echo e($statusCounts['inactive'] ?? 0); ?>)
        </button>
    </div>
</div>

<!-- Tenants Table -->
<div class="tenants-table-container">
        
    <table class="tenants-table">
        <thead>
            <tr>
                <th class="sortable <?php echo e($sortBy === 'last_name' ? 'active' : ''); ?>" onclick="sortTable('last_name')">
                    Name
                    <?php if($sortBy === 'last_name'): ?>
                        <i class="bi bi-<?php echo e($sortDir === 'asc' ? 'sort-up' : 'sort-down'); ?> sort-icon"></i>
                    <?php else: ?>
                        <i class="bi bi-arrow-down-up sort-icon"></i>
                    <?php endif; ?>
                </th>
                <th>Room</th>
                <th class="sortable <?php echo e($sortBy === 'contact_num' ? 'active' : ''); ?>" onclick="sortTable('contact_num')">
                    Contact Number
                    <?php if($sortBy === 'contact_num'): ?>
                        <i class="bi bi-<?php echo e($sortDir === 'asc' ? 'sort-up' : 'sort-down'); ?> sort-icon"></i>
                    <?php else: ?>
                        <i class="bi bi-arrow-down-up sort-icon"></i>
                    <?php endif; ?>
                </th>
                <th class="sortable <?php echo e($sortBy === 'emer_contact_num' ? 'active' : ''); ?>" onclick="sortTable('emer_contact_num')">
                    Emergency Contact
                    <?php if($sortBy === 'emer_contact_num'): ?>
                        <i class="bi bi-<?php echo e($sortDir === 'asc' ? 'sort-up' : 'sort-down'); ?> sort-icon"></i>
                    <?php else: ?>
                        <i class="bi bi-arrow-down-up sort-icon"></i>
                    <?php endif; ?>
                </th>
                <th class="sortable <?php echo e($sortBy === 'birth_date' ? 'active' : ''); ?>" onclick="sortTable('birth_date')">
                    Age
                    <?php if($sortBy === 'birth_date'): ?>
                        <i class="bi bi-<?php echo e($sortDir === 'asc' ? 'sort-up' : 'sort-down'); ?> sort-icon"></i>
                    <?php else: ?>
                        <i class="bi bi-arrow-down-up sort-icon"></i>
                    <?php endif; ?>
                </th>
                <th class="sortable <?php echo e($sortBy === 'status' ? 'active' : ''); ?>" onclick="sortTable('status')">
                    Status
                    <?php if($sortBy === 'status'): ?>
                        <i class="bi bi-<?php echo e($sortDir === 'asc' ? 'sort-up' : 'sort-down'); ?> sort-icon"></i>
                    <?php else: ?>
                        <i class="bi bi-arrow-down-up sort-icon"></i>
                    <?php endif; ?>
                </th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $tenants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tenant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <strong><?php echo e($tenant->full_name); ?></strong>
                    </td>
                    <td>
                        <?php echo e($tenant->currentBooking && $tenant->currentBooking->room ? $tenant->currentBooking->room->room_num : 'N/A'); ?>

                    </td>
                    <td>
                        <?php echo e($tenant->contact_num ?? 'N/A'); ?>

                    </td>
                    <td>
                        <?php echo e($tenant->emer_contact_num ?? 'N/A'); ?>

                    </td>
                    <td>
                        <?php echo e($tenant->age ?? 'N/A'); ?>

                    </td>
                    <td>
                        <span class="status-badge <?php echo e($tenant->status); ?>">
                            <?php echo e(ucfirst($tenant->status)); ?>

                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="<?php echo e(route('tenants.show', $tenant->tenant_id)); ?>" class="btn-view">
                                <i class="bi bi-eye"></i> View
                            </a>
                            <?php if($tenant->status === 'active'): ?>
                                <form action="<?php echo e(route('tenants.archive', $tenant->tenant_id)); ?>" method="POST" style="display: inline;" id="archiveTenantForm<?php echo e($tenant->tenant_id); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="button" class="btn-archive" onclick="confirmAction('Are you sure you want to archive this tenant?', function() { document.getElementById('archiveTenantForm<?php echo e($tenant->tenant_id); ?>').submit(); }, { title: 'Archive Tenant', confirmText: 'Yes, Archive', type: 'warning' })">
                                        <i class="bi bi-archive"></i> Archive
                                    </button>
                                </form>
                            <?php else: ?>
                                <form action="<?php echo e(route('tenants.activate', $tenant->tenant_id)); ?>" method="POST" style="display: inline;" id="activateTenantForm<?php echo e($tenant->tenant_id); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="button" class="btn-activate" onclick="confirmAction('Are you sure you want to activate this tenant?', function() { document.getElementById('activateTenantForm<?php echo e($tenant->tenant_id); ?>').submit(); }, { title: 'Activate Tenant', confirmText: 'Yes, Activate', type: 'info' })">
                                        <i class="bi bi-check-circle"></i> Activate
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No tenants found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="pagination-wrapper">
        <div class="pagination-left">
            <form method="GET" action="<?php echo e(route('tenants')); ?>" class="d-flex align-items-center gap-2">
                <input type="hidden" name="search" value="<?php echo e($searchTerm); ?>">
                <input type="hidden" name="status" value="<?php echo e($activeStatus); ?>">
                <label for="perPage" class="text-muted small mb-0">Rows per page</label>
                <select class="form-select form-select-sm" id="perPage" name="per_page" onchange="this.form.submit()">
                    <?php $__currentLoopData = [5, 10, 15, 20]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($option); ?>" <?php echo e((int) $perPage === $option ? 'selected' : ''); ?>>
                            <?php echo e($option); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </form>
        </div>
        <div class="pagination-center">
            <p class="small text-muted mb-0">
                Showing
                <span class="fw-semibold"><?php echo e($tenants->firstItem() ?? 0); ?></span>
                to
                <span class="fw-semibold"><?php echo e($tenants->lastItem() ?? 0); ?></span>
                of
                <span class="fw-semibold"><?php echo e($tenants->total()); ?></span>
                results
            </p>
        </div>
        <div class="pagination-right">
            <?php echo e($tenants->appends(['status' => $activeStatus, 'search' => $searchTerm, 'per_page' => $perPage, 'sort_by' => $sortBy, 'sort_dir' => $sortDir])->links()); ?>

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
            <form action="<?php echo e(route('tenants.store')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   id="first_name" name="first_name" value="<?php echo e(old('first_name')); ?>" required>
                            <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="middle_name" class="form-label">Middle Name</label>
                            <input type="text" class="form-control <?php $__errorArgs = ['middle_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   id="middle_name" name="middle_name" value="<?php echo e(old('middle_name')); ?>">
                            <?php $__errorArgs = ['middle_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   id="last_name" name="last_name" value="<?php echo e(old('last_name')); ?>" required>
                            <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   id="email" name="email" value="<?php echo e(old('email')); ?>">
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="contact_num" class="form-label">Contact Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php $__errorArgs = ['contact_num'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   id="contact_num" name="contact_num" value="<?php echo e(old('contact_num')); ?>"
                                   placeholder="09123456789" maxlength="11" minlength="11" pattern="[0-9]{11}" required>
                            <?php $__errorArgs = ['contact_num'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="emer_contact_num" class="form-label">Emergency Contact Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php $__errorArgs = ['emer_contact_num'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   id="emer_contact_num" name="emer_contact_num" value="<?php echo e(old('emer_contact_num')); ?>"
                                   placeholder="09179694567" maxlength="11" minlength="11" pattern="[0-9]{11}" required>
                            <?php $__errorArgs = ['emer_contact_num'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="emer_contact_name" class="form-label">Emergency Contact Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php $__errorArgs = ['emer_contact_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   id="emer_contact_name" name="emer_contact_name" value="<?php echo e(old('emer_contact_name')); ?>"
                                   placeholder="e.g., Parent, Spouse, Sibling" required>
                            <?php $__errorArgs = ['emer_contact_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="birth_date" class="form-label">Birth Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control <?php $__errorArgs = ['birth_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   id="birth_date" name="birth_date" value="<?php echo e(old('birth_date')); ?>"
                                   max="<?php echo e(now()->subYears(12)->format('Y-m-d')); ?>" required>
                            <?php $__errorArgs = ['birth_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                      id="address" name="address" rows="2"><?php echo e(old('address')); ?></textarea>
                            <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_document" class="form-label">ID Document <span class="text-danger">*</span></label>
                            <input type="file" class="form-control <?php $__errorArgs = ['id_document'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   id="id_document" name="id_document" accept="image/*" required>
                            <small class="text-muted">Upload a photo of valid ID (Driver's License, Passport, etc.)</small>
                            <?php $__errorArgs = ['id_document'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="status" name="status" required>
                                <option value="active" <?php echo e(old('status', 'active') === 'active' ? 'selected' : ''); ?>>Active</option>
                                <option value="inactive" <?php echo e(old('status') === 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                            </select>
                            <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
    showToast('Edit tenant ' + tenantId, 'info');
}

function deleteTenant(tenantId) {
    confirmAction('Are you sure you want to delete this tenant? This action cannot be undone.', function() {
        // TODO: Implement delete functionality
        showToast('Delete tenant ' + tenantId, 'info');
    }, { title: 'Delete Tenant', confirmText: 'Yes, Delete', type: 'danger' });
}

// Sorting function
function sortTable(column) {
    const url = new URL(window.location.href);
    const currentSort = url.searchParams.get('sort_by');
    const currentDir = url.searchParams.get('sort_dir') || 'desc';

    // If clicking the same column, toggle direction
    if (currentSort === column) {
        url.searchParams.set('sort_dir', currentDir === 'asc' ? 'desc' : 'asc');
    } else {
        // New column, default to ascending for names, descending for others
        url.searchParams.set('sort_by', column);
        url.searchParams.set('sort_dir', column.includes('name') ? 'asc' : 'desc');
    }

    window.location.href = url.toString();
}
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hmmth\sanasa_dormitory\resources\views/contents/tenants.blade.php ENDPATH**/ ?>