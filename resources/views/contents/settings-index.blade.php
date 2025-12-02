@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<style>
    .settings-header {
        background-color: white;
        margin-bottom: 2rem;
    }
    .settings-title {
        font-size: 2rem;
        font-weight: 700;
        color: #03255b;
        margin: 0;
    }
    .settings-card {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        padding: 2rem;
        margin-bottom: 1.5rem;
    }
    .settings-section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #03255b;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e9ecef;
    }
    .settings-section-icon {
        width: 36px;
        height: 36px;
        background-color: #f8f9fa;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
        color: #03255b;
    }
    .form-label-custom {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    .form-help-text {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }
    .input-group-text-custom {
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        color: #495057;
        font-weight: 500;
    }
    .form-control-custom {
        border: 1px solid #ced4da;
        border-radius: 8px;
        padding: 0.75rem 1rem;
    }
    .form-control-custom:focus {
        border-color: #03255b;
        box-shadow: 0 0 0 0.2rem rgba(3, 37, 91, 0.15);
    }
    .form-select-custom {
        border: 1px solid #ced4da;
        border-radius: 8px;
        padding: 0.75rem 1rem;
    }
    .form-select-custom:focus {
        border-color: #03255b;
        box-shadow: 0 0 0 0.2rem rgba(3, 37, 91, 0.15);
    }
    .btn-save {
        background-color: #03255b;
        color: white;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        transition: background-color 0.3s ease;
    }
    .btn-save:hover {
        background-color: #021d47;
        color: white;
    }
    .penalty-preview {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
    }
    .penalty-preview-title {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    .penalty-preview-text {
        color: #6c757d;
        font-size: 0.9rem;
    }
    .alert-info-custom {
        background-color: #e7f3ff;
        border: 1px solid #b6d4fe;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
</style>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="settings-header d-flex justify-content-between align-items-center">
        <h1 class="settings-title">System Settings</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Late Payment Penalties Section -->
        <div class="settings-card">
            <div class="d-flex align-items-center mb-3">
                <div class="settings-section-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <h2 class="settings-section-title mb-0">Late Payment Penalties</h2>
            </div>

            <div class="alert-info-custom">
                <i class="bi bi-info-circle me-2"></i>
                Configure how late payment penalties are calculated and applied to overdue invoices.
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="late_penalty_type" class="form-label form-label-custom">Penalty Type</label>
                    <select name="late_penalty_type" id="late_penalty_type" class="form-select form-select-custom" required>
                        <option value="percentage" {{ $settings['late_penalty_type'] == 'percentage' ? 'selected' : '' }}>Percentage of Total Due</option>
                        <option value="fixed" {{ $settings['late_penalty_type'] == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                    </select>
                    <div class="form-help-text">Choose whether the penalty is a percentage or a fixed peso amount.</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="late_penalty_rate" class="form-label form-label-custom">Penalty Rate</label>
                    <div class="input-group">
                        <input type="number" step="0.01" min="0" max="100"
                               name="late_penalty_rate" id="late_penalty_rate"
                               class="form-control form-control-custom"
                               value="{{ $settings['late_penalty_rate'] }}" required>
                        <span class="input-group-text input-group-text-custom" id="penalty-rate-suffix">
                            {{ $settings['late_penalty_type'] == 'percentage' ? '%' : 'P' }}
                        </span>
                    </div>
                    <div class="form-help-text">
                        <span id="rate-help-text">
                            {{ $settings['late_penalty_type'] == 'percentage' ? 'Percentage to add to the total due.' : 'Fixed peso amount to add.' }}
                        </span>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="late_penalty_grace_days" class="form-label form-label-custom">Grace Period (Days)</label>
                    <input type="number" min="0" max="60"
                           name="late_penalty_grace_days" id="late_penalty_grace_days"
                           class="form-control form-control-custom"
                           value="{{ $settings['late_penalty_grace_days'] }}" required>
                    <div class="form-help-text">Number of days after the due date before penalties are applied.</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="late_penalty_frequency" class="form-label form-label-custom">Penalty Frequency</label>
                    <select name="late_penalty_frequency" id="late_penalty_frequency" class="form-select form-select-custom" required>
                        <option value="once" {{ $settings['late_penalty_frequency'] == 'once' ? 'selected' : '' }}>One-Time (Applied Once)</option>
                        <option value="daily" {{ $settings['late_penalty_frequency'] == 'daily' ? 'selected' : '' }}>Daily (Compounds Daily)</option>
                        <option value="weekly" {{ $settings['late_penalty_frequency'] == 'weekly' ? 'selected' : '' }}>Weekly (Compounds Weekly)</option>
                        <option value="monthly" {{ $settings['late_penalty_frequency'] == 'monthly' ? 'selected' : '' }}>Monthly (Compounds Monthly)</option>
                    </select>
                    <div class="form-help-text">How often the penalty is applied to overdue invoices.</div>
                </div>
            </div>

            <!-- Penalty Preview -->
            <div class="penalty-preview">
                <div class="penalty-preview-title"><i class="bi bi-calculator me-2"></i>Penalty Preview</div>
                <div class="penalty-preview-text" id="penalty-preview">
                    <!-- Dynamically updated by JavaScript -->
                </div>
            </div>
        </div>

        <!-- Invoice Settings Section -->
        <div class="settings-card">
            <div class="d-flex align-items-center mb-3">
                <div class="settings-section-icon">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <h2 class="settings-section-title mb-0">Invoice Settings</h2>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="invoice_due_days" class="form-label form-label-custom">Default Due Date</label>
                    <div class="input-group">
                        <input type="number" min="1" max="60"
                               name="invoice_due_days" id="invoice_due_days"
                               class="form-control form-control-custom"
                               value="{{ $settings['invoice_due_days'] }}" required>
                        <span class="input-group-text input-group-text-custom">days after invoice</span>
                    </div>
                    <div class="form-help-text">Number of days after invoice generation before it becomes due.</div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-save">
                <i class="bi bi-check-lg me-2"></i>Save Settings
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const penaltyType = document.getElementById('late_penalty_type');
    const penaltyRate = document.getElementById('late_penalty_rate');
    const graceDays = document.getElementById('late_penalty_grace_days');
    const frequency = document.getElementById('late_penalty_frequency');
    const penaltyRateSuffix = document.getElementById('penalty-rate-suffix');
    const rateHelpText = document.getElementById('rate-help-text');
    const penaltyPreview = document.getElementById('penalty-preview');

    function updatePenaltyUI() {
        const type = penaltyType.value;
        const rate = parseFloat(penaltyRate.value) || 0;
        const grace = parseInt(graceDays.value) || 0;
        const freq = frequency.value;

        // Update suffix
        if (type === 'percentage') {
            penaltyRateSuffix.textContent = '%';
            rateHelpText.textContent = 'Percentage to add to the total due.';
        } else {
            penaltyRateSuffix.textContent = 'P';
            rateHelpText.textContent = 'Fixed peso amount to add.';
        }

        // Calculate example penalty
        const exampleTotal = 5000; // Example P5,000 invoice
        let penalty = 0;
        let freqText = '';

        if (type === 'percentage') {
            penalty = (exampleTotal * rate) / 100;
        } else {
            penalty = rate;
        }

        switch (freq) {
            case 'daily':
                freqText = 'per day';
                break;
            case 'weekly':
                freqText = 'per week';
                break;
            case 'monthly':
                freqText = 'per month';
                break;
            case 'once':
            default:
                freqText = '(one-time)';
        }

        // Generate preview text
        let preview = `For an invoice of P${exampleTotal.toLocaleString()}, `;
        preview += `if payment is not received within ${grace} days after the due date, `;
        preview += `a penalty of P${penalty.toFixed(2)} ${freqText} will be applied.`;

        if (freq === 'daily') {
            preview += ` After 10 days overdue, total penalty would be P${(penalty * Math.max(0, 10 - grace)).toFixed(2)}.`;
        } else if (freq === 'weekly') {
            preview += ` After 2 weeks overdue, total penalty would be P${(penalty * Math.max(0, Math.ceil((14 - grace) / 7))).toFixed(2)}.`;
        } else if (freq === 'monthly') {
            preview += ` After 1 month overdue, total penalty would be P${(penalty * Math.max(0, Math.ceil((30 - grace) / 30))).toFixed(2)}.`;
        }

        penaltyPreview.textContent = preview;
    }

    // Update on change
    penaltyType.addEventListener('change', updatePenaltyUI);
    penaltyRate.addEventListener('input', updatePenaltyUI);
    graceDays.addEventListener('input', updatePenaltyUI);
    frequency.addEventListener('change', updatePenaltyUI);

    // Initial update
    updatePenaltyUI();
});
</script>
@endsection
