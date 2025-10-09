@extends('backend.layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>Payment Note</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item active">Payment Note</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Create Payment Note ,nmbn</h5>
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

                        <form class="row g-3" action="{{ route('backend.payment-note.store') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                            @if($greenNote)
                                <input type="hidden" name="green_note_id" value="{{ $greenNote->id }}">
                            @endif

                            <div class="col-4">
                                <label for="one" class="form-label">Note No</label>
                                <input type="text" class="form-control" name="note_no" value="{{ $orderNumber }}"
                                    id="one" value="{{ old('note_no') }}" readonly>
                                @error('note_no')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            @if($greenNote)
                            <div class="col-4">
                                <label for="one" class="form-label">Green Note No</label>
                                <input type="text" class="form-control" value="{{ $greenNote->formatted_order_no }}" readonly>
                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">Department</label>
                                <input type="text" class="form-control" value="{{ $greenNote->department->name }}" readonly>
                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">Green Note App Date:</label>
                                @php
                                    $lastStep = $greenNote->approvalLogs->last();
                                @endphp
                                @if ($lastStep)
                                    <input type="text" class="form-control"
                                        value="{{ $lastStep->created_at->format('d/m/Y H:i A') }}" readonly>
                                @endif
                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">Green Note Approver:</label>
                                @if ($lastStep)
                                    <input type="text" class="form-control" value="{{ $lastStep->reviewer->name }}"
                                        readonly>
                                @endif
                            </div>
                            <div class="col-12">
                                <label for="one" class="form-label">Subject:</label>
                                <textarea id="subject" required name="subject" cols="30" rows="2" class="form-control">{{ old('subject', $greenNote ? 'Payment for ' . $greenNote->brief_of_goods_services : '') }}</textarea>
                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">Vendor Code</label>
                                <input type="text" class="form-control"
                                    value="{{ $greenNote->supplier->vendor_code ?? '-' }}" readonly>
                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">Vendor Name</label>
                                <input type="text" class="form-control"
                                    value="{{ $greenNote->supplier->vendor_name ?? '-' }}" readonly>
                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">Invoice No.</label>
                                <input type="text" class="form-control" value="{{ $greenNote->invoice_number }}" readonly>
                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">Invoice Date</label>
                                <input type="text" class="form-control"
                                    value="{{ $greenNote->invoice_date ? date('d/m/Y', strtotime($greenNote->invoice_date)) : '-' }}"
                                    readonly>
                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">Invoice Amount:</label>
                                <input type="text" class="form-control" value="{{ $greenNote->invoice_value }}" readonly>
                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">Invoice Approved by:</label>
                                @if ($lastStep)
                                    <input type="text" class="form-control" value="{{ $lastStep->reviewer->name }}"
                                        readonly>
                                @endif
                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">LOA/PO No.:</label>
                                <input type="text" class="form-control" value="{{ $greenNote->order_no }}" readonly>
                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">LOA/PO Date:</label>
                                <input type="text" class="form-control" value="{{ $greenNote->order_date }}" readonly>
                            </div>

                            <div class="col-4">
                                <label for="one" class="form-label">LOA/PO Amount:</label>
                                <input type="text" class="form-control" value="{{ $greenNote->total_amount }}" readonly>
                            </div>

                            <div class="">
                                <div class="row mt-2">
                                    <div class="col-5 mb-2">
                                        <input type="text" value="Taxable Amount" class="form-control"
                                            placeholder="Taxable Amount" readonly>
                                    </div>
                                    <div class="col-5 mb-2">
                                        <input type="number" value="{{ $greenNote->invoice_base_value }}"
                                            class="form-control" placeholder="Amount" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 mb-2">
                                        <input type="text" value="Add : GST" class="form-control"
                                            placeholder="Add : GST " readonly>
                                    </div>
                                    <div class="col-5 mb-2">
                                        <input type="number" value="{{ $greenNote->invoice_gst }}" class="form-control"
                                            placeholder="Amount" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 mb-2">
                                        <input type="text" value="Add: Other charges " class="form-control"
                                            placeholder="Add: Other charges " readonly>
                                    </div>
                                    <div class="col-5 mb-2">
                                        <input type="number" value="{{ $greenNote->invoice_other_charges }}"
                                            class="form-control" placeholder="Amount" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 mb-2">
                                        <input type="text" value="Gross Amount " class="form-control"
                                            placeholder="Gross Amount " readonly>
                                    </div>
                                    <div class="col-5 mb-2">
                                        <input type="number" value="{{ $grossAmount }}" id="gross_amount"
                                            class="form-control" placeholder="Amount" readonly>
                                    </div>
                                </div>
                            </div>
                            @endif
                            {{-- <div class="">
                                <label>Less: Particulars & Payable Amount</label>
                                <div class="row mt-2">
                                    <div class="col-5 mb-2">
                                        <input type="text" name="less_particulars[0][particular]" class="form-control"
                                            placeholder="Particular">
                                    </div>
                                    <div class="col-5 mb-2">
                                        <input type="number" name="less_particulars[0][amount]" class="form-control "
                                            placeholder="Amount">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 mb-2">
                                        <input type="text" name="less_particulars[1][particular]" class="form-control"
                                            placeholder="Particular">
                                    </div>
                                    <div class="col-5 mb-2">
                                        <input type="number" name="less_particulars[1][amount]" class="form-control"
                                            placeholder="Amount">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 mb-2">
                                        <input type="text" name="less_particulars[2][particular]" class="form-control"
                                            placeholder="Particular">
                                    </div>
                                    <div class="col-5 mb-2">
                                        <input type="number" name="less_particulars[2][amount]" class="form-control"
                                            placeholder="Amount">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 mb-2">
                                        <input type="text" name="less_particulars[3][particular]" class="form-control"
                                            placeholder="Particular">
                                    </div>
                                    <div class="col-5 mb-2">
                                        <input type="number" name="less_particulars[3][amount]" class="form-control"
                                            placeholder="Amount">
                                    </div>
                                </div>
                            </div>

                            <div class="">
                                <label>Add: Particulars & Payable Amount</label>
                                <div class="row mt-2">
                                    <div class="col-5 mb-2">
                                        <input type="text" name="add_particulars[0][particular]" class="form-control"
                                            placeholder="Particular">
                                    </div>
                                    <div class="col-5 mb-2">
                                        <input type="number" name="add_particulars[0][amount]"
                                            class="form-control add-amount" placeholder="Amount">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 mb-2">
                                        <input type="text" name="add_particulars[1][particular]" class="form-control"
                                            placeholder="Particular">
                                    </div>
                                    <div class="col-5 mb-2">
                                        <input type="number" name="add_particulars[1][amount]"
                                            class="form-control add-amount" placeholder="Amount">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 mb-2">
                                        <input type="text" name="add_particulars[2][particular]" class="form-control"
                                            placeholder="Particular">
                                    </div>
                                    <div class="col-5 mb-2">
                                        <input type="number" name="add_particulars[2][amount]"
                                            class="form-control add-amount" placeholder="Amount">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 mb-2">
                                        <input type="text" name="add_particulars[3][particular]" class="form-control"
                                            placeholder="Particular">
                                    </div>
                                    <div class="col-5 mb-2">
                                        <input type="number" name="add_particulars[3][amount]"
                                            class="form-control add-amount" placeholder="Amount">
                                    </div>
                                </div>
                            </div> --}}
                            <div class="card border-warning mb-4">
                                <div class="card-header bg-warning text-dark">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="bi bi-dash-circle me-2"></i>Less: Particulars & Payable Amount
                                        </h6>
                                        <button type="button" class="btn btn-sm btn-outline-dark" onclick="addParticularRow('less')">
                                            <i class="bi bi-plus-circle me-1"></i>Add Row
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="lessContainer">
                                        <div class="row mt-2 particular-row">
                                            <div class="col-5 mb-2">
                                                <div class="form-floating">
                                                    <input type="text" name="less_particulars[0][particular]" 
                                                           class="form-control" placeholder="Particular">
                                                    <label>Particular</label>
                                                </div>
                                            </div>
                                            <div class="col-5 mb-2">
                                                <div class="form-floating">
                                                    <input type="number" name="less_particulars[0][amount]" 
                                                           class="form-control less-amount" placeholder="Amount" 
                                                           onchange="calculateNetAmount()" step="0.01">
                                                    <label>Amount (₹)</label>
                                                </div>
                                            </div>
                                            <div class="col-2 mb-2 d-flex align-items-center">
                                                <button type="button" class="btn btn-outline-danger btn-sm" 
                                                        onclick="removeParticularRow(this)" title="Remove Row">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <strong>Total Less Amount: ₹<span id="totalLessAmount">0.00</span></strong>
                                    </div>
                                </div>
                            </div>

                            <div class="card border-success mb-4">
                                <div class="card-header bg-success text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="bi bi-plus-circle me-2"></i>Add: Particulars & Payable Amount
                                        </h6>
                                        <button type="button" class="btn btn-sm btn-outline-light" onclick="addParticularRow('add')">
                                            <i class="bi bi-plus-circle me-1"></i>Add Row
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="addContainer">
                                        <div class="row mt-2 particular-row">
                                            <div class="col-5 mb-2">
                                                <div class="form-floating">
                                                    <input type="text" name="add_particulars[0][particular]" 
                                                           class="form-control" placeholder="Particular">
                                                    <label>Particular</label>
                                                </div>
                                            </div>
                                            <div class="col-5 mb-2">
                                                <div class="form-floating">
                                                    <input type="number" name="add_particulars[0][amount]" 
                                                           class="form-control add-amount" placeholder="Amount" 
                                                           onchange="calculateNetAmount()" step="0.01">
                                                    <label>Amount (₹)</label>
                                                </div>
                                            </div>
                                            <div class="col-2 mb-2 d-flex align-items-center">
                                                <button type="button" class="btn btn-outline-danger btn-sm" 
                                                        onclick="removeParticularRow(this)" title="Remove Row">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <strong>Total Add Amount: ₹<span id="totalAddAmount">0.00</span></strong>
                                    </div>
                                </div>
                            </div>

                            <div class="card border-primary mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">
                                        <i class="bi bi-calculator me-2"></i>Net Payable Amount Calculation
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="text-center p-3 bg-light rounded">
                                                <h6 class="text-muted">Gross Amount</h6>
                                                <h4 class="text-primary">₹<span id="displayGrossAmount">{{ number_format($grossAmount ?? 0, 2) }}</span></h4>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center p-3 bg-light rounded">
                                                <h6 class="text-muted">Less Amount</h6>
                                                <h4 class="text-warning">-₹<span id="displayLessAmount">0.00</span></h4>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center p-3 bg-light rounded">
                                                <h6 class="text-muted">Add Amount</h6>
                                                <h4 class="text-success">+₹<span id="displayAddAmount">0.00</span></h4>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center p-3 bg-primary text-white rounded">
                                                <h6>Net Payable</h6>
                                                <h4>₹<span id="displayNetAmount">{{ number_format($grossAmount ?? 0, 2) }}</span></h4>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="net_payable_amount" name="net_payable_amount" value="{{ $grossAmount ?? 0 }}">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-5 mb-2">
                                    <input type="text" value="Net Payable Amount (Round Off)" class="form-control"
                                        placeholder="Net Payable Amount (Round Off)" readonly>
                                </div>
                                <div class="col-5 mb-2">
                                    <input type="text" class="form-control" placeholder="Amount" readonly
                                        id="net_payable_round_off" name="net_payable_round_off">
                                </div>
                            </div>
                            <div class="">
                                <label>Bank Details:</label>
                                
                                {{-- Vendor Account Selection --}}
                                @if($greenNote && $greenNote->supplier && $greenNote->supplier->accounts && $greenNote->supplier->accounts->count() > 1)
                                    <div class="row mt-2">
                                        <div class="col-12 mb-3">
                                            <label class="form-label">Select Vendor Account:</label>
                                            <select class="form-select" id="vendor_account_select" name="vendor_account_id">
                                                <option value="">Select Account</option>
                                                @foreach($greenNote->supplier->accounts as $account)
                                                    <option value="{{ $account->id }}" 
                                                            {{ $account->is_primary ? 'selected' : '' }}
                                                            data-account-name="{{ $account->account_name }}"
                                                            data-account-number="{{ $account->account_number }}"
                                                            data-bank-name="{{ $account->name_of_bank }}"
                                                            data-ifsc-code="{{ $account->ifsc_code }}"
                                                            data-branch-name="{{ $account->branch_name }}">
                                                        {{ $account->account_number }} - {{ $account->name_of_bank }}
                                                        @if($account->is_primary) (Primary) @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif

                                <div class="row mt-2">
                                    <div class="col-5 mb-2">
                                        <input type="text" value="Name of Account holder" class="form-control"
                                            placeholder="Name of Account holder" readonly>
                                    </div>
                                    <div class="col-5 mb-2">
                                        <input type="text" id="account_holder_name" 
                                            value="@if($greenNote && $greenNote->supplier){{ $greenNote->supplier->primaryAccount->account_name ?? $greenNote->supplier->vendor_name ?? '-' }}@endif"
                                            class="form-control" placeholder="Account Holder Name" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 mb-2">
                                        <input type="text" value="Bank Name" class="form-control"
                                            placeholder="Bank Name " readonly>
                                    </div>
                                    <div class="col-5 mb-2">
                                        <input type="text" id="bank_name"
                                            value="@if($greenNote && $greenNote->supplier){{ $greenNote->supplier->primaryAccount->name_of_bank ?? $greenNote->supplier->name_of_bank ?? '-' }}@endif"
                                            class="form-control" placeholder="Bank Name" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 mb-2">
                                        <input type="text" value="Bank Account" class="form-control"
                                            placeholder="Bank Account" readonly>
                                    </div>
                                    <div class="col-5 mb-2">
                                        <input type="text" id="account_number"
                                            value="@if($greenNote && $greenNote->supplier){{ $greenNote->supplier->primaryAccount->account_number ?? $greenNote->supplier->account_number ?? '-' }}@endif"
                                            class="form-control" placeholder="Account Number" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 mb-2">
                                        <input type="text" value="IFSC" class="form-control" placeholder="IFSC"
                                            readonly>
                                    </div>
                                    <div class="col-5 mb-2">
                                        <input type="text" id="ifsc_code"
                                            value="@if($greenNote && $greenNote->supplier){{ $greenNote->supplier->primaryAccount->ifsc_code ?? $greenNote->supplier->ifsc_code ?? '-' }}@endif"
                                            class="form-control" placeholder="IFSC Code" readonly>
                                    </div>
                                </div>
                                @if($greenNote && $greenNote->supplier->primaryAccount && $greenNote->supplier->primaryAccount->branch_name)
                                    <div class="row">
                                        <div class="col-5 mb-2">
                                            <input type="text" value="Branch Name" class="form-control"
                                                placeholder="Branch Name" readonly>
                                        </div>
                                        <div class="col-5 mb-2">
                                            <input type="text" id="branch_name"
                                                value="{{ $greenNote->supplier->primaryAccount->branch_name }}"
                                                class="form-control" placeholder="Branch Name" readonly>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="col-12">
                                <label for="one" class="form-label">Recommendation of Payment</label>
                                <textarea id="recommendation_of_payment" name="recommendation_of_payment" required cols="30" rows="2"
                                    class="form-control">Proposed to release the payment</textarea>

                            </div>
                            {{-- <div id="add-particulars">
                                    <label>Add: Particulars & Payable Amount</label>
                                    <div class="row add-row-template">
                                        <div class="col-md-5">
                                            <input type="text" name="add_particulars[0][particular]" class="form-control"
                                                placeholder="Particular">
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" name="add_particulars[0][amount]" class="form-control"
                                                placeholder="Amount">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-success add-row">+</button>
                                        </div>
                                    </div>
                                </div>

                                <div id="less-particulars" class="mt-3">
                                    <label>Less: Particulars & Payable Amount</label>
                                    <div class="row less-row-template">
                                        <div class="col-md-5">
                                            <input type="text" name="less_particulars[0][particular]" class="form-control"
                                                placeholder="Particular">
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" name="less_particulars[0][amount]" class="form-control"
                                                placeholder="Amount">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-success add-less-row">+</button>
                                        </div>
                                    </div>
                                </div> --}}




                            <div class="text-center">
                                {{-- <button type="submit" class="btn btn-primary">Submit</button> --}}
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
                        <h5 class="card-title">Attached Supporting Docs @if($greenNote)<a class="btn btn-primary"
                                href="{{ route('backend.note.view.pdf', $greenNote->id) }}">View Green Note</a>@endif</h5>
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
                                @if($greenNote && isset($documents))
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
                                            <td> <a href="{{ asset('notes/documents/' . $document->file_path) }}" download>
                                                    <i class="bi bi-download"></i>

                                                </a> |
                                                <form action="{{ route('backend.documents.destroy', $document->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-none btn-sm delete-btn"><i
                                                            class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif

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
    {{-- - --}}

    <script>
        let lessRowIndex = 1;
        let addRowIndex = 1;

        document.addEventListener("DOMContentLoaded", function() {
            // Initial calculation
            calculateNetAmount();
        });

        // Add new particular row
        function addParticularRow(type) {
            const container = document.getElementById(type + 'Container');
            const index = type === 'less' ? lessRowIndex : addRowIndex;
            
            const newRow = document.createElement('div');
            newRow.className = 'row mt-2 particular-row';
            newRow.innerHTML = `
                <div class="col-5 mb-2">
                    <div class="form-floating">
                        <input type="text" name="${type}_particulars[${index}][particular]" 
                               class="form-control" placeholder="Particular">
                        <label>Particular</label>
                    </div>
                </div>
                <div class="col-5 mb-2">
                    <div class="form-floating">
                        <input type="number" name="${type}_particulars[${index}][amount]" 
                               class="form-control ${type}-amount" placeholder="Amount" 
                               onchange="calculateNetAmount()" step="0.01">
                        <label>Amount (₹)</label>
                    </div>
                </div>
                <div class="col-2 mb-2 d-flex align-items-center">
                    <button type="button" class="btn btn-outline-danger btn-sm" 
                            onclick="removeParticularRow(this)" title="Remove Row">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;
            
            container.appendChild(newRow);
            
            if (type === 'less') {
                lessRowIndex++;
            } else {
                addRowIndex++;
            }
            
            showNotification(`${type === 'less' ? 'Deduction' : 'Addition'} row added successfully!`, 'success');
        }

        // Remove particular row
        function removeParticularRow(button) {
            const row = button.closest('.particular-row');
            const container = row.parentNode;
            
            // Don't allow removing the last row
            if (container.children.length <= 1) {
                showNotification('At least one row is required!', 'warning');
                return;
            }
            
            if (confirm('Are you sure you want to remove this row?')) {
                row.remove();
                calculateNetAmount();
                showNotification('Row removed successfully!', 'info');
            }
        }

        // Calculate net amount
        function calculateNetAmount() {
            const grossAmountElement = document.getElementById('gross_amount');
            const grossAmount = grossAmountElement ? parseFloat(grossAmountElement.value) || 0 : {{ $grossAmount ?? 0 }};

            // Calculate total less amount
            let totalLessAmount = 0;
            document.querySelectorAll('.less-amount').forEach(input => {
                totalLessAmount += parseFloat(input.value) || 0;
            });

            // Calculate total add amount
            let totalAddAmount = 0;
            document.querySelectorAll('.add-amount').forEach(input => {
                totalAddAmount += parseFloat(input.value) || 0;
            });

            // Calculate net payable amount
            const netAmount = grossAmount - totalLessAmount + totalAddAmount;
            const netAmountRounded = Math.round(netAmount);

            // Update display elements
            updateElement('displayGrossAmount', formatCurrency(grossAmount));
            updateElement('displayLessAmount', formatCurrency(totalLessAmount));
            updateElement('displayAddAmount', formatCurrency(totalAddAmount));
            updateElement('displayNetAmount', formatCurrency(netAmount));
            updateElement('totalLessAmount', formatCurrency(totalLessAmount));
            updateElement('totalAddAmount', formatCurrency(totalAddAmount));

            // Update form fields
            updateElement('net_payable_amount', netAmount.toFixed(2));
            updateElement('net_payable_round_off', netAmountRounded.toFixed(2));
        }

        // Helper function to update element content
        function updateElement(id, value) {
            const element = document.getElementById(id);
            if (element) {
                if (element.tagName === 'INPUT') {
                    element.value = value;
                } else {
                    element.textContent = value;
                }
            }
        }

        // Format currency for display
        function formatCurrency(amount) {
            return new Intl.NumberFormat('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(amount);
        }

        // Show notification
        function showNotification(message, type = 'info') {
            const bgColor = type === 'success' ? 'bg-success' : type === 'warning' ? 'bg-warning' : type === 'error' ? 'bg-danger' : 'bg-info';
            const icon = type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : type === 'error' ? 'x-circle' : 'info-circle';
            
            const notification = document.createElement('div');
            notification.className = `alert alert-dismissible fade show position-fixed ${bgColor} text-white`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="bi bi-${icon} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }

        // Vendor Account Selection Handler
        document.addEventListener('DOMContentLoaded', function() {
            const vendorAccountSelect = document.getElementById("vendor_account_select");
            if (vendorAccountSelect) {
                vendorAccountSelect.addEventListener("change", function() {
                    const selectedOption = this.selectedOptions[0];
                    
                    if (selectedOption && selectedOption.value) {
                        // Update banking details fields
                        const fields = {
                            'account_holder_name': selectedOption.dataset.accountName,
                            'bank_name': selectedOption.dataset.bankName,
                            'account_number': selectedOption.dataset.accountNumber,
                            'ifsc_code': selectedOption.dataset.ifscCode,
                            'branch_name': selectedOption.dataset.branchName
                        };
                        
                        Object.keys(fields).forEach(fieldId => {
                            const field = document.getElementById(fieldId);
                            if (field && fields[fieldId]) {
                                field.value = fields[fieldId];
                            }
                        });
                        
                        showNotification('Banking details updated successfully!', 'success');
                    }
                });
            }
        });
    </script>
    {{-- <script>
        $(document).ready(function() {
            let addRowIndex = 1;
            let lessRowIndex = 1;
            $('.add-row').click(function() {
                let row = `<div class="row">
                    <div class="col-md-5"><input type="text" name="add_particulars[${addRowIndex}][particular]" class="form-control" placeholder="Particular"></div>
                    <div class="col-md-5"><input type="text" name="add_particulars[${addRowIndex}][amount]" class="form-control" placeholder="Amount"></div>
                    <div class="col-md-2"><button type="button" class="btn btn-danger remove-row">-</button></div>
                </div>`;
                $('#add-particulars').append(row);
                addRowIndex++;
            });

            $('.add-less-row').click(function() {
                let row = `<div class="row">
                    <div class="col-md-5"><input type="text" name="less_particulars[${lessRowIndex}][particular]" class="form-control" placeholder="Particular"></div>
                    <div class="col-md-5"><input type="text" name="less_particulars[${lessRowIndex}][amount]" class="form-control" placeholder="Amount"></div>
                    <div class="col-md-2"><button type="button" class="btn btn-danger remove-row">-</button></div>
                </div>`;
                $('#less-particulars').append(row);
                lessRowIndex++;
            });

            $(document).on('click', '.remove-row', function() {
                $(this).parent().parent().remove();
            });
        });
    </script> --}}
@endpush
