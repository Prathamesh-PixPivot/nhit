@extends('backend.layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>Reimbursement Note</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item active">Reimbursement Note</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Create Reimbursement Note</h5>
                        <!-- Vertical Form -->
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

                        <form class="row g-3" action="{{ route('backend.reimbursement-note.store') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

                            <div class="col-3">
                                <label for="one" class="form-label">Note No</label>
                                <input type="text" class="form-control" name="note_no" value="{{ $orderNumber }}"
                                    id="one" value="{{ old('note_no') }}" readonly>
                                @error('note_no')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-3">
                                <label for="project_id" class="form-label">Project Name</label>
                                <select class="form-select form-control" id="project_id" name="project_id">
                                    <option value="">Select Project</option>
                                    @foreach ($filteredItems as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('project_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->project }}</option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-3">
                                <label for="five" class="form-label">User Department</label>
                                <input type="text" class="form-control" value="{{ $user->department->name ?? '-' }}"
                                    readonly>
                            </div>
                            <div class="col-3">
                                <label for="one" class="form-label">Employee Name:</label>
                                <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                            </div>
                            <div class="col-3">
                                <label for="one" class="form-label">Employee ID:</label>
                                <input type="text" class="form-control" value="{{ $user->emp_id }}" readonly>
                            </div>
                            <div class="col-3">
                                <label for="one" class="form-label">Employee Designation:</label>
                                <input type="text" class="form-control" value="{{ $user->designation->name ?? '-' }}"
                                    readonly>
                            </div>
                            <div class="col-3">
                                <label for="one" class="form-label">Date of Travel: </label>
                                <input type="date" class="form-control" name="date_of_travel" id="one"
                                    value="{{ old('date_of_travel') }}">
                                @error('date_of_travel')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-3">
                                <label for="one" class="form-label">Mode of Travel:</label>
                                <input type="text" class="form-control" name="mode_of_travel" id="one"
                                    value="{{ old('mode_of_travel') }}">
                                @error('mode_of_travel')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-3">
                                <label for="one" class="form-label">Travel Mode Eligibility:</label>
                                <input type="text" class="form-control" name="travel_mode_eligibility" id="one"
                                    value="{{ old('travel_mode_eligibility') }}">
                                @error('travel_mode_eligibility')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-3">
                                <label for="one" class="form-label">Initial Approver's Name:</label>
                                <select class="form-select form-control select2" id="approver_id" name="approver_id">
                                    <option value="">Select Approver</option>
                                    @foreach ($users as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('approver_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('approver_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- <div class="col-3">
                                <label for="one" class="form-label">Approver's designation: </label>
                                <input type="text" class="form-control" name="approver_designation" id="one"
                                    value="{{ old('approver_designation') }}">
                                @error('approver_designation')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div> --}}
                            {{-- <div class="col-3">
                                <label for="one" class="form-label">Approval Date: </label>
                                <input type="date" class="form-control" name="approval_date" id="one"
                                    value="{{ old('approval_date') }}">
                                @error('approval_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div> --}}

                            <div class="col-12">
                                <label for="one" class="form-label">Purpose of travel:</label>
                                <textarea id="purpose_of_travel" name="purpose_of_travel" cols="30" rows="2" class="form-control">{{ old('purpose_of_travel') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label for="one" class="form-label">Expense Details::</label>
                            </div>
                            <div class="row">
                                <label for="one" class="form-label col-2" style="font-size: 12px">Expense
                                    Type</label>
                                <label for="one" class="form-label col-2" style="font-size: 12px">Bill Date</label>
                                <label for="one" class="form-label col-1" style="font-size: 10px">Bill
                                    Number</label>
                                <label for="one" class="form-label col-2" style="font-size: 12px">Vendor
                                    Name</label>
                                <label for="one" class="form-label col-1" style="font-size: 12px">Bill
                                    Amount</label>
                                <label for="one" class="form-label col-2" style="font-size: 12px">Supporting
                                    Available</label>
                                <label for="one" class="form-label col-2" style="font-size: 12px"> Remarks (if any)
                                </label>
                            </div>

                            <div id="expense-container">
                                <div class="row mb-2 expense-row">
                                    <div class="col-2">
                                        <input class="form-control" type="text" name="expense_type[]">
                                    </div>
                                    <div class="col-2">
                                        <input class="form-control" type="date" name="bill_date[]">
                                    </div>
                                    <div class="col-1">
                                        <input class="form-control" type="text" name="bill_number[]">
                                    </div>
                                    <div class="col-2">
                                        <input class="form-control" type="text" name="vendor_name[]">
                                    </div>
                                    <div class="col-1">
                                        <input class="form-control bill-amount" type="number" name="bill_amount[]"
                                            oninput="calculateTotal()">
                                    </div>
                                    <div class="col-2">
                                        <select class="form-select form-control" id="supporting_available[]"
                                            name="supporting_available[]">
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>
                                    <div class="col-2">
                                        <textarea id="remarks" name="remarks[]" cols="30" rows="2" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>

                            {{--                     
                            <div class="col-3">
                                <label for="one" class="form-label">Advance Adjusted (if Any)</label>
                                <input class="form-control " type="number" name="adjusted">
                            </div> --}}
                            <div class="row mt-4">
                                <div class="col-3">
                                    <label class="form-label">Total Payable Amount</label>
                                    <input class="form-control" type="number" id="total-amount" readonly>
                                </div>
                                <div class="col-3">
                                    <label class="form-label">Advance Adjusted (if Any)</label>
                                    <input class="form-control" type="number" id="adjusted-amount" name="adjusted"
                                        oninput="calculateTotal()">
                                </div>
                                <div class="col-3">
                                    <label class="form-label">Net Payable Amount</label>
                                    <input class="form-control" type="number" id="net-amount" readonly>
                                </div>
                            </div>

                            <div class="col-3">
                                <label for="one" class="form-label">Name of Account holder: </label>
                                <input type="text" class="form-control" name="account_holder" id="one"
                                    value="{{ old('account_holder') }}">
                                @error('account_holder')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-3">
                                <label for="one" class="form-label">Bank Name:</label>
                                <input type="text" class="form-control" name="bank_name" id="one"
                                    value="{{ old('bank_name') }}">
                                @error('bank_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-3">
                                <label for="one" class="form-label">Bank Account:</label>
                                <input type="text" class="form-control" name="bank_account" id="one"
                                    value="{{ old('bank_account') }}">
                                @error('bank_account')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-3">
                                <label for="one" class="form-label">IFSC:</label>
                                <input type="text" class="form-control" name="IFSC_code" id="one"
                                    value="{{ old('IFSC_code') }}">
                                @error('IFSC_code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>


                            <div class="col-6">
                                <label for="file_path" class="form-label">Attach File</label>
                                <input type="file" class="form-control form-upload" id="file_path" name="file_path[]"
                                    multiple>
                                @error('file_path')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                        <!-- Vertical Form -->
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection

@push('script')
    {{-- - --}}

    <script>
        $('.select2').select2();

        document.addEventListener("DOMContentLoaded", function() {
            let container = document.getElementById("expense-container");
            let rowHTML = container.innerHTML; // Save the first row structure

            // Add 11 more rows dynamically
            for (let i = 1; i < 12; i++) {
                container.insertAdjacentHTML("beforeend", rowHTML);
            }
        });

        function calculateTotal() {
            let total = 0;
            document.querySelectorAll(".bill-amount").forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            document.getElementById("total-amount").value = total;

            let adjusted = parseFloat(document.getElementById("adjusted-amount").value) || 0;
            document.getElementById("net-amount").value = total - adjusted;
        }
    </script>
@endpush
