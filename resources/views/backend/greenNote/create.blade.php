@extends('backend.layouts.app')

@section('title', 'Create Green Note')

@section('content')
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-primary mb-1">
                        <i class="bi bi-plus-circle me-2"></i>Create Green Note
                    </h2>
                    <p class="text-muted mb-0">Fill in the details to create a new green note</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('backend.note.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Notes
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('backend.dashboard.index') }}">
                            <i class="bi bi-house-door me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('backend.note.index') }}">Green Notes</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Create</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <form action="{{ route('backend.note.store') }}" method="post" enctype="multipart/form-data" class="modern-form">
                @csrf
                <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

                <!-- Basic Information Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle text-primary me-2"></i>Basic Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('approval_for') is-invalid @enderror"
                                            id="approval_for" name="approval_for" required>
                                        <option value="Invoice" {{ old('approval_for') == 'Invoice' ? 'selected' : '' }}>
                                            Invoice</option>
                                        <option value="Advance" {{ old('approval_for') == 'Advance' ? 'selected' : '' }}>
                                            Advance</option>
                                        <option value="Adhoc" {{ old('approval_for') == 'Adhoc' ? 'selected' : '' }}>
                                            Adhoc</option>
                                    </select>
                                    <label for="approval_for">Approval for</label>
                                    @error('approval_for')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('vendor_id') is-invalid @enderror"
                                            id="vendor_id" name="vendor_id" required>
                                        <option value="">Select Project</option>
                                        @foreach ($filteredItems as $item)
                                            <option value="{{ $item->id }}"
                                                {{ old('vendor_id') == $item->id ? 'selected' : '' }}>
                                                {{ $item->project }}</option>
                                        @endforeach
                                    </select>
                                    <label for="vendor_id">Project Name</label>
                                    @error('vendor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Project Details Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-building text-primary me-2"></i>Project Details
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('department_id') is-invalid @enderror"
                                            id="department_id" name="department_id" required>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}"
                                                {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="department_id">User Department</label>
                                    @error('department_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('order_no') is-invalid @enderror"
                                           name="order_no" id="order_no" required
                                           value="{{ old('order_no', $orderNumber) }}"
                                           placeholder="Work/Purchase Order no.">
                                    <label for="order_no">Work/Purchase Order no.</label>
                                    @error('order_no')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control @error('order_date') is-invalid @enderror"
                                           name="order_date" id="order_date" required
                                           value="{{ old('order_date') }}">
                                    <label for="order_date">Work/Purchase Order date</label>
                                    @error('order_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Details Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-currency-dollar text-primary me-2"></i>Financial Details
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="fw-semibold text-primary mb-3">Order Amount</h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="number" step="0.01"
                                                   class="form-control @error('base_value') is-invalid @enderror"
                                                   name="base_value" id="base_value"
                                                   value="{{ old('base_value') }}"
                                                   oninput="calculateTotal()"
                                                   placeholder="Base value">
                                            <label for="base_value">Base Value</label>
                                            @error('base_value')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="number" step="0.01"
                                                   class="form-control @error('other_charges') is-invalid @enderror"
                                                   id="other_charges" name="other_charges"
                                                   value="{{ old('other_charges') }}"
                                                   oninput="calculateTotal()"
                                                   placeholder="Other Charges">
                                            <label for="other_charges">Other Charges</label>
                                            @error('other_charges')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="number" step="0.01"
                                                   class="form-control @error('gst') is-invalid @enderror"
                                                   id="gst" name="gst"
                                                   value="{{ old('gst') }}"
                                                   oninput="calculateTotal()"
                                                   placeholder="GST on Above">
                                            <label for="gst">GST on Above</label>
                                            @error('gst')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="number" step="0.01"
                                                   class="form-control" id="total_amount"
                                                   name="total_amount"
                                                   value="{{ old('total_amount') }}"
                                                   placeholder="Total Amount" readonly>
                                            <label for="total_amount">Total Amount</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="fw-semibold text-primary mb-3">Invoice Details</h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text"
                                                   class="form-control @error('invoice_number') is-invalid @enderror"
                                                   id="invoice_number" name="invoice_number"
                                                   value="{{ old('invoice_number') }}"
                                                   placeholder="Invoice Number" required>
                                            <label for="invoice_number">Invoice Number</label>
                                            @error('invoice_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="date"
                                                   class="form-control @error('invoice_date') is-invalid @enderror"
                                                   id="invoice_date" name="invoice_date"
                                                   value="{{ old('invoice_date') }}" required>
                                            <label for="invoice_date">Invoice Date</label>
                                            @error('invoice_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="number" step="0.01"
                                                   class="form-control @error('invoice_base_value') is-invalid @enderror"
                                                   id="invoice_base_value" name="invoice_base_value"
                                                   value="{{ old('invoice_base_value') }}"
                                                   oninput="calculateInvoiceTotal()"
                                                   placeholder="Taxable Value" required>
                                            <label for="invoice_base_value">Taxable Value</label>
                                            @error('invoice_base_value')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="number" step="0.01"
                                                   class="form-control @error('invoice_gst') is-invalid @enderror"
                                                   id="invoice_gst" name="invoice_gst"
                                                   value="{{ old('invoice_gst') }}"
                                                   oninput="calculateInvoiceTotal()"
                                                   placeholder="Add: GST on above">
                                            <label for="invoice_gst">Add: GST on above</label>
                                            @error('invoice_gst')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="number" step="0.01"
                                                   class="form-control @error('invoice_other_charges') is-invalid @enderror"
                                                   id="invoice_other_charges" name="invoice_other_charges"
                                                   value="{{ old('invoice_other_charges') }}"
                                                   oninput="calculateInvoiceTotal()"
                                                   placeholder="Invoice Other Charges">
                                            <label for="invoice_other_charges">Invoice Other Charges</label>
                                            @error('invoice_other_charges')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="number" step="0.01"
                                                   class="form-control" id="invoice_value"
                                                   name="invoice_value"
                                                   value="{{ old('invoice_value') }}"
                                                   placeholder="Invoice Value" readonly>
                                            <label for="invoice_value">Invoice Value</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Multiple Invoices Section -->
                            <div class="col-md-6">
                                <h6 class="fw-semibold text-primary mb-3">
                                    <i class="bi bi-receipt-cutoff me-2"></i>Multiple Invoices 
                                    <small class="text-muted">(Optional)</small>
                                </h6>
                                <div class="card border-primary shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="alert alert-info mb-3">
                                            <i class="bi bi-info-circle me-2"></i>
                                            <strong>New Feature!</strong> You can now add multiple invoices to a single expense note.
                                        </div>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="enable_multiple_invoices" name="enable_multiple_invoices" style="width: 3em; height: 1.5em;">
                                            <label class="form-check-label ms-2" for="enable_multiple_invoices">
                                                <strong class="text-primary">Enable Multiple Invoices</strong>
                                            </label>
                                            <small class="text-muted d-block mt-2">
                                                <i class="bi bi-arrow-right me-1"></i>
                                                Toggle this switch to add multiple invoice entries for this expense note
                                            </small>
                                        </div>

                                        <div id="multiple_invoices_section" style="display: none;">
                                            <div class="border rounded p-3 mb-3 bg-light">
                                                <h6 class="mb-3">Invoice Entries</h6>
                                                <div id="invoice_entries_container">
                                                    <!-- Invoice entries will be added here dynamically -->
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addInvoiceEntry()">
                                                    <i class="bi bi-plus-circle"></i> Add Invoice Entry
                                                </button>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="number" step="0.01" class="form-control"
                                                               id="total_invoice_value" name="total_invoice_value"
                                                               value="0.00" readonly>
                                                        <label for="total_invoice_value">Total Invoice Value</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="number" step="0.01" class="form-control"
                                                               id="total_invoice_gst" name="total_invoice_gst"
                                                               value="0.00" readonly>
                                                        <label for="total_invoice_gst">Total GST</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                <!-- Supplier & Classification Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-person-badge text-primary me-2"></i>Supplier & Classification
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('supplier_id') is-invalid @enderror select2"
                                            id="supplier_id" name="supplier_id" required>
                                        <option value="">Select Vendor Name</option>
                                        @foreach ($filteredVendorItems as $item)
                                            <option value="{{ $item->id }}"
                                                    data-msme="{{ $item->msme_classification }}"
                                                    data-activity="{{ $item->activity_type }}"
                                                    {{ old('supplier_id') == $item->id ? 'selected' : '' }}>
                                                {{ $item->vendor_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="supplier_id">Name of Supplier</label>
                                    @error('supplier_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="msme_classification"
                                           name="msme_classification" readonly
                                           value="{{ old('msme_classification') }}">
                                    <label for="msme_classification">MSME Classification</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="activity_type"
                                           name="activity_type" readonly
                                           value="{{ old('activity_type') }}">
                                    <label for="activity_type">Activity Type</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contract Details Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-calendar-check text-primary me-2"></i>Contract Details
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-primary mb-3">Contract Period</label>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="date" class="form-control"
                                                   name="contract_start_date"
                                                   value="{{ old('contract_start_date') }}">
                                            <label>Start Date</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="date" class="form-control"
                                                   name="contract_end_date"
                                                   value="{{ old('contract_end_date') }}">
                                            <label>End Date</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-primary mb-3">Supply Period</label>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="date" class="form-control"
                                                   name="supply_period_start"
                                                   value="{{ old('supply_period_start') }}">
                                            <label>Start Date</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="date" class="form-control"
                                                   name="supply_period_end"
                                                   value="{{ old('supply_period_end') }}">
                                            <label>End Date</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control"
                                           id="appointed_start_date" name="appointed_start_date"
                                           value="{{ old('appointed_start_date') }}">
                                    <label for="appointed_start_date">Appointed Start Date</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('whether_contract') is-invalid @enderror"
                                            id="whether_contract" name="whether_contract">
                                        <option value="Y" {{ old('whether_contract') == 'Y' ? 'selected' : '' }}>Yes</option>
                                        <option value="N" {{ old('whether_contract') == 'N' ? 'selected' : '' }}>No</option>
                                    </select>
                                    <label for="whether_contract">Contract Period Completed</label>
                                    @error('whether_contract')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-file-text text-primary me-2"></i>Additional Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('protest_note_raised') is-invalid @enderror"
                                            id="protest_note_raised" name="protest_note_raised">
                                        <option value="N">Select</option>
                                        <option value="Y" {{ old('protest_note_raised') == 'Y' ? 'selected' : '' }}>Yes</option>
                                        <option value="N" {{ old('protest_note_raised') == 'N' ? 'selected' : '' }}>No</option>
                                    </select>
                                    <label for="protest_note_raised">Protest Note Raised</label>
                                    @error('protest_note_raised')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mt-3">
                                    <input type="file" id="file_input_2" name="file_input_2"
                                           class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.csv">
                                    <small class="text-muted">Upload supporting document (PDF, DOC, XLS max 5MB)</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <textarea class="form-control @error('brief_of_goods_services') is-invalid @enderror"
                                              id="brief_of_goods_services" name="brief_of_goods_services"
                                              rows="4" placeholder="Brief of Goods / Services">{{ old('brief_of_goods_services') }}</textarea>
                                    <label for="brief_of_goods_services">Brief of Goods / Services</label>
                                    @error('brief_of_goods_services')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <textarea class="form-control @error('delayed_damages') is-invalid @enderror"
                                              id="delayed_damages" name="delayed_damages"
                                              rows="3" placeholder="Delayed damages">{{ old('delayed_damages') }}</textarea>
                                    <label for="delayed_damages">Delayed Damages</label>
                                    @error('delayed_damages')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('nature_of_expenses') is-invalid @enderror select2"
                                            id="nature_of_expenses" name="nature_of_expenses">
                                        <option value="">Select Nature of Expenses</option>
                                        <option value="OHC-001 - Manpower (Salaries, Wages, Director's Remuneration)"
                                                {{ old('nature_of_expenses') == 'OHC-001 - Manpower (Salaries, Wages, Director\'s Remuneration)' ? 'selected' : '' }}>
                                            OHC-001 - Manpower (Salaries, Wages, Director's Remuneration)</option>
                                        <option value="OHC-002 - Staff Welfare (Medical, Training, Recreational Activities)"
                                                {{ old('nature_of_expenses') == 'OHC-002 - Staff Welfare (Medical, Training, Recreational Activities)' ? 'selected' : '' }}>
                                            OHC-002 - Staff Welfare (Medical, Training, Recreational Activities)</option>
                                        <option value="OHC-003 - Office Rent & Utilities (Electricity, Water, Maintenance)"
                                                {{ old('nature_of_expenses') == 'OHC-003 - Office Rent & Utilities (Electricity, Water, Maintenance)' ? 'selected' : '' }}>
                                            OHC-003 - Office Rent & Utilities (Electricity, Water, Maintenance)</option>
                                        <!-- Add more options as needed -->
                                        <!-- <label for="nature_of_expenses">Nature of Expenses</label> -->
                                    </select>
                                    @error('nature_of_expenses')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Budget Information Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-graph-up text-primary me-2"></i>Budget Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" step="0.01"
                                           class="form-control @error('budget_expenditure') is-invalid @enderror"
                                           id="budget_expenditure" name="budget_expenditure"
                                           value="{{ old('budget_expenditure') }}"
                                           oninput="calculateTotalBudget()"
                                           placeholder="Budget Expenditure">
                                    <label for="budget_expenditure">Budget Expenditure</label>
                                    @error('budget_expenditure')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" step="0.01"
                                           class="form-control @error('actual_expenditure') is-invalid @enderror"
                                           id="actual_expenditure" name="actual_expenditure"
                                           value="{{ old('actual_expenditure') }}"
                                           oninput="calculateTotalBudget()"
                                           placeholder="Actual Expenditure">
                                    <label for="actual_expenditure">Actual Expenditure</label>
                                    @error('actual_expenditure')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control"
                                           id="expenditure_over_budget" name="expenditure_over_budget"
                                           value="{{ old('expenditure_over_budget') }}"
                                           placeholder="Expenditure over budget" readonly>
                                    <label for="expenditure_over_budget">Expenditure over Budget</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <button type="button" class="btn btn-outline-secondary me-2" onclick="resetForm()">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                                </button>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('backend.note.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-lg me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary submit-btn" onclick="setStatus('D'); showSpinner(this); return false;">
                                    <span class="btn-text"><i class="bi bi-check-lg me-1"></i>Create Green Note</span>
                                    <span class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true" style="display: none;"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .modern-form .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .modern-form .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    }

    .form-floating > .form-control,
    .form-floating > .form-select {
        height: calc(3.5rem + 2px);
        border-radius: 0.5rem;
    }

    .form-floating > label {
        padding: 1rem 0.75rem;
        font-weight: 500;
    }

    .form-floating > .form-control:focus,
    .form-floating > .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    .invalid-feedback {
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px;
        padding-left: 12px;
        padding-right: 30px;
        font-size: 0.875rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 54px;
    }

    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    /* Enhanced card styling */
    .card-title {
        font-size: 1.1rem;
        font-weight: 600;
    }

    .card-title i {
        opacity: 0.8;
    }

    /* Better spacing for form sections */
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }

    /* File input styling */
    .form-control[type="file"] {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .form-floating > .form-control,
        .form-floating > .form-select {
            height: calc(3rem + 2px);
        }

        .form-floating > label {
            padding: 0.75rem 0.5rem;
        }

        .btn {
            font-size: 0.875rem;
        }

        .card-title {
            font-size: 1rem;
        }
    }
</style>
@endpush

@push('script')
<script>
    // Initialize Select2 for enhanced dropdowns
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: function() {
                return $(this).data('placeholder') || 'Select an option';
            }
        });
    });

    // Auto-populate MSME and Activity Type when supplier is selected
    $('#supplier_id').on('change', function() {
        // Correctly get data attributes from select2
        var selectedOption = $(this).select2('data')[0];
        if (selectedOption && selectedOption.element) {
            var msme = $(selectedOption.element).data('msme');
            var activity = $(selectedOption.element).data('activity');

            $('#msme_classification').val(msme || '');
            $('#activity_type').val(activity || '');
        }
    });

    // Calculate total amount
    function calculateTotal() {
        var baseValue = parseFloat($('#base_value').val()) || 0;
        var otherCharges = parseFloat($('#other_charges').val()) || 0;
        var gst = parseFloat($('#gst').val()) || 0;

        var total = baseValue + otherCharges + gst;
        $('#total_amount').val(total.toFixed(2));
    }

    // Calculate invoice total
    function calculateInvoiceTotal() {
        var baseValue = parseFloat($('#invoice_base_value').val()) || 0;
        var gst = parseFloat($('#invoice_gst').val()) || 0;
        var otherCharges = parseFloat($('#invoice_other_charges').val()) || 0;

        var total = baseValue + gst + otherCharges;
        $('#invoice_value').val(total.toFixed(2));

        // Update main invoice fields when multiple invoices are not enabled
        if (!$('#enable_multiple_invoices').is(':checked')) {
            $('input[name="invoice_value"]').val(total.toFixed(2));
            $('input[name="invoice_base_value"]').val(baseValue.toFixed(2));
            $('input[name="invoice_gst"]').val(gst.toFixed(2));
            $('input[name="invoice_other_charges"]').val(otherCharges.toFixed(2));
        }
    }

    // Multiple invoices functionality
    let invoiceEntryCount = 0;

    function addInvoiceEntry() {
        invoiceEntryCount++;

        const invoiceEntryHtml = `
            <div class="invoice-entry border rounded p-3 mb-3" data-entry="${invoiceEntryCount}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Invoice #${invoiceEntryCount}</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeInvoiceEntry(${invoiceEntryCount})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <input type="text" class="form-control form-control-sm"
                               name="invoices[${invoiceEntryCount}][invoice_number]"
                               placeholder="Invoice Number" required>
                    </div>
                    <div class="col-md-6">
                        <input type="date" class="form-control form-control-sm"
                               name="invoices[${invoiceEntryCount}][invoice_date]"
                               required>
                    </div>
                    <div class="col-md-4">
                        <input type="number" step="0.01" class="form-control form-control-sm invoice-base-value"
                               name="invoices[${invoiceEntryCount}][invoice_base_value]"
                               placeholder="Base Value" oninput="calculateMultipleInvoiceTotal()">
                    </div>
                    <div class="col-md-4">
                        <input type="number" step="0.01" class="form-control form-control-sm invoice-gst"
                               name="invoices[${invoiceEntryCount}][invoice_gst]"
                               placeholder="GST" oninput="calculateMultipleInvoiceTotal()">
                    </div>
                    <div class="col-md-4">
                        <input type="number" step="0.01" class="form-control form-control-sm invoice-other-charges"
                               name="invoices[${invoiceEntryCount}][invoice_other_charges]"
                               placeholder="Other Charges" oninput="calculateMultipleInvoiceTotal()">
                    </div>
                    <div class="col-12">
                        <input type="number" step="0.01" class="form-control form-control-sm invoice-total"
                               name="invoices[${invoiceEntryCount}][invoice_value]"
                               placeholder="Total Value" readonly>
                    </div>
                </div>
            </div>
        `;

        $('#invoice_entries_container').append(invoiceEntryHtml);
    }

    function removeInvoiceEntry(entryId) {
        $(`.invoice-entry[data-entry="${entryId}"]`).remove();
        calculateMultipleInvoiceTotal();
    }

    function calculateMultipleInvoiceTotal() {
        let totalValue = 0;
        let totalGST = 0;
        let totalBaseValue = 0;
        let totalOtherCharges = 0;

        $('.invoice-entry').each(function() {
            const baseValue = parseFloat($(this).find('.invoice-base-value').val()) || 0;
            const gst = parseFloat($(this).find('.invoice-gst').val()) || 0;
            const otherCharges = parseFloat($(this).find('.invoice-other-charges').val()) || 0;
            const invoiceTotal = baseValue + gst + otherCharges;

            $(this).find('.invoice-total').val(invoiceTotal.toFixed(2));

            totalBaseValue += baseValue;
            totalGST += gst;
            totalOtherCharges += otherCharges;
            totalValue += invoiceTotal;
        });

        $('#total_invoice_value').val(totalValue.toFixed(2));
        $('#total_invoice_gst').val(totalGST.toFixed(2));

        // Update main invoice fields
        if ($('#enable_multiple_invoices').is(':checked')) {
            $('input[name="invoice_value"]').val(totalValue.toFixed(2));
            $('input[name="invoice_base_value"]').val(totalBaseValue.toFixed(2));
            $('input[name="invoice_gst"]').val(totalGST.toFixed(2));
            $('input[name="invoice_other_charges"]').val(totalOtherCharges.toFixed(2));
        }
    }

    // Toggle multiple invoices section
    $('#enable_multiple_invoices').on('change', function() {
        if ($(this).is(':checked')) {
            $('#multiple_invoices_section').slideDown();
            // Clear main invoice fields when multiple invoices are enabled
            $('#invoice_number').val('');
            $('#invoice_date').val('');
            $('#invoice_base_value').val('');
            $('#invoice_gst').val('');
            $('#invoice_other_charges').val('');
            $('#invoice_value').val('');
        } else {
            $('#multiple_invoices_section').slideUp();
            $('#invoice_entries_container').empty();
            invoiceEntryCount = 0;
        }
    });

    // Calculate budget comparison
    function calculateTotalBudget() {
        var budget = parseFloat($('#budget_expenditure').val()) || 0;
        var actual = parseFloat($('#actual_expenditure').val()) || 0;

        var difference = actual - budget;
        var status = difference > 0 ? 'Over Budget' : (difference < 0 ? 'Under Budget' : 'On Budget');

        $('#expenditure_over_budget').val(difference.toFixed(2) + ' (' + status + ')');
    }

    // Form reset function
    function resetForm() {
        if (confirm('Are you sure you want to reset the form? All data will be lost.')) {
            // Reset form fields
            $('form')[0].reset();

            // Reset Select2 elements
            $('.select2').val('').trigger('change');

            // Reset calculated fields
            $('#total_amount').val('');
            $('#invoice_value').val('');
            $('#expenditure_over_budget').val('');

            // Reset readonly fields
            $('#msme_classification').val('');
            $('#activity_type').val('');
        }
    }

    // Enhanced form validation
    $('form').on('submit', function() {
    var isValid = true;
    var firstInvalidField = null;
    // ... validation code ...
    if (!isValid) {
        if (firstInvalidField) {
            firstInvalidField.focus();
            $('html, body').animate({
                scrollTop: firstInvalidField.offset().top - 100
            }, 500);
        }
        // REMOVE everything from here (line ~793) to just before 'return false;'
        return false;
    }
    return true;
});

        if (!isValid) {
            if (firstInvalidField) {
                firstInvalidField.focus();
                $('html, body').animate({
                    scrollTop: firstInvalidField.offset().top - 100
                }, 500);
                            @csrf
                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                            <div class="col-6">
                                <label for="approval_for" class="form-label">Approval for</label>
                                <select class="form-select form-control" id="approval_for" name="approval_for" required>
                                    <option value="Invoice" {{ old('approval_for') == 'Invoice' ? 'selected' : '' }}>
                                        Invoice</option>
                                    <option value="Advance" {{ old('approval_for') == 'Advance' ? 'selected' : '' }}>
                                        Advance</option>
                                    <option value="Adhoc" {{ old('approval_for') == 'Adhoc' ? 'selected' : '' }}>Adhoc
                                    </option>
                                </select>
                                @error('approval_for')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="vendor_id" class="form-label">Project Name</label>
                                <select class="form-select form-control" id="vendor_id" name="vendor_id" required>
                                    <option value="">Select Project</option>
                                    @foreach ($filteredItems as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('vendor_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->project }}</option>
                                    @endforeach
                                </select>
                                @error('vendor_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>


                            <div class="col-6">
                                <label for="five" class="form-label">User Department</label>
                                <select class="form-select form-control" id="five" name="department_id" required>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}</option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="two" class="form-label">Work/Purchase Order no.</label>
                                <input type="text" class="form-control" name="order_no" id="two" required
                                    value="{{ old('order_no', $orderNumber) }}">
                                @error('order_no')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="one" class="form-label">Work/Purchase Order date</label>
                                <input type="date" class="form-control" name="order_date" id="one" required
                                    value="{{ old('order_date') }}">
                                @error('order_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>


                            <div class="col-6">
                                <label for="four" class="form-label">Amount of Work/Purchase Order</label>
                                <input type="number" placeholder="Base value" name="base_value" class="form-control mb-2"
                                    id="base_value" value="{{ old('base_value') }}" oninput="calculateTotal()" required>
                                @error('base_value')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror

                                <input type="number" class="form-control mb-2" placeholder="Other Charges"
                                    id="other_charges" name="other_charges" value="{{ old('other_charges') }}" required
                                    oninput="calculateTotal()">
                                @error('other_charges')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror

                                <input type="number" placeholder="GST on Above" name="gst" class="form-control mb-2"
                                    id="gst" value="{{ old('gst') }}" oninput="calculateTotal()" required>
                                @error('gst')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror


                                <label for="four" class="form-label mt-2">Total Amount</label>

                                <input type="number" class="form-control mt-1" placeholder="Total Amount" id="total_amount"
                                    name="total_amount" value="{{ old('total_amount') }}" readonly>
                            </div>
                            <div class="col-6">
                                <label for="supplier_id" class="form-label">Name of Supplier</label>
                                <select class="form-select form-control select2" id="supplier_id" name="supplier_id"
                                    required>
                                    <option value="">Select Vendor Name</option>
                                    @foreach ($filteredVendorItems as $item)
                                        <option value="{{ $item->id }}" data-msme="{{ $item->msme_classification }}"
                                            data-activity="{{ $item->activity_type }}"
                                            {{ old('supplier_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->vendor_name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('supplier_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="msme_classification" class="form-label">MSME Classification</label>
                                <input type="text" class="form-control" id="msme_text_input"
                                    name="msme_classification" readonly>

                                {{-- <select class="form-select form-control" id="msme_classification"
                                    name="msme_classification">
                                    <option value="Micro" {{ old('msme_classification') == 'Micro' ? 'selected' : '' }}>
                                        Micro
                                    </option>
                                    <option value="Small" {{ old('msme_classification') == 'Small' ? 'selected' : '' }}>
                                        Small
                                    </option>
                                    <option value="Medium" {{ old('msme_classification') == 'Medium' ? 'selected' : '' }}>
                                        Medium
                                    </option>
                                    <option value="Non MSME"
                                        {{ old('msme_classification') == 'Non MSME' ? 'selected' : '' }}>
                                        Non MSME
                                    </option>
                                </select> --}}
                                @error('msme_classification')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="activity_type" class="form-label">Activity Type</label>
                                <input type="text" class="form-control" id="activity_type" name="activity_type"
                                    readonly>
                                @error('activity_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6" id="msme_div">
                                <label for="protest_note_raised" class="form-label">Protest Note Raised</label>
                                <select class="form-select form-control" id="protest_note_raised"
                                    name="protest_note_raised">
                                    <option value="N">Select</option>
                                    <option value="Y" {{ old('protest_note_raised') == 'Y' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="N" {{ old('protest_note_raised') == 'N' ? 'selected' : '' }}>No
                                    </option>
                                </select>
                                <input type="file" id="file_input_2" name="file_input_2" class="form-control mt-2"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.csv">
                                @error('file_input_2')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                @error('protest_note_raised')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="brief_of_goods_services" class="form-label">Brief of Goods / Services</label>

                                <textarea id="brief_of_goods_services" name="brief_of_goods_services" cols="30" rows="2"
                                    class="form-control">{{ old('brief_of_goods_services') }}</textarea>

                                @error('brief_of_goods_services')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-6">
                                <label for="invoice_number" class="form-label">Invoice Number</label>
                                <input type="text" class="form-control" placeholder="Invoice Number"
                                    id="invoice_number" name="invoice_number" value="{{ old('invoice_number') }}"
                                    required>
                                @error('invoice_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-6">
                                <label for="invoice_date" class="form-label">Invoice Date</label>
                                <input type="date" class="form-control" id="invoice_date" name="invoice_date"
                                    required value="{{ old('invoice_date') }}">
                                @error('invoice_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                {{-- <label for="fourteen" class="form-label">Taxable Value</label> --}}
                                <input type="number" class="form-control  mb-2" placeholder="Taxable Value"
                                    id="invoice_base_value" name="invoice_base_value" required
                                    value="{{ old('invoice_base_value') }}" oninput="calculateInvoiceTotal()">
                                @error('invoice_base_value')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                {{-- <label for="fourteen" class="form-label">Add: GST on above</label> --}}
                                <input type="number" class="form-control  mb-2" placeholder="Add: GST on above"
                                    id="invoice_gst" name="invoice_gst" value="{{ old('invoice_gst') }}"
                                    oninput="calculateInvoiceTotal()">
                                @error('invoice_gst')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror

                                {{-- <label for="invoice_other_charges" class="form-label">Invoice Other Charges</label> --}}
                                <input type="number" class="form-control  mb-2" id="invoice_other_charges"
                                    placeholder="Invoice Other Charges" name="invoice_other_charges"
                                    value="{{ old('invoice_other_charges') }}" oninput="calculateInvoiceTotal()">
                                @error('invoice_other_charges')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <label for="fourteen" class="form-label">Invoice Value</label>
                                <input type="number" class="form-control  mb-2" id="invoice_value" name="invoice_value"
                                    value="{{ old('invoice_value') }}" readonly>
                                @error('invoice_value')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-6">
                                <label for="six" class="form-label">Contract Period</label>

                                <div class="row">
                                    <div class="col-6">

                                        <label for="six" class="form-label">Start Date</label>

                                        <input type="date" class="form-control mb-3" name="contract_start_date"
                                            value="{{ old('contract_start_date') }}">
                                        @error('contract_start_date')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <label for="six" class="form-label">End Date</label>

                                        <input type="date" class="form-control" name="contract_end_date"
                                            value="{{ old('contract_end_date') }}">

                                        @error('contract_end_date')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                            </div>

                            <div class="col-6">
                                <label for="appointed_start_date" class="form-label">Appointed date/Date for start of
                                    work</label>
                                <input type="date" class="form-control" id="appointed_start_date"
                                    name="appointed_start_date" value="{{ old('appointed_start_date') }}">
                                @error('appointed_start_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="fifteen" class="form-label">Period of Supply of services/goods
                                    Invoiced</label>
                                <div class="row">
                                    <div class="col-6">
                                        <label for="six" class="form-label">Start Date</label>

                                        <input type="date" class="form-control  mb-2" id="six"
                                            name="supply_period_start" value="{{ old('supply_period_start') }}">
                                        @error('supply_period_start')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="col-6">
                                        <label for="six2" class="form-label">End Date</label>

                                        <input type="date" class="form-control  mb-2" id="six2"
                                            name="supply_period_end" value="{{ old('supply_period_end') }}">
                                        @error('supply_period_end')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <label for="extension_contract_period" class="form-label">Whether contract period
                                    completed</label>
                                <select class="form-select form-control" id="whether_contract" name="whether_contract">
                                    <option value="Y" {{ old('whether_contract') == 'Y' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="N" {{ old('whether_contract') == 'N' ? 'selected' : '' }}>No
                                    </option>
                                </select>
                                {{-- <input type="file" id="file_input_6" name="file_input_6" class="form-control mt-2"
                                    style="display:none;">
                                @error('file_input_6')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror --}}
                                @error('whether_contract')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6" id="extension_contract_period_show">
                                <label for="extension_contract_period" class="form-label">Extension of contract period
                                    executed</label>
                                <select class="form-select form-control" id="extension_contract_period"
                                    name="extension_contract_period">
                                    <option value="N">Select</option>
                                    <option value="Y"
                                        {{ old('extension_contract_period') == 'Y' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="N"
                                        {{ old('extension_contract_period') == 'N' ? 'selected' : '' }}>No
                                    </option>
                                </select>
                                <input type="file" id="file_input_1" name="file_input_1" class="form-control mt-2"
                                    style="display:none;" accept=".pdf,.doc,.docx,.xls,.xlsx,.csv">
                                @error('file_input_1')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                @error('extension_contract_period')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="delayed_damages" class="form-label">Delayed damages</label>
                                <textarea id="delayed_damages" name="delayed_damages" cols="30" rows="2" class="form-control">{{ old('delayed_damages') }}</textarea>
                                @error('delayed_damages')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>


                            <div class="col-6">
                                <label for="four" class="form-label">Budget Utilisation </label>

                                <input type="number" class="form-control mb-2" placeholder="Budget Expenditure"
                                    id="budget_expenditure" name="budget_expenditure"
                                    value="{{ old('budget_expenditure') }}" oninput="calculateTotalBudget()">
                                @error('budget_expenditure')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror

                                <input type="number" class="form-control mb-2" placeholder="Actual Expenditure "
                                    id="actual_expenditure" name="actual_expenditure"
                                    value="{{ old('actual_expenditure') }}" oninput="calculateTotalBudget()">
                                @error('actual_expenditure')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <input type="text" class="form-control mb-2" placeholder="Expenditure over budget"
                                    id="expenditure_over_budget" name="expenditure_over_budget" readonly
                                    value="{{ old('expenditure_over_budget') }}">
                                @error('expenditure_over_budget')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="nature_of_expenses" class="form-label">Nature of Expenses</label>
                                <select class="form-select form-control select2" id="nature_of_expenses"
                                    name="nature_of_expenses">
                                    <option value="">select an option</option>

                                    {{-- <option value="Initial Improvement Works"
                                        {{ old('nature_of_expenses') == 'Initial Improvement Works' ? 'selected' : '' }}>
                                        Initial Improvement Works
                                    </option>
                                    <option value="O&M Expenses"
                                        {{ old('nature_of_expenses') == 'O&M Expenses' ? 'selected' : '' }}>
                                        O&M Expenses
                                    </option> --}}
                                    <option value="OHC-001 - Manpower (Salaries, Wages, Director's Remuneration)"
                                        {{ old('nature_of_expenses') == 'OHC-001 - Manpower (Salaries, Wages, Director\'s Remuneration)' ? 'selected' : '' }}>
                                        OHC-001 - Manpower (Salaries, Wages, Director's Remuneration)</option>
                                    <option value="OHC-002 - Staff Welfare (Medical, Training, Recreational Activities)"
                                        {{ old('nature_of_expenses') == 'OHC-002 - Staff Welfare (Medical, Training, Recreational Activities)' ? 'selected' : '' }}>
                                        OHC-002 - Staff Welfare (Medical, Training, Recreational Activities)</option>
                                    <option value="OHC-003 - Office Rent & Utilities (Electricity, Water, Maintenance)"
                                        {{ old('nature_of_expenses') == 'OHC-003 - Office Rent & Utilities (Electricity, Water, Maintenance)' ? 'selected' : '' }}>
                                        OHC-003 - Office Rent & Utilities (Electricity, Water, Maintenance)</option>
                                    <option value="OHC-004 - Insurance (Project, Employee, Equipment)"
                                        {{ old('nature_of_expenses') == 'OHC-004 - Insurance (Project, Employee, Equipment)' ? 'selected' : '' }}>
                                        OHC-004 - Insurance (Project, Employee, Equipment)</option>
                                    <option value="OHC-005 - Independent Engineer Fees"
                                        {{ old('nature_of_expenses') == 'OHC-005 - Independent Engineer Fees' ? 'selected' : '' }}>
                                        OHC-005 - Independent Engineer Fees</option>
                                    <option value="OHC-006 - Travelling & Accommodation (Outstation Travel, Hotel Stays)"
                                        {{ old('nature_of_expenses') == 'OHC-006 - Travelling & Accommodation (Outstation Travel, Hotel Stays)' ? 'selected' : '' }}>
                                        OHC-006 - Travelling & Accommodation (Outstation Travel, Hotel Stays)</option>
                                    <option value="OHC-007 - Local Conveyance (Fuel, Taxi, Transport)"
                                        {{ old('nature_of_expenses') == 'OHC-007 - Local Conveyance (Fuel, Taxi, Transport)' ? 'selected' : '' }}>
                                        OHC-007 - Local Conveyance (Fuel, Taxi, Transport)</option>
                                    <option value="OHC-008 - Stationery, Printing & Courier"
                                        {{ old('nature_of_expenses') == 'OHC-008 - Stationery, Printing & Courier' ? 'selected' : '' }}>
                                        OHC-008 - Stationery, Printing & Courier</option>
                                    <option value="OHC-009 - Audit Fees (Statutory & Internal Audit)"
                                        {{ old('nature_of_expenses') == 'OHC-009 - Audit Fees (Statutory & Internal Audit)' ? 'selected' : '' }}>
                                        OHC-009 - Audit Fees (Statutory & Internal Audit)</option>
                                    <option value="OHC-010 - Professional Fees (Advisory, Legal, Compliance, Filings)"
                                        {{ old('nature_of_expenses') == 'OHC-010 - Professional Fees (Advisory, Legal, Compliance, Filings)' ? 'selected' : '' }}>
                                        OHC-010 - Professional Fees (Advisory, Legal, Compliance, Filings)</option>
                                    <option value="OHC-011 - Communication & IT (Telephone, Internet, Data Management)"
                                        {{ old('nature_of_expenses') == 'OHC-011 - Communication & IT (Telephone, Internet, Data Management)' ? 'selected' : '' }}>
                                        OHC-011 - Communication & IT (Telephone, Internet, Data Management)</option>
                                    <option value="OHC-012 - Advertisement & Publicity (Toll Fee & General Awareness)"
                                        {{ old('nature_of_expenses') == 'OHC-012 - Advertisement & Publicity (Toll Fee & General Awareness)' ? 'selected' : '' }}>
                                        OHC-012 - Advertisement & Publicity (Toll Fee & General Awareness)</option>
                                    <option value="OHC-013 - Environmental, Health & Safety (EHS) Expenses"
                                        {{ old('nature_of_expenses') == 'OHC-013 - Environmental, Health & Safety (EHS) Expenses' ? 'selected' : '' }}>
                                        OHC-013 - Environmental, Health & Safety (EHS) Expenses</option>
                                    <option value="OHC-014 - General Repair & Maintenance (Office, Equipment)"
                                        {{ old('nature_of_expenses') == 'OHC-014 - General Repair & Maintenance (Office, Equipment)' ? 'selected' : '' }}>
                                        OHC-014 - General Repair & Maintenance (Office, Equipment)</option>
                                    <option value="OHC-015 - Hiring Charges (Vehicles, Equipment, Office Assets) - at HO"
                                        {{ old('nature_of_expenses') == 'OHC-015 - Hiring Charges (Vehicles, Equipment, Office Assets) - at HO' ? 'selected' : '' }}>
                                        OHC-015 - Hiring Charges (Vehicles, Equipment, Office Assets) - at HO</option>
                                    <option
                                        value="OHC-016 - Hiring Charges (Vehicles, Equipment, Office Assets) - at Projects"
                                        {{ old('nature_of_expenses') == 'OHC-016 - Hiring Charges (Vehicles, Equipment, Office Assets) - at Projects' ? 'selected' : '' }}>
                                        OHC-016 - Hiring Charges (Vehicles, Equipment, Office Assets) - at Projects</option>
                                    <option value="OHC-017 - Rates & Taxes (Excluding Office Rent)"
                                        {{ old('nature_of_expenses') == 'OHC-017 - Rates & Taxes (Excluding Office Rent)' ? 'selected' : '' }}>
                                        OHC-017 - Rates & Taxes (Excluding Office Rent)</option>
                                    <option value="OHC-018 - Bank Charges & Financial Fees"
                                        {{ old('nature_of_expenses') == 'OHC-018 - Bank Charges & Financial Fees' ? 'selected' : '' }}>
                                        OHC-018 - Bank Charges & Financial Fees</option>
                                    <option value="OHC-019 - Survey & Investigation (Land, Traffic, Geotechnical)"
                                        {{ old('nature_of_expenses') == 'OHC-019 - Survey & Investigation (Land, Traffic, Geotechnical)' ? 'selected' : '' }}>
                                        OHC-019 - Survey & Investigation (Land, Traffic, Geotechnical)</option>
                                    <option value="OHC-020 - Community Engagement Programs"
                                        {{ old('nature_of_expenses') == 'OHC-020 - Community Engagement Programs' ? 'selected' : '' }}>
                                        OHC-020 - Community Engagement Programs</option>
                                    <option value="OHC-021 - Director Strategy Meeting Expenses"
                                        {{ old('nature_of_expenses') == 'OHC-021 - Director Strategy Meeting Expenses' ? 'selected' : '' }}>
                                        OHC-021 - Director Strategy Meeting Expenses</option>
                                    <option value="OHC-022 - Site Upgradation Cost"
                                        {{ old('nature_of_expenses') == 'OHC-022 - Site Upgradation Cost' ? 'selected' : '' }}>
                                        OHC-022 - Site Upgradation Cost</option>
                                    <option value="OHC-023 - IT Hardware & Software Expenses"
                                        {{ old('nature_of_expenses') == 'OHC-023 - IT Hardware & Software Expenses' ? 'selected' : '' }}>
                                        OHC-023 - IT Hardware & Software Expenses</option>
                                    <option value="OHC-024 - Guest House charges"
                                        {{ old('nature_of_expenses') == 'OHC-024 - Guest House charges' ? 'selected' : '' }}>
                                        OHC-024 - Guest House charges</option>
                                    <option value="OHC-025 - Staff Accommodation & Facilities - at Projects"
                                        {{ old('nature_of_expenses') == 'OHC-025 - Staff Accommodation & Facilities - at Projects' ? 'selected' : '' }}>
                                        OHC-025 - Staff Accommodation & Facilities - at Projects</option>
                                    <option value="OHC-026 - Competency building and Team development"
                                        {{ old('nature_of_expenses') == 'OHC-026 - Competency building and Team development' ? 'selected' : '' }}>
                                        OHC-026 - Competency building and Team development</option>
                                    <option value="OHC-027 - Corporate Communication"
                                        {{ old('nature_of_expenses') == 'OHC-027 - Corporate Communication' ? 'selected' : '' }}>
                                        OHC-027 - Corporate Communication</option>
                                    <option value="OHC-028 - Miscellaneous & Items Not Covered Above"
                                        {{ old('nature_of_expenses') == 'OHC-028 - Miscellaneous & Items Not Covered Above' ? 'selected' : '' }}>
                                        OHC-028 - Miscellaneous & Items Not Covered Above</option>
                                    <option value="OHCC-001 - Manpower Supply Consultancies"
                                        {{ old('nature_of_expenses') == 'OHCC-001 - Manpower Supply Consultancies' ? 'selected' : '' }}>
                                        OHCC-001 - Manpower Supply Consultancies</option>
                                    <option value="OHCC-002 - Technical Due Diligence (TDD)"
                                        {{ old('nature_of_expenses') == 'OHCC-002 - Technical Due Diligence (TDD)' ? 'selected' : '' }}>
                                        OHCC-002 - Technical Due Diligence (TDD)</option>
                                    <option value="OHCC-003 - Quality Control and Testing Consultancies"
                                        {{ old('nature_of_expenses') == 'OHCC-003 - Quality Control and Testing Consultancies' ? 'selected' : '' }}>
                                        OHCC-003 - Quality Control and Testing Consultancies</option>
                                    <option value="OHCC-004 - Environmental and Safety Consultancies"
                                        {{ old('nature_of_expenses') == 'OHCC-004 - Environmental and Safety Consultancies' ? 'selected' : '' }}>
                                        OHCC-004 - Environmental and Safety Consultancies</option>
                                    <option value="OHCC-005 - Design and Engineering Consultancies"
                                        {{ old('nature_of_expenses') == 'OHCC-005 - Design and Engineering Consultancies' ? 'selected' : '' }}>
                                        OHCC-005 - Design and Engineering Consultancies</option>
                                    <option value="OHCC-006 - Traffic and Transportation Management Consultancies"
                                        {{ old('nature_of_expenses') == 'OHCC-006 - Traffic and Transportation Management Consultancies' ? 'selected' : '' }}>
                                        OHCC-006 - Traffic and Transportation Management Consultancies</option>
                                    <option value="OHCC-007 - IT and MIS Support Consultancies"
                                        {{ old('nature_of_expenses') == 'OHCC-007 - IT and MIS Support Consultancies' ? 'selected' : '' }}>
                                        OHCC-007 - IT and MIS Support Consultancies</option>
                                    <option value="OHCC-008 - Financial and Legal Advisory Consultancies"
                                        {{ old('nature_of_expenses') == 'OHCC-008 - Financial and Legal Advisory Consultancies' ? 'selected' : '' }}>
                                        OHCC-008 - Financial and Legal Advisory Consultancies</option>
                                    <option value="OHCC-009 - General Consultancy Services"
                                        {{ old('nature_of_expenses') == 'OHCC-009 - General Consultancy Services' ? 'selected' : '' }}>
                                        OHCC-009 - General Consultancy Services</option>

                                    <option value="PMC-001 - Purchase of Equipment"
                                        {{ old('nature_of_expenses') == 'PMC-001 - Purchase of Equipment' ? 'selected' : '' }}>
                                        PMC-001 - Purchase of Equipment</option>
                                    <option value="PMC-002 - Hiring & Leasing of Equipment"
                                        {{ old('nature_of_expenses') == 'PMC-002 - Hiring & Leasing of Equipment' ? 'selected' : '' }}>
                                        PMC-002 - Hiring & Leasing of Equipment</option>
                                    <option value="PMC-003 - Fuel & Lubricants"
                                        {{ old('nature_of_expenses') == 'PMC-003 - Fuel & Lubricants' ? 'selected' : '' }}>
                                        PMC-003 - Fuel & Lubricants</option>
                                    <option value="PMC-004 - Repair & Maintenance of Equipment"
                                        {{ old('nature_of_expenses') == 'PMC-004 - Repair & Maintenance of Equipment' ? 'selected' : '' }}>
                                        PMC-004 - Repair & Maintenance of Equipment</option>
                                    <option value="PMC-005 - Depreciation & Spares"
                                        {{ old('nature_of_expenses') == 'PMC-005 - Depreciation & Spares' ? 'selected' : '' }}>
                                        PMC-005 - Depreciation & Spares</option>
                                    <option value="PMC-006 - Mobilization & Demobilization"
                                        {{ old('nature_of_expenses') == 'PMC-006 - Mobilization & Demobilization' ? 'selected' : '' }}>
                                        PMC-006 - Mobilization & Demobilization</option>
                                    <option value="PMC-007 - Operator Charges"
                                        {{ old('nature_of_expenses') == 'PMC-007 - Operator Charges' ? 'selected' : '' }}>
                                        PMC-007 - Operator Charges</option>



                                    <option value="DCCC-001 - Site Preparation (Schd-B activity)"
                                        {{ old('nature_of_expenses') == 'DCCC-001 - Site Preparation (Schd-B activity)' ? 'selected' : '' }}>
                                        DCCC-001 - Site Preparation (Schd-B activity)</option>
                                    <option value="DCCC-002 - Site Enabling Works (Schd-B activity)"
                                        {{ old('nature_of_expenses') == 'DCCC-002 - Site Enabling Works (Schd-B activity)' ? 'selected' : '' }}>
                                        DCCC-002 - Site Enabling Works (Schd-B activity)</option>
                                    <option value="DCCC-003 - Clearing & Grubbing, Milling & Dismantling (Schd-B activity)"
                                        {{ old('nature_of_expenses') == 'DCCC-003 - Clearing & Grubbing, Milling & Dismantling (Schd-B activity)' ? 'selected' : '' }}>
                                        DCCC-003 - Clearing & Grubbing, Milling & Dismantling (Schd-B activity)</option>
                                    <option
                                        value="DCCC-004 - Earthwork (Cutting, Filling, Embankment, Subgrade) (Schd-B activity)"
                                        {{ old('nature_of_expenses') == 'DCCC-004 - Earthwork (Cutting, Filling, Embankment, Subgrade) (Schd-B activity)' ? 'selected' : '' }}>
                                        DCCC-004 - Earthwork (Cutting, Filling, Embankment, Subgrade) (Schd-B activity)
                                    </option>
                                    <option value="DCCC-005 - Granular & WMM Layers (Schd-B activity)"
                                        {{ old('nature_of_expenses') == 'DCCC-005 - Granular & WMM Layers (Schd-B activity)' ? 'selected' : '' }}>
                                        DCCC-005 - Granular & WMM Layers (Schd-B activity)</option>
                                    <option
                                        value="DCCC-006 - Bituminous Layers (DBM, BC, SMA) - Flexible pavement (Schd-B activity)"
                                        {{ old('nature_of_expenses') == 'DCCC-006 - Bituminous Layers (DBM, BC, SMA) - Flexible pavement (Schd-B activity)' ? 'selected' : '' }}>
                                        DCCC-006 - Bituminous Layers (DBM, BC, SMA) - Flexible pavement (Schd-B activity)
                                    </option>
                                    <option
                                        value="DCCC-007 - Concrete Pavement (PQC, DLC) - Rigid pavement (Schd-B activity)"
                                        {{ old('nature_of_expenses') == 'DCCC-007 - Concrete Pavement (PQC, DLC) - Rigid pavement (Schd-B activity)' ? 'selected' : '' }}>
                                        DCCC-007 - Concrete Pavement (PQC, DLC) - Rigid pavement (Schd-B activity)</option>
                                    <option value="DCCC-008 - Drainage & Cross-Drainage Works (Schd-B activity)"
                                        {{ old('nature_of_expenses') == 'DCCC-008 - Drainage & Cross-Drainage Works (Schd-B activity)' ? 'selected' : '' }}>
                                        DCCC-008 - Drainage & Cross-Drainage Works (Schd-B activity)</option>
                                    <option value="DCCC-009 - Retaining Structures (RE Walls, Gabions) (Schd-B activity)"
                                        {{ old('nature_of_expenses') == 'DCCC-009 - Retaining Structures (RE Walls, Gabions) (Schd-B activity)' ? 'selected' : '' }}>
                                        DCCC-009 - Retaining Structures (RE Walls, Gabions) (Schd-B activity)</option>
                                    <option value="DCCC-010 - Bridge & Culverts (Schd-B activity)"
                                        {{ old('nature_of_expenses') == 'DCCC-010 - Bridge & Culverts (Schd-B activity)' ? 'selected' : '' }}>
                                        DCCC-010 - Bridge & Culverts (Schd-B activity)</option>
                                    <option value="DCCC-011 - Road Marking & Surface Treatments (Schd-B activity)"
                                        {{ old('nature_of_expenses') == 'DCCC-011 - Road Marking & Surface Treatments (Schd-B activity)' ? 'selected' : '' }}>
                                        DCCC-011 - Road Marking & Surface Treatments (Schd-B activity)</option>
                                    <option value="DCCC-012 - Rain Water Harvesting Pits (Schd-B activity)"
                                        {{ old('nature_of_expenses') == 'DCCC-012 - Rain Water Harvesting Pits (Schd-B activity)' ? 'selected' : '' }}>
                                        DCCC-012 - Rain Water Harvesting Pits (Schd-B activity)</option>
                                    <option value="DCCC-013 - Medians (Schd-B activity)"
                                        {{ old('nature_of_expenses') == 'DCCC-013 - Medians (Schd-B activity)' ? 'selected' : '' }}>
                                        DCCC-013 - Medians (Schd-B activity)</option>
                                    <option value="DCCC-014 - Miscellaneous Civil Works (Schd-B activity)"
                                        {{ old('nature_of_expenses') == 'DCCC-014 - Miscellaneous Civil Works (Schd-B activity)' ? 'selected' : '' }}>
                                        DCCC-014 - Miscellaneous Civil Works (Schd-B activity)</option>
                                    <option value="DCCO-001 - Site Maintenance & Grading (Periodic Maintenance)"
                                        {{ old('nature_of_expenses') == 'DCCO-001 - Site Maintenance & Grading (Periodic Maintenance)' ? 'selected' : '' }}>
                                        DCCO-001 - Site Maintenance & Grading (Periodic Maintenance)</option>
                                    <option value="DCCO-002 - Temporary Facilities Maintenance (Periodic Maintenance)"
                                        {{ old('nature_of_expenses') == 'DCCO-002 - Temporary Facilities Maintenance (Periodic Maintenance)' ? 'selected' : '' }}>
                                        DCCO-002 - Temporary Facilities Maintenance (Periodic Maintenance)</option>
                                    <option value="DCCO-003 - Earthwork Repairs & Maintenance (Periodic Maintenance)"
                                        {{ old('nature_of_expenses') == 'DCCO-003 - Earthwork Repairs & Maintenance (Periodic Maintenance)' ? 'selected' : '' }}>
                                        DCCO-003 - Earthwork Repairs & Maintenance (Periodic Maintenance)</option>
                                    <option value="DCCO-004 - Base Layer Repairs & Recompaction (Periodic Maintenance)"
                                        {{ old('nature_of_expenses') == 'DCCO-004 - Base Layer Repairs & Recompaction (Periodic Maintenance)' ? 'selected' : '' }}>
                                        DCCO-004 - Base Layer Repairs & Recompaction (Periodic Maintenance)</option>
                                    <option
                                        value="DCCO-005 - Bituminous Layer Maintenance (Resurfacing, Patchwork) (Periodic Maintenance)"
                                        {{ old('nature_of_expenses') == 'DCCO-005 - Bituminous Layer Maintenance (Resurfacing, Patchwork) (Periodic Maintenance)' ? 'selected' : '' }}>
                                        DCCO-005 - Bituminous Layer Maintenance (Resurfacing, Patchwork) (Periodic
                                        Maintenance)</option>
                                    <option value="DCCO-006 - Concrete Pavement Repairs (Periodic Maintenance)"
                                        {{ old('nature_of_expenses') == 'DCCO-006 - Concrete Pavement Repairs (Periodic Maintenance)' ? 'selected' : '' }}>
                                        DCCO-006 - Concrete Pavement Repairs (Periodic Maintenance)</option>
                                    <option
                                        value="DCCO-007 - Drainage System Overhaul & Major Repairs (Periodic Maintenance)"
                                        {{ old('nature_of_expenses') == 'DCCO-007 - Drainage System Overhaul & Major Repairs (Periodic Maintenance)' ? 'selected' : '' }}>
                                        DCCO-007 - Drainage System Overhaul & Major Repairs (Periodic Maintenance)</option>
                                    <option
                                        value="DCCO-008 - Retaining Wall & Gabion Structural Repairs (Periodic Maintenance)"
                                        {{ old('nature_of_expenses') == 'DCCO-008 - Retaining Wall & Gabion Structural Repairs (Periodic Maintenance)' ? 'selected' : '' }}>
                                        DCCO-008 - Retaining Wall & Gabion Structural Repairs (Periodic Maintenance)
                                    </option>
                                    <option
                                        value="DCCO-009 - Bridge & Culvert Repairs & Maintenance (Periodic Maintenance)"
                                        {{ old('nature_of_expenses') == 'DCCO-009 - Bridge & Culvert Repairs & Maintenance (Periodic Maintenance)' ? 'selected' : '' }}>
                                        DCCO-009 - Bridge & Culvert Repairs & Maintenance (Periodic Maintenance)</option>
                                    <option value="DCCO-010 - Minor Civil Works & Repairs (Periodic Maintenance)"
                                        {{ old('nature_of_expenses') == 'DCCO-010 - Minor Civil Works & Repairs (Periodic Maintenance)' ? 'selected' : '' }}>
                                        DCCO-010 - Minor Civil Works & Repairs (Periodic Maintenance)</option>

                                    <option value="DCCO-011 - Shoulder & Median Maintenance (Periodic Maintenance)"
                                        {{ old('nature_of_expenses') == 'DCCO-011 - Shoulder & Median Maintenance (Periodic Maintenance)' ? 'selected' : '' }}>
                                        DCCO-011 - Shoulder & Median Maintenance (Periodic Maintenance)
                                    </option>
                                    <option value="DCCO-012 - Roadside Emergency Response (Periodic Maintenance)"
                                        {{ old('nature_of_expenses') == 'DCCO-012 - Roadside Emergency Response (Periodic Maintenance)' ? 'selected' : '' }}>
                                        DCCO-012 - Roadside Emergency Response (Periodic Maintenance)
                                    </option>
                                    <option value="DCCO-013 - Tunnel & Underpass Maintenance (Periodic Maintenance)"
                                        {{ old('nature_of_expenses') == 'DCCO-013 - Tunnel & Underpass Maintenance (Periodic Maintenance)' ? 'selected' : '' }}>
                                        DCCO-013 - Tunnel & Underpass Maintenance (Periodic Maintenance)
                                    </option>
                                    <option value="DCCO-014 - Miscellaneous Minor Works (Periodic Maintenance)"
                                        {{ old('nature_of_expenses') == 'DCCO-014 - Miscellaneous Minor Works (Periodic Maintenance)' ? 'selected' : '' }}>
                                        DCCO-014 - Miscellaneous Minor Works (Periodic Maintenance)
                                    </option>
                                    <option value="DCEC-001 - HT & LT Cabling Works (Supply & Installation) (Capex)"
                                        {{ old('nature_of_expenses') == 'DCEC-001 - HT & LT Cabling Works (Supply & Installation) (Capex)' ? 'selected' : '' }}>
                                        DCEC-001 - HT & LT Cabling Works (Supply & Installation) (Capex)
                                    </option>
                                    <option value="DCEC-002 - Street Lighting & Poles (Supply & Installation) (Capex)"
                                        {{ old('nature_of_expenses') == 'DCEC-002 - Street Lighting & Poles (Supply & Installation) (Capex)' ? 'selected' : '' }}>
                                        DCEC-002 - Street Lighting & Poles (Supply & Installation) (Capex)
                                    </option>
                                    <option value="DCEC-003 - Earthing System (Capex)"
                                        {{ old('nature_of_expenses') == 'DCEC-003 - Earthing System (Capex)' ? 'selected' : '' }}>
                                        DCEC-003 - Earthing System (Capex)
                                    </option>
                                    <option value="DCEC-004 - Power Supply & Transformers (Capex)"
                                        {{ old('nature_of_expenses') == 'DCEC-004 - Power Supply & Transformers (Capex)' ? 'selected' : '' }}>
                                        DCEC-004 - Power Supply & Transformers (Capex)
                                    </option>
                                    <option value="DCEC-005 - Roadside Electrical Panels & Automation (Capex)"
                                        {{ old('nature_of_expenses') == 'DCEC-005 - Roadside Electrical Panels & Automation (Capex)' ? 'selected' : '' }}>
                                        DCEC-005 - Roadside Electrical Panels & Automation (Capex)
                                    </option>
                                    <option value="DCEC-006 - HPSV to LED Conversion (Capex)"
                                        {{ old('nature_of_expenses') == 'DCEC-006 - HPSV to LED Conversion (Capex)' ? 'selected' : '' }}>
                                        DCEC-006 - HPSV to LED Conversion (Capex)
                                    </option>
                                    <option value="DCEC-007 - Solar Power System Installation (Capex)"
                                        {{ old('nature_of_expenses') == 'DCEC-007 - Solar Power System Installation (Capex)' ? 'selected' : '' }}>
                                        DCEC-007 - Solar Power System Installation (Capex)
                                    </option>
                                    <option value="DCEC-008 - Public EV Charging Station (Capex)"
                                        {{ old('nature_of_expenses') == 'DCEC-008 - Public EV Charging Station (Capex)' ? 'selected' : '' }}>
                                        DCEC-008 - Public EV Charging Station (Capex)
                                    </option>
                                    <option value="DCEC-009 - DG Set Supply & Installation (Capex)"
                                        {{ old('nature_of_expenses') == 'DCEC-009 - DG Set Supply & Installation (Capex)' ? 'selected' : '' }}>
                                        DCEC-009 - DG Set Supply & Installation (Capex)
                                    </option>
                                    <option value="DCEC-010 - Electrical & Electronics Appliances (Capex)"
                                        {{ old('nature_of_expenses') == 'DCEC-010 - Electrical & Electronics Appliances (Capex)' ? 'selected' : '' }}>
                                        DCEC-010 - Electrical & Electronics Appliances (Capex)
                                    </option>
                                    <option value="DCEC-011 - Electrical Spares - First time purchase (Capex)"
                                        {{ old('nature_of_expenses') == 'DCEC-011 - Electrical Spares - First time purchase (Capex)' ? 'selected' : '' }}>
                                        DCEC-011 - Electrical Spares - First time purchase (Capex)
                                    </option>
                                    <option value="DCEC-012 - SCADA Integration & Electrical Automation (Capex)"
                                        {{ old('nature_of_expenses') == 'DCEC-012 - SCADA Integration & Electrical Automation (Capex)' ? 'selected' : '' }}>
                                        DCEC-012 - SCADA Integration & Electrical Automation (Capex)
                                    </option>
                                    <option value="DCEC-013 - Miscellaneous Electrical (Capex)"
                                        {{ old('nature_of_expenses') == 'DCEC-013 - Miscellaneous Electrical (Capex)' ? 'selected' : '' }}>
                                        DCEC-013 - Miscellaneous Electrical (Capex)
                                    </option>
                                    <option value="DCEO-001 - HT & LT Cabling Works (Maintenance & Repairs) (Opex)"
                                        {{ old('nature_of_expenses') == 'DCEO-001 - HT & LT Cabling Works (Maintenance & Repairs) (Opex)' ? 'selected' : '' }}>
                                        DCEO-001 - HT & LT Cabling Works (Maintenance & Repairs) (Opex)
                                    </option>
                                    <option value="DCEO-002 - Street Lighting & Poles (Maintenance & Repairs) (Opex)"
                                        {{ old('nature_of_expenses') == 'DCEO-002 - Street Lighting & Poles (Maintenance & Repairs) (Opex)' ? 'selected' : '' }}>
                                        DCEO-002 - Street Lighting & Poles (Maintenance & Repairs) (Opex)
                                    </option>
                                    <option value="DCEO-003 - Earthing System (Maintenance & Testing) (Opex)"
                                        {{ old('nature_of_expenses') == 'DCEO-003 - Earthing System (Maintenance & Testing) (Opex)' ? 'selected' : '' }}>
                                        DCEO-003 - Earthing System (Maintenance & Testing) (Opex)
                                    </option>
                                    <option value="DCEO-004 - Power Supply & Transformers (Maintenance & Repairs) (Opex)"
                                        {{ old('nature_of_expenses') == 'DCEO-004 - Power Supply & Transformers (Maintenance & Repairs) (Opex)' ? 'selected' : '' }}>
                                        DCEO-004 - Power Supply & Transformers (Maintenance & Repairs) (Opex)
                                    </option>
                                    <option value="DCEO-005 - Roadside Electrical Panels & Automation (Maintenance) (Opex)"
                                        {{ old('nature_of_expenses') == 'DCEO-005 - Roadside Electrical Panels & Automation (Maintenance) (Opex)' ? 'selected' : '' }}>
                                        DCEO-005 - Roadside Electrical Panels & Automation (Maintenance) (Opex)
                                    </option>
                                    <option value="DCEO-006 - LED Street Light Maintenance (Opex)"
                                        {{ old('nature_of_expenses') == 'DCEO-006 - LED Street Light Maintenance (Opex)' ? 'selected' : '' }}>
                                        DCEO-006 - LED Street Light Maintenance (Opex)
                                    </option>
                                    <option value="DCEO-007 - Solar Power System Maintenance (Opex)"
                                        {{ old('nature_of_expenses') == 'DCEO-007 - Solar Power System Maintenance (Opex)' ? 'selected' : '' }}>
                                        DCEO-007 - Solar Power System Maintenance (Opex)
                                    </option>
                                    <option value="DCEO-008 - EV Charging Station Maintenance (Opex)"
                                        {{ old('nature_of_expenses') == 'DCEO-008 - EV Charging Station Maintenance (Opex)' ? 'selected' : '' }}>
                                        DCEO-008 - EV Charging Station Maintenance (Opex)
                                    </option>
                                    <option value="DCEO-009 - DG Set Maintenance (Fuel, Lubricants & Servicing) (Opex)"
                                        {{ old('nature_of_expenses') == 'DCEO-009 - DG Set Maintenance (Fuel, Lubricants & Servicing) (Opex)' ? 'selected' : '' }}>
                                        DCEO-009 - DG Set Maintenance (Fuel, Lubricants & Servicing) (Opex)
                                    </option>
                                    <option value="DCEO-010 - Electrical & Electronics Appliances (Opex)"
                                        {{ old('nature_of_expenses') == 'DCEO-010 - Electrical & Electronics Appliances (Opex)' ? 'selected' : '' }}>
                                        DCEO-010 - Electrical & Electronics Appliances (Opex)
                                    </option>
                                    <option value="DCEO-011 - Electrical Spares - Regular Maintenance (Opex)"
                                        {{ old('nature_of_expenses') == 'DCEO-011 - Electrical Spares - Regular Maintenance (Opex)' ? 'selected' : '' }}>
                                        DCEO-011 - Electrical Spares - Regular Maintenance (Opex)
                                    </option>
                                    <option value="DCEO-012 - SCADA Integration & Electrical Automation Maintenance (Opex)"
                                        {{ old('nature_of_expenses') == 'DCEO-012 - SCADA Integration & Electrical Automation Maintenance (Opex)' ? 'selected' : '' }}>
                                        DCEO-012 - SCADA Integration & Electrical Automation Maintenance (Opex)
                                    </option>
                                    <option value="DCEO-013 - Miscellaneous Electrical  (Opex)"
                                        {{ old('nature_of_expenses') == 'DCEO-013 - Miscellaneous Electrical  (Opex)' ? 'selected' : '' }}>
                                        DCEO-013 - Miscellaneous Electrical (Opex)
                                    </option>

                                    <option value="ITS-001 - Smart Traffic & ITS Systems (Capex)"
                                        {{ old('nature_of_expenses') == 'ITS-001 - Smart Traffic & ITS Systems (Capex)' ? 'selected' : '' }}>
                                        ITS-001 - Smart Traffic & ITS Systems (Capex)
                                    </option>
                                    <option value="ITS-002 - Communication Systems (CCTV, PA, Wireless) (Capex)"
                                        {{ old('nature_of_expenses') == 'ITS-002 - Communication Systems (CCTV, PA, Wireless) (Capex)' ? 'selected' : '' }}>
                                        ITS-002 - Communication Systems (CCTV, PA, Wireless) (Capex)
                                    </option>
                                    <option value="ITS-003 - ATMS Procurement & Deployment (Capex)"
                                        {{ old('nature_of_expenses') == 'ITS-003 - ATMS Procurement & Deployment (Capex)' ? 'selected' : '' }}>
                                        ITS-003 - ATMS Procurement & Deployment (Capex)
                                    </option>
                                    <option value="ITS-004 - TMS (Toll Management System) Procurement (Capex)"
                                        {{ old('nature_of_expenses') == 'ITS-004 - TMS (Toll Management System) Procurement (Capex)' ? 'selected' : '' }}>
                                        ITS-004 - TMS (Toll Management System) Procurement (Capex)
                                    </option>
                                    <option value="ITS-005 - Weigh-in-Motion (Capex)"
                                        {{ old('nature_of_expenses') == 'ITS-005 - Weigh-in-Motion (Capex)' ? 'selected' : '' }}>
                                        ITS-005 - Weigh-in-Motion (Capex)
                                    </option>
                                    <option value="ITS-006 - Static Weigh Bridge (including civil works) (Capex)"
                                        {{ old('nature_of_expenses') == 'ITS-006 - Static Weigh Bridge (including civil works) (Capex)' ? 'selected' : '' }}>
                                        ITS-006 - Static Weigh Bridge (including civil works) (Capex)
                                    </option>
                                    <option value="DSE-001 - Environmental & Safety Compliance"
                                        {{ old('nature_of_expenses') == 'DSE-001 - Environmental & Safety Compliance' ? 'selected' : '' }}>
                                        DSE-001 - Environmental & Safety Compliance
                                    </option>
                                    <option value="DSE-002 - Traffic Cones & Safety Barriers"
                                        {{ old('nature_of_expenses') == 'DSE-002 - Traffic Cones & Safety Barriers' ? 'selected' : '' }}>
                                        DSE-002 - Traffic Cones & Safety Barriers
                                    </option>
                                    <option value="DSE-003 - Sustainability program"
                                        {{ old('nature_of_expenses') == 'DSE-003 - Sustainability program' ? 'selected' : '' }}>
                                        DSE-003 - Sustainability program
                                    </option>
                                    <option value="DSE-004 - Road Safety Aids"
                                        {{ old('nature_of_expenses') == 'DSE-004 - Road Safety Aids' ? 'selected' : '' }}>
                                        DSE-004 - Road Safety Aids
                                    </option>
                                    <option value="DSE-005 - Personal protective Equipment"
                                        {{ old('nature_of_expenses') == 'DSE-005 - Personal protective Equipment' ? 'selected' : '' }}>
                                        DSE-005 - Personal protective Equipment
                                    </option>
                                    <option value="DSE-006 - Waste Disposal & Site Cleanup"
                                        {{ old('nature_of_expenses') == 'DSE-006 - Waste Disposal & Site Cleanup' ? 'selected' : '' }}>
                                        DSE-006 - Waste Disposal & Site Cleanup
                                    </option>
                                    <option value="DCS-001 - Project Management Consultancies (PMC)"
                                        {{ old('nature_of_expenses') == 'DCS-001 - Project Management Consultancies (PMC)' ? 'selected' : '' }}>
                                        DCS-001 - Project Management Consultancies (PMC)
                                    </option>
                                    <option value="DCS-002 - Asset Monitoring & Performance – Road"
                                        {{ old('nature_of_expenses') == 'DCS-002 - Asset Monitoring & Performance – Road' ? 'selected' : '' }}>
                                        DCS-002 - Asset Monitoring & Performance – Road
                                    </option>
                                    <option value="DCS-003 - Asset Monitoring & Performance – Road Asset Furniture"
                                        {{ old('nature_of_expenses') == 'DCS-003 - Asset Monitoring & Performance – Road Asset Furniture' ? 'selected' : '' }}>
                                        DCS-003 - Asset Monitoring & Performance – Road Asset Furniture
                                    </option>
                                    <option value="DCS-004 - Asset Monitoring & Performance – Structure"
                                        {{ old('nature_of_expenses') == 'DCS-004 - Asset Monitoring & Performance – Structure' ? 'selected' : '' }}>
                                        DCS-004 - Asset Monitoring & Performance – Structure
                                    </option>
                                    <option value="DCS-005 - Revalidation Survey (Traffic, Revenue & Condition Assessment)"
                                        {{ old('nature_of_expenses') == 'DCS-005 - Revalidation Survey (Traffic, Revenue & Condition Assessment)' ? 'selected' : '' }}>
                                        DCS-005 - Revalidation Survey (Traffic, Revenue & Condition Assessment)
                                    </option>
                                    <option value="DCP-001 - Bitumen (VG30, VG40, PMB, Emulsion)"
                                        {{ old('nature_of_expenses') == 'DCP-001 - Bitumen (VG30, VG40, PMB, Emulsion)' ? 'selected' : '' }}>
                                        DCP-001 - Bitumen (VG30, VG40, PMB, Emulsion)
                                    </option>
                                    <option value="DCP-002 - Cement (OPC, PPC, Fly Ash Based)"
                                        {{ old('nature_of_expenses') == 'DCP-002 - Cement (OPC, PPC, Fly Ash Based)' ? 'selected' : '' }}>
                                        DCP-002 - Cement (OPC, PPC, Fly Ash Based)
                                    </option>
                                    <option value="DCP-003 - Structural Steel (TMT, Plates, Girders)"
                                        {{ old('nature_of_expenses') == 'DCP-003 - Structural Steel (TMT, Plates, Girders)' ? 'selected' : '' }}>
                                        DCP-003 - Structural Steel (TMT, Plates, Girders)
                                    </option>
                                    <option value="DCP-004 - Aggregates & Sand"
                                        {{ old('nature_of_expenses') == 'DCP-004 - Aggregates & Sand' ? 'selected' : '' }}>
                                        DCP-004 - Aggregates & Sand
                                    </option>
                                    <option value="DCP-005 - Admixtures & Chemicals"
                                        {{ old('nature_of_expenses') == 'DCP-005 - Admixtures & Chemicals' ? 'selected' : '' }}>
                                        DCP-005 - Admixtures & Chemicals
                                    </option>
                                    <option value="DCP-006 - Precast Elements"
                                        {{ old('nature_of_expenses') == 'DCP-006 - Precast Elements' ? 'selected' : '' }}>
                                        DCP-006 - Precast Elements
                                    </option>
                                    <option value="DCP-007 - Water & Binding Materials"
                                        {{ old('nature_of_expenses') == 'DCP-007 - Water & Binding Materials' ? 'selected' : '' }}>
                                        DCP-007 - Water & Binding Materials
                                    </option>
                                    <option value="DCP-008 - Formwork & Temporary Construction Materials"
                                        {{ old('nature_of_expenses') == 'DCP-008 - Formwork & Temporary Construction Materials' ? 'selected' : '' }}>
                                        DCP-008 - Formwork & Temporary Construction Materials
                                    </option>
                                    <option value="DCP-009 - Specialized Construction Materials"
                                        {{ old('nature_of_expenses') == 'DCP-009 - Specialized Construction Materials' ? 'selected' : '' }}>
                                        DCP-009 - Specialized Construction Materials
                                    </option>
                                    <option value="DCP-010 - Miscellaneous Civil Construction Materials"
                                        {{ old('nature_of_expenses') == 'DCP-010 - Miscellaneous Civil Construction Materials' ? 'selected' : '' }}>
                                        DCP-010 - Miscellaneous Civil Construction Materials
                                    </option>
                                    <option value="DRF-001 - Road Signages, Gantries & Reflective Markings"
                                        {{ old('nature_of_expenses') == 'DRF-001 - Road Signages, Gantries & Reflective Markings' ? 'selected' : '' }}>
                                        DRF-001 - Road Signages, Gantries & Reflective Markings
                                    </option>
                                    <option value="DRF-002 - Reflective Pavement Markers"
                                        {{ old('nature_of_expenses') == 'DRF-002 - Reflective Pavement Markers' ? 'selected' : '' }}>
                                        DRF-002 - Reflective Pavement Markers
                                    </option>
                                    <option value="DRF-003 - Road Marking Repainting & Surface Maintenance"
                                        {{ old('nature_of_expenses') == 'DRF-003 - Road Marking Repainting & Surface Maintenance' ? 'selected' : '' }}>
                                        DRF-003 - Road Marking Repainting & Surface Maintenance
                                    </option>
                                    <option value="DRF-004 - Crash Barriers (W-Beam, Thrie-Beam, Concrete, Wire)"
                                        {{ old('nature_of_expenses') == 'DRF-004 - Crash Barriers (W-Beam, Thrie-Beam, Concrete, Wire)' ? 'selected' : '' }}>
                                        DRF-004 - Crash Barriers (W-Beam, Thrie-Beam, Concrete, Wire)
                                    </option>
                                    <option value="DRF-005 - Bamboo Crash Barriers"
                                        {{ old('nature_of_expenses') == 'DRF-005 - Bamboo Crash Barriers' ? 'selected' : '' }}>
                                        DRF-005 - Bamboo Crash Barriers
                                    </option>
                                    <option value="DRF-006 - Kerb Stones (Capex & During Periodic Maintenance)"
                                        {{ old('nature_of_expenses') == 'DRF-006 - Kerb Stones (Capex & During Periodic Maintenance)' ? 'selected' : '' }}>
                                        DRF-006 - Kerb Stones (Capex & During Periodic Maintenance)
                                    </option>
                                    <option value="DRF-007 - Handrails & Railings"
                                        {{ old('nature_of_expenses') == 'DRF-007 - Handrails & Railings' ? 'selected' : '' }}>
                                        DRF-007 - Handrails & Railings
                                    </option>
                                    <option value="DRF-008 - Guardrails & Crash Cushions"
                                        {{ old('nature_of_expenses') == 'DRF-008 - Guardrails & Crash Cushions' ? 'selected' : '' }}>
                                        DRF-008 - Guardrails & Crash Cushions
                                    </option>
                                    <option value="DRF-009 - Bollards & Delineators"
                                        {{ old('nature_of_expenses') == 'DRF-009 - Bollards & Delineators' ? 'selected' : '' }}>
                                        DRF-009 - Bollards & Delineators
                                    </option>
                                    <option value="DRF-010 - Speed Breakers (Rubber, Concrete, Plastic)"
                                        {{ old('nature_of_expenses') == 'DRF-010 - Speed Breakers (Rubber, Concrete, Plastic)' ? 'selected' : '' }}>
                                        DRF-010 - Speed Breakers (Rubber, Concrete, Plastic)
                                    </option>
                                    <option value="DRF-011 - Rumble Strips"
                                        {{ old('nature_of_expenses') == 'DRF-011 - Rumble Strips' ? 'selected' : '' }}>
                                        DRF-011 - Rumble Strips
                                    </option>
                                    <option value="DRF-012 - Bus Shelters"
                                        {{ old('nature_of_expenses') == 'DRF-012 - Bus Shelters' ? 'selected' : '' }}>
                                        DRF-012 - Bus Shelters
                                    </option>
                                    <option value="DRF-013 - Foot Overbridges (FOBs) & Pedestrian Underpasses (PUPs)"
                                        {{ old('nature_of_expenses') == 'DRF-013 - Foot Overbridges (FOBs) & Pedestrian Underpasses (PUPs)' ? 'selected' : '' }}>
                                        DRF-013 - Foot Overbridges (FOBs) & Pedestrian Underpasses (PUPs)
                                    </option>
                                    <option value="DRF-014 - Benches & Waiting Sheds"
                                        {{ old('nature_of_expenses') == 'DRF-014 - Benches & Waiting Sheds' ? 'selected' : '' }}>
                                        DRF-014 - Benches & Waiting Sheds
                                    </option>
                                    <option value="DRF-015 - Cycle Stands"
                                        {{ old('nature_of_expenses') == 'DRF-015 - Cycle Stands' ? 'selected' : '' }}>
                                        DRF-015 - Cycle Stands
                                    </option>
                                    <option value="DRF-016 - Boom Barriers & Automated Gates"
                                        {{ old('nature_of_expenses') == 'DRF-016 - Boom Barriers & Automated Gates' ? 'selected' : '' }}>
                                        DRF-016 - Boom Barriers & Automated Gates
                                    </option>
                                    <option value="DRF-017 - Road Blockers & Bollard Systems"
                                        {{ old('nature_of_expenses') == 'DRF-017 - Road Blockers & Bollard Systems' ? 'selected' : '' }}>
                                        DRF-017 - Road Blockers & Bollard Systems
                                    </option>
                                    <option value="DOMT-001 - Manpower (Toll Collection, Security & Traffic Management)"
                                        {{ old('nature_of_expenses') == 'DOMT-001 - Manpower (Toll Collection, Security & Traffic Management)' ? 'selected' : '' }}>
                                        DOMT-001 - Manpower (Toll Collection, Security & Traffic Management)
                                    </option>
                                    <option value="DOMT-002 - Power Charges"
                                        {{ old('nature_of_expenses') == 'DOMT-002 - Power Charges' ? 'selected' : '' }}>
                                        DOMT-002 - Power Charges
                                    </option>
                                    <option value="DOMT-003 - TCMS Operations"
                                        {{ old('nature_of_expenses') == 'DOMT-003 - TCMS Operations' ? 'selected' : '' }}>
                                        DOMT-003 - TCMS Operations
                                    </option>
                                    <option value="DOMT-004 - TCMS Spares"
                                        {{ old('nature_of_expenses') == 'DOMT-004 - TCMS Spares' ? 'selected' : '' }}>
                                        DOMT-004 - TCMS Spares
                                    </option>
                                    <option value="DOMT-005 - Internet Lease Line / Broadband"
                                        {{ old('nature_of_expenses') == 'DOMT-005 - Internet Lease Line / Broadband' ? 'selected' : '' }}>
                                        DOMT-005 - Internet Lease Line / Broadband
                                    </option>
                                    <option value="DOMT-006 - Office Running Expenses"
                                        {{ old('nature_of_expenses') == 'DOMT-006 - Office Running Expenses' ? 'selected' : '' }}>
                                        DOMT-006 - Office Running Expenses
                                    </option>
                                    <option value="DOMT-007 - Toll Operations - Outsourced Staff by SPV"
                                        {{ old('nature_of_expenses') == 'DOMT-007 - Toll Operations - Outsourced Staff by SPV' ? 'selected' : '' }}>
                                        DOMT-007 - Toll Operations - Outsourced Staff by SPV
                                    </option>
                                    <option value="DOMT-008 - Toll Operation Vehicle at Toll Plaza"
                                        {{ old('nature_of_expenses') == 'DOMT-008 - Toll Operation Vehicle at Toll Plaza' ? 'selected' : '' }}>
                                        DOMT-008 - Toll Operation Vehicle at Toll Plaza
                                    </option>
                                    <option value="DOMI-001 - Route Patrolling"
                                        {{ old('nature_of_expenses') == 'DOMI-001 - Route Patrolling' ? 'selected' : '' }}>
                                        DOMI-001 - Route Patrolling
                                    </option>
                                    <option value="DOMI-002 - Ambulance Services"
                                        {{ old('nature_of_expenses') == 'DOMI-002 - Ambulance Services' ? 'selected' : '' }}>
                                        DOMI-002 - Ambulance Services
                                    </option>
                                    <option value="DOMI-003 - Recovery Cranes"
                                        {{ old('nature_of_expenses') == 'DOMI-003 - Recovery Cranes' ? 'selected' : '' }}>
                                        DOMI-003 - Recovery Cranes
                                    </option>
                                    <option value="DOMI-004 - Police Assistance Vehicle"
                                        {{ old('nature_of_expenses') == 'DOMI-004 - Police Assistance Vehicle' ? 'selected' : '' }}>
                                        DOMI-004 - Police Assistance Vehicle
                                    </option>
                                    <option value="DOMI-005 - Tow Vehicle"
                                        {{ old('nature_of_expenses') == 'DOMI-005 - Tow Vehicle' ? 'selected' : '' }}>
                                        DOMI-005 - Tow Vehicle
                                    </option>
                                    <option value="DOMI-006 - Mechanical Broom"
                                        {{ old('nature_of_expenses') == 'DOMI-006 - Mechanical Broom' ? 'selected' : '' }}>
                                        DOMI-006 - Mechanical Broom
                                    </option>
                                    <option value="DOMI-007 - Fuel & Lubricants for Incident Management Vehicles"
                                        {{ old('nature_of_expenses') == 'DOMI-007 - Fuel & Lubricants for Incident Management Vehicles' ? 'selected' : '' }}>
                                        DOMI-007 - Fuel & Lubricants for Incident Management Vehicles
                                    </option>
                                    <option value="DOMI-008 - Manpower for Incident Management"
                                        {{ old('nature_of_expenses') == 'DOMI-008 - Manpower for Incident Management' ? 'selected' : '' }}>
                                        DOMI-008 - Manpower for Incident Management
                                    </option>
                                    <option value="DOMI-009 - Miscellaneous Expenses for Incident Management"
                                        {{ old('nature_of_expenses') == 'DOMI-009 - Miscellaneous Expenses for Incident Management' ? 'selected' : '' }}>
                                        DOMI-009 - Miscellaneous Expenses for Incident Management
                                    </option>
                                    <option value="DOMM-001 - Routine Maintenance - Works (Opex)"
                                        {{ old('nature_of_expenses') == 'DOMM-001 - Routine Maintenance - Works (Opex)' ? 'selected' : '' }}>
                                        DOMM-001 - Routine Maintenance - Works (Opex)
                                    </option>
                                    <option value="DOMM-002 - Routine Maintenance - Manpower (Opex)"
                                        {{ old('nature_of_expenses') == 'DOMM-002 - Routine Maintenance - Manpower (Opex)' ? 'selected' : '' }}>
                                        DOMM-002 - Routine Maintenance - Manpower (Opex)
                                    </option>
                                    <option value="DOMM-003 - Highway Lighting (Incl. Energy Charges)"
                                        {{ old('nature_of_expenses') == 'DOMM-003 - Highway Lighting (Incl. Energy Charges)' ? 'selected' : '' }}>
                                        DOMM-003 - Highway Lighting (Incl. Energy Charges)
                                    </option>
                                    <option value="DOMR-001 - R&R - Routine Maintenance (General Upkeep & Minor Repairs)"
                                        {{ old('nature_of_expenses') == 'DOMR-001 - R&R - Routine Maintenance (General Upkeep & Minor Repairs)' ? 'selected' : '' }}>
                                        DOMR-001 - R&R - Routine Maintenance (General Upkeep & Minor Repairs)
                                    </option>
                                    <option value="DOMR-002 - R&R - Pavement"
                                        {{ old('nature_of_expenses') == 'DOMR-002 - R&R - Pavement' ? 'selected' : '' }}>
                                        DOMR-002 - R&R - Pavement
                                    </option>
                                    <option value="DOMR-003 - R&R - Drainage"
                                        {{ old('nature_of_expenses') == 'DOMR-003 - R&R - Drainage' ? 'selected' : '' }}>
                                        DOMR-003 - R&R - Drainage
                                    </option>
                                    <option value="DOMR-004 - R&R- Shoulders, Slopes, Earthworks"
                                        {{ old('nature_of_expenses') == 'DOMR-004 - R&R- Shoulders, Slopes, Earthworks' ? 'selected' : '' }}>
                                        DOMR-004 - R&R- Shoulders, Slopes, Earthworks
                                    </option>
                                    <option value="DOMR-005 - R&R - Road Furniture"
                                        {{ old('nature_of_expenses') == 'DOMR-005 - R&R - Road Furniture' ? 'selected' : '' }}>
                                        DOMR-005 - R&R - Road Furniture
                                    </option>
                                    <option value="DOMR-006 - R&R - Structures"
                                        {{ old('nature_of_expenses') == 'DOMR-006 - R&R - Structures' ? 'selected' : '' }}>
                                        DOMR-006 - R&R - Structures
                                    </option>

                                    <option value="DOMR-007 - R&R - Toll Plaza & Buildings Maintenance"
                                        {{ old('nature_of_expenses') == 'DOMR-007 - R&R - Toll Plaza & Buildings Maintenance' ? 'selected' : '' }}>
                                        DOMR-007 - R&R - Toll Plaza & Buildings Maintenance
                                    </option>
                                    <option value="DOMR-008 - R&R - Horticulture"
                                        {{ old('nature_of_expenses') == 'DOMR-008 - R&R - Horticulture' ? 'selected' : '' }}>
                                        DOMR-008 - R&R - Horticulture
                                    </option>
                                    <option value="DOMR-009 - R&R - Contingency Expenses"
                                        {{ old('nature_of_expenses') == 'DOMR-009 - R&R - Contingency Expenses' ? 'selected' : '' }}>
                                        DOMR-009 - R&R - Contingency Expenses
                                    </option>
                                    <option value="DOMA-001 - Traffic Management Centre & Sub-Centre"
                                        {{ old('nature_of_expenses') == 'DOMA-001 - Traffic Management Centre & Sub-Centre' ? 'selected' : '' }}>
                                        DOMA-001 - Traffic Management Centre & Sub-Centre
                                    </option>
                                    <option value="DOMA-002 - Traffic Monitoring Camera System Equipment (TMCS)"
                                        {{ old('nature_of_expenses') == 'DOMA-002 - Traffic Monitoring Camera System Equipment (TMCS)' ? 'selected' : '' }}>
                                        DOMA-002 - Traffic Monitoring Camera System Equipment (TMCS)
                                    </option>
                                    <option value="DOMA-003 - Video Incident Detection System Equipment (VIDS)"
                                        {{ old('nature_of_expenses') == 'DOMA-003 - Video Incident Detection System Equipment (VIDS)' ? 'selected' : '' }}>
                                        DOMA-003 - Video Incident Detection System Equipment (VIDS)
                                    </option>
                                    <option value="DOMA-004 - Vehicle Speed Detection System Equipment (VSDS) (LHS + RHS)"
                                        {{ old('nature_of_expenses') == 'DOMA-004 - Vehicle Speed Detection System Equipment (VSDS) (LHS + RHS)' ? 'selected' : '' }}>
                                        DOMA-004 - Vehicle Speed Detection System Equipment (VSDS) (LHS + RHS)
                                    </option>
                                    <option value="DOMA-005 - Control Room Manpower"
                                        {{ old('nature_of_expenses') == 'DOMA-005 - Control Room Manpower' ? 'selected' : '' }}>
                                        DOMA-005 - Control Room Manpower
                                    </option>
                                    <option value="DOMA-006 - Power Charges for ATMS"
                                        {{ old('nature_of_expenses') == 'DOMA-006 - Power Charges for ATMS' ? 'selected' : '' }}>
                                        DOMA-006 - Power Charges for ATMS
                                    </option>
                                    <option value="DOMA-007 - Fiber Backbone Maintenance"
                                        {{ old('nature_of_expenses') == 'DOMA-007 - Fiber Backbone Maintenance' ? 'selected' : '' }}>
                                        DOMA-007 - Fiber Backbone Maintenance
                                    </option>
                                    <option value="DQT-001 - Material Testing (Lab & Field)"
                                        {{ old('nature_of_expenses') == 'DQT-001 - Material Testing (Lab & Field)' ? 'selected' : '' }}>
                                        DQT-001 - Material Testing (Lab & Field)
                                    </option>
                                    <option value="DQT-002 - Quality Assurance & Certification"
                                        {{ old('nature_of_expenses') == 'DQT-002 - Quality Assurance & Certification' ? 'selected' : '' }}>
                                        DQT-002 - Quality Assurance & Certification
                                    </option>
                                    <option value="DQT-003 - Third-Party Audit & Inspection"
                                        {{ old('nature_of_expenses') == 'DQT-003 - Third-Party Audit & Inspection' ? 'selected' : '' }}>
                                        DQT-003 - Third-Party Audit & Inspection
                                    </option>
                                    <option value="DQT-004 - Calibration of Equipment"
                                        {{ old('nature_of_expenses') == 'DQT-004 - Calibration of Equipment' ? 'selected' : '' }}>
                                        DQT-004 - Calibration of Equipment
                                    </option>
                                    <option value="DQT-005 - Non-Destructive Testing (NDT)"
                                        {{ old('nature_of_expenses') == 'DQT-005 - Non-Destructive Testing (NDT)' ? 'selected' : '' }}>
                                        DQT-005 - Non-Destructive Testing (NDT)
                                    </option>
                                    <option value="DQT-006 - Core Cutting & Strength Testing"
                                        {{ old('nature_of_expenses') == 'DQT-006 - Core Cutting & Strength Testing' ? 'selected' : '' }}>
                                        DQT-006 - Core Cutting & Strength Testing
                                    </option>
                                    <option value="DQT-007 - Lab equipment & Survey Tools & equipment"
                                        {{ old('nature_of_expenses') == 'DQT-007 - Lab equipment & Survey Tools & equipment' ? 'selected' : '' }}>
                                        DQT-007 - Lab equipment & Survey Tools & equipment
                                    </option>
                                    <option value="DTP-001 - Toll Booth & Canopy Construction"
                                        {{ old('nature_of_expenses') == 'DTP-001 - Toll Booth & Canopy Construction' ? 'selected' : '' }}>
                                        DTP-001 - Toll Booth & Canopy Construction
                                    </option>
                                    <option value="DTP-002 - Toll Collection System Installation"
                                        {{ old('nature_of_expenses') == 'DTP-002 - Toll Collection System Installation' ? 'selected' : '' }}>
                                        DTP-002 - Toll Collection System Installation
                                    </option>
                                    <option value="DTP-003 - Weigh-in-Motion (WIM) System - Maintenance"
                                        {{ old('nature_of_expenses') == 'DTP-003 - Weigh-in-Motion (WIM) System - Maintenance' ? 'selected' : '' }}>
                                        DTP-003 - Weigh-in-Motion (WIM) System - Maintenance
                                    </option>
                                    <option value="DTP-004 - Static Weighbridge - Maintenance"
                                        {{ old('nature_of_expenses') == 'DTP-004 - Static Weighbridge - Maintenance' ? 'selected' : '' }}>
                                        DTP-004 - Static Weighbridge - Maintenance
                                    </option>
                                    <option value="DTP-005 - Surveillance & Security System"
                                        {{ old('nature_of_expenses') == 'DTP-005 - Surveillance & Security System' ? 'selected' : '' }}>
                                        DTP-005 - Surveillance & Security System
                                    </option>
                                    <option
                                        value="DTP-006 - Project Buildings - Toll Plaza & Ancillary Structures) - Capex"
                                        {{ old('nature_of_expenses') == 'DTP-006 - Project Buildings - Toll Plaza & Ancillary Structures) - Capex' ? 'selected' : '' }}>
                                        DTP-006 - Project Buildings - Toll Plaza & Ancillary Structures) - Capex
                                    </option>
                                    <option value="DTP-007 - Queue Managers"
                                        {{ old('nature_of_expenses') == 'DTP-007 - Queue Managers' ? 'selected' : '' }}>
                                        DTP-007 - Queue Managers
                                    </option>
                                    <option value="DMC-001 - Temporary Works & Traffic Management"
                                        {{ old('nature_of_expenses') == 'DMC-001 - Temporary Works & Traffic Management' ? 'selected' : '' }}>
                                        DMC-001 - Temporary Works & Traffic Management
                                    </option>
                                    <option value="DMC-002 - Water & Power Supply for Construction"
                                        {{ old('nature_of_expenses') == 'DMC-002 - Water & Power Supply for Construction' ? 'selected' : '' }}>
                                        DMC-002 - Water & Power Supply for Construction
                                    </option>
                                    <option value="DCCU-001 - Change of Scope & Utility shifting"
                                        {{ old('nature_of_expenses') == 'DCCU-001 - Change of Scope & Utility shifting' ? 'selected' : '' }}>
                                        DCCU-001 - Change of Scope & Utility shifting
                                    </option>


                                </select>
                                @error('nature_of_expenses')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="milestone_status" class="form-label">Milestone Status -Achived ?</label>
                                <select class="form-select form-control" id="milestone_status" name="milestone_status">
                                    <option value="Y" {{ old('milestone_status') == 'Y' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="N" {{ old('milestone_status') == 'N' ? 'selected' : '' }}>No
                                    </option>
                                </select>
                                @error('milestone_status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6" id="file_input_3" style="display:none;">
                                <label for="twentyone" class="form-label">Milestone Remarks</label>
                                <textarea class="form-control form-text" id="twentyone" name="milestone_remarks">{{ old('milestone_remarks') }}</textarea>
                                @error('milestone_remarks')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="expense_amount_within_contract" class="form-label">Expense amount within
                                    contract</label>
                                <select class="form-select form-control" id="expense_amount_within_contract"
                                    name="expense_amount_within_contract">
                                    <option value="Y"
                                        {{ old('expense_amount_within_contract') == 'Y' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="N"
                                        {{ old('expense_amount_within_contract') == 'N' ? 'selected' : '' }}>No
                                    </option>
                                </select>
                                @error('expense_amount_within_contract')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-6" id="file_input_5" style="display:none;">
                                <label for="twentyone" class="form-label">Remarks</label>
                                <textarea class="form-control form-text" id="twentyone" name="specify_deviation">{{ old('specify_deviation') }}</textarea>
                                @error('specify_deviation')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>



                            {{-- <div class="col-12">
                                <h4>HR Department</h4>
                            </div>
                            <div class="col-6">
                                <label for="documents_workdone_supply" class="form-label">Documents verified for the
                                    Period of Workdone/Supply</label>
                                <textarea id="documents_workdone_supply" name="documents_workdone_supply" cols="30" rows="2"
                                    class="form-control">{{ old('documents_workdone_supply') }}</textarea>
                                @error('documents_workdone_supply')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-6">
                                <label for="required_submitted" class="form-label">Whether all the documents required
                                    submitted</label>
                                <select class="form-select form-control" id="required_submitted"
                                    name="required_submitted">
                                    <option value="Y" {{ old('required_submitted') == 'Y' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="N" {{ old('required_submitted') == 'N' ? 'selected' : '' }}>No
                                    </option>
                                </select>
                                @error('required_submitted')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="documents_discrepancy" class="form-label">Documents discrepancy</label>
                                <textarea id="documents_discrepancy" name="documents_discrepancy" cols="30" rows="2"
                                    class="form-control">{{ old('documents_discrepancy') }}</textarea>
                                @error('documents_discrepancy')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="amount_submission_non" class="form-label">Amount if any to be retained for non
                                    submission/non compliance of HR</label>
                                <textarea id="amount_submission_non" name="amount_submission_non" cols="30" rows="2"
                                    class="form-control">{{ old('amount_submission_non') }}</textarea>
                                @error('amount_submission_non')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="remarks" class="form-label">Remarks if any</label>
                                <textarea id="remarks" name="remarks" cols="30" rows="2" class="form-control">{{ old('remarks') }}</textarea>
                                @error('remarks')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div> --}}


                            {{-- <div class="col-12">
                                <h4>Auditor Department</h4>
                            </div>
                            <div class="col-6">
                                <label for="remarks" class="form-label">Attachment if any</label>

                                <input type="file" id="file_input_4" name="file_input_4" class="form-control">
                                @error('file_input_4')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="remarks" class="form-label">Remarks if any</label>
                                <textarea id="auditor_remarks" name="auditor_remarks" cols="30" rows="2" class="form-control">{{ old('auditor_remarks') }}</textarea>
                                @error('auditor_remarks')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div> --}}
                            <div class="col-6">
                                <label for="deviations" class="form-label">If payment approved with Deviation</label>
                                <select class="form-select form-control" id="deviations" name="deviations">
                                    <option value="Y" {{ old('deviations') == 'Y' ? 'selected' : '' }}>Yes</option>
                                    <option value="N" {{ old('deviations') == 'N' ? 'selected' : '' }}>No</option>
                                </select>
                                <input type="file" id="file_input_6" name="file_input_6" class="form-control mt-2"
                                    style="display:none;" accept=".pdf,.doc,.docx,.xls,.xlsx,.csv">
                                @error('file_input_6')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                @error('deviations')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            {{-- <div class="col-6"  style="display:none;">
                             
                                <label for="twentyone" class="form-label">Specify deviation</label>
                                <textarea class="form-control form-text" id="twentyone" name="specify_deviation">{{ old('specify_deviation') }}</textarea>
                                @error('specify_deviation')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div> --}}


                            <div class="text-center">
                                <input type="hidden" name="status" id="status" value="D">
                                <button type="submit" class="btn btn-success" onclick="setStatus('D')">Save
                                    Draft</button>
                                <button type="submit" class="btn btn-primary" onclick="setStatus('D')">Submit</button>
                            </div>
                        </form>
                        <!-- Vertical Form -->
                    </div>
                </div>
            </div>
            {{-- <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Supporting Docs</h5>
                        <!-- Vertical Form -->
                        <form class="row g-3">
                            <div class="col-6">
                                <label for="supone" class="form-label">Doc Name</label>
                                <input type="text" class="form-control" id="supone">
                            </div>
                            <div class="col-6">
                                <label for="supone" class="form-label">Attach File</label>
                                <input type="file" class="form-control form-upload" id="supone">
                            </div>
                            <div class="text-center">
                                <button type="reset" class="btn btn-success">Upload</button>
                            </div>
                        </form><!-- Vertical Form -->
                    </div>
                </div>
            </div> --}}
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Attached Supporting Docs</h5>
                        <!-- Vertical Form -->
                        <!-- Table with stripped rows -->
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>
                                        S no.
                                    </th>
                                    <th>File name</th>
                                    <th>File</th>
                                    <th data-type="date" data-format="DD/MM/YYYY">Upload Date</th>
                                    <th>Uploaded By</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>GST certificate</td>
                                    <td><a href=""><i class="bi bi-file-earmark-text-fill"></i></a></td>
                                    <td>2005/02/11</td>
                                    <td>Maker</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Pan Number</td>
                                    <td><a href=""><i class="bi bi-file-earmark-text-fill"></i></a></td>
                                    <td>2005/02/11</td>
                        @push('script')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
                placeholder: function() {
                    return $(this).data('placeholder') || 'Select an option';
                }
            });

            function updateSupplierMetadata(option) {
                var msme = option ? option.data('msme') : '';
                var activity = option ? option.data('activity') : '';
                $('input[name="msme_classification"]').val(msme || '');
                $('input[name="activity_type"]').val(activity || '');
            }

            $('select[name="supplier_id"]').on('change', function() {
                var option = $(this).find('option:selected');
                updateSupplierMetadata(option);
            });

            $('select[name="supplier_id"]').each(function() {
                var option = $(this).find('option:selected');
                if (option.length && option.val()) {
                    updateSupplierMetadata(option);
                }
            });
        });

        function calculateTotal() {
            var baseValue = parseFloat($('#base_value').val()) || 0;
            var otherCharges = parseFloat($('#other_charges').val()) || 0;
            var gstRate = parseFloat($('#gst_rate').val()) || 0;
            var gst = (baseValue * gstRate) / 100;
            var total = baseValue + otherCharges + gst;
            $('#total_amount').val(total.toFixed(2));
        }

        function calculateInvoiceTotal() {
            var baseValue = parseFloat($('#invoice_base_value').val()) || 0;
            var gst = parseFloat($('#invoice_gst').val()) || 0;
            var otherCharges = parseFloat($('#invoice_other_charges').val()) || 0;
            var total = baseValue + gst + otherCharges;
            $('#invoice_value').val(total.toFixed(2));
        }

        function calculateTotalBudget() {
            var budget = parseFloat($('#budget_expenditure').val()) || 0;
            var actual = parseFloat($('#actual_expenditure').val()) || 0;
            var difference = actual - budget;
            var status = difference > 0 ? 'Over Budget' : (difference < 0 ? 'Under Budget' : 'On Budget');
            $('#expenditure_over_budget').val(difference.toFixed(2) + ' (' + status + ')');
        }

        function resetForm() {
            if (confirm('Are you sure you want to reset the form? All data will be lost.')) {
                $('form')[0].reset();
                $('.select2').val('').trigger('change');
                $('#total_amount').val('');
                $('#invoice_value').val('');
                $('#expenditure_over_budget').val('');
                $('#msme_classification').val('');
                $('#activity_type').val('');
            }
        }

        function setStatus(status) {
            $('#status').val(status);
        }

        function showSpinner(button) {
            var btnText = button.querySelector('.btn-text');
            var spinner = button.querySelector('.spinner-border');
            if (btnText && spinner) {
                btnText.style.display = 'none';
                spinner.style.display = 'inline-block';
                button.disabled = true;
                setTimeout(function() {
                    button.closest('form').submit();
                }, 100);
            }
        }

        // Multiple Invoices Functionality
        let invoiceEntryIndex = 0;

        // Toggle multiple invoices section
        $('#enable_multiple_invoices').on('change', function() {
            if ($(this).is(':checked')) {
                $('#multiple_invoices_section').slideDown();
                // Add first invoice entry if none exist
                if ($('#invoice_entries_container').children().length === 0) {
                    addInvoiceEntry();
                }
                // Disable single invoice fields
                $('#invoice_number, #invoice_date, #invoice_base_value, #invoice_gst, #invoice_other_charges').prop('readonly', true).addClass('bg-light');
            } else {
                $('#multiple_invoices_section').slideUp();
                // Enable single invoice fields
                $('#invoice_number, #invoice_date, #invoice_base_value, #invoice_gst, #invoice_other_charges').prop('readonly', false).removeClass('bg-light');
                // Clear all invoice entries
                $('#invoice_entries_container').empty();
                invoiceEntryIndex = 0;
                updateMultipleInvoiceTotals();
            }
        });

        function addInvoiceEntry() {
            const entryHtml = `
                <div class="invoice-entry border rounded p-3 mb-3 bg-white" data-index="${invoiceEntryIndex}">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-primary mb-0">
                            <i class="bi bi-receipt me-2"></i>Invoice #${invoiceEntryIndex + 1}
                        </h6>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeInvoiceEntry(this)">
                            <i class="bi bi-trash"></i> Remove
                        </button>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" 
                                       name="invoices[${invoiceEntryIndex}][invoice_number]" 
                                       placeholder="Invoice Number" required>
                                <label>Invoice Number *</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" class="form-control" 
                                       name="invoices[${invoiceEntryIndex}][invoice_date]" 
                                       placeholder="Invoice Date" required>
                                <label>Invoice Date *</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="number" step="0.01" class="form-control invoice-base-value" 
                                       name="invoices[${invoiceEntryIndex}][invoice_base_value]" 
                                       placeholder="Base Value" onchange="calculateInvoiceEntryTotal(this)">
                                <label>Base Value (₹)</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="number" step="0.01" class="form-control invoice-gst-value" 
                                       name="invoices[${invoiceEntryIndex}][invoice_gst]" 
                                       placeholder="GST" onchange="calculateInvoiceEntryTotal(this)">
                                <label>GST (₹)</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="number" step="0.01" class="form-control invoice-other-charges" 
                                       name="invoices[${invoiceEntryIndex}][invoice_other_charges]" 
                                       placeholder="Other Charges" onchange="calculateInvoiceEntryTotal(this)">
                                <label>Other Charges (₹)</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="number" step="0.01" class="form-control invoice-total-value" 
                                       name="invoices[${invoiceEntryIndex}][invoice_value]" 
                                       placeholder="Total Value" readonly>
                                <label>Total Value (₹)</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" 
                                       name="invoices[${invoiceEntryIndex}][description]" 
                                       placeholder="Description">
                                <label>Description (Optional)</label>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('#invoice_entries_container').append(entryHtml);
            invoiceEntryIndex++;
            
            // Show success notification
            showNotification('Invoice entry added successfully!', 'success');
        }

        function removeInvoiceEntry(button) {
            const entriesCount = $('#invoice_entries_container .invoice-entry').length;
            
            if (entriesCount <= 1) {
                showNotification('At least one invoice entry is required!', 'warning');
                return;
            }
            
            if (confirm('Are you sure you want to remove this invoice entry?')) {
                $(button).closest('.invoice-entry').fadeOut(300, function() {
                    $(this).remove();
                    updateMultipleInvoiceTotals();
                    // Renumber remaining invoices
                    $('#invoice_entries_container .invoice-entry').each(function(index) {
                        $(this).find('h6').html(`<i class="bi bi-receipt me-2"></i>Invoice #${index + 1}`);
                    });
                    showNotification('Invoice entry removed successfully!', 'info');
                });
            }
        }

        function calculateInvoiceEntryTotal(input) {
            const entryRow = $(input).closest('.invoice-entry');
            const baseValue = parseFloat(entryRow.find('.invoice-base-value').val()) || 0;
            const gst = parseFloat(entryRow.find('.invoice-gst-value').val()) || 0;
            const otherCharges = parseFloat(entryRow.find('.invoice-other-charges').val()) || 0;
            const total = baseValue + gst + otherCharges;
            
            entryRow.find('.invoice-total-value').val(total.toFixed(2));
            updateMultipleInvoiceTotals();
        }

        function updateMultipleInvoiceTotals() {
            let totalValue = 0;
            let totalGst = 0;
            
            $('#invoice_entries_container .invoice-entry').each(function() {
                const entryTotal = parseFloat($(this).find('.invoice-total-value').val()) || 0;
                const entryGst = parseFloat($(this).find('.invoice-gst-value').val()) || 0;
                totalValue += entryTotal;
                totalGst += entryGst;
            });
            
            $('#total_invoice_value').val(totalValue.toFixed(2));
            $('#total_invoice_gst').val(totalGst.toFixed(2));
        }

        function showNotification(message, type = 'info') {
            const bgColor = type === 'success' ? 'bg-success' : type === 'warning' ? 'bg-warning' : type === 'error' ? 'bg-danger' : 'bg-info';
            const icon = type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : type === 'error' ? 'x-circle' : 'info-circle';
            
            const notification = $(`
                <div class="alert alert-dismissible fade show position-fixed ${bgColor} text-white" 
                     style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-${icon} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            `);
            
            $('body').append(notification);
            
            setTimeout(function() {
                notification.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
        }

        $('form').on('submit', function(e) {
            var isValid = true;
            var firstInvalidField = null;
            $(this).find('[required]').each(function() {
                if (!$(this).val()) {
                    $(this).addClass('is-invalid');
                    isValid = false;
                    if (!firstInvalidField) {
                        firstInvalidField = $(this);
                    }
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if (!isValid) {
                if (firstInvalidField) {
                    firstInvalidField.focus();
                    $('html, body').animate({
                        scrollTop: firstInvalidField.offset().top - 100
                    }, 500);
                }
                e.preventDefault();
                return false;
            }

            return true;
        });
    </script>
@endpush

