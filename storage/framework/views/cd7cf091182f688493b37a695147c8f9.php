<?php $__env->startSection('title', 'Tenant Details'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .tenant-details-container {
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
        white-space: nowrap;
    }

    .status-badge.active {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-badge.inactive {
        background-color: #e5e7eb;
        color: #4b5563;
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

    .info-value-view {
        display: block;
    }

    .info-value-edit {
        display: none;
    }

    .edit-mode .info-value-view {
        display: none;
    }

    .edit-mode .info-value-edit {
        display: block;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        flex-shrink: 0;
    }

    .btn-action {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
        white-space: nowrap;
    }

    .btn-edit {
        background-color: #e0f2fe;
        color: #0369a1;
    }

    .btn-edit:hover {
        background-color: #bae6fd;
    }

    .btn-save {
        background-color: #10b981;
        color: white;
    }

    .btn-save:hover {
        background-color: #059669;
    }

    .btn-cancel {
        background-color: #6b7280;
        color: white;
    }

    .btn-cancel:hover {
        background-color: #4b5563;
    }

    .btn-archive {
        background-color: #fef3c7;
        color: #92400e;
    }

    .btn-archive:hover {
        background-color: #fde68a;
    }

    .btn-archive:disabled {
        background-color: #e5e7eb;
        color: #9ca3af;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .btn-archive:disabled:hover {
        background-color: #e5e7eb;
    }

    .btn-activate {
        background-color: #d1fae5;
        color: #065f46;
    }

    .btn-activate:hover {
        background-color: #a7f3d0;
    }

    .edit-mode .btn-edit {
        display: none;
    }

    .btn-save, .btn-cancel {
        display: none;
    }

    .edit-mode .btn-save,
    .edit-mode .btn-cancel {
        display: inline-block;
    }

    /* Payment Method Badges */
    .payment-method-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .payment-method-badge.cash {
        background-color: #d1fae5;
        color: #065f46;
    }

    .payment-method-badge.gcash {
        background-color: #dbeafe;
        color: #1d4ed8;
    }

    .payment-method-badge.bank {
        background-color: #e0e7ff;
        color: #4338ca;
    }

    .payment-method-badge.check {
        background-color: #fef3c7;
        color: #92400e;
    }

    .payment-method-badge.other {
        background-color: #f3f4f6;
        color: #4b5563;
    }

    /* Payment Type Badges */
    .payment-type-badge {
        display: inline-block;
        padding: 0.3rem 0.75rem;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .payment-type-badge.rent {
        background-color: #dbeafe;
        color: #1d4ed8;
    }

    .payment-type-badge.utility {
        background-color: #fef3c7;
        color: #92400e;
    }

    .payment-type-badge.deposit {
        background-color: #d1fae5;
        color: #065f46;
    }

    .payment-type-badge.other {
        background-color: #f3f4f6;
        color: #4b5563;
    }

    .btn-receipt {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.35rem 0.75rem;
        background-color: #03255b;
        color: white;
        border: none;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-receipt:hover {
        background-color: #021d47;
        color: white;
    }

    .bookings-table-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        overflow: hidden;
        margin-top: 1.5rem;
    }

    .bookings-table {
        width: 100%;
        border-collapse: collapse;
    }

    .bookings-table thead {
        background-color: #f7fafc;
    }

    .bookings-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #2d3748;
        font-size: 0.875rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .bookings-table td {
        padding: 1rem;
        color: #4a5568;
        font-size: 0.875rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .bookings-table tbody tr:hover {
        background-color: #f7fafc;
    }

    .bookings-table tbody tr:last-child td {
        border-bottom: none;
    }

    .btn-view {
        background-color: #e0f2fe;
        color: #0369a1;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 500;
        display: inline-block;
    }

    .btn-view:hover {
        background-color: #bae6fd;
        color: #0369a1;
    }

    .form-control, .form-select {
        font-size: 0.95rem;
        padding: 0.5rem;
    }
</style>

<div class="tenant-details-container" id="tenantContainer">
    <div class="details-header">
        <div>
            <h1 class="details-title">Tenant Details</h1>
            <span class="status-badge <?php echo e($tenant->status); ?>" id="statusBadge"><?php echo e(ucfirst($tenant->status)); ?></span>
        </div>
        <div class="action-buttons">
            <a href="<?php echo e(route('tenants')); ?>" class="btn-action btn-edit">
                <i class="bi bi-arrow-left"></i> Back to Tenants
            </a>
            <button type="button" class="btn-action btn-edit" id="editBtn" onclick="toggleEditMode()">
                <i class="bi bi-pencil-square"></i> Edit
            </button>
            <button type="button" class="btn-action btn-save" id="saveBtn" onclick="saveTenant()">
                <i class="bi bi-check-circle"></i> Save
            </button>
            <button type="button" class="btn-action btn-cancel" id="cancelBtn" onclick="cancelEdit()">
                <i class="bi bi-x-circle"></i> Cancel
            </button>
            <?php if($tenant->status === 'active'): ?>
                <?php
                    $hasActiveBooking = $tenant->bookings()->where('status', 'Active')->exists();
                ?>
                <form action="<?php echo e(route('tenants.archive', $tenant->tenant_id)); ?>" method="POST" style="display: inline;" id="archiveForm">
                    <?php echo csrf_field(); ?>
                    <button type="button"
                            class="btn-action btn-archive"
                            <?php if($hasActiveBooking): ?> disabled <?php endif; ?>
                            title="<?php echo e($hasActiveBooking ? 'Cannot archive tenant while they have an active booking' : 'Archive this tenant'); ?>"
                            onclick="<?php echo e($hasActiveBooking ? '' : 'confirmAction(\'Are you sure you want to archive this tenant?\', function() { document.getElementById(\'archiveForm\').submit(); }, { title: \'Archive Tenant\', confirmText: \'Yes, Archive\', type: \'warning\' })'); ?>">
                        <i class="bi bi-archive"></i> Archive
                    </button>
                </form>

            <?php else: ?>
                <form action="<?php echo e(route('tenants.activate', $tenant->tenant_id)); ?>" method="POST" style="display: inline;" id="activateForm">
                    <?php echo csrf_field(); ?>
                    <button type="button" class="btn-action btn-activate" onclick="confirmAction('Are you sure you want to activate this tenant?', function() { document.getElementById('activateForm').submit(); }, { title: 'Activate Tenant', confirmText: 'Yes, Activate', type: 'info' })">
                        <i class="bi bi-check-circle"></i> Activate
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger mb-4">
            <h5 class="alert-heading">Please fix the following errors:</h5>
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('tenants.update', $tenant->tenant_id)); ?>" method="POST" id="tenantForm" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <!-- Tenant Information -->
        <div class="info-section">
            <h2 class="info-section-title">Personal Information</h2>
            <!-- Three Column Layout -->
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 2rem; margin-bottom: 1.5rem;">
                <!-- Column 1: Name Fields (Stacked) -->
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div class="info-item">
                        <span class="info-label">First Name</span>
                        <span class="info-value info-value-view"><strong><?php echo e($tenant->first_name); ?></strong></span>
                        <input type="text" class="form-control info-value-edit <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               name="first_name" value="<?php echo e(old('first_name', $tenant->first_name)); ?>" required>
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
                    <div class="info-item">
                        <span class="info-label">Middle Name</span>
                        <span class="info-value info-value-view"><?php echo e($tenant->middle_name ?? 'N/A'); ?></span>
                        <input type="text" class="form-control info-value-edit <?php $__errorArgs = ['middle_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               name="middle_name" value="<?php echo e(old('middle_name', $tenant->middle_name)); ?>">
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
                    <div class="info-item">
                        <span class="info-label">Last Name</span>
                        <span class="info-value info-value-view"><strong><?php echo e($tenant->last_name); ?></strong></span>
                        <input type="text" class="form-control info-value-edit <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               name="last_name" value="<?php echo e(old('last_name', $tenant->last_name)); ?>" required>
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

                <!-- Column 2: Contact Info (Stacked) -->
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div class="info-item">
                        <span class="info-label">Contact Number</span>
                        <span class="info-value info-value-view"><?php echo e($tenant->contact_num ?? 'N/A'); ?></span>
                        <input type="text" class="form-control info-value-edit <?php $__errorArgs = ['contact_num'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               name="contact_num" value="<?php echo e(old('contact_num', $tenant->contact_num)); ?>">
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
                    <div class="info-item">
                        <span class="info-label">Email</span>
                        <span class="info-value info-value-view"><?php echo e($tenant->email ?? 'N/A'); ?></span>
                        <input type="email" class="form-control info-value-edit <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               name="email" value="<?php echo e(old('email', $tenant->email)); ?>">
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
                    <div class="info-item">
                        <span class="info-label">Age</span>
                        <span class="info-value info-value-view"><?php echo e($tenant->age ? $tenant->age . ' years old' : 'N/A'); ?></span>
                        <div class="info-value-edit" style="padding: 0.5rem; background-color: #f8fafc; border-radius: 4px; color: #64748b;">
                            <?php echo e($tenant->age ? $tenant->age . ' years old' : 'N/A'); ?> <small>(calculated from birth date)</small>
                        </div>
                    </div>
                </div>

                <!-- Column 3: Additional Info (Stacked) -->
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div class="info-item">
                        <span class="info-label">Emergency Contact Name</span>
                        <span class="info-value info-value-view"><?php echo e($tenant->emer_contact_name ?? 'N/A'); ?></span>
                        <input type="text" class="form-control info-value-edit <?php $__errorArgs = ['emer_contact_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               name="emer_contact_name" value="<?php echo e(old('emer_contact_name', $tenant->emer_contact_name)); ?>"
                               placeholder="e.g., Juan Dela Cruz (Father)">
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
                    <div class="info-item">
                        <span class="info-label">Emergency Contact Number</span>
                        <span class="info-value info-value-view"><?php echo e($tenant->emer_contact_num ?? 'N/A'); ?></span>
                        <input type="text" class="form-control info-value-edit <?php $__errorArgs = ['emer_contact_num'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               name="emer_contact_num" value="<?php echo e(old('emer_contact_num', $tenant->emer_contact_num)); ?>">
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
                    <div class="info-item">
                        <span class="info-label">Birth Date</span>
                        <span class="info-value info-value-view"><?php echo e($tenant->birth_date ? $tenant->birth_date->format('M d, Y') : 'N/A'); ?></span>
                        <input type="date" class="form-control info-value-edit <?php $__errorArgs = ['birth_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               name="birth_date" value="<?php echo e(old('birth_date', $tenant->birth_date ? $tenant->birth_date->format('Y-m-d') : '')); ?>">
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
                    <div class="info-item">
                        <span class="info-label">ID Document</span>
                        <span class="info-value info-value-view">
                            <?php if($tenant->id_document): ?>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#idDocumentModal">
                                    <i class="bi bi-eye"></i> View ID
                                </button>
                            <?php else: ?>
                                <span class="text-muted">No ID uploaded</span>
                            <?php endif; ?>
                        </span>
                        <div class="info-value-edit">
                            <?php if($tenant->id_document): ?>
                                <div class="mb-2">
                                    <img src="<?php echo e(asset($tenant->id_document)); ?>" alt="Current ID" class="img-thumbnail" style="max-height: 80px;">
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control <?php $__errorArgs = ['id_document'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   name="id_document" accept="image/*">
                            <small class="text-muted"><?php echo e($tenant->id_document ? 'Upload new to replace' : 'Upload ID image'); ?></small>
                        </div>
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
                </div>
            </div>

            <!-- Address (Full Width) -->
            <div class="info-item" style="margin-top: 1rem;">
                <span class="info-label">Address</span>
                <span class="info-value info-value-view"><?php echo e($tenant->address ?? 'N/A'); ?></span>
                <textarea class="form-control info-value-edit <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                          name="address" rows="2"><?php echo e(old('address', $tenant->address)); ?></textarea>
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

        <!-- Status Field (Hidden in view, shown in edit mode) -->
        <div class="info-section" id="statusSection" style="display: none;">
            <h2 class="info-section-title">Account Status</h2>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Status</span>
                    <select class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            name="status" required>
                        <option value="active" <?php echo e(old('status', $tenant->status) === 'active' ? 'selected' : ''); ?>>Active</option>
                        <option value="inactive" <?php echo e(old('status', $tenant->status) === 'inactive' ? 'selected' : ''); ?>>Inactive</option>
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
    </form>

    <!-- Payment History -->
    <div class="info-section">
        <h2 class="info-section-title">
            <i class="bi bi-credit-card me-2"></i>Payment History
            <span style="font-weight: 400; font-size: 0.85rem; color: #64748b;">
                (<?php echo e($payments->count()); ?> <?php echo e(Str::plural('payment', $payments->count())); ?> · Total: ₱<?php echo e(number_format($totalPaid, 2)); ?>)
            </span>
        </h2>
        <?php if($payments->count() > 0): ?>
            <div class="bookings-table-container">
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Room</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th>Collected By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $methodClass = match(strtolower($payment->payment_method ?? '')) {
                                    'cash' => 'cash',
                                    'gcash' => 'gcash',
                                    'bank transfer', 'bank' => 'bank',
                                    'check', 'cheque' => 'check',
                                    default => 'other'
                                };
                                $typeClass = match(true) {
                                    str_contains(strtolower($payment->payment_type ?? ''), 'rent') => 'rent',
                                    str_contains(strtolower($payment->payment_type ?? ''), 'electric') || str_contains(strtolower($payment->payment_type ?? ''), 'utility') => 'utility',
                                    str_contains(strtolower($payment->payment_type ?? ''), 'deposit') => 'deposit',
                                    default => 'other'
                                };
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo e($payment->date_received ? $payment->date_received->format('M d, Y') : 'N/A'); ?></strong>
                                    <br><small class="text-muted"><?php echo e($payment->created_at->format('g:i A')); ?></small>
                                </td>
                                <td>
                                    <span class="payment-type-badge <?php echo e($typeClass); ?>"><?php echo e($payment->payment_type); ?></span>
                                </td>
                                <td>
                                    <?php if($payment->booking && $payment->booking->room): ?>
                                        Room <?php echo e($payment->booking->room->room_num); ?>

                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-weight: 600; color: #059669;">₱<?php echo e(number_format($payment->amount, 2)); ?></td>
                                <td>
                                    <span class="payment-method-badge <?php echo e($methodClass); ?>">
                                        <?php if($methodClass === 'cash'): ?>
                                            <i class="bi bi-cash"></i>
                                        <?php elseif($methodClass === 'gcash'): ?>
                                            <i class="bi bi-phone"></i>
                                        <?php elseif($methodClass === 'bank'): ?>
                                            <i class="bi bi-bank"></i>
                                        <?php elseif($methodClass === 'check'): ?>
                                            <i class="bi bi-file-text"></i>
                                        <?php else: ?>
                                            <i class="bi bi-credit-card"></i>
                                        <?php endif; ?>
                                        <?php echo e($payment->payment_method); ?>

                                    </span>
                                </td>
                                <td><?php echo e($payment->reference_number ?? '-'); ?></td>
                                <td><?php echo e($payment->collectedBy->full_name ?? 'N/A'); ?></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="<?php echo e(route('payments.receipt', $payment->payment_id)); ?>" class="btn-receipt" target="_blank">
                                            <i class="bi bi-printer"></i> Receipt
                                        </a>
                                        <?php if($payment->booking): ?>
                                            <a href="<?php echo e(route('bookings.show', $payment->booking->booking_id)); ?>" class="btn-view">View</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 2rem; color: #64748b;">
                <i class="bi bi-inbox" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                <p class="mb-0">No payment records found for this tenant.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Booking History -->
    <div class="info-section">
        <h2 class="info-section-title">Booking History (<?php echo e($tenant->bookings_count ?? 0); ?>)</h2>
        <?php if($tenant->bookings->count() > 0): ?>
            <div class="bookings-table-container">
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>Room</th>
                            <th>Check-in Date</th>
                            <th>Check-out Date</th>
                            <th>Rate</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $tenant->bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <strong>Room <?php echo e($booking->room->room_num ?? 'N/A'); ?></strong>
                                </td>
                                <td>
                                    <?php echo e($booking->checkin_date->format('M d, Y')); ?>

                                    <?php if($booking->checked_in_at): ?>
                                        <br><small class="text-success"><?php echo e($booking->checked_in_at->format('g:i A')); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo e($booking->checkout_date->format('M d, Y')); ?>

                                    <?php if($booking->checked_out_at): ?>
                                        <br><small class="text-success"><?php echo e($booking->checked_out_at->format('g:i A')); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($booking->rate->duration_type ?? 'N/A'); ?> &middot; ₱<?php echo e(number_format($booking->rate->base_price ?? 0, 2)); ?></td>
                                <td>
                                    <span class="status-badge <?php echo e(str_replace(' ', '-', $booking->effective_status)); ?>">
                                        <?php echo e($booking->effective_status); ?>

                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('bookings.show', $booking->booking_id)); ?>" class="btn-view">View</a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted">No bookings found for this tenant.</p>
        <?php endif; ?>
    </div>
</div>

<script>
let isEditMode = false;
const originalValues = {};

function toggleEditMode() {
    const container = document.getElementById('tenantContainer');
    const statusSection = document.getElementById('statusSection');
    isEditMode = !isEditMode;

    if (isEditMode) {
        container.classList.add('edit-mode');
        // Show status section in edit mode
        if (statusSection) statusSection.style.display = 'block';
        // Store original values
        document.querySelectorAll('.info-value-edit').forEach(input => {
            originalValues[input.name] = input.value;
        });
        // Also store status select
        const statusSelect = document.querySelector('[name="status"]');
        if (statusSelect) originalValues['status'] = statusSelect.value;
        // Hide archive/activate forms
        const archiveForm = document.getElementById('archiveForm');
        const activateForm = document.getElementById('activateForm');
        if (archiveForm) archiveForm.style.display = 'none';
        if (activateForm) activateForm.style.display = 'none';
    } else {
        container.classList.remove('edit-mode');
        // Hide status section in view mode
        if (statusSection) statusSection.style.display = 'none';
        // Restore original values
        Object.keys(originalValues).forEach(name => {
            const input = document.querySelector(`[name="${name}"]`);
            if (input) input.value = originalValues[name];
        });
        // Show archive/activate forms
        const archiveForm = document.getElementById('archiveForm');
        const activateForm = document.getElementById('activateForm');
        if (archiveForm) archiveForm.style.display = 'inline';
        if (activateForm) activateForm.style.display = 'inline';
    }
}

function cancelEdit() {
    toggleEditMode();
}

function saveTenant() {
    document.getElementById('tenantForm').submit();
}

// Update status badge when status changes
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.querySelector('[name="status"]');
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            const statusBadge = document.getElementById('statusBadge');
            if (statusBadge) {
                statusBadge.textContent = this.value.charAt(0).toUpperCase() + this.value.slice(1);
                statusBadge.className = 'status-badge ' + this.value;
            }
        });
    }
});
</script>

<!-- ID Document View Modal -->
<?php if($tenant->id_document): ?>
<div class="modal fade" id="idDocumentModal" tabindex="-1" aria-labelledby="idDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="idDocumentModalLabel">ID Document - <?php echo e($tenant->full_name); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="<?php echo e(asset($tenant->id_document)); ?>" alt="ID Document" class="img-fluid" style="max-height: 70vh;">
            </div>
            <div class="modal-footer">
                <a href="<?php echo e(asset($tenant->id_document)); ?>" target="_blank" class="btn btn-outline-primary">
                    <i class="bi bi-box-arrow-up-right"></i> Open in New Tab
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\hmmth\sanasa_dormitory\resources\views/contents/tenants-show.blade.php ENDPATH**/ ?>