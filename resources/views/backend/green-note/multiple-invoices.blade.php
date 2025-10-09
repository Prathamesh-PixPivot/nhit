@extends('backend.layouts.app')

@section('title', 'Multiple Invoices - ' . $greenNote->formatted_order_no)

@push('styles')
<link href="{{ asset('css/modern-design-system.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="modern-container">
    <!-- Modern Header -->
    <div class="modern-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h1 class="modern-page-title">
                    <i class="bi bi-receipt-cutoff text-primary me-3"></i>Multiple Invoices
                </h1>
                <p class="modern-page-subtitle">Manage multiple invoices for Expense Note #{{ $greenNote->formatted_order_no }}</p>
            </div>
            <div class="modern-action-group">
                <a href="{{ route('backend.note.show', $greenNote->id) }}" class="btn-modern btn-modern-secondary">
                    <i class="bi bi-arrow-left"></i>Back to Note
                </a>
            </div>
        </div>
    </div>

    <!-- Modern Breadcrumb -->
    <div class="modern-breadcrumb">
        <a href="{{ route('backend.dashboard.index') }}">
            <i class="bi bi-house-door me-1"></i>Dashboard
        </a>
        <span class="modern-breadcrumb-separator">/</span>
        <a href="{{ route('backend.note.index') }}">Expense Notes</a>
        <span class="modern-breadcrumb-separator">/</span>
        <a href="{{ route('backend.note.show', $greenNote->id) }}">{{ $greenNote->formatted_order_no }}</a>
        <span class="modern-breadcrumb-separator">/</span>
        <span>Multiple Invoices</span>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <!-- Instructions Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Instructions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary"><i class="bi bi-1-circle me-1"></i>Adding Invoices</h6>
                            <ul class="small text-muted">
                                <li>Click "Add Another Invoice" to add more invoices</li>
                                <li>Invoice Number and Date are mandatory fields</li>
                                <li>Invoice Value is required and will be summed automatically</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary"><i class="bi bi-2-circle me-1"></i>Managing Invoices</h6>
                            <ul class="small text-muted">
                                <li>Use "Remove" button to delete unwanted invoices</li>
                                <li>At least one invoice must remain</li>
                                <li>Total value is calculated automatically</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Form Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-receipt text-primary me-2"></i>Invoice Details
                        </h5>
                        <div class="badge bg-info fs-6">
                            <i class="bi bi-calculator me-1"></i>Total: ₹<span id="total-value">0.00</span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">

                    @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill text-danger me-3 fs-4"></i>
                                <div>
                                    <h6 class="alert-heading mb-2">Please fix the following errors:</h6>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                        <form action="{{ route('backend.green-note.multiple-invoices.update', $greenNote) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div id="invoices-container">
                                @if(!empty($greenNote->invoices) && is_array($greenNote->invoices))
                                    @foreach($greenNote->invoices as $index => $invoice)
                                        <div class="invoice-row border rounded p-4 mb-4 bg-light" data-index="{{ $index }}">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="text-primary mb-0">
                                                    <i class="bi bi-receipt me-2"></i>Invoice #{{ $index + 1 }}
                                                </h6>
                                                <span class="badge bg-primary">{{ $invoice['invoice_number'] ?? 'New Invoice' }}</span>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" name="invoices[{{ $index }}][invoice_number]" 
                                                               class="form-control" value="{{ $invoice['invoice_number'] ?? '' }}" 
                                                               id="invoice_number_{{ $index }}" required placeholder="Invoice Number">
                                                        <label for="invoice_number_{{ $index }}">Invoice Number *</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="date" name="invoices[{{ $index }}][invoice_date]" 
                                                               class="form-control" value="{{ $invoice['invoice_date'] ?? '' }}" 
                                                               id="invoice_date_{{ $index }}" required placeholder="Invoice Date">
                                                        <label for="invoice_date_{{ $index }}">Invoice Date *</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-3">
                                                    <div class="form-floating">
                                                        <input type="number" step="0.01" name="invoices[{{ $index }}][invoice_value]" 
                                                               class="form-control invoice-value" value="{{ $invoice['invoice_value'] ?? '' }}" 
                                                               id="invoice_value_{{ $index }}" required placeholder="Invoice Value">
                                                        <label for="invoice_value_{{ $index }}">Invoice Value * (₹)</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-floating">
                                                        <input type="number" step="0.01" name="invoices[{{ $index }}][invoice_base_value]" 
                                                               class="form-control" value="{{ $invoice['invoice_base_value'] ?? '' }}" 
                                                               id="invoice_base_value_{{ $index }}" placeholder="Base Value">
                                                        <label for="invoice_base_value_{{ $index }}">Base Value (₹)</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-floating">
                                                        <input type="number" step="0.01" name="invoices[{{ $index }}][invoice_gst]" 
                                                               class="form-control" value="{{ $invoice['invoice_gst'] ?? '' }}" 
                                                               id="invoice_gst_{{ $index }}" placeholder="GST Amount">
                                                        <label for="invoice_gst_{{ $index }}">GST Amount (₹)</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-floating">
                                                        <input type="number" step="0.01" name="invoices[{{ $index }}][invoice_other_charges]" 
                                                               class="form-control" value="{{ $invoice['invoice_other_charges'] ?? '' }}" 
                                                               id="invoice_other_charges_{{ $index }}" placeholder="Other Charges">
                                                        <label for="invoice_other_charges_{{ $index }}">Other Charges (₹)</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-10">
                                                    <div class="form-floating">
                                                        <input type="text" name="invoices[{{ $index }}][description]" 
                                                               class="form-control" value="{{ $invoice['description'] ?? '' }}" 
                                                               id="description_{{ $index }}" placeholder="Description">
                                                        <label for="description_{{ $index }}">Description (Optional)</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 d-flex align-items-center">
                                                    <button type="button" class="btn btn-outline-danger remove-invoice w-100" title="Remove Invoice">
                                                        <i class="bi bi-trash me-1"></i>Remove
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    {{-- Show single invoice if exists --}}
                                    @if($greenNote->invoice_number)
                                        <div class="invoice-row border p-3 mb-3" data-index="0">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label">Invoice Number *</label>
                                                    <input type="text" name="invoices[0][invoice_number]" 
                                                           class="form-control" value="{{ $greenNote->invoice_number }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Invoice Date *</label>
                                                    <input type="date" name="invoices[0][invoice_date]" 
                                                           class="form-control" value="{{ $greenNote->invoice_date }}" required>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-md-3">
                                                    <label class="form-label">Invoice Value *</label>
                                                    <input type="number" step="0.01" name="invoices[0][invoice_value]" 
                                                           class="form-control invoice-value" value="{{ $greenNote->invoice_value }}" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Base Value</label>
                                                    <input type="number" step="0.01" name="invoices[0][invoice_base_value]" 
                                                           class="form-control" value="{{ $greenNote->invoice_base_value }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">GST Amount</label>
                                                    <input type="number" step="0.01" name="invoices[0][invoice_gst]" 
                                                           class="form-control" value="{{ $greenNote->invoice_gst }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Other Charges</label>
                                                    <input type="number" step="0.01" name="invoices[0][invoice_other_charges]" 
                                                           class="form-control" value="{{ $greenNote->invoice_other_charges }}">
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-md-10">
                                                    <label class="form-label">Description</label>
                                                    <input type="text" name="invoices[0][description]" 
                                                           class="form-control" value="">
                                                </div>
                                                <div class="col-md-2 d-flex align-items-end">
                                                    <button type="button" class="btn btn-danger remove-invoice">Remove</button>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        {{-- Empty invoice row --}}
                                        <div class="invoice-row border p-3 mb-3" data-index="0">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label">Invoice Number *</label>
                                                    <input type="text" name="invoices[0][invoice_number]" 
                                                           class="form-control" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Invoice Date *</label>
                                                    <input type="date" name="invoices[0][invoice_date]" 
                                                           class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-md-3">
                                                    <label class="form-label">Invoice Value *</label>
                                                    <input type="number" step="0.01" name="invoices[0][invoice_value]" 
                                                           class="form-control invoice-value" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Base Value</label>
                                                    <input type="number" step="0.01" name="invoices[0][invoice_base_value]" 
                                                           class="form-control">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">GST Amount</label>
                                                    <input type="number" step="0.01" name="invoices[0][invoice_gst]" 
                                                           class="form-control">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Other Charges</label>
                                                    <input type="number" step="0.01" name="invoices[0][invoice_other_charges]" 
                                                           class="form-control">
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-md-10">
                                                    <label class="form-label">Description</label>
                                                    <input type="text" name="invoices[0][description]" 
                                                           class="form-control">
                                                </div>
                                                <div class="col-md-2 d-flex align-items-end">
                                                    <button type="button" class="btn btn-danger remove-invoice">Remove</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <button type="button" id="add-invoice" class="btn btn-success">
                            <i class="bi bi-plus-circle me-1"></i>Add Another Invoice
                        </button>
                        <div class="text-end">
                            <h5 class="mb-0 text-primary">
                                <i class="bi bi-calculator me-1"></i>Total Value: ₹<span id="total-value-display">0.00</span>
                            </h5>
                        </div>
                    </div>

                    <div class="text-center border-top pt-4">
                        <button type="submit" class="btn btn-primary btn-lg me-3">
                            <i class="bi bi-check-circle me-1"></i>Update Invoices
                        </button>
                        <a href="{{ route('backend.note.show', $greenNote) }}" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-x-circle me-1"></i>Cancel
                        </a>
                    </div>
                        </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let invoiceIndex = {{ !empty($greenNote->invoices) ? count($greenNote->invoices) : 1 }};
    
    // Add new invoice row
    document.getElementById('add-invoice').addEventListener('click', function() {
        const container = document.getElementById('invoices-container');
        const newRow = createInvoiceRow(invoiceIndex);
        container.appendChild(newRow);
        invoiceIndex++;
        updateTotal();
    });
    
    // Remove invoice row
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-invoice')) {
            const invoiceRows = document.querySelectorAll('.invoice-row');
            if (invoiceRows.length > 1) {
                e.target.closest('.invoice-row').remove();
                updateTotal();
            } else {
                alert('At least one invoice is required.');
            }
        }
    });
    
    // Update total when invoice values change
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('invoice-value')) {
            updateTotal();
        }
    });
    
    function createInvoiceRow(index) {
        const div = document.createElement('div');
        div.className = 'invoice-row border rounded p-4 mb-4 bg-light';
        div.setAttribute('data-index', index);
        div.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="text-primary mb-0">
                    <i class="bi bi-receipt me-2"></i>Invoice #${index + 1}
                </h6>
                <span class="badge bg-primary">New Invoice</span>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" name="invoices[${index}][invoice_number]" 
                               class="form-control" id="invoice_number_${index}" required placeholder="Invoice Number">
                        <label for="invoice_number_${index}">Invoice Number *</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="date" name="invoices[${index}][invoice_date]" 
                               class="form-control" id="invoice_date_${index}" required placeholder="Invoice Date">
                        <label for="invoice_date_${index}">Invoice Date *</label>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="number" step="0.01" name="invoices[${index}][invoice_value]" 
                               class="form-control invoice-value" id="invoice_value_${index}" required placeholder="Invoice Value">
                        <label for="invoice_value_${index}">Invoice Value * (₹)</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="number" step="0.01" name="invoices[${index}][invoice_base_value]" 
                               class="form-control" id="invoice_base_value_${index}" placeholder="Base Value">
                        <label for="invoice_base_value_${index}">Base Value (₹)</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="number" step="0.01" name="invoices[${index}][invoice_gst]" 
                               class="form-control" id="invoice_gst_${index}" placeholder="GST Amount">
                        <label for="invoice_gst_${index}">GST Amount (₹)</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating">
                        <input type="number" step="0.01" name="invoices[${index}][invoice_other_charges]" 
                               class="form-control" id="invoice_other_charges_${index}" placeholder="Other Charges">
                        <label for="invoice_other_charges_${index}">Other Charges (₹)</label>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-10">
                    <div class="form-floating">
                        <input type="text" name="invoices[${index}][description]" 
                               class="form-control" id="description_${index}" placeholder="Description">
                        <label for="description_${index}">Description (Optional)</label>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-center">
                    <button type="button" class="btn btn-outline-danger remove-invoice w-100" title="Remove Invoice">
                        <i class="bi bi-trash me-1"></i>Remove
                    </button>
                </div>
            </div>
        `;
        return div;
    }
    
    function updateTotal() {
        const invoiceValues = document.querySelectorAll('.invoice-value');
        let total = 0;
        invoiceValues.forEach(input => {
            const value = parseFloat(input.value) || 0;
            total += value;
        });
        const formattedTotal = new Intl.NumberFormat('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(total);
        
        document.getElementById('total-value').textContent = formattedTotal;
        const totalDisplay = document.getElementById('total-value-display');
        if (totalDisplay) {
            totalDisplay.textContent = formattedTotal;
        }
    }
    
    // Initial total calculation
    updateTotal();
});
</script>
@endpush
