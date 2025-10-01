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
                        <h5 class="card-title">Create Payment Note</h5>
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
                            <input type="hidden" name="reimbursement_note_id" value="{{ $note->id }}">

                            <div class="col-4">
                                <label for="one" class="form-label">Note No</label>
                                <input type="text" class="form-control" name="note_no" value="{{ $orderNumber }}"
                                    id="one" value="{{ old('note_no') }}" readonly>
                                @error('note_no')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">Green Note No</label>
                                <input type="text" class="form-control" value="{{ $note->formatted_order_no ?? '' }}"
                                    readonly>
                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">Department</label>
                                <input type="text" class="form-control" value="{{ $note->user->department->name ?? '' }}"
                                    readonly>
                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">Green Note App Date:</label>
                                <input type="text" class="form-control"
                                    value="{{ $note->latestApproval?->created_at->format('d/m/Y H:i A') ?? '-' }}" readonly>
                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">Green Note Approver:</label>
                                <input type="text" class="form-control" value="{{ $note->approver->name ?? '-' }}"
                                    readonly>
                            </div>
                            <div class="col-12">
                                <label for="one" class="form-label">Subject:</label>
                                <textarea id="subject" required name="subject" cols="30" rows="2" class="form-control">{{ old('subject') }}</textarea>
                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">Vendor Code</label>
                                <input type="text" class="form-control" value="{{ 'N/A' }}" readonly>
                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">Vendor Name</label>
                                <input type="text" class="form-control" value="{{ $note->user->name ?? '' }}" readonly>
                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">Invoice No.</label>
                                <input type="text" class="form-control" value="{{ 'N/A' }}" readonly>
                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">Invoice Date</label>
                                <input type="text" class="form-control" value="{{ 'N/A' }}" readonly>
                            </div>
                            @php
                                $totalPayable = $note->expenses->sum('bill_amount');
                                $advanceAdjusted = $note->adjusted;
                                $netPayable = $totalPayable - $advanceAdjusted;
                            @endphp
                            <div class="col-4">
                                <label for="one" class="form-label">Invoice Amount:</label>
                                <input type="text" class="form-control" value="{{ $netPayable ?? '' }}" readonly>
                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">Invoice Approved by:</label>
                                <input type="text" class="form-control" value="{{ 'N/A' }}" readonly>
                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">LOA/PO No.:</label>
                                <input type="text" class="form-control" value="{{ 'N/A' }}" readonly>
                            </div>
                            <div class="col-4">
                                <label for="one" class="form-label">LOA/PO Date:</label>
                                <input type="text" class="form-control" value="{{ 'N/A' }}" readonly>
                            </div>

                            <div class="col-4">
                                <label for="one" class="form-label">LOA/PO Amount:</label>
                                <input type="text" class="form-control" value="{{ 'N/A' }}" readonly>
                            </div>

                            <div class="">
                                <div class="row mt-2">
                                    <div class="col-5 mb-2">
                                        <input type="text" value="Taxable Amount" class="form-control"
                                            placeholder="Taxable Amount" readonly>
                                    </div>
                                    <div class="col-5 mb-2">
                                        <input type="number" value="{{ $netPayable ?? '' }}" class="form-control"
                                            placeholder="Amount" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 mb-2">
                                        <input type="text" value="Add : GST" class="form-control"
                                            placeholder="Add : GST " readonly>
                                    </div>
                                    <div class="col-5 mb-2">
                                        <input type="number" value="{{ $note->invoice_gst ?? '' }}"
                                            class="form-control" placeholder="Amount" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 mb-2">
                                        <input type="text" value="Add: Other charges " class="form-control"
                                            placeholder="Add: Other charges " readonly>
                                    </div>
                                    <div class="col-5 mb-2">
                                        <input type="number" value="{{ $note->invoice_other_charges ?? '' }}"
                                            class="form-control" placeholder="Amount" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 mb-2">
                                        <input type="text" value="Gross Amount " class="form-control"
                                            placeholder="Gross Amount " readonly>
                                    </div>
                                    <div class="col-5 mb-2">
                                        <input type="number" value="{{ $netPayable ?? '' }}" id="gross_amount"
                                            class="form-control" placeholder="Amount" readonly>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label>Less: Particulars & Payable Amount</label>
                                <div id="lessContainer">
                                    @for ($i = 0; $i < 4; $i++)
                                        <div class="row mt-2">
                                            <div class="col-5 mb-2">
                                                <input type="text"
                                                    name="less_particulars[{{ $i }}][particular]"
                                                    class="form-control" placeholder="Particular">
                                            </div>
                                            <div class="col-5 mb-2">
                                                <input type="number"
                                                    name="less_particulars[{{ $i }}][amount]"
                                                    class="form-control less-amount" placeholder="Amount">
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>

                            <div>
                                <label>Add: Particulars & Payable Amount</label>
                                <div id="addContainer">
                                    @for ($i = 0; $i < 4; $i++)
                                        <div class="row mt-2">
                                            <div class="col-5 mb-2">
                                                <input type="text"
                                                    name="add_particulars[{{ $i }}][particular]"
                                                    class="form-control" placeholder="Particular">
                                            </div>
                                            <div class="col-5 mb-2">
                                                <input type="number" name="add_particulars[{{ $i }}][amount]"
                                                    class="form-control add-amount" placeholder="Amount">
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-5 mb-2">
                                    <input type="text" value="Net Payable Amount" class="form-control"
                                        placeholder="Net Payable Amount" readonly>
                                </div>
                                <div class="col-5 mb-2">
                                    <input type="text" class="form-control" placeholder="Amount" readonly
                                        id="net_payable_amount" name="net_payable_amount">
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
                                        <input type="text" value="{{ $note->account_holder ?? '-' }}"
                                            class="form-control" placeholder="Amount" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 mb-2">
                                        <input type="text" value="Bank Name" class="form-control"
                                            placeholder="Bank Name " readonly>
                                    </div>
                                    <div class="col-5 mb-2">
                                        <input type="text" value="{{ $note->bank_name ?? '-' }}" class="form-control"
                                            placeholder="Amount" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 mb-2">
                                        <input type="text" value="Bank Account" class="form-control"
                                            placeholder="Bank Account" readonly>
                                    </div>
                                    <div class="col-5 mb-2">
                                        <input type="text" value="{{ $note->bank_account ?? '-' }}"
                                            class="form-control" placeholder="Amount" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 mb-2">
                                        <input type="text" value="IFSC" class="form-control" placeholder="IFSC "
                                            readonly>
                                    </div>
                                    <div class="col-5 mb-2">
                                        <input type="text" value="{{ $note->IFSC_code ?? '-' }}" class="form-control"
                                            placeholder="Amount" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="one" class="form-label">Recommendation of Payment</label>
                                <textarea id="recommendation_of_payment" name="recommendation_of_payment" required cols="30" rows="2"
                                    class="form-control">{{ old('recommendation_of_payment') }}</textarea>

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
                        <h5 class="card-title">Attach Files <a class="btn btn-primary"
                                href="{{ route('backend.reimbursement-note.view.pdf', $note->id) }}">View Reimbursement
                                Note </a></h5>
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
                                                <a href="{{ asset('storage/rn/' . $file) }}" download
                                                    class="btn btn-sm btn-primary">
                                                    Download
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
        </div>
    </section>
@endsection

@push('script')
    {{-- - --}}

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
