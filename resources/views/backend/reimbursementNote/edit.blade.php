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

                        <form class="row g-3" action="{{ route('backend.reimbursement-note.update', $note->id) }}"
                            enctype="multipart/form-data" method="post">
                            @csrf
                            @method('PUT')


                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                            <input type="hidden" name="select_user_id" value="{{ @$note->selectUser->id }}">

                            <div class="col-3">
                                <label for="one" class="form-label">Note No</label>
                                <input type="text" class="form-control" name="note_no"
                                    value="{{ $note->note_no ?? '-' }}" id="one" value="{{ old('note_no') }}"
                                    readonly>
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
                                            {{ old('project_id', $note->project_id) == $item->id ? 'selected' : '' }}>
                                            {{ $item->project }}</option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-3">
                                <label for="five" class="form-label">User Department</label>
                                <input type="text" class="form-control"
                                    value="{{ @$note->selectUser->department->name ?? '-' }}" readonly>

                            </div>
                            <div class="col-3">
                                <label for="one" class="form-label">Employee Name:</label>
                                <input type="text" class="form-control" value="{{ $note->selectUser->name ?? '-' }}"
                                    readonly>
                            </div>
                            <div class="col-3">
                                <label for="one" class="form-label">Employee ID:</label>
                                <input type="text" class="form-control" value="{{ $note->selectUser->emp_id ?? '-' }}"
                                    readonly>
                            </div>
                            <div class="col-3">
                                <label for="one" class="form-label">Employee Designation:</label>
                                <input type="text" class="form-control"
                                    value="{{ @$note->selectUser->designation->name ?? '-' }}" readonly>
                            </div>
                            <div class="col-3">
                                <label for="one" class="form-label">Date of Travel/Expenses: </label>
                                <input type="date" class="form-control" name="date_of_travel" id="one"
                                    value="{{ old('date_of_travel', $note->date_of_travel) }}">
                                @error('date_of_travel')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-3">
                                <label for="one" class="form-label">Mode of Travel:</label>
                                <input type="text" class="form-control" name="mode_of_travel" id="one"
                                    value="{{ old('mode_of_travel', $note->mode_of_travel) }}">
                                @error('mode_of_travel')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-3">
                                <label for="one" class="form-label">Travel Mode Eligibility:</label>
                                <input type="text" class="form-control" name="travel_mode_eligibility" id="one"
                                    value="{{ old('travel_mode_eligibility', $note->travel_mode_eligibility) }}">
                                @error('travel_mode_eligibility')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-3">
                                <label for="one" class="form-label">Initial Approver's Name:</label>
                                <select class="form-select form-control" id="approver_id" name="approver_id">
                                    <option value="">Select Approver</option>
                                    @foreach ($users as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('approver_id', $note->approver_id) == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('approver_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="one" class="form-label">Purpose of travel:</label>
                                <textarea id="purpose_of_travel" name="purpose_of_travel" cols="30" rows="2" class="form-control">{{ old('purpose_of_travel', $note->purpose_of_travel) }}</textarea>
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
                            @foreach ($note->expenses as $expense)
                                <div class="row mb-2 expense-row">
                                    <div class="col-2">
                                        <input class="form-control" type="text" value="{{ $expense->expense_type }}"
                                            name="expense_type[]">
                                    </div>
                                    <div class="col-2">
                                        <input class="form-control" type="date" value="{{ $expense->bill_date }}"
                                            name="bill_date[]">
                                    </div>
                                    <div class="col-1">
                                        <input class="form-control" type="text" value="{{ $expense->bill_number }}"
                                            name="bill_number[]">
                                    </div>
                                    <div class="col-2">
                                        <input class="form-control" type="text" value="{{ $expense->vendor_name }}"
                                            name="vendor_name[]">
                                    </div>
                                    <div class="col-1">
                                        <input class="form-control bill-amount" type="number"
                                            value="{{ $expense->bill_amount }}" name="bill_amount[]"
                                            oninput="calculateTotal()" {{ $note->status !== 'D' ? 'readonly' : '' }}>
                                    </div>
                                    <div class="col-2">
                                        <select class="form-select form-control" id="supporting_available[]"
                                            name="supporting_available[]">
                                            <option value="Yes"
                                                {{ old('supporting_available', $expense->supporting_available) == 'Yes' ? 'selected' : '' }}>
                                                Yes</option>
                                            <option value="No"
                                                {{ old('supporting_available', $expense->supporting_available) == 'No' ? 'selected' : '' }}>
                                                No</option>
                                        </select>
                                    </div>
                                    <div class="col-2">
                                        <textarea id="remarks" name="remarks[]" cols="30" rows="2" class="form-control">{{ $expense->remarks }}</textarea>
                                    </div>
                                </div>
                            @endforeach
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
                                            oninput="calculateTotal()" {{ $note->status !== 'D' ? 'readonly' : '' }}>
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
                            @php
                                $totalPayable = $note->expenses->sum('bill_amount');
                                $advanceAdjusted = $note->adjusted;
                                $netPayable = $totalPayable - $advanceAdjusted;
                            @endphp
                            <div class="row mt-4">
                                <div class="col-3">
                                    <label class="form-label">Total Payable Amount</label>
                                    <input class="form-control" type="number" id="total-amount"
                                        value="{{ $totalPayable }}" readonly>
                                </div>
                                <div class="col-3">
                                    <label class="form-label">Advance Adjusted (if Any)</label>
                                    <input class="form-control" type="number" id="adjusted-amount" name="adjusted"
                                        value="{{ $note->adjusted }}.00" oninput="calculateTotal()"
                                        {{ $note->status !== 'D' ? 'readonly' : '' }}>
                                </div>
                                <div class="col-3">
                                    <label class="form-label">Net Payable Amount</label>
                                    <input class="form-control" type="number" id="net-amount" readonly
                                        value="{{ $netPayable }}">
                                </div>
                            </div>

                            <div class="col-3">
                                <label for="one" class="form-label">Name of Account holder: </label>
                                <input type="text" class="form-control" name="account_holder" id="one"
                                    value="{{ old('account_holder', $note->account_holder) }}">
                                @error('account_holder')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-3">
                                <label for="one" class="form-label">Bank Name:</label>
                                <input type="text" class="form-control" name="bank_name" id="one"
                                    value="{{ old('bank_name', $note->bank_name) }}">
                                @error('bank_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-3">
                                <label for="one" class="form-label">Bank Account:</label>
                                <input type="text" class="form-control" name="bank_account" id="one"
                                    value="{{ old('bank_account', $note->bank_account) }}">
                                @error('bank_account')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-3">
                                <label for="one" class="form-label">IFSC:</label>
                                <input type="text" class="form-control" name="IFSC_code" id="one"
                                    value="{{ old('IFSC_code', $note->IFSC_code) }}">
                                @error('IFSC_code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            @if (auth()->user()->id == $note->user_id)
                                <div class="col-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select form-control" id="status" name="status">
                                        <option value="D"
                                            {{ old('status', $note->status) == 'D' ? 'selected' : '' }}>
                                            Draft
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
                                <input type="hidden" name="status" value="{{ $note->status ?? 'S' }}">
                            @endif

                            <div class="col-6">
                                <label for="file_path" class="form-label">Attach File</label>
                                <input type="file" class="form-control form-upload" id="file_path" name="file_path[]"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.csv" multiple>
                                @error('file_path')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary  button-with-spinner">
                                    <span>Submit</span>
                                    <div class="spinner-border spinner-border-sm" role="status"></div>
                                </button>
                            </div>
                        </form>
                        <!-- Vertical Form -->
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Attach Files</h5>
                        @if ($note->file_path)
                            <table class="table table-bordered mt-2">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Preview</th>
                                        <th>Download</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (json_decode($note->file_path, true) as $key => $file)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                @php
                                                    $extension = pathinfo($file, PATHINFO_EXTENSION);
                                                    $isImage = in_array(strtolower($extension), [
                                                        'jpg',
                                                        'jpeg',
                                                        'png',
                                                        'gif',
                                                        'webp',
                                                    ]);
                                                @endphp

                                                @if ($isImage)
                                                    <img src="{{ asset('storage/rn/' . $file) }}" alt="Preview"
                                                        width="50">
                                                @elseif (strtolower($extension) == 'pdf')
                                                    <a href="{{ asset('storage/rn/' . $file) }}" target="_blank">View
                                                        PDF</a>
                                                @else
                                                    <span>N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ asset('storage/rn/' . $file) }}" style="margin-left: 10px"
                                                    download>
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                |
                                                <form
                                                    action="{{ route('backend.reimbursement-note.file.delete', ['id' => $note->id, 'filename' => $file]) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this file?');">
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
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection

@push('script')
    {{-- - --}}

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let container = document.getElementById("expense-container");
            let rowHTML = container.innerHTML; // Save the first row structure

            // Add 11 more rows dynamically
            for (let i = 1; i < 6; i++) {
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
