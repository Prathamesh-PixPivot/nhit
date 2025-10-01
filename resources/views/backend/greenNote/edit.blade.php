@extends('backend.layouts.app')
@section('content')
    <div class="pagetitle">
        <h1>Green Note</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item active">Green Note</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Edit Green Note ({{ $note->formatted_order_no }})</h5>
                        @if ($errors->any())
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                                <strong>Validation Error(s):</strong>
                                <ul class="list-disc pl-5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <!-- Vertical Form -->
                        <form class="row g-3" action="{{ route('backend.note.update', $note->id) }}" method="post"
                            enctype="multipart/form-data" id="editForm">
                            @csrf
                            @method('PUT')
                            @php
                                // Get logged-in user's roles
                                $userRoles = auth()->user()->getRoleNames();
                            @endphp
                            @if ($userRoles->contains('Hr And Admin'))
                                <div class="col-6">
                                    <label for="approval_for" class="form-label">Approval for</label>
                                    <input type="text" class="form-control" value="{{ $note->approval_for }}"
                                        name="approval_for" readonly>
                                </div>
                                @if ($note->status == 'D' || $note->status == 'PMPL')
                                    <div class="col-6">
                                        <label for="vendor_id" class="form-label">Project Name</label>
                                        <select class="form-select form-control" id="vendor_id" name="vendor_id" required>
                                            <option value="">Select Project</option>
                                            @foreach ($filteredItems as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ old('vendor_id', $note->vendor_id) == $item->id ? 'selected' : '' }}>
                                                    {{ $item->project }}</option>
                                            @endforeach
                                        </select>
                                        @error('vendor_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @else
                                    <div class="col-6">
                                        <label for="vendor_id" class="form-label">Project Name</label>
                                        <input type="text" class="form-control" id="vendor_id" name="" readonly
                                            value="{{ old('vendor_id', $note->vendor->project) }}">
                                        <input type="hidden" name="vendor_id" value="{{ $note->vendor_id }}">
                                        @error('vendor_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif

                                <div class="col-6">
                                    <label for="five" class="form-label">User Department</label>
                                    <input type="text" class="form-control" id="department_id" name="" readonly
                                        value="{{ old('department_id', $note->department->name) }}">
                                    <input type="hidden" name="department_id" value="{{ $note->department_id }}">
                                </div>
                                <div class="col-6">
                                    <label for="two" class="form-label">Work/Purchase Order no.</label>
                                    <input type="text" readonly class="form-control" name="order_no" id="two"
                                        value="{{ old('order_no', $note->order_no) }}">
                                    @error('order_no')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label for="one" class="form-label">Work/Purchase Order date</label>
                                    <input type="date" class="form-control" name="order_date" id="one" readonly
                                        value="{{ old('order_date', $note->order_date) }}">
                                    @error('order_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label for="four" class="form-label">Amount of Work/Purchase Order</label>
                                    <input type="text" placeholder="Base value" name="base_value" readonly
                                        class="form-control mb-2" id="base_value"
                                        value="{{ old('base_value', $note->base_value) }}" oninput="calculateTotal()">
                                    @error('base_value')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                    <input type="text" placeholder="GST on Above" name="gst" readonly
                                        class="form-control mb-2" id="gst" value="{{ old('gst', $note->gst) }}"
                                        oninput="calculateTotal()">
                                    @error('gst')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror


                                    <input type="text" placeholder="Other Charges" name="other_charges"
                                        class="form-control mb-2" id="other_charges" readonly
                                        value="{{ old('other_charges', $note->other_charges) }}"
                                        oninput="calculateTotal()">
                                    @error('other_charges')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror


                                    <input type="text" class="form-control" id="total_amount" name="total_amount"
                                        readonly value="{{ old('total_amount', $note->total_amount) }}">
                                    @error('total_amount')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label for="supplier_id" class="form-label">Name of Supplier</label>
                                    <input type="text" class="form-control" id="supplier_id" name="" readonly
                                        value="{{ old('supplier_id', $note->supplier->vendor_name) }}">
                                    <input type="hidden" name="supplier_id" value="{{ $note->supplier_id }}">

                                </div>

                                <div class="col-6">
                                    <label for="msme_classification" class="form-label">MSME Classification</label>
                                    <input type="text" class="form-control" id="msme_classification" readonly
                                        name="msme_classification" readonly
                                        value="{{ old('msme_classification', $note->msme_classification) }}">
                                </div>
                                <div class="col-6">
                                    <label for="activity_type" class="form-label">Activity Type</label>
                                    <input type="text" class="form-control" id="activity_type" name="activity_type"
                                        {{ $note->activity_type }} readonly>
                                    @error('activity_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label for="twentytwo" class="form-label">Protest Note Raised</label>
                                    <input type="text" class="form-control" readonly
                                        value="{{ $note->protest_note_raised == 'Y' ? 'Yes' : 'No' }}">
                                    <input type="hidden" name="protest_note_raised"
                                        value="{{ $note->protest_note_raised }}">
                                </div>
                                <div class="col-6">
                                    <label for="brief_of_goods_services" class="form-label">Brief of Goods /
                                        Services</label>
                                    <input type="text" class="form-control" id="brief_of_goods_services" readonly
                                        name="brief_of_goods_services"
                                        value="{{ old('brief_of_goods_services', $note->brief_of_goods_services) }}">
                                    @error('brief_of_goods_services')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label for="invoice_number" class="form-label">Invoice Number</label>
                                    <input type="text" class="form-control" id="invoice_number" name="invoice_number"
                                        readonly value="{{ old('invoice_number', $note->invoice_number) }}">
                                    @error('invoice_number')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label for="invoice_date" class="form-label">Invoice Date</label>
                                    <input type="date" class="form-control" id="invoice_date" name="invoice_date"
                                        readonly value="{{ old('invoice_date', $note->invoice_date) }}">
                                    @error('invoice_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label for="fourteen" class="form-label">Taxable Value</label>

                                    <input type="text" class="form-control" id="invoice_base_value" readonly
                                        name="invoice_base_value"
                                        value="{{ old('invoice_base_value', $note->invoice_base_value) }}"
                                        oninput="calculateInvoiceTotal()">
                                    @error('invoice_base_value')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <label for="fourteen" class="form-label">Add: GST on above</label>

                                    <input type="text" class="form-control" id="invoice_gst" name="invoice_gst"
                                        readonly value="{{ old('invoice_gst', $note->invoice_gst) }}"
                                        oninput="calculateInvoiceTotal()">
                                    @error('invoice_gst')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                    <label for="invoice_other_charges" class="form-label">Invoice Other
                                        Charges</label>
                                    <input type="number" class="form-control  mb-2" id="invoice_other_charges" readonly
                                        placeholder="Invoice Other Charges" name="invoice_other_charges"
                                        value="{{ old('invoice_other_charges', $note->invoice_other_charges) }}"
                                        oninput="calculateInvoiceTotal()">
                                    @error('invoice_other_charges')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <label for="fourteen" class="form-label">Invoice Value</label>

                                    <input type="text" class="form-control" id="invoice_value" name="invoice_value"
                                        readonly value="{{ old('invoice_value', $note->invoice_value) }}">
                                    @error('invoice_value')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                </div>

                                <div class="col-6">
                                    <label for="six" class="form-label">Contract Period</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <label for="six" class="form-label">Start Date</label>

                                            <input type="date" class="form-control col-3 mb-3"
                                                name="contract_start_date" readonly
                                                value="{{ old('contract_start_date', $note->contract_start_date) }}">
                                            @error('contract_start_date')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-6">
                                            <label for="six" class="form-label">End Date</label>

                                            <input type="date" class="form-control col-3" name="contract_end_date"
                                                readonly value="{{ old('contract_end_date', $note->contract_end_date) }}">

                                            @error('contract_end_date')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label for="appointed_start_date" class="form-label">Appointed date/Date for start
                                        of
                                        work</label>
                                    <input type="date" class="form-control" id="appointed_start_date"
                                        name="appointed_start_date" readonly
                                        value="{{ old('appointed_start_date', $note->appointed_start_date) }}">
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

                                            <input type="date" class="form-control" id="fifteen" readonly
                                                name="supply_period_start"
                                                value="{{ old('supply_period_start', $note->supply_period_start) }}">
                                            @error('supply_period_start')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>


                                        <div class="col-6">
                                            <label for="six" class="form-label">Start Date</label>

                                            <input type="date" class="form-control" id="fifteen" readonly
                                                name="supply_period_end"
                                                value="{{ old('supply_period_end', $note->supply_period_end) }}">
                                            @error('supply_period_end')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <label for="extension_contract_period" class="form-label">Whether contract period
                                        completed</label>
                                    <input type="text" class="form-control" readonly
                                        value="{{ $note->whether_contract == 'Y' ? 'Yes' : 'No' }}">
                                    <input type="hidden" id="whether_contract" name="whether_contract"
                                        value="{{ $note->whether_contract }}">

                                </div>
                                <div class="col-6">
                                    <label for="extension_contract_period" class="form-label">Extension of contract
                                        period
                                        executed</label>
                                    <input type="text" class="form-control" readonly
                                        value="{{ $note->extension_contract_period == 'Y' ? 'Yes' : 'No' }}">
                                    <input type="hidden" id="extension_contract_period" name="extension_contract_period"
                                        value="{{ $note->extension_contract_period }}">
                                </div>
                                <div class="col-6">
                                    <label for="delayed_damages" class="form-label">Delayed damages</label>
                                    <textarea id="delayed_damages" name="delayed_damages" readonly cols="30" rows="2" class="form-control">{{ old('delayed_damages', $note->delayed_damages) }}</textarea>
                                    @error('delayed_damages')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label for="four" class="form-label">Budget Utilisation </label>

                                    <input type="number" class="form-control mb-2" placeholder="Budget Expenditure"
                                        id="budget_expenditure" name="budget_expenditure" readonly
                                        value="{{ old('budget_expenditure', $note->budget_expenditure) }}">
                                    @error('budget_expenditure')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                    <input type="number" class="form-control mb-2" placeholder="Actual Expenditure "
                                        id="actual_expenditure" name="actual_expenditure" readonly
                                        value="{{ old('actual_expenditure', $note->actual_expenditure) }}">
                                    @error('actual_expenditure')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <input type="text" readonly class="form-control mb-2"
                                        placeholder="Expenditure over budget" id="expenditure_over_budget"
                                        name="expenditure_over_budget" readonly
                                        value="{{ old('expenditure_over_budget', $note->expenditure_over_budget) }}">
                                    @error('expenditure_over_budget')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label for="nature_of_expenses" class="form-label">Nature of Expenses</label>
                                    <input type="text" class="form-control" id="nature_of_expenses"
                                        name="nature_of_expenses" readonly value="{{ $note->nature_of_expenses }}">
                                </div>
                                <div class="col-6">
                                    <label for="milestone_status" class="form-label">Milestone Status</label>
                                    <input type="text" class="form-control" id="milestone_status"
                                        name="milestone_status" readonly
                                        value="{{ $note->milestone_status == 'Y' ? 'Yes' : 'No' }}">
                                    <input type="hidden" id="milestone_status" name="milestone_status"
                                        value="{{ $note->milestone_status }}">

                                </div>
                                <div class="col-6" id="file_input_3" style="display:none;">
                                    <label for="twentyone" class="form-label">Milestone Remarks</label>
                                    <!--Conditional on Milestone Status if No - show this -->
                                    <textarea class="form-control form-text" id="twentyone" readonly name="milestone_remarks">{{ old('milestone_remarks', $note->milestone_remarks) }}</textarea>
                                    @error('milestone_remarks')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-6">
                                    <label for="expense_amount_within_contract" class="form-label">Expense amount
                                        within
                                        contract</label>
                                    <input type="text" class="form-control" id="expense_amount_within_contract"
                                        name="expense_amount_within_contract" readonly
                                        value="{{ $note->expense_amount_within_contract == 'Y' ? 'Yes' : 'No' }}">
                                    <input type="hidden" id="expense_amount_within_contract"
                                        name="expense_amount_within_contract"
                                        value="{{ $note->expense_amount_within_contract }}">
                                </div>

                                <div class="col-6">
                                    <label for="deviations" class="form-label">If payment approved with
                                        Deviation</label>
                                    <input type="text" class="form-control" id="deviations" name="deviations"
                                        readonly value="{{ $note->deviations == 'Y' ? 'Yes' : 'No' }}">
                                    <input type="hidden" id="deviations" name="deviations"
                                        value="{{ $note->deviations }}">
                                    {{-- <select class="form-select form-control" id="deviations" name="deviations" readonly>
                                        <option value="Y"
                                            {{ old('deviations', $note->deviations) == 'Y' ? 'selected' : '' }}>Yes
                                        </option>
                                        <option value="N"
                                            {{ old('deviations', $note->deviations) == 'N' ? 'selected' : '' }}>No</option>
                                    </select> --}}
                                    @error('deviations')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6" id="file_input_5" style="display:none;">
                                    <label for="twentyone" class="form-label">Specify deviation</label>
                                    <textarea class="form-control form-text" id="twentyone" readonly name="specify_deviation">{{ old('specify_deviation', $note->specify_deviation) }}</textarea>
                                    @error('specify_deviation')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>




                                {{-- -09=-0=-0=-0=-=-=-=-=-=-=-=-=-=-=-=-=- --}}
                            @else
                                <div class="col-6">
                                    <label for="approval_for" class="form-label">Approval for</label>
                                    <select class="form-select form-control" id="approval_for" name="approval_for">
                                        <option value="Invoice"
                                            {{ old('approval_for', $note->approval_for) == 'Invoice' ? 'selected' : '' }}>
                                            Invoice</option>
                                        <option value="Advance"
                                            {{ old('approval_for', $note->approval_for) == 'Advance' ? 'selected' : '' }}>
                                            Advance</option>
                                        <option value="Adhoc"
                                            {{ old('approval_for', $note->approval_for) == 'Adhoc' ? 'selected' : '' }}>
                                            Adhoc
                                        </option>
                                    </select>
                                    @error('approval_for')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                @if ($note->status == 'D' || $note->status == 'PMPL')
                                    <div class="col-6">
                                        <label for="vendor_id" class="form-label">Project Name</label>
                                        <select class="form-select form-control" id="vendor_id" name="vendor_id"
                                            required>
                                            <option value="">Select Project</option>
                                            @foreach ($filteredItems as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ old('vendor_id', $note->vendor_id) == $item->id ? 'selected' : '' }}>
                                                    {{ $item->project }}</option>
                                            @endforeach
                                        </select>
                                        @error('vendor_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-6">
                                        <label for="five" class="form-label">User Department</label>
                                        <select class="form-select form-control " id="five" name="department_id">
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->id }}"
                                                    {{ old('department_id', $note->department_id) == $department->id ? 'selected' : '' }}>
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('department_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @else
                                    <div class="col-6">
                                        <label for="vendor_id" class="form-label">Project Name</label>
                                        <input type="text" class="form-control" id="vendor_id" name=""
                                            readonly value="{{ old('vendor_id', $note->vendor->project) }}">
                                        <input type="hidden" name="vendor_id" value="{{ $note->vendor_id }}">
                                        @error('vendor_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <label for="department_id" class="form-label">User Department</label>
                                        <input type="text" class="form-control" id="department_id" name=""
                                            readonly value="{{ old('department_id', $note->department->name) }}">
                                        <input type="hidden" name="department_id" value="{{ $note->department_id }}">
                                        @error('department_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif

                                <div class="col-6">
                                    <label for="two" class="form-label">Work/Purchase Order no.</label>
                                    <!--Sequential--><input type="text" class="form-control" name="order_no"
                                        id="two" value="{{ old('order_no', $note->order_no) }}">
                                    @error('order_no')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label for="one" class="form-label">Work/Purchase Order date</label>
                                    <input type="date" class="form-control" name="order_date" id="one"
                                        value="{{ old('order_date', $note->order_date) }}">
                                    @error('order_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label for="four" class="form-label">Amount of Work/Purchase Order</label>
                                    <input type="text" placeholder="Base value" name="base_value"
                                        class="form-control mb-2" id="base_value"
                                        value="{{ old('base_value', $note->base_value) }}" oninput="calculateTotal()"
                                        {{ !in_array($note->status, ['D', 'PMPL']) ? 'readonly' : '' }}>
                                    @error('base_value')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                    <input type="text" placeholder="Other Charges" name="other_charges"
                                        class="form-control mb-2" id="other_charges"
                                        value="{{ old('other_charges', $note->other_charges) }}"
                                        oninput="calculateTotal()"
                                        {{ !in_array($note->status, ['D', 'PMPL']) ? 'readonly' : '' }}>
                                    @error('other_charges')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                    <input type="text" placeholder="GST on Above" name="gst"
                                        class="form-control mb-2" id="gst" value="{{ old('gst', $note->gst) }}"
                                        oninput="calculateTotal()"
                                        {{ !in_array($note->status, ['D', 'PMPL']) ? 'readonly' : '' }}>
                                    @error('gst')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                    <input type="text" class="form-control" id="total_amount" name="total_amount"
                                        value="{{ old('total_amount', $note->total_amount) }}"
                                        {{ !in_array($note->status, ['D', 'PMPL']) ? 'readonly' : '' }}>
                                    @error('total_amount')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                @if ($note->status == 'D' || $note->status == 'PMPL')
                                    <div class="col-6">
                                        <label for="supplier_id" class="form-label">Name of Supplier</label>

                                        <select
                                            class="form-select form-control select2 @if ($userRoles->contains('Hr And Admin')) readonly-select @endif"
                                            id="supplier_id" name="supplier_id">
                                            <option value="">Select Vendor Name</option>
                                            @foreach ($filteredVendorItems as $item)
                                                <option value="{{ $item->id }}"
                                                    data-msme="{{ $item->msme_classification }}"
                                                    data-activity="{{ $item->activity_type }}"
                                                    {{ old('supplier_id', $note->supplier_id) == $item->id ? 'selected' : '' }}>
                                                    {{ $item->vendor_name }} </option>
                                            @endforeach
                                        </select>
                                        @error('supplier_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @else
                                    <div class="col-6">
                                        <label for="supplier_id" class="form-label">Select Vendor Name</label>
                                        <input type="text" class="form-control" id="supplier_id" name=""
                                            readonly value="{{ old('supplier_id', $note->supplier->vendor_name) }}">
                                        <input type="hidden" name="supplier_id" value="{{ $note->supplier_id }}">
                                        @error('supplier_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif


                                <div class="col-6">
                                    <label for="msme_classification" class="form-label">MSME Classification</label>
                                    <input type="text" class="form-control" id="msme_classification"
                                        name="msme_classification" readonly
                                        value="{{ old('msme_classification', $note->msme_classification) }}">

                                    @error('msme_classification')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label for="activity_type" class="form-label">Activity Type</label>
                                    <input type="text" class="form-control" id="activity_type" name="activity_type"
                                        value="{{ $note->activity_type }}" readonly>
                                    @error('activity_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label for="twentytwo" class="form-label">Protest Note Raised</label>
                                    <select class="form-select form-control" name="protest_note_raised"
                                        id="protest_note_raised">
                                        <option value="Y"
                                            {{ old('protest_note_raised', $note->protest_note_raised) == 'Y' ? 'selected' : '' }}>
                                            Yes
                                        </option>
                                        <option value="N"
                                            {{ old('protest_note_raised', $note->protest_note_raised) == 'N' ? 'selected' : '' }}>
                                            No
                                        </option>
                                    </select>
                                    <input type="file" id="file_input_2" name="file_input_2"
                                        class="form-control mt-2" accept=".pdf,.doc,.docx,.xls,.xlsx,.csv">
                                    @error('file_input_2')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    @error('protest_note_raised')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                </div>
                                <div class="col-6">
                                    <label for="brief_of_goods_services" class="form-label">Brief of Goods /
                                        Services</label>
                                    <input type="text" class="form-control" id="brief_of_goods_services"
                                        name="brief_of_goods_services"
                                        value="{{ old('brief_of_goods_services', $note->brief_of_goods_services) }}">
                                    @error('brief_of_goods_services')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label for="invoice_number" class="form-label">Invoice Number</label>
                                    <input type="text" class="form-control" id="invoice_number" name="invoice_number"
                                        value="{{ old('invoice_number', $note->invoice_number) }}">
                                    @error('invoice_number')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label for="invoice_date" class="form-label">Invoice Date</label>
                                    <input type="date" class="form-control" id="invoice_date" name="invoice_date"
                                        value="{{ old('invoice_date', $note->invoice_date) }}">
                                    @error('invoice_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label for="fourteen" class="form-label">Taxable Value</label>

                                    <input type="text" class="form-control" id="invoice_base_value"
                                        name="invoice_base_value"
                                        value="{{ old('invoice_base_value', $note->invoice_base_value) }}"
                                        oninput="calculateInvoiceTotal()"
                                        {{ !in_array($note->status, ['D', 'PMPL']) ? 'readonly' : '' }}>
                                    @error('invoice_base_value')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <label for="fourteen" class="form-label">Add: GST on above</label>

                                    <input type="text" class="form-control" id="invoice_gst" name="invoice_gst"
                                        value="{{ old('invoice_gst', $note->invoice_gst) }}"
                                        oninput="calculateInvoiceTotal()"
                                        {{ !in_array($note->status, ['D', 'PMPL']) ? 'readonly' : '' }}>
                                    @error('invoice_gst')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                    <label for="invoice_other_charges" class="form-label">Invoice Other
                                        Charges</label>
                                    <input type="number" class="form-control  mb-2" id="invoice_other_charges"
                                        placeholder="Invoice Other Charges" name="invoice_other_charges"
                                        value="{{ old('invoice_other_charges', $note->invoice_other_charges) }}"
                                        oninput="calculateInvoiceTotal()"
                                        {{ !in_array($note->status, ['D', 'PMPL']) ? 'readonly' : '' }}>
                                    @error('invoice_other_charges')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <label for="fourteen" class="form-label">Invoice Value</label>

                                    <input type="text" class="form-control" id="invoice_value" name="invoice_value"
                                        value="{{ old('invoice_value', $note->invoice_value) }}"
                                        {{ !in_array($note->status, ['D', 'PMPL']) ? 'readonly' : '' }}>
                                    @error('invoice_value')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                </div>

                                <div class="col-6">
                                    <label for="six" class="form-label">Contract Period</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <label for="six" class="form-label">Start Date</label>

                                            <input type="date" class="form-control col-3 mb-3"
                                                name="contract_start_date"
                                                value="{{ old('contract_start_date', $note->contract_start_date) }}">
                                            @error('contract_start_date')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-6">
                                            <label for="six" class="form-label">End Date</label>

                                            <input type="date" class="form-control col-3" name="contract_end_date"
                                                value="{{ old('contract_end_date', $note->contract_end_date) }}">

                                            @error('contract_end_date')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label for="appointed_start_date" class="form-label">Appointed date/Date for start
                                        of
                                        work</label>
                                    <input type="date" class="form-control" id="appointed_start_date"
                                        name="appointed_start_date"
                                        value="{{ old('appointed_start_date', $note->appointed_start_date) }}">
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

                                            <input type="date" class="form-control" id="fifteen"
                                                name="supply_period_start"
                                                value="{{ old('supply_period_start', $note->supply_period_start) }}">
                                            @error('supply_period_start')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>


                                        <div class="col-6">
                                            <label for="six" class="form-label">Start Date</label>

                                            <input type="date" class="form-control" id="fifteen"
                                                name="supply_period_end"
                                                value="{{ old('supply_period_end', $note->supply_period_end) }}">
                                            @error('supply_period_end')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <label for="extension_contract_period" class="form-label">Whether contract period
                                        completed</label>
                                    <select class="form-select form-control" id="whether_contract"
                                        name="whether_contract">
                                        <option value="Y"
                                            {{ old('whether_contract', $note->whether_contract) == 'Y' ? 'selected' : '' }}>
                                            Yes
                                        </option>
                                        <option value="N"
                                            {{ old('whether_contract', $note->whether_contract) == 'N' ? 'selected' : '' }}>
                                            No
                                        </option>
                                    </select>

                                    @error('whether_contract')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6" id="extension_contract_period_show">
                                    <label for="extension_contract_period" class="form-label">Extension of contract
                                        period
                                        executed</label>
                                    <select class="form-select form-control" id="extension_contract_period"
                                        name="extension_contract_period">
                                        <option value="Y"
                                            {{ old('extension_contract_period', $note->extension_contract_period) == 'Y' ? 'selected' : '' }}>
                                            Yes
                                        </option>
                                        <option value="N"
                                            {{ old('extension_contract_period', $note->extension_contract_period) == 'N' ? 'selected' : '' }}>
                                            No
                                        </option>
                                    </select>
                                    <input type="file" id="file_input_1" name="file_input_1" class="form-control"
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
                                    <textarea id="delayed_damages" name="delayed_damages" cols="30" rows="2" class="form-control">{{ old('delayed_damages', $note->delayed_damages) }}</textarea>
                                    @error('delayed_damages')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label for="four" class="form-label">Budget Utilisation </label>

                                    <input type="number" class="form-control mb-2" placeholder="Budget Expenditure"
                                        id="budget_expenditure" name="budget_expenditure"
                                        value="{{ old('budget_expenditure', $note->budget_expenditure) }}">
                                    @error('budget_expenditure')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                    <input type="number" class="form-control mb-2" placeholder="Actual Expenditure "
                                        id="actual_expenditure" name="actual_expenditure"
                                        value="{{ old('actual_expenditure', $note->actual_expenditure) }}">
                                    @error('actual_expenditure')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <input type="text" readonly class="form-control mb-2"
                                        placeholder="Expenditure over budget" id="expenditure_over_budget"
                                        name="expenditure_over_budget"
                                        value="{{ old('expenditure_over_budget', $note->expenditure_over_budget) }}">
                                    @error('expenditure_over_budget')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label for="nature_of_expenses" class="form-label">Nature of Expenses</label>
                                    <select class="form-select form-control select2" id="nature_of_expenses"
                                        name="nature_of_expenses">
                                        {{-- <option value="Initial Improvement Works"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'Initial Improvement Works' ? 'selected' : '' }}>
                                            Initial Improvement Works
                                        </option>
                                        <option value="O&M Expenses"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'O&M Expenses' ? 'selected' : '' }}>
                                            O&M Expenses
                                        </option> --}}
                                        <option value="OHC-001 - Manpower (Salaries, Wages, Director's Remuneration)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHC-001 - Manpower (Salaries, Wages, Director\'s Remuneration)' ? 'selected' : '' }}>
                                            OHC-001 - Manpower (Salaries, Wages, Director's Remuneration)</option>
                                        <option
                                            value="OHC-002 - Staff Welfare (Medical, Training, Recreational Activities)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHC-002 - Staff Welfare (Medical, Training, Recreational Activities)' ? 'selected' : '' }}>
                                            OHC-002 - Staff Welfare (Medical, Training, Recreational Activities)
                                        </option>
                                        <option value="OHC-003 - Office Rent & Utilities (Electricity, Water, Maintenance)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHC-003 - Office Rent & Utilities (Electricity, Water, Maintenance)' ? 'selected' : '' }}>
                                            OHC-003 - Office Rent & Utilities (Electricity, Water, Maintenance)</option>
                                        <option value="OHC-004 - Insurance (Project, Employee, Equipment)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHC-004 - Insurance (Project, Employee, Equipment)' ? 'selected' : '' }}>
                                            OHC-004 - Insurance (Project, Employee, Equipment)</option>
                                        <option value="OHC-005 - Independent Engineer Fees"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHC-005 - Independent Engineer Fees' ? 'selected' : '' }}>
                                            OHC-005 - Independent Engineer Fees</option>
                                        <option
                                            value="OHC-006 - Travelling & Accommodation (Outstation Travel, Hotel Stays)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHC-006 - Travelling & Accommodation (Outstation Travel, Hotel Stays)' ? 'selected' : '' }}>
                                            OHC-006 - Travelling & Accommodation (Outstation Travel, Hotel Stays)
                                        </option>
                                        <option value="OHC-007 - Local Conveyance (Fuel, Taxi, Transport)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHC-007 - Local Conveyance (Fuel, Taxi, Transport)' ? 'selected' : '' }}>
                                            OHC-007 - Local Conveyance (Fuel, Taxi, Transport)</option>
                                        <option value="OHC-008 - Stationery, Printing & Courier"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHC-008 - Stationery, Printing & Courier' ? 'selected' : '' }}>
                                            OHC-008 - Stationery, Printing & Courier</option>
                                        <option value="OHC-009 - Audit Fees (Statutory & Internal Audit)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHC-009 - Audit Fees (Statutory & Internal Audit)' ? 'selected' : '' }}>
                                            OHC-009 - Audit Fees (Statutory & Internal Audit)</option>
                                        <option value="OHC-010 - Professional Fees (Advisory, Legal, Compliance, Filings)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHC-010 - Professional Fees (Advisory, Legal, Compliance, Filings)' ? 'selected' : '' }}>
                                            OHC-010 - Professional Fees (Advisory, Legal, Compliance, Filings)</option>
                                        <option value="OHC-011 - Communication & IT (Telephone, Internet, Data Management)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHC-011 - Communication & IT (Telephone, Internet, Data Management)' ? 'selected' : '' }}>
                                            OHC-011 - Communication & IT (Telephone, Internet, Data Management)</option>
                                        <option value="OHC-012 - Advertisement & Publicity (Toll Fee & General Awareness)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHC-012 - Advertisement & Publicity (Toll Fee & General Awareness)' ? 'selected' : '' }}>
                                            OHC-012 - Advertisement & Publicity (Toll Fee & General Awareness)</option>
                                        <option value="OHC-013 - Environmental, Health & Safety (EHS) Expenses"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHC-013 - Environmental, Health & Safety (EHS) Expenses' ? 'selected' : '' }}>
                                            OHC-013 - Environmental, Health & Safety (EHS) Expenses</option>
                                        <option value="OHC-014 - General Repair & Maintenance (Office, Equipment)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHC-014 - General Repair & Maintenance (Office, Equipment)' ? 'selected' : '' }}>
                                            OHC-014 - General Repair & Maintenance (Office, Equipment)</option>
                                        <option
                                            value="OHC-015 - Hiring Charges (Vehicles, Equipment, Office Assets) - at HO"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHC-015 - Hiring Charges (Vehicles, Equipment, Office Assets) - at HO' ? 'selected' : '' }}>
                                            OHC-015 - Hiring Charges (Vehicles, Equipment, Office Assets) - at HO
                                        </option>
                                        <option
                                            value="OHC-016 - Hiring Charges (Vehicles, Equipment, Office Assets) - at Projects"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHC-016 - Hiring Charges (Vehicles, Equipment, Office Assets) - at Projects' ? 'selected' : '' }}>
                                            OHC-016 - Hiring Charges (Vehicles, Equipment, Office Assets) - at Projects
                                        </option>
                                        <option value="OHC-017 - Rates & Taxes (Excluding Office Rent)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHC-017 - Rates & Taxes (Excluding Office Rent)' ? 'selected' : '' }}>
                                            OHC-017 - Rates & Taxes (Excluding Office Rent)</option>
                                        <option value="OHC-018 - Bank Charges & Financial Fees"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHC-018 - Bank Charges & Financial Fees' ? 'selected' : '' }}>
                                            OHC-018 - Bank Charges & Financial Fees</option>
                                        <option value="OHC-019 - Survey & Investigation (Land, Traffic, Geotechnical)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHC-019 - Survey & Investigation (Land, Traffic, Geotechnical)' ? 'selected' : '' }}>
                                            OHC-019 - Survey & Investigation (Land, Traffic, Geotechnical)</option>
                                        <option value="OHC-020 - Community Engagement Programs"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHC-020 - Community Engagement Programs' ? 'selected' : '' }}>
                                            OHC-020 - Community Engagement Programs</option>
                                        <option value="OHC-021 - Director Strategy Meeting Expenses"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHC-021 - Director Strategy Meeting Expenses' ? 'selected' : '' }}>
                                            OHC-021 - Director Strategy Meeting Expenses</option>
                                        <option value="OHC-022 - Site Upgradation Cost"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHC-022 - Site Upgradation Cost' ? 'selected' : '' }}>
                                            OHC-022 - Site Upgradation Cost</option>
                                        <option value="OHC-023 - IT Hardware & Software Expenses"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHC-023 - IT Hardware & Software Expenses' ? 'selected' : '' }}>
                                            OHC-023 - IT Hardware & Software Expenses</option>
                                        <option value="OHC-024 - Guest House charges"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHC-024 - Guest House charges' ? 'selected' : '' }}>
                                            OHC-024 - Guest House charges</option>
                                        <option value="OHC-025 - Staff Accommodation & Facilities - at Projects"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHC-025 - Staff Accommodation & Facilities - at Projects' ? 'selected' : '' }}>
                                            OHC-025 - Staff Accommodation & Facilities - at Projects</option>
                                        <option value="OHC-026 - Competency building and Team development"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHC-026 - Competency building and Team development' ? 'selected' : '' }}>
                                            OHC-026 - Competency building and Team development</option>
                                        <option value="OHC-027 - Corporate Communication"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHC-027 - Corporate Communication' ? 'selected' : '' }}>
                                            OHC-027 - Corporate Communication</option>
                                        <option value="OHC-028 - Miscellaneous & Items Not Covered Above"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHC-028 - Miscellaneous & Items Not Covered Above' ? 'selected' : '' }}>
                                            OHC-028 - Miscellaneous & Items Not Covered Above</option>
                                        <option value="OHCC-001 - Manpower Supply Consultancies"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHCC-001 - Manpower Supply Consultancies' ? 'selected' : '' }}>
                                            OHCC-001 - Manpower Supply Consultancies</option>
                                        <option value="OHCC-002 - Technical Due Diligence (TDD)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHCC-002 - Technical Due Diligence (TDD)' ? 'selected' : '' }}>
                                            OHCC-002 - Technical Due Diligence (TDD)</option>
                                        <option value="OHCC-003 - Quality Control and Testing Consultancies"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHCC-003 - Quality Control and Testing Consultancies' ? 'selected' : '' }}>
                                            OHCC-003 - Quality Control and Testing Consultancies</option>
                                        <option value="OHCC-004 - Environmental and Safety Consultancies"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHCC-004 - Environmental and Safety Consultancies' ? 'selected' : '' }}>
                                            OHCC-004 - Environmental and Safety Consultancies</option>
                                        <option value="OHCC-005 - Design and Engineering Consultancies"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHCC-005 - Design and Engineering Consultancies' ? 'selected' : '' }}>
                                            OHCC-005 - Design and Engineering Consultancies</option>
                                        <option value="OHCC-006 - Traffic and Transportation Management Consultancies"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHCC-006 - Traffic and Transportation Management Consultancies' ? 'selected' : '' }}>
                                            OHCC-006 - Traffic and Transportation Management Consultancies</option>
                                        <option value="OHCC-007 - IT and MIS Support Consultancies"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHCC-007 - IT and MIS Support Consultancies' ? 'selected' : '' }}>
                                            OHCC-007 - IT and MIS Support Consultancies</option>
                                        <option value="OHCC-008 - Financial and Legal Advisory Consultancies"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHCC-008 - Financial and Legal Advisory Consultancies' ? 'selected' : '' }}>
                                            OHCC-008 - Financial and Legal Advisory Consultancies</option>
                                        <option value="OHCC-009 - General Consultancy Services"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'OHCC-009 - General Consultancy Services' ? 'selected' : '' }}>
                                            OHCC-009 - General Consultancy Services</option>

                                        <option value="PMC-001 - Purchase of Equipment"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'PMC-001 - Purchase of Equipment' ? 'selected' : '' }}>
                                            PMC-001 - Purchase of Equipment</option>
                                        <option value="PMC-002 - Hiring & Leasing of Equipment"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'PMC-002 - Hiring & Leasing of Equipment' ? 'selected' : '' }}>
                                            PMC-002 - Hiring & Leasing of Equipment</option>
                                        <option value="PMC-003 - Fuel & Lubricants"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'PMC-003 - Fuel & Lubricants' ? 'selected' : '' }}>
                                            PMC-003 - Fuel & Lubricants</option>
                                        <option value="PMC-004 - Repair & Maintenance of Equipment"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'PMC-004 - Repair & Maintenance of Equipment' ? 'selected' : '' }}>
                                            PMC-004 - Repair & Maintenance of Equipment</option>
                                        <option value="PMC-005 - Depreciation & Spares"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'PMC-005 - Depreciation & Spares' ? 'selected' : '' }}>
                                            PMC-005 - Depreciation & Spares</option>
                                        <option value="PMC-006 - Mobilization & Demobilization"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'PMC-006 - Mobilization & Demobilization' ? 'selected' : '' }}>
                                            PMC-006 - Mobilization & Demobilization</option>
                                        <option value="PMC-007 - Operator Charges"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'PMC-007 - Operator Charges' ? 'selected' : '' }}>
                                            PMC-007 - Operator Charges</option>



                                        <option value="DCCC-001 - Site Preparation (Schd-B activity)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCC-001 - Site Preparation (Schd-B activity)' ? 'selected' : '' }}>
                                            DCCC-001 - Site Preparation (Schd-B activity)</option>
                                        <option value="DCCC-002 - Site Enabling Works (Schd-B activity)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCC-002 - Site Enabling Works (Schd-B activity)' ? 'selected' : '' }}>
                                            DCCC-002 - Site Enabling Works (Schd-B activity)</option>
                                        <option
                                            value="DCCC-003 - Clearing & Grubbing, Milling & Dismantling (Schd-B activity)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCC-003 - Clearing & Grubbing, Milling & Dismantling (Schd-B activity)' ? 'selected' : '' }}>
                                            DCCC-003 - Clearing & Grubbing, Milling & Dismantling (Schd-B activity)
                                        </option>
                                        <option
                                            value="DCCC-004 - Earthwork (Cutting, Filling, Embankment, Subgrade) (Schd-B activity)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCC-004 - Earthwork (Cutting, Filling, Embankment, Subgrade) (Schd-B activity)' ? 'selected' : '' }}>
                                            DCCC-004 - Earthwork (Cutting, Filling, Embankment, Subgrade) (Schd-B
                                            activity)
                                        </option>
                                        <option value="DCCC-005 - Granular & WMM Layers (Schd-B activity)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCC-005 - Granular & WMM Layers (Schd-B activity)' ? 'selected' : '' }}>
                                            DCCC-005 - Granular & WMM Layers (Schd-B activity)</option>
                                        <option
                                            value="DCCC-006 - Bituminous Layers (DBM, BC, SMA) - Flexible pavement (Schd-B activity)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCC-006 - Bituminous Layers (DBM, BC, SMA) - Flexible pavement (Schd-B activity)' ? 'selected' : '' }}>
                                            DCCC-006 - Bituminous Layers (DBM, BC, SMA) - Flexible pavement (Schd-B
                                            activity)
                                        </option>
                                        <option
                                            value="DCCC-007 - Concrete Pavement (PQC, DLC) - Rigid pavement (Schd-B activity)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCC-007 - Concrete Pavement (PQC, DLC) - Rigid pavement (Schd-B activity)' ? 'selected' : '' }}>
                                            DCCC-007 - Concrete Pavement (PQC, DLC) - Rigid pavement (Schd-B activity)
                                        </option>
                                        <option value="DCCC-008 - Drainage & Cross-Drainage Works (Schd-B activity)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCC-008 - Drainage & Cross-Drainage Works (Schd-B activity)' ? 'selected' : '' }}>
                                            DCCC-008 - Drainage & Cross-Drainage Works (Schd-B activity)</option>
                                        <option
                                            value="DCCC-009 - Retaining Structures (RE Walls, Gabions) (Schd-B activity)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCC-009 - Retaining Structures (RE Walls, Gabions) (Schd-B activity)' ? 'selected' : '' }}>
                                            DCCC-009 - Retaining Structures (RE Walls, Gabions) (Schd-B activity)
                                        </option>
                                        <option value="DCCC-010 - Bridge & Culverts (Schd-B activity)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCC-010 - Bridge & Culverts (Schd-B activity)' ? 'selected' : '' }}>
                                            DCCC-010 - Bridge & Culverts (Schd-B activity)</option>
                                        <option value="DCCC-011 - Road Marking & Surface Treatments (Schd-B activity)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCC-011 - Road Marking & Surface Treatments (Schd-B activity)' ? 'selected' : '' }}>
                                            DCCC-011 - Road Marking & Surface Treatments (Schd-B activity)</option>
                                        <option value="DCCC-012 - Rain Water Harvesting Pits (Schd-B activity)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCC-012 - Rain Water Harvesting Pits (Schd-B activity)' ? 'selected' : '' }}>
                                            DCCC-012 - Rain Water Harvesting Pits (Schd-B activity)</option>
                                        <option value="DCCC-013 - Medians (Schd-B activity)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCC-013 - Medians (Schd-B activity)' ? 'selected' : '' }}>
                                            DCCC-013 - Medians (Schd-B activity)</option>
                                        <option value="DCCC-014 - Miscellaneous Civil Works (Schd-B activity)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCC-014 - Miscellaneous Civil Works (Schd-B activity)' ? 'selected' : '' }}>
                                            DCCC-014 - Miscellaneous Civil Works (Schd-B activity)</option>
                                        <option value="DCCO-001 - Site Maintenance & Grading (Periodic Maintenance)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCO-001 - Site Maintenance & Grading (Periodic Maintenance)' ? 'selected' : '' }}>
                                            DCCO-001 - Site Maintenance & Grading (Periodic Maintenance)</option>
                                        <option value="DCCO-002 - Temporary Facilities Maintenance (Periodic Maintenance)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCO-002 - Temporary Facilities Maintenance (Periodic Maintenance)' ? 'selected' : '' }}>
                                            DCCO-002 - Temporary Facilities Maintenance (Periodic Maintenance)</option>
                                        <option value="DCCO-003 - Earthwork Repairs & Maintenance (Periodic Maintenance)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCO-003 - Earthwork Repairs & Maintenance (Periodic Maintenance)' ? 'selected' : '' }}>
                                            DCCO-003 - Earthwork Repairs & Maintenance (Periodic Maintenance)</option>
                                        <option value="DCCO-004 - Base Layer Repairs & Recompaction (Periodic Maintenance)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCO-004 - Base Layer Repairs & Recompaction (Periodic Maintenance)' ? 'selected' : '' }}>
                                            DCCO-004 - Base Layer Repairs & Recompaction (Periodic Maintenance)</option>
                                        <option
                                            value="DCCO-005 - Bituminous Layer Maintenance (Resurfacing, Patchwork) (Periodic Maintenance)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCO-005 - Bituminous Layer Maintenance (Resurfacing, Patchwork) (Periodic Maintenance)' ? 'selected' : '' }}>
                                            DCCO-005 - Bituminous Layer Maintenance (Resurfacing, Patchwork) (Periodic
                                            Maintenance)</option>
                                        <option value="DCCO-006 - Concrete Pavement Repairs (Periodic Maintenance)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCO-006 - Concrete Pavement Repairs (Periodic Maintenance)' ? 'selected' : '' }}>
                                            DCCO-006 - Concrete Pavement Repairs (Periodic Maintenance)</option>
                                        <option
                                            value="DCCO-007 - Drainage System Overhaul & Major Repairs (Periodic Maintenance)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCO-007 - Drainage System Overhaul & Major Repairs (Periodic Maintenance)' ? 'selected' : '' }}>
                                            DCCO-007 - Drainage System Overhaul & Major Repairs (Periodic Maintenance)
                                        </option>
                                        <option
                                            value="DCCO-008 - Retaining Wall & Gabion Structural Repairs (Periodic Maintenance)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCO-008 - Retaining Wall & Gabion Structural Repairs (Periodic Maintenance)' ? 'selected' : '' }}>
                                            DCCO-008 - Retaining Wall & Gabion Structural Repairs (Periodic Maintenance)
                                        </option>
                                        <option
                                            value="DCCO-009 - Bridge & Culvert Repairs & Maintenance (Periodic Maintenance)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCO-009 - Bridge & Culvert Repairs & Maintenance (Periodic Maintenance)' ? 'selected' : '' }}>
                                            DCCO-009 - Bridge & Culvert Repairs & Maintenance (Periodic Maintenance)
                                        </option>
                                        <option value="DCCO-010 - Minor Civil Works & Repairs (Periodic Maintenance)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCO-010 - Minor Civil Works & Repairs (Periodic Maintenance)' ? 'selected' : '' }}>
                                            DCCO-010 - Minor Civil Works & Repairs (Periodic Maintenance)</option>

                                        <option value="DCCO-011 - Shoulder & Median Maintenance (Periodic Maintenance)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCO-011 - Shoulder & Median Maintenance (Periodic Maintenance)' ? 'selected' : '' }}>
                                            DCCO-011 - Shoulder & Median Maintenance (Periodic Maintenance)
                                        </option>
                                        <option value="DCCO-012 - Roadside Emergency Response (Periodic Maintenance)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCO-012 - Roadside Emergency Response (Periodic Maintenance)' ? 'selected' : '' }}>
                                            DCCO-012 - Roadside Emergency Response (Periodic Maintenance)
                                        </option>
                                        <option value="DCCO-013 - Tunnel & Underpass Maintenance (Periodic Maintenance)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCO-013 - Tunnel & Underpass Maintenance (Periodic Maintenance)' ? 'selected' : '' }}>
                                            DCCO-013 - Tunnel & Underpass Maintenance (Periodic Maintenance)
                                        </option>
                                        <option value="DCCO-014 - Miscellaneous Minor Works (Periodic Maintenance)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCO-014 - Miscellaneous Minor Works (Periodic Maintenance)' ? 'selected' : '' }}>
                                            DCCO-014 - Miscellaneous Minor Works (Periodic Maintenance)
                                        </option>
                                        <option value="DCEC-001 - HT & LT Cabling Works (Supply & Installation) (Capex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCEC-001 - HT & LT Cabling Works (Supply & Installation) (Capex)' ? 'selected' : '' }}>
                                            DCEC-001 - HT & LT Cabling Works (Supply & Installation) (Capex)
                                        </option>
                                        <option value="DCEC-002 - Street Lighting & Poles (Supply & Installation) (Capex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCEC-002 - Street Lighting & Poles (Supply & Installation) (Capex)' ? 'selected' : '' }}>
                                            DCEC-002 - Street Lighting & Poles (Supply & Installation) (Capex)
                                        </option>
                                        <option value="DCEC-003 - Earthing System (Capex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCEC-003 - Earthing System (Capex)' ? 'selected' : '' }}>
                                            DCEC-003 - Earthing System (Capex)
                                        </option>
                                        <option value="DCEC-004 - Power Supply & Transformers (Capex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCEC-004 - Power Supply & Transformers (Capex)' ? 'selected' : '' }}>
                                            DCEC-004 - Power Supply & Transformers (Capex)
                                        </option>
                                        <option value="DCEC-005 - Roadside Electrical Panels & Automation (Capex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCEC-005 - Roadside Electrical Panels & Automation (Capex)' ? 'selected' : '' }}>
                                            DCEC-005 - Roadside Electrical Panels & Automation (Capex)
                                        </option>
                                        <option value="DCEC-006 - HPSV to LED Conversion (Capex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCEC-006 - HPSV to LED Conversion (Capex)' ? 'selected' : '' }}>
                                            DCEC-006 - HPSV to LED Conversion (Capex)
                                        </option>
                                        <option value="DCEC-007 - Solar Power System Installation (Capex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCEC-007 - Solar Power System Installation (Capex)' ? 'selected' : '' }}>
                                            DCEC-007 - Solar Power System Installation (Capex)
                                        </option>
                                        <option value="DCEC-008 - Public EV Charging Station (Capex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCEC-008 - Public EV Charging Station (Capex)' ? 'selected' : '' }}>
                                            DCEC-008 - Public EV Charging Station (Capex)
                                        </option>
                                        <option value="DCEC-009 - DG Set Supply & Installation (Capex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCEC-009 - DG Set Supply & Installation (Capex)' ? 'selected' : '' }}>
                                            DCEC-009 - DG Set Supply & Installation (Capex)
                                        </option>
                                        <option value="DCEC-010 - Electrical & Electronics Appliances (Capex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCEC-010 - Electrical & Electronics Appliances (Capex)' ? 'selected' : '' }}>
                                            DCEC-010 - Electrical & Electronics Appliances (Capex)
                                        </option>
                                        <option value="DCEC-011 - Electrical Spares - First time purchase (Capex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCEC-011 - Electrical Spares - First time purchase (Capex)' ? 'selected' : '' }}>
                                            DCEC-011 - Electrical Spares - First time purchase (Capex)
                                        </option>
                                        <option value="DCEC-012 - SCADA Integration & Electrical Automation (Capex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCEC-012 - SCADA Integration & Electrical Automation (Capex)' ? 'selected' : '' }}>
                                            DCEC-012 - SCADA Integration & Electrical Automation (Capex)
                                        </option>
                                        <option value="DCEC-013 - Miscellaneous Electrical (Capex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCEC-013 - Miscellaneous Electrical (Capex)' ? 'selected' : '' }}>
                                            DCEC-013 - Miscellaneous Electrical (Capex)
                                        </option>
                                        <option value="DCEO-001 - HT & LT Cabling Works (Maintenance & Repairs) (Opex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCEO-001 - HT & LT Cabling Works (Maintenance & Repairs) (Opex)' ? 'selected' : '' }}>
                                            DCEO-001 - HT & LT Cabling Works (Maintenance & Repairs) (Opex)
                                        </option>
                                        <option value="DCEO-002 - Street Lighting & Poles (Maintenance & Repairs) (Opex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCEO-002 - Street Lighting & Poles (Maintenance & Repairs) (Opex)' ? 'selected' : '' }}>
                                            DCEO-002 - Street Lighting & Poles (Maintenance & Repairs) (Opex)
                                        </option>
                                        <option value="DCEO-003 - Earthing System (Maintenance & Testing) (Opex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCEO-003 - Earthing System (Maintenance & Testing) (Opex)' ? 'selected' : '' }}>
                                            DCEO-003 - Earthing System (Maintenance & Testing) (Opex)
                                        </option>
                                        <option
                                            value="DCEO-004 - Power Supply & Transformers (Maintenance & Repairs) (Opex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCEO-004 - Power Supply & Transformers (Maintenance & Repairs) (Opex)' ? 'selected' : '' }}>
                                            DCEO-004 - Power Supply & Transformers (Maintenance & Repairs) (Opex)
                                        </option>
                                        <option
                                            value="DCEO-005 - Roadside Electrical Panels & Automation (Maintenance) (Opex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCEO-005 - Roadside Electrical Panels & Automation (Maintenance) (Opex)' ? 'selected' : '' }}>
                                            DCEO-005 - Roadside Electrical Panels & Automation (Maintenance) (Opex)
                                        </option>
                                        <option value="DCEO-006 - LED Street Light Maintenance (Opex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCEO-006 - LED Street Light Maintenance (Opex)' ? 'selected' : '' }}>
                                            DCEO-006 - LED Street Light Maintenance (Opex)
                                        </option>
                                        <option value="DCEO-007 - Solar Power System Maintenance (Opex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCEO-007 - Solar Power System Maintenance (Opex)' ? 'selected' : '' }}>
                                            DCEO-007 - Solar Power System Maintenance (Opex)
                                        </option>
                                        <option value="DCEO-008 - EV Charging Station Maintenance (Opex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCEO-008 - EV Charging Station Maintenance (Opex)' ? 'selected' : '' }}>
                                            DCEO-008 - EV Charging Station Maintenance (Opex)
                                        </option>
                                        <option value="DCEO-009 - DG Set Maintenance (Fuel, Lubricants & Servicing) (Opex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCEO-009 - DG Set Maintenance (Fuel, Lubricants & Servicing) (Opex)' ? 'selected' : '' }}>
                                            DCEO-009 - DG Set Maintenance (Fuel, Lubricants & Servicing) (Opex)
                                        </option>
                                        <option value="DCEO-010 - Electrical & Electronics Appliances (Opex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCEO-010 - Electrical & Electronics Appliances (Opex)' ? 'selected' : '' }}>
                                            DCEO-010 - Electrical & Electronics Appliances (Opex)
                                        </option>
                                        <option value="DCEO-011 - Electrical Spares - Regular Maintenance (Opex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCEO-011 - Electrical Spares - Regular Maintenance (Opex)' ? 'selected' : '' }}>
                                            DCEO-011 - Electrical Spares - Regular Maintenance (Opex)
                                        </option>
                                        <option
                                            value="DCEO-012 - SCADA Integration & Electrical Automation Maintenance (Opex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCEO-012 - SCADA Integration & Electrical Automation Maintenance (Opex)' ? 'selected' : '' }}>
                                            DCEO-012 - SCADA Integration & Electrical Automation Maintenance (Opex)
                                        </option>
                                        <option value="DCEO-013 - Miscellaneous Electrical  (Opex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCEO-013 - Miscellaneous Electrical  (Opex)' ? 'selected' : '' }}>
                                            DCEO-013 - Miscellaneous Electrical (Opex)
                                        </option>

                                        <option value="ITS-001 - Smart Traffic & ITS Systems (Capex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'ITS-001 - Smart Traffic & ITS Systems (Capex)' ? 'selected' : '' }}>
                                            ITS-001 - Smart Traffic & ITS Systems (Capex)
                                        </option>
                                        <option value="ITS-002 - Communication Systems (CCTV, PA, Wireless) (Capex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'ITS-002 - Communication Systems (CCTV, PA, Wireless) (Capex)' ? 'selected' : '' }}>
                                            ITS-002 - Communication Systems (CCTV, PA, Wireless) (Capex)
                                        </option>
                                        <option value="ITS-003 - ATMS Procurement & Deployment (Capex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'ITS-003 - ATMS Procurement & Deployment (Capex)' ? 'selected' : '' }}>
                                            ITS-003 - ATMS Procurement & Deployment (Capex)
                                        </option>
                                        <option value="ITS-004 - TMS (Toll Management System) Procurement (Capex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'ITS-004 - TMS (Toll Management System) Procurement (Capex)' ? 'selected' : '' }}>
                                            ITS-004 - TMS (Toll Management System) Procurement (Capex)
                                        </option>
                                        <option value="ITS-005 - Weigh-in-Motion (Capex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'ITS-005 - Weigh-in-Motion (Capex)' ? 'selected' : '' }}>
                                            ITS-005 - Weigh-in-Motion (Capex)
                                        </option>
                                        <option value="ITS-006 - Static Weigh Bridge (including civil works) (Capex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'ITS-006 - Static Weigh Bridge (including civil works) (Capex)' ? 'selected' : '' }}>
                                            ITS-006 - Static Weigh Bridge (including civil works) (Capex)
                                        </option>
                                        <option value="DSE-001 - Environmental & Safety Compliance"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DSE-001 - Environmental & Safety Compliance' ? 'selected' : '' }}>
                                            DSE-001 - Environmental & Safety Compliance
                                        </option>
                                        <option value="DSE-002 - Traffic Cones & Safety Barriers"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DSE-002 - Traffic Cones & Safety Barriers' ? 'selected' : '' }}>
                                            DSE-002 - Traffic Cones & Safety Barriers
                                        </option>
                                        <option value="DSE-003 - Sustainability program"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DSE-003 - Sustainability program' ? 'selected' : '' }}>
                                            DSE-003 - Sustainability program
                                        </option>
                                        <option value="DSE-004 - Road Safety Aids"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DSE-004 - Road Safety Aids' ? 'selected' : '' }}>
                                            DSE-004 - Road Safety Aids
                                        </option>
                                        <option value="DSE-005 - Personal protective Equipment"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DSE-005 - Personal protective Equipment' ? 'selected' : '' }}>
                                            DSE-005 - Personal protective Equipment
                                        </option>
                                        <option value="DSE-006 - Waste Disposal & Site Cleanup"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DSE-006 - Waste Disposal & Site Cleanup' ? 'selected' : '' }}>
                                            DSE-006 - Waste Disposal & Site Cleanup
                                        </option>
                                        <option value="DCS-001 - Project Management Consultancies (PMC)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCS-001 - Project Management Consultancies (PMC)' ? 'selected' : '' }}>
                                            DCS-001 - Project Management Consultancies (PMC)
                                        </option>
                                        <option value="DCS-002 - Asset Monitoring & Performance – Road"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCS-002 - Asset Monitoring & Performance – Road' ? 'selected' : '' }}>
                                            DCS-002 - Asset Monitoring & Performance – Road
                                        </option>
                                        <option value="DCS-003 - Asset Monitoring & Performance – Road Asset Furniture"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCS-003 - Asset Monitoring & Performance – Road Asset Furniture' ? 'selected' : '' }}>
                                            DCS-003 - Asset Monitoring & Performance – Road Asset Furniture
                                        </option>
                                        <option value="DCS-004 - Asset Monitoring & Performance – Structure"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCS-004 - Asset Monitoring & Performance – Structure' ? 'selected' : '' }}>
                                            DCS-004 - Asset Monitoring & Performance – Structure
                                        </option>
                                        <option
                                            value="DCS-005 - Revalidation Survey (Traffic, Revenue & Condition Assessment)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCS-005 - Revalidation Survey (Traffic, Revenue & Condition Assessment)' ? 'selected' : '' }}>
                                            DCS-005 - Revalidation Survey (Traffic, Revenue & Condition Assessment)
                                        </option>
                                        <option value="DCP-001 - Bitumen (VG30, VG40, PMB, Emulsion)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCP-001 - Bitumen (VG30, VG40, PMB, Emulsion)' ? 'selected' : '' }}>
                                            DCP-001 - Bitumen (VG30, VG40, PMB, Emulsion)
                                        </option>
                                        <option value="DCP-002 - Cement (OPC, PPC, Fly Ash Based)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCP-002 - Cement (OPC, PPC, Fly Ash Based)' ? 'selected' : '' }}>
                                            DCP-002 - Cement (OPC, PPC, Fly Ash Based)
                                        </option>
                                        <option value="DCP-003 - Structural Steel (TMT, Plates, Girders)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCP-003 - Structural Steel (TMT, Plates, Girders)' ? 'selected' : '' }}>
                                            DCP-003 - Structural Steel (TMT, Plates, Girders)
                                        </option>
                                        <option value="DCP-004 - Aggregates & Sand"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCP-004 - Aggregates & Sand' ? 'selected' : '' }}>
                                            DCP-004 - Aggregates & Sand
                                        </option>
                                        <option value="DCP-005 - Admixtures & Chemicals"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCP-005 - Admixtures & Chemicals' ? 'selected' : '' }}>
                                            DCP-005 - Admixtures & Chemicals
                                        </option>
                                        <option value="DCP-006 - Precast Elements"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCP-006 - Precast Elements' ? 'selected' : '' }}>
                                            DCP-006 - Precast Elements
                                        </option>
                                        <option value="DCP-007 - Water & Binding Materials"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCP-007 - Water & Binding Materials' ? 'selected' : '' }}>
                                            DCP-007 - Water & Binding Materials
                                        </option>
                                        <option value="DCP-008 - Formwork & Temporary Construction Materials"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCP-008 - Formwork & Temporary Construction Materials' ? 'selected' : '' }}>
                                            DCP-008 - Formwork & Temporary Construction Materials
                                        </option>
                                        <option value="DCP-009 - Specialized Construction Materials"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCP-009 - Specialized Construction Materials' ? 'selected' : '' }}>
                                            DCP-009 - Specialized Construction Materials
                                        </option>
                                        <option value="DCP-010 - Miscellaneous Civil Construction Materials"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCP-010 - Miscellaneous Civil Construction Materials' ? 'selected' : '' }}>
                                            DCP-010 - Miscellaneous Civil Construction Materials
                                        </option>
                                        <option value="DRF-001 - Road Signages, Gantries & Reflective Markings"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DRF-001 - Road Signages, Gantries & Reflective Markings' ? 'selected' : '' }}>
                                            DRF-001 - Road Signages, Gantries & Reflective Markings
                                        </option>
                                        <option value="DRF-002 - Reflective Pavement Markers"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DRF-002 - Reflective Pavement Markers' ? 'selected' : '' }}>
                                            DRF-002 - Reflective Pavement Markers
                                        </option>
                                        <option value="DRF-003 - Road Marking Repainting & Surface Maintenance"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DRF-003 - Road Marking Repainting & Surface Maintenance' ? 'selected' : '' }}>
                                            DRF-003 - Road Marking Repainting & Surface Maintenance
                                        </option>
                                        <option value="DRF-004 - Crash Barriers (W-Beam, Thrie-Beam, Concrete, Wire)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DRF-004 - Crash Barriers (W-Beam, Thrie-Beam, Concrete, Wire)' ? 'selected' : '' }}>
                                            DRF-004 - Crash Barriers (W-Beam, Thrie-Beam, Concrete, Wire)
                                        </option>
                                        <option value="DRF-005 - Bamboo Crash Barriers"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DRF-005 - Bamboo Crash Barriers' ? 'selected' : '' }}>
                                            DRF-005 - Bamboo Crash Barriers
                                        </option>
                                        <option value="DRF-006 - Kerb Stones (Capex & During Periodic Maintenance)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DRF-006 - Kerb Stones (Capex & During Periodic Maintenance)' ? 'selected' : '' }}>
                                            DRF-006 - Kerb Stones (Capex & During Periodic Maintenance)
                                        </option>
                                        <option value="DRF-007 - Handrails & Railings"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DRF-007 - Handrails & Railings' ? 'selected' : '' }}>
                                            DRF-007 - Handrails & Railings
                                        </option>
                                        <option value="DRF-008 - Guardrails & Crash Cushions"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DRF-008 - Guardrails & Crash Cushions' ? 'selected' : '' }}>
                                            DRF-008 - Guardrails & Crash Cushions
                                        </option>
                                        <option value="DRF-009 - Bollards & Delineators"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DRF-009 - Bollards & Delineators' ? 'selected' : '' }}>
                                            DRF-009 - Bollards & Delineators
                                        </option>
                                        <option value="DRF-010 - Speed Breakers (Rubber, Concrete, Plastic)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DRF-010 - Speed Breakers (Rubber, Concrete, Plastic)' ? 'selected' : '' }}>
                                            DRF-010 - Speed Breakers (Rubber, Concrete, Plastic)
                                        </option>
                                        <option value="DRF-011 - Rumble Strips"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DRF-011 - Rumble Strips' ? 'selected' : '' }}>
                                            DRF-011 - Rumble Strips
                                        </option>
                                        <option value="DRF-012 - Bus Shelters"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DRF-012 - Bus Shelters' ? 'selected' : '' }}>
                                            DRF-012 - Bus Shelters
                                        </option>
                                        <option value="DRF-013 - Foot Overbridges (FOBs) & Pedestrian Underpasses (PUPs)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DRF-013 - Foot Overbridges (FOBs) & Pedestrian Underpasses (PUPs)' ? 'selected' : '' }}>
                                            DRF-013 - Foot Overbridges (FOBs) & Pedestrian Underpasses (PUPs)
                                        </option>
                                        <option value="DRF-014 - Benches & Waiting Sheds"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DRF-014 - Benches & Waiting Sheds' ? 'selected' : '' }}>
                                            DRF-014 - Benches & Waiting Sheds
                                        </option>
                                        <option value="DRF-015 - Cycle Stands"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DRF-015 - Cycle Stands' ? 'selected' : '' }}>
                                            DRF-015 - Cycle Stands
                                        </option>
                                        <option value="DRF-016 - Boom Barriers & Automated Gates"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DRF-016 - Boom Barriers & Automated Gates' ? 'selected' : '' }}>
                                            DRF-016 - Boom Barriers & Automated Gates
                                        </option>
                                        <option value="DRF-017 - Road Blockers & Bollard Systems"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DRF-017 - Road Blockers & Bollard Systems' ? 'selected' : '' }}>
                                            DRF-017 - Road Blockers & Bollard Systems
                                        </option>
                                        <option
                                            value="DOMT-001 - Manpower (Toll Collection, Security & Traffic Management)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMT-001 - Manpower (Toll Collection, Security & Traffic Management)' ? 'selected' : '' }}>
                                            DOMT-001 - Manpower (Toll Collection, Security & Traffic Management)
                                        </option>
                                        <option value="DOMT-002 - Power Charges"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMT-002 - Power Charges' ? 'selected' : '' }}>
                                            DOMT-002 - Power Charges
                                        </option>
                                        <option value="DOMT-003 - TCMS Operations"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMT-003 - TCMS Operations' ? 'selected' : '' }}>
                                            DOMT-003 - TCMS Operations
                                        </option>
                                        <option value="DOMT-004 - TCMS Spares"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMT-004 - TCMS Spares' ? 'selected' : '' }}>
                                            DOMT-004 - TCMS Spares
                                        </option>
                                        <option value="DOMT-005 - Internet Lease Line / Broadband"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMT-005 - Internet Lease Line / Broadband' ? 'selected' : '' }}>
                                            DOMT-005 - Internet Lease Line / Broadband
                                        </option>
                                        <option value="DOMT-006 - Office Running Expenses"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMT-006 - Office Running Expenses' ? 'selected' : '' }}>
                                            DOMT-006 - Office Running Expenses
                                        </option>
                                        <option value="DOMT-007 - Toll Operations - Outsourced Staff by SPV"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMT-007 - Toll Operations - Outsourced Staff by SPV' ? 'selected' : '' }}>
                                            DOMT-007 - Toll Operations - Outsourced Staff by SPV
                                        </option>
                                        <option value="DOMT-008 - Toll Operation Vehicle at Toll Plaza"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMT-008 - Toll Operation Vehicle at Toll Plaza' ? 'selected' : '' }}>
                                            DOMT-008 - Toll Operation Vehicle at Toll Plaza
                                        </option>
                                        <option value="DOMI-001 - Route Patrolling"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMI-001 - Route Patrolling' ? 'selected' : '' }}>
                                            DOMI-001 - Route Patrolling
                                        </option>
                                        <option value="DOMI-002 - Ambulance Services"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMI-002 - Ambulance Services' ? 'selected' : '' }}>
                                            DOMI-002 - Ambulance Services
                                        </option>
                                        <option value="DOMI-003 - Recovery Cranes"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMI-003 - Recovery Cranes' ? 'selected' : '' }}>
                                            DOMI-003 - Recovery Cranes
                                        </option>
                                        <option value="DOMI-004 - Police Assistance Vehicle"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMI-004 - Police Assistance Vehicle' ? 'selected' : '' }}>
                                            DOMI-004 - Police Assistance Vehicle
                                        </option>
                                        <option value="DOMI-005 - Tow Vehicle"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMI-005 - Tow Vehicle' ? 'selected' : '' }}>
                                            DOMI-005 - Tow Vehicle
                                        </option>
                                        <option value="DOMI-006 - Mechanical Broom"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMI-006 - Mechanical Broom' ? 'selected' : '' }}>
                                            DOMI-006 - Mechanical Broom
                                        </option>
                                        <option value="DOMI-007 - Fuel & Lubricants for Incident Management Vehicles"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMI-007 - Fuel & Lubricants for Incident Management Vehicles' ? 'selected' : '' }}>
                                            DOMI-007 - Fuel & Lubricants for Incident Management Vehicles
                                        </option>
                                        <option value="DOMI-008 - Manpower for Incident Management"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMI-008 - Manpower for Incident Management' ? 'selected' : '' }}>
                                            DOMI-008 - Manpower for Incident Management
                                        </option>
                                        <option value="DOMI-009 - Miscellaneous Expenses for Incident Management"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMI-009 - Miscellaneous Expenses for Incident Management' ? 'selected' : '' }}>
                                            DOMI-009 - Miscellaneous Expenses for Incident Management
                                        </option>
                                        <option value="DOMM-001 - Routine Maintenance - Works (Opex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMM-001 - Routine Maintenance - Works (Opex)' ? 'selected' : '' }}>
                                            DOMM-001 - Routine Maintenance - Works (Opex)
                                        </option>
                                        <option value="DOMM-002 - Routine Maintenance - Manpower (Opex)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMM-002 - Routine Maintenance - Manpower (Opex)' ? 'selected' : '' }}>
                                            DOMM-002 - Routine Maintenance - Manpower (Opex)
                                        </option>
                                        <option value="DOMM-003 - Highway Lighting (Incl. Energy Charges)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMM-003 - Highway Lighting (Incl. Energy Charges)' ? 'selected' : '' }}>
                                            DOMM-003 - Highway Lighting (Incl. Energy Charges)
                                        </option>
                                        <option
                                            value="DOMR-001 - R&R - Routine Maintenance (General Upkeep & Minor Repairs)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMR-001 - R&R - Routine Maintenance (General Upkeep & Minor Repairs)' ? 'selected' : '' }}>
                                            DOMR-001 - R&R - Routine Maintenance (General Upkeep & Minor Repairs)
                                        </option>
                                        <option value="DOMR-002 - R&R - Pavement"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMR-002 - R&R - Pavement' ? 'selected' : '' }}>
                                            DOMR-002 - R&R - Pavement
                                        </option>
                                        <option value="DOMR-003 - R&R - Drainage"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMR-003 - R&R - Drainage' ? 'selected' : '' }}>
                                            DOMR-003 - R&R - Drainage
                                        </option>
                                        <option value="DOMR-004 - R&R- Shoulders, Slopes, Earthworks"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMR-004 - R&R- Shoulders, Slopes, Earthworks' ? 'selected' : '' }}>
                                            DOMR-004 - R&R- Shoulders, Slopes, Earthworks
                                        </option>
                                        <option value="DOMR-005 - R&R - Road Furniture"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMR-005 - R&R - Road Furniture' ? 'selected' : '' }}>
                                            DOMR-005 - R&R - Road Furniture
                                        </option>
                                        <option value="DOMR-006 - R&R - Structures"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMR-006 - R&R - Structures' ? 'selected' : '' }}>
                                            DOMR-006 - R&R - Structures
                                        </option>

                                        <option value="DOMR-007 - R&R - Toll Plaza & Buildings Maintenance"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMR-007 - R&R - Toll Plaza & Buildings Maintenance' ? 'selected' : '' }}>
                                            DOMR-007 - R&R - Toll Plaza & Buildings Maintenance
                                        </option>
                                        <option value="DOMR-008 - R&R - Horticulture"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMR-008 - R&R - Horticulture' ? 'selected' : '' }}>
                                            DOMR-008 - R&R - Horticulture
                                        </option>
                                        <option value="DOMR-009 - R&R - Contingency Expenses"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMR-009 - R&R - Contingency Expenses' ? 'selected' : '' }}>
                                            DOMR-009 - R&R - Contingency Expenses
                                        </option>
                                        <option value="DOMA-001 - Traffic Management Centre & Sub-Centre"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMA-001 - Traffic Management Centre & Sub-Centre' ? 'selected' : '' }}>
                                            DOMA-001 - Traffic Management Centre & Sub-Centre
                                        </option>
                                        <option value="DOMA-002 - Traffic Monitoring Camera System Equipment (TMCS)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMA-002 - Traffic Monitoring Camera System Equipment (TMCS)' ? 'selected' : '' }}>
                                            DOMA-002 - Traffic Monitoring Camera System Equipment (TMCS)
                                        </option>
                                        <option value="DOMA-003 - Video Incident Detection System Equipment (VIDS)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMA-003 - Video Incident Detection System Equipment (VIDS)' ? 'selected' : '' }}>
                                            DOMA-003 - Video Incident Detection System Equipment (VIDS)
                                        </option>
                                        <option
                                            value="DOMA-004 - Vehicle Speed Detection System Equipment (VSDS) (LHS + RHS)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMA-004 - Vehicle Speed Detection System Equipment (VSDS) (LHS + RHS)' ? 'selected' : '' }}>
                                            DOMA-004 - Vehicle Speed Detection System Equipment (VSDS) (LHS + RHS)
                                        </option>
                                        <option value="DOMA-005 - Control Room Manpower"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMA-005 - Control Room Manpower' ? 'selected' : '' }}>
                                            DOMA-005 - Control Room Manpower
                                        </option>
                                        <option value="DOMA-006 - Power Charges for ATMS"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMA-006 - Power Charges for ATMS' ? 'selected' : '' }}>
                                            DOMA-006 - Power Charges for ATMS
                                        </option>
                                        <option value="DOMA-007 - Fiber Backbone Maintenance"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DOMA-007 - Fiber Backbone Maintenance' ? 'selected' : '' }}>
                                            DOMA-007 - Fiber Backbone Maintenance
                                        </option>
                                        <option value="DQT-001 - Material Testing (Lab & Field)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DQT-001 - Material Testing (Lab & Field)' ? 'selected' : '' }}>
                                            DQT-001 - Material Testing (Lab & Field)
                                        </option>
                                        <option value="DQT-002 - Quality Assurance & Certification"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DQT-002 - Quality Assurance & Certification' ? 'selected' : '' }}>
                                            DQT-002 - Quality Assurance & Certification
                                        </option>
                                        <option value="DQT-003 - Third-Party Audit & Inspection"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DQT-003 - Third-Party Audit & Inspection' ? 'selected' : '' }}>
                                            DQT-003 - Third-Party Audit & Inspection
                                        </option>
                                        <option value="DQT-004 - Calibration of Equipment"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DQT-004 - Calibration of Equipment' ? 'selected' : '' }}>
                                            DQT-004 - Calibration of Equipment
                                        </option>
                                        <option value="DQT-005 - Non-Destructive Testing (NDT)"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DQT-005 - Non-Destructive Testing (NDT)' ? 'selected' : '' }}>
                                            DQT-005 - Non-Destructive Testing (NDT)
                                        </option>
                                        <option value="DQT-006 - Core Cutting & Strength Testing"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DQT-006 - Core Cutting & Strength Testing' ? 'selected' : '' }}>
                                            DQT-006 - Core Cutting & Strength Testing
                                        </option>
                                        <option value="DQT-007 - Lab equipment & Survey Tools & equipment"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DQT-007 - Lab equipment & Survey Tools & equipment' ? 'selected' : '' }}>
                                            DQT-007 - Lab equipment & Survey Tools & equipment
                                        </option>
                                        <option value="DTP-001 - Toll Booth & Canopy Construction"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DTP-001 - Toll Booth & Canopy Construction' ? 'selected' : '' }}>
                                            DTP-001 - Toll Booth & Canopy Construction
                                        </option>
                                        <option value="DTP-002 - Toll Collection System Installation"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DTP-002 - Toll Collection System Installation' ? 'selected' : '' }}>
                                            DTP-002 - Toll Collection System Installation
                                        </option>
                                        <option value="DTP-003 - Weigh-in-Motion (WIM) System - Maintenance"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DTP-003 - Weigh-in-Motion (WIM) System - Maintenance' ? 'selected' : '' }}>
                                            DTP-003 - Weigh-in-Motion (WIM) System - Maintenance
                                        </option>
                                        <option value="DTP-004 - Static Weighbridge - Maintenance"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DTP-004 - Static Weighbridge - Maintenance' ? 'selected' : '' }}>
                                            DTP-004 - Static Weighbridge - Maintenance
                                        </option>
                                        <option value="DTP-005 - Surveillance & Security System"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DTP-005 - Surveillance & Security System' ? 'selected' : '' }}>
                                            DTP-005 - Surveillance & Security System
                                        </option>
                                        <option
                                            value="DTP-006 - Project Buildings - Toll Plaza & Ancillary Structures) - Capex"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DTP-006 - Project Buildings - Toll Plaza & Ancillary Structures) - Capex' ? 'selected' : '' }}>
                                            DTP-006 - Project Buildings - Toll Plaza & Ancillary Structures) - Capex
                                        </option>
                                        <option value="DTP-007 - Queue Managers"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DTP-007 - Queue Managers' ? 'selected' : '' }}>
                                            DTP-007 - Queue Managers
                                        </option>
                                        <option value="DMC-001 - Temporary Works & Traffic Management"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DMC-001 - Temporary Works & Traffic Management' ? 'selected' : '' }}>
                                            DMC-001 - Temporary Works & Traffic Management
                                        </option>
                                        <option value="DMC-002 - Water & Power Supply for Construction"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DMC-002 - Water & Power Supply for Construction' ? 'selected' : '' }}>
                                            DMC-002 - Water & Power Supply for Construction
                                        </option>
                                        <option value="DCCU-001 - Change of Scope & Utility shifting"
                                            {{ old('nature_of_expenses', $note->nature_of_expenses) == 'DCCU-001 - Change of Scope & Utility shifting' ? 'selected' : '' }}>
                                            DCCU-001 - Change of Scope & Utility shifting
                                        </option>
                                    </select>
                                    @error('nature_of_expenses')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label for="milestone_status" class="form-label">Milestone Status</label>
                                    <select class="form-select form-control" id="milestone_status"
                                        name="milestone_status">
                                        <option value="Y"
                                            {{ old('milestone_status', $note->milestone_status) == 'Y' ? 'selected' : '' }}>
                                            Yes
                                        </option>
                                        <option value="N"
                                            {{ old('milestone_status', $note->milestone_status) == 'N' ? 'selected' : '' }}>
                                            No
                                        </option>
                                    </select>
                                    @error('milestone_status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6" id="file_input_3" style="display:none;">
                                    <label for="twentyone" class="form-label">Milestone Remarks</label>
                                    <!--Conditional on Milestone Status if No - show this -->
                                    <textarea class="form-control form-text" id="twentyone" name="milestone_remarks">{{ old('milestone_remarks', $note->milestone_remarks) }}</textarea>
                                    @error('milestone_remarks')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-6">
                                    <label for="expense_amount_within_contract" class="form-label">Expense amount
                                        within
                                        contract</label>
                                    <select class="form-select form-control" id="expense_amount_within_contract"
                                        name="expense_amount_within_contract">
                                        <option value="Y"
                                            {{ old('expense_amount_within_contract', $note->expense_amount_within_contract) == 'Y' ? 'selected' : '' }}>
                                            Yes
                                        </option>
                                        <option value="N"
                                            {{ old('expense_amount_within_contract', $note->expense_amount_within_contract) == 'N' ? 'selected' : '' }}>
                                            No
                                        </option>
                                    </select>
                                    @error('expense_amount_within_contract')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6" id="file_input_5" style="display:none;">
                                    <label for="twentyone" class="form-label">Remarks</label>
                                    <textarea class="form-control form-text" id="twentyone" name="specify_deviation">{{ old('specify_deviation', $note->specify_deviation) }}</textarea>
                                    @error('specify_deviation')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label for="deviations" class="form-label">If payment approved with
                                        Deviation</label>
                                    <select class="form-select form-control" id="deviations" name="deviations">
                                        <option value="Y"
                                            {{ old('deviations', $note->deviations) == 'Y' ? 'selected' : '' }}>Yes
                                        </option>
                                        <option value="N"
                                            {{ old('deviations', $note->deviations) == 'N' ? 'selected' : '' }}>No
                                        </option>
                                    </select>
                                    <input type="file" id="file_input_6" name="file_input_6"
                                        class="form-control mt-2" style="display:none;"
                                        accept=".pdf,.doc,.docx,.xls,.xlsx,.csv">
                                    @error('file_input_6')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    @error('deviations')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            @if ($userRoles->contains('Hr And Admin'))
                                <div class="col-12">
                                    <h4>HR Department</h4>
                                </div>
                                <div class="col-6">
                                    <label for="documents_workdone_supply" class="form-label">Documents verified for the
                                        Period of Workdone/Supply</label>
                                    <textarea id="documents_workdone_supply" name="documents_workdone_supply" cols="30" rows="2"
                                        class="form-control">{{ old('documents_workdone_supply', $note->documents_workdone_supply) }}</textarea>
                                    @error('documents_workdone_supply')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-6">
                                    <label for="required_submitted" class="form-label">Whether all the documents required
                                        submitted</label>
                                    <select class="form-select form-control" id="required_submitted"
                                        name="required_submitted">
                                        <option value="Y"
                                            {{ old('required_submitted', $note->required_submitted) == 'Y' ? 'selected' : '' }}>
                                            Yes
                                        </option>
                                        <option value="N"
                                            {{ old('required_submitted', $note->required_submitted) == 'N' ? 'selected' : '' }}>
                                            No
                                        </option>
                                    </select>
                                    @error('required_submitted')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label for="documents_discrepancy" class="form-label">Documents discrepancy</label>
                                    <textarea id="documents_discrepancy" name="documents_discrepancy" cols="30" rows="2"
                                        class="form-control">{{ old('documents_discrepancy', $note->documents_discrepancy) }}</textarea>
                                    @error('documents_discrepancy')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label for="amount_submission_non" class="form-label">Amount if any to be retained for
                                        non
                                        submission/non compliance of HR</label>
                                    <textarea id="amount_submission_non" name="amount_submission_non" cols="30" rows="2"
                                        class="form-control">{{ old('amount_submission_non', $note->amount_submission_non) }}</textarea>
                                    @error('amount_submission_non')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label for="remarks" class="form-label">Remarks</label>
                                    <textarea id="remarks" name="remarks" cols="30" rows="2" class="form-control">{{ old('remarks', $note->remarks) }}</textarea>
                                    @error('remarks')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                            @if ($userRoles->contains('Qs'))
                                <div class="col-12">
                                    <h4>Auditor Department</h4>
                                </div>
                                <div class="col-6">
                                    <label for="remarks" class="form-label">Attachment</label>
                                    <input type="file" id="file_input_4" name="file_input_4" class="form-control"
                                        accept=".pdf,.doc,.docx,.xls,.xlsx,.csv">
                                    @error('file_input_4')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-6">
                                    <label for="remarks" class="form-label">Remarks</label>
                                    <textarea id="auditor_remarks" name="auditor_remarks" cols="30" rows="2" class="form-control">{{ old('auditor_remarks', $note->auditor_remarks) }}</textarea>
                                    @error('auditor_remarks')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif



                            @if (auth()->user()->id == $note->user_id && $note->status != 'R')
                                <div class="col-12">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select form-control" id="status" name="status">
                                        <option value="D"
                                            {{ old('status', $note->status) == 'D' ? 'selected' : '' }}>
                                            Draft
                                        </option>
                                        <option value="PMPL"
                                            {{ old('status', $note->status) == 'PMPL' ? 'selected' : '' }}>
                                            Sent to PMC
                                        </option>
                                        <option value="S"
                                            {{ old('status', $note->status) == 'S' ? 'selected' : '' }}>
                                            Sent for
                                            Approval</option>
                                    </select>
                                    @error('status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            @else
                                <input type="hidden" name="status" value="{{ $note->status }}">
                            @endif

                            <div class="text-center">
                                {{-- <button type="submit" class="btn btn-success" onclick="setStatus('D')">Save
                                    Draft</button> --}}
                                <button type="submit" class="btn btn-primary" id="loadBtn">
                                    <span id="approveSpinner" class="spinner-border spinner-border-sm d-none"
                                        role="status" aria-hidden="true"></span>
                                    Submit</button>
                            </div>
                        </form>
                        <!-- Vertical Form -->
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Supporting Docs</h5>
                        <!-- Vertical Form -->
                        <form class="row g-3" action="{{ route('backend.documents.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="green_note_id" value="{{ $note->id }}">
                            <div class="col-6">
                                <label for="name" class="form-label">Doc Name</label>
                                <input type="text" class="form-control" id="name" name="name">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                @error('green_note_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="file_path" class="form-label">Attach File</label>
                                <input type="file" class="form-control form-upload" id="file_path"
                                    name="file_path" accept=".pdf,.doc,.docx,.xls,.xlsx,.csv">
                                @error('file_path')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-success">Upload</button>
                            </div>
                        </form>
                        <!-- Vertical Form -->
                    </div>
                </div>
            </div>
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
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($documents as $index => $document)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $document->name }}</td>
                                        <td>
                                            @php
                                                $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
                                            @endphp

                                            @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                                                <img src="{{ asset('notes/documents/' . $document->file_path) }}"
                                                    alt="Document Image" width="70" height="70">
                                            @else
                                                <a href="{{ asset('notes/documents/' . $document->file_path) }}"
                                                    target="_blank"><i class="bi bi-file-earmark-text-fill"></i></a>
                                            @endif
                                        </td>
                                        <td>{{ $document->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') }}
                                        </td>
                                        <td>{{ $document->user->name }}</td>
                                        <td>
                                            <a href="{{ asset('notes/documents/' . $document->file_path) }}"
                                                download="{{ $document->name }}">
                                                <i class="bi bi-download"></i>
                                            </a> |
                                            <form action="{{ route('backend.documents.destroy', $document->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-none btn-sm delete-btn"><i
                                                        class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                        <!-- End Table with stripped rows -->
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('script')
    <script>
        document.getElementById('editForm').addEventListener('submit', function() {
            const btn = document.getElementById('loadBtn');
            const spinner = document.getElementById('approveSpinner');
            btn.disabled = true;
            spinner.classList.remove('d-none');
        });



        document.addEventListener('DOMContentLoaded', function() {
            $('#supplier_id').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const msmeClassification = selectedOption.data('msme');
                const activityClassification = selectedOption.data('activity');

                // console.log("Selected data-msme:", msmeClassification);

                const msmeInput = document.getElementById('msme_classification');
                const activityInput = document.getElementById('activity_type');
                if (msmeInput) {
                    msmeInput.value = msmeClassification ? msmeClassification : 'N/A';
                }
                if (activityInput) {
                    activityInput.value = activityClassification ? activityClassification : 'N/A';
                }
                toggleFileInput('msme_classification', 'msme_div', 'Non MSME', 'Medium');
            });

            // Optional: trigger once on page load
            $('#supplier_id').trigger('change');
        });

        function calculateTotal() {
            let baseValue = parseFloat(document.getElementById('base_value').value) || 0;
            let gst = parseFloat(document.getElementById('gst').value) || 0;
            let otherCharges = parseFloat(document.getElementById('other_charges').value) || 0;

            let total = baseValue + gst + otherCharges;
            document.getElementById('total_amount').value = total.toFixed(2); // 2 decimal places
        }

        function calculateInvoiceTotal() {
            let baseValue = parseFloat(document.getElementById('invoice_base_value').value) || 0;
            let gst = parseFloat(document.getElementById('invoice_gst').value) || 0;
            let otherCharges = parseFloat(document.getElementById('invoice_other_charges').value) || 0;

            let total = baseValue + gst + otherCharges;
            document.getElementById('invoice_value').value = total.toFixed(2); // 2 decimal places
        }

        function calculateTotal() {
            let baseValue = parseFloat(document.getElementById('base_value').value) || 0;
            let gst = parseFloat(document.getElementById('gst').value) || 0;
            let otherCharges = parseFloat(document.getElementById('other_charges').value) || 0;

            let total = baseValue + gst + otherCharges;
            document.getElementById('total_amount').value = total.toFixed(2); // 2 decimal places
        }

        function setStatus(status) {
            document.getElementById('status').value = status;
        }
        // Function to toggle the file input visibility based on dropdown selection
        function toggleFileInput(dropdownId, fileInputId, hideOn) {
            var dropdown = document.getElementById(dropdownId);
            var fileInput = document.getElementById(fileInputId);

            // Debugging: Check if dropdown and file input exist
            if (dropdown && fileInput) {
                if (dropdown.value == hideOn) {
                    // Hide file input if 'Yes' is selected
                    fileInput.style.display = 'none';
                } else {
                    // Show file input if 'No' is selected
                    fileInput.style.display = 'block';
                }
            } else {
                console.error('Dropdown or File input not found:', dropdownId, fileInputId);
            }
        }

        // Initialize visibility when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            toggleFileInput('extension_contract_period', 'file_input_1', 'N');
            toggleFileInput('protest_note_raised', 'file_input_2', 'N');
            toggleFileInput('milestone_status', 'file_input_3', 'Y');
            toggleFileInput('whether_contract', 'extension_contract_period_show', 'N');
            toggleFileInput('deviations', 'file_input_6', 'N');
            toggleFileInput('expense_amount_within_contract', 'file_input_5', 'Y');
            toggleFileInput('msme_classification', 'msme_div', 'Non MSME', 'Medium');
        });

        document.getElementById('extension_contract_period')?.addEventListener('change', function() {
            toggleFileInput('extension_contract_period', 'file_input_1', 'N');
        });
        document.getElementById('protest_note_raised')?.addEventListener('change', function() {
            toggleFileInput('protest_note_raised', 'file_input_2', 'N');
        });
        document.getElementById('milestone_status')?.addEventListener('change', function() {
            toggleFileInput('milestone_status', 'file_input_3', 'Y');
        });

        document.getElementById('expense_amount_within_contract')?.addEventListener('change', function() {
            toggleFileInput('expense_amount_within_contract', 'file_input_5', 'Y');
        });
        document.getElementById('deviations')?.addEventListener('change', function() {
            toggleFileInput('deviations', 'file_input_6', 'N');
        });
        document.getElementById('whether_contract')?.addEventListener('change', function() {
            toggleFileInput('whether_contract', 'extension_contract_period_show', 'N');
        });
        document.getElementById('msme_classification')?.addEventListener('change', function() {
            toggleFileInput('msme_classification', 'msme_div', 'Non MSME', 'Medium');
        });


        $('.select2').select2();
    </script>
@endpush
