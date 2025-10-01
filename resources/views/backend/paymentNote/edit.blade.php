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
                        <h5 class="card-title">Edit Payment Note ({{ $note->note_no }})</h5>
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
                        <form class="row g-3" action="{{ route('backend.payment-note.update', $note->id) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="col-6">
                                <label for="one" class="form-label">Note No</label>
                                <input type="text" class="form-control" name="note_no" id="one"
                                    value="{{ old('note_no', $note->note_no) }}" readonly>
                                @error('note_no')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">Green Note No</label>
                                @if ($note->greenNote)
                                    <input type="text" class="form-control" value="{{ $note->greenNote->order_no }}"
                                        readonly>
                                @elseif ($note->reimbursementNote)
                                    <input type="text" class="form-control"
                                        value="{{ $note->reimbursementNote->note_no }}" readonly>
                                @else
                                    <input type="text" class="form-control" value="N/A" readonly>
                                @endif

                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">Department</label>
                                @if ($note->greenNote)
                                    <input type="text" class="form-control"
                                        value="{{ $note->greenNote->department->name }}" readonly>
                                @elseif ($note->reimbursementNote)
                                    <input type="text" class="form-control"
                                        value="{{ $note->reimbursementNote->selectUser ? $note->reimbursementNote->selectUser->department->name : $note->reimbursementNote->user->department->name ?? '' }}"
                                        readonly>
                                @else
                                    <input type="text" class="form-control" value="N/A" readonly>
                                @endif

                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">Green Note App Date:</label>
                                @if ($note->greenNote)
                                    @php
                                        $lastStep = $note->greenNote->approvalLogs->last();
                                    @endphp
                                    @if ($lastStep)
                                        <input type="text" class="form-control"
                                            value="{{ $lastStep->reviewer->created_at->format('d/m/Y H:i A') }}" readonly>
                                    @endif
                                @elseif ($note->reimbursementNote)
                                    <input type="text" class="form-control"
                                        value="{{ $note->reimbursementNote->latestApproval?->created_at->format('d/m/Y H:i A') ?? '-' }}"
                                        readonly>
                                @else
                                    <input type="text" class="form-control" value="N/A" readonly>
                                @endif

                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">Green Note Approver:</label>
                                @if ($note->greenNote)
                                    @if ($lastStep)
                                        <input type="text" class="form-control" value="{{ $lastStep->reviewer->name }}"
                                            readonly>
                                    @endif
                                @elseif ($note->reimbursementNote)
                                    <input type="text" class="form-control"
                                        value="{{ $note->reimbursementNote->approver->name ?? '-' }}" readonly>
                                @else
                                    <input type="text" class="form-control" value="N/A" readonly>
                                @endif

                            </div>
                            <div class="col-12">
                                <label for="one" class="form-label">Subject:</label>
                                <textarea id="subject" name="subject" cols="30" rows="2" class="form-control">{{ old('subject', $note->subject) }}</textarea>
                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">Vendor Code</label>
                                @if ($note->greenNote)
                                    <input type="text" class="form-control"
                                        value="{{ $note->greenNote->supplier->vendor_code ?? '-' }}" readonly>
                                @elseif ($note->reimbursementNote)
                                    <input type="text" class="form-control" value="N/A" readonly>
                                @else
                                    <input type="text" class="form-control" value="N/A" readonly>
                                @endif

                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">Vendor Name</label>
                                @if ($note->greenNote)
                                    <input type="text" class="form-control"
                                        value="{{ $note->greenNote->supplier->vendor_name ?? '-' }}" readonly>
                                @elseif ($note->reimbursementNote)
                                    <input type="text" class="form-control"
                                        value="{{ $note->reimbursementNote->selectUser ? $note->reimbursementNote->selectUser->name : $note->reimbursementNote->user->name ?? '' }}"
                                        readonly>
                                @else
                                    <input type="text" class="form-control" value="N/A" readonly>
                                @endif

                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">Invoice No.</label>
                                @if ($note->greenNote)
                                    <input type="text" class="form-control"
                                        value="{{ $note->greenNote->invoice_number }}" readonly>
                                @elseif ($note->reimbursementNote)
                                    <input type="text" class="form-control" value="N/A" readonly>
                                @else
                                    <input type="text" class="form-control" value="N/A" readonly>
                                @endif

                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">Invoice Date</label>
                                @if ($note->greenNote)
                                    <input type="text" class="form-control"
                                        value="{{ $note->greenNote->invoice_date }}" readonly>
                                @elseif ($note->reimbursementNote)
                                    <input type="text" class="form-control" value="N/A" readonly>
                                @else
                                    <input type="text" class="form-control" value="N/A" readonly>
                                @endif

                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">Invoice Amount:</label>
                                @if ($note->greenNote)
                                    <input type="text" class="form-control"
                                        value="{{ $note->greenNote->invoice_value }}" readonly>
                                @elseif ($note->reimbursementNote)
                                    @php
                                        $totalPayable = $note->reimbursementNote->expenses->sum('bill_amount') ?? 0;
                                        $advanceAdjusted = $note->reimbursementNote->adjusted ?? 0;
                                        $netPayable = $totalPayable - $advanceAdjusted;
                                    @endphp
                                    <input type="text" class="form-control" value="{{ $netPayable ?? '' }}" readonly>
                                @else
                                    <input type="text" class="form-control" value="N/A" readonly>
                                @endif

                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">Invoice Approved by:</label>
                                @if ($note->greenNote)
                                    @if ($lastStep)
                                        <input type="text" class="form-control"
                                            value="{{ $lastStep->reviewer->name }}" readonly>
                                    @endif
                                @elseif ($note->reimbursementNote)
                                    <input type="text" class="form-control" value="N/A" readonly>
                                @else
                                    <input type="text" class="form-control" value="N/A" readonly>
                                @endif

                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">LOA/PO No.:</label>
                                @if ($note->greenNote)
                                    <input type="text" class="form-control" value="{{ $note->greenNote->order_no }}"
                                        readonly>
                                @elseif ($note->reimbursementNote)
                                    <input type="text" class="form-control" value="N/A" readonly>
                                @else
                                    <input type="text" class="form-control" value="N/A" readonly>
                                @endif

                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">LOA/PO Date:</label>
                                @if ($note->greenNote)
                                    <input type="text" class="form-control"
                                        value="{{ $note->greenNote->order_date }}" readonly>
                                @elseif ($note->reimbursementNote)
                                    <input type="text" class="form-control" value="N/A" readonly>
                                @else
                                    <input type="text" class="form-control" value="N/A" readonly>
                                @endif

                            </div>

                            <div class="col-4">
                                <label for="one" class="form-label">LOA/PO Amount:</label>
                                @if ($note->greenNote)
                                    <input type="text" class="form-control"
                                        value="{{ $note->greenNote->total_amount }}" readonly>
                                @elseif ($note->reimbursementNote)
                                    <input type="text" class="form-control" value="N/A" readonly>
                                @else
                                    <input type="text" class="form-control" value="N/A" readonly>
                                @endif

                            </div>
                            <div class="">
                                <div class="row mt-2">
                                    <div class="col-5 mb-2">
                                        <input type="text" value="Taxable Amount" class="form-control"
                                            placeholder="Taxable Amount" readonly>
                                    </div>
                                    <div class="col-5 mb-2">
                                        @if ($note->greenNote)
                                            <input type="text" value="{{ $note->greenNote->invoice_base_value }}"
                                                class="form-control" placeholder="Amount" readonly>
                                        @elseif ($note->reimbursementNote)
                                            <input type="text" class="form-control" value="{{ $netPayable ?? '' }}"
                                                readonly>
                                        @else
                                            <input type="text" class="form-control" value="N/A" readonly>
                                        @endif

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 mb-2">
                                        <input type="text" value="Add : GST" class="form-control"
                                            placeholder="Add : GST " readonly>
                                    </div>
                                    <div class="col-5 mb-2">
                                        @if ($note->greenNote)
                                            <input type="number" value="{{ $note->greenNote->invoice_gst }}"
                                                class="form-control" placeholder="Amount" readonly>
                                        @elseif ($note->reimbursementNote)
                                            <input type="text" class="form-control" value="N/A" readonly>
                                        @else
                                            <input type="text" class="form-control" value="N/A" readonly>
                                        @endif

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 mb-2">
                                        <input type="text" value="Add: Other charges " class="form-control"
                                            placeholder="Add: Other charges " readonly>
                                    </div>
                                    <div class="col-5 mb-2">
                                        @if ($note->greenNote)
                                            <input type="text" value="{{ $note->greenNote->invoice_other_charges }}"
                                                class="form-control" placeholder="Amount" readonly>
                                        @elseif ($note->reimbursementNote)
                                            <input type="text" class="form-control" value="N/A" readonly>
                                        @else
                                            <input type="text" class="form-control" value="N/A" readonly>
                                        @endif

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 mb-2">
                                        <input type="text" value="Gross Amount " class="form-control"
                                            placeholder="Gross Amount " readonly>
                                    </div>
                                    <div class="col-5 mb-2">
                                        @if ($note->greenNote)
                                            <input type="text" value="{{ $grossAmount }}" id="gross_amount"
                                                class="form-control" placeholder="Amount" readonly>
                                        @elseif ($note->reimbursementNote)
                                            <input type="text" class="form-control" id="gross_amount"
                                                value="{{ $netPayable ?? '' }}" readonly>
                                        @else
                                            <input type="text" class="form-control" value="N/A" readonly>
                                        @endif

                                    </div>
                                </div>
                            </div>
                            <div class="">
                                <label>Less: Particulars & Payable Amount</label>
                                @php
                                    $totalLessFields = 4;
                                    $existingLessCount = collect($lessParticulars)
                                        ->filter(fn($p) => !empty($p['particular']))
                                        ->count();
                                    $remainingLessFields = max(0, $totalLessFields - $existingLessCount);
                                @endphp
                                @foreach ($lessParticulars as $i => $particular)
                                    @if ($particular['particular'])
                                        <div class="row mt-2">
                                            <div class="col-5 mb-2">
                                                <input type="text"
                                                    name="less_particulars[{{ $i }}][particular]"
                                                    class="form-control" placeholder="Particular"
                                                    value="{{ $particular['particular'] ?? '-' }}">
                                            </div>
                                            <div class="col-5 mb-2">
                                                <input type="number"
                                                    name="less_particulars[{{ $i }}][amount]"
                                                    class="form-control less-amount" placeholder="Amount"
                                                    value="{{ $particular['amount'] }}">
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                                @for ($j = 0; $j < $remainingLessFields; $j++)
                                    <div class="row mt-2">
                                        <div class="col-5 mb-2">
                                            <input type="text"
                                                name="less_particulars[{{ $existingLessCount + $j }}][particular]"
                                                class="form-control" placeholder="Particular" value="">
                                        </div>
                                        <div class="col-5 mb-2">
                                            <input type="number"
                                                name="less_particulars[{{ $existingLessCount + $j }}][amount]"
                                                class="form-control less-amount" placeholder="Amount" value="">
                                        </div>
                                    </div>
                                @endfor
                            </div>
                            <div class="">
                                @php
                                    $totalAddFields = 4;
                                    $existingAddCount = collect($addParticulars)
                                        ->filter(fn($p) => !empty($p['particular']))
                                        ->count();
                                    $remainingAddFields = max(0, $totalAddFields - $existingAddCount);
                                @endphp
                                <label>Add: Particulars & Payable Amount </label>
                                @foreach ($addParticulars as $i => $particular)
                                    @if ($particular['particular'])
                                        <div class="row mt-2">
                                            <div class="col-5 mb-2">
                                                <input type="text"
                                                    name="add_particulars[{{ $i }}][particular]"
                                                    class="form-control" placeholder="Particular"
                                                    value="{{ $particular['particular'] ?? '-' }}">
                                            </div>
                                            <div class="col-5 mb-2">
                                                <input type="number" name="add_particulars[{{ $i }}][amount]"
                                                    class="form-control add-amount" placeholder="Amount"
                                                    value="{{ $particular['amount'] }}">
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                                @for ($j = 0; $j < $remainingAddFields; $j++)
                                    <div class="row mt-2">
                                        <div class="col-5 mb-2">
                                            <input type="text"
                                                name="add_particulars[{{ $existingAddCount + $j }}][particular]"
                                                class="form-control" placeholder="Particular" value="">
                                        </div>
                                        <div class="col-5 mb-2">
                                            <input type="number"
                                                name="add_particulars[{{ $existingAddCount + $j }}][amount]"
                                                class="form-control add-amount" placeholder="Amount" value="">
                                        </div>
                                    </div>
                                @endfor
                            </div>
                            <div class="row mt-2">
                                <div class="col-5 mb-2">
                                    <input type="text" value="Net Payable Amount" class="form-control"
                                        placeholder="Net Payable Amount" readonly>
                                </div>
                                <div class="col-5 mb-2">
                                    <input type="text" class="form-control" placeholder="Amount" readonly
                                        id="net_payable_amount">
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

                                <div class="row mt-2">
                                    <div class="col-5 mb-2">
                                        <input type="text" value="Name of Account holder" class="form-control"
                                            placeholder="Name of Account holder" readonly>
                                    </div>
                                    <div class="col-5 mb-2">

                                        @if ($note->greenNote)
                                            <input type="text"
                                                value="{{ $note->greenNote->supplier->vendor_name ?? '-' }}"
                                                class="form-control" placeholder="Amount" readonly>
                                        @elseif ($note->reimbursementNote)
                                            <input type="text"
                                                value="{{ $note->reimbursementNote->account_holder ?? '-' }}"
                                                class="form-control" placeholder="Amount" readonly>
                                        @else
                                            <input type="text" class="form-control" value="N/A" readonly>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 mb-2">
                                        <input type="text" value="Bank Name" class="form-control"
                                            placeholder="Bank Name " readonly>
                                    </div>
                                    <div class="col-5 mb-2">
                                        @if ($note->greenNote)
                                            <input type="text"
                                                value="{{ $note->greenNote->supplier->name_of_bank ?? '-' }}"
                                                class="form-control" placeholder="Amount" readonly>
                                        @elseif ($note->reimbursementNote)
                                            <input type="text"
                                                value="{{ $note->reimbursementNote->bank_name ?? '-' }}"
                                                class="form-control" placeholder="Amount" readonly>
                                        @else
                                            <input type="text" class="form-control" value="N/A" readonly>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 mb-2">
                                        <input type="text" value="Bank Account" class="form-control"
                                            placeholder="Bank Account" readonly>
                                    </div>
                                    <div class="col-5 mb-2">

                                        @if ($note->greenNote)
                                            <input type="text"
                                                value="{{ $note->greenNote->supplier->account_number ?? '-' }}"
                                                class="form-control" placeholder="Amount" readonly>
                                        @elseif ($note->reimbursementNote)
                                            <input type="text"
                                                value="{{ $note->reimbursementNote->bank_account ?? '-' }}"
                                                class="form-control" placeholder="Amount" readonly>
                                        @else
                                            <input type="text" class="form-control" value="N/A" readonly>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 mb-2">
                                        <input type="text" value="IFSC" class="form-control" placeholder="IFSC"
                                            readonly>
                                    </div>
                                    <div class="col-5 mb-2">

                                        @if ($note->greenNote)
                                            <input type="text"
                                                value="{{ $note->greenNote->supplier->ifsc_code ?? '-' }}"
                                                class="form-control" placeholder="Amount" readonly>
                                        @elseif ($note->reimbursementNote)
                                            <input type="text"
                                                value="{{ $note->reimbursementNote->IFSC_code ?? '-' }}"
                                                class="form-control" placeholder="Amount" readonly>
                                        @else
                                            <input type="text" class="form-control" value="N/A" readonly>
                                        @endif
                                    </div>
                                </div>
                            </div>


                            <div class="col-12">
                                <label for="one" class="form-label">Recommendation of Payment</label>
                                <textarea id="subject" name="recommendation_of_payment" cols="30" rows="2" class="form-control">{{ old('recommendation_of_payment', $note->recommendation_of_payment) }}</textarea>

                            </div>
                            @if (auth()->user()->id == $note->user_id)
                                <div class="col-12">
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
            @if ($note->greenNote)
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Attached Supporting Docs <a class="btn btn-primary"
                                    href="{{ route('backend.note.view.pdf', $note->greenNote->id) }}">View Green Note</a>
                            </h5>
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
                                            <td> <a href="{{ asset('notes/documents/' . $document->file_path) }}"
                                                    download>
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

                                </tbody>
                            </table>
                            <!-- End Table with stripped rows -->
                        </div>
                    </div>
                </div>
            @elseif ($note->reimbursementNote)
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Attach Files <a class="btn btn-primary"
                                    href="{{ route('backend.reimbursement-note.view.pdf', $note->reimbursementNote->id) }}">View
                                    Reimbursement
                                    Note </a></h5>
                            @if ($note->reimbursementNote->file_path)
                                <table class="table table-bordered mt-2">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Preview</th>
                                            <th>Download</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach (json_decode($note->reimbursementNote->file_path, true) as $key => $file)
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
                                                    <a href="{{ asset('storage/rn/' . $file) }}" download
                                                        class="btn btn-sm btn-primary">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>
            @else
            @endif

        </div>
    </section>
@endsection
@push('script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function calculateNetPayable() {
                let grossAmount = parseFloat(document.getElementById("gross_amount").value) || 0;

                // Sum of Less Amounts
                let lessAmount = Array.from(document.querySelectorAll(".less-amount")).reduce((total, input) => {
                    return total + (parseFloat(input.value) || 0);
                }, 0);

                // Sum of Add Amounts
                let addAmount = Array.from(document.querySelectorAll(".add-amount")).reduce((total, input) => {
                    return total + (parseFloat(input.value) || 0);
                }, 0);

                // Net Payable Calculation
                let netPayable = grossAmount - lessAmount + addAmount;
                let netPayableRound = Math.round(netPayable);

                // Update values
                document.getElementById("net_payable_amount").value = netPayable.toFixed(2);
                document.getElementById("net_payable_round_off").value = netPayableRound.toFixed(2);
            }

            // Attach event listeners
            document.querySelectorAll(".less-amount, .add-amount").forEach(input => {
                input.addEventListener("input", calculateNetPayable);
            });

            // Initial Calculation
            calculateNetPayable();
        });
    </script>
@endpush
