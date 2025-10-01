@extends('backend.layouts.app')
@section('content')
    {{-- <div class="pagetitle">
        <h1>Blank Page {{ request()->route('slno') ?? 'N/A' }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item">Pages</li>
                <li class="breadcrumb-item active">Blank</li>
            </ol>
        </nav>
    </div><!-- End Page Title --> --}}

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('backend.templates.templateCommon', request()->route('tpl')) }}" method="post"
                            id="tplForm">
                            @csrf
                            {{-- <h5 class="card-title">Letter Preview ({{ucwords(str_replace("-", " ", request()->route('tpl')))}})</h5> --}}
                            <input type="hidden" id="slno" name="slno" value="{{ request()->slno ?? '' }}"
                                placeholder="Enter SL NO.">
                            <!--<p>This is an examle page with no contrnt. You can use it as a starter for your custom pages.-->
                            <!--</p>-->
                            <div class="row mb-3 mt-3" bis_skin_checked="1">
                                <div class="col-sm-10" bis_skin_checked="1">
                                    <button type="button" class="btn btn-sm btn-primary templateGeneratePDF"
                                        data-url="{{ route('backend.templates.templateCommonGenPdf', request()->route('tpl')) }}"
                                        style="display: none;"><i class="bi bi-download"></i> Download PDF</button>
                                </div>
                            </div>
                            <div id="tplTableData">

                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Approval</h5>
                        @php
                            $approvers = \App\Models\BankLetterApprovalLog::where('sl_no', request()->slno)
                                ->where('status', 'A')
                                ->get();

                        @endphp
                        @if ($approvers->last()?->logPriorities->last()?->priority)
                            @php
                                $showButton = false;

                                foreach ($approvers->last()->logPriorities as $log) {
                                    if ($log->priority && $log->priority->reviewer_id == Auth::id()) {
                                        $showButton = true;
                                        break;
                                    }
                                }

                            @endphp
                            <div class="d-flex gap-2">
                                @if ($showButton)
                                    <form id="approveForm" action="{{ route('backend.payments.logUpdate') }}"
                                        method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="A">
                                        <input type="hidden" name="sl_no" value="{{ request()->slno }}">
                                        <button type="submit" class="btn btn-success d-flex align-items-center gap-2"
                                            id="approveBtn">
                                            <span id="approveSpinner" class="spinner-border spinner-border-sm d-none"
                                                role="status" aria-hidden="true"></span>
                                            <span>Approve</span>
                                        </button>
                                    </form>
                                    <form id="rejectForm" action="{{ route('backend.payments.logUpdate') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="R">
                                        <input type="hidden" name="sl_no" value="{{ request()->slno }}">

                                        <!-- Button to show the remarks field -->
                                        <button type="button" class="btn btn-danger" id="showRemarksBtn">Reject</button>

                                        <!-- Remarks field and Submit ton (hidden initially) -->
                                        <div id="remarksSection" style="display: none; margin-top: 10px;">
                                            <label for="remarks" class="form-label">Remarks</label>
                                            <textarea name="remarks" id="remarks" class="form-control" placeholder="Enter remarks..." required></textarea>
                                            <button type="submit"
                                                class="btn btn-danger mt-2 d-flex align-items-center gap-2"
                                                id="rejectSubmitBtn">
                                                <span id="rejectSpinner" class="spinner-border spinner-border-sm d-none"
                                                    role="status" aria-hidden="true"></span>
                                                <span>Submit</span>
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Flow</h5>

                        @php
                            $sl_no = request()->slno;

                            $approvers = \App\Models\BankLetterApprovalLog::with('reviewer')
                                ->where('sl_no', $sl_no)
                                ->get()
                                ->filter(function ($log) {
                                    return $log->reviewer->getRoleNames()->contains('PN Approver');
                                });
                            $approverNames = $approvers->map(function ($log) {
                                return $log->reviewer;
                            });
                        @endphp
                        @foreach ($approvers as $index => $step)
                            <div class="d-flex align-items-center position-relative mb-4 mt-5" style="padding-left: 30px;">
                                <!-- Step Dot -->
                                <div class="position-absolute bg-primary rounded-circle"
                                    style="width: 12px; height: 12px; left: 5px;">
                                </div>
                                <!-- Step Info -->
                                <div>
                                    <p class="fw-bold mb-1">Step {{ $index }}</p>
                                    <p class="text-muted small">{{ $index == 0 ? 'Maker' : 'Reviewer' }}:
                                        {{ $step->reviewer->name }}</p>
                                    </p>
                                    @if ($step->status == 'R')
                                        <p class="text-muted small">Remarks:
                                            {{ $step->comments ?? '-' }}</p>
                                        </p>
                                    @endif
                                    <p class="text-muted small">
                                        @if ($step->status == 'A')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($step->status == 'P')
                                            <span class="badge bg-warning text-dark">Draft</span>
                                        @elseif($step->status == 'R')
                                            <span class="badge bg-danger">Rejected</span>
                                        @elseif($step->status == 'S')
                                            <span class="badge bg-success">Approved</span>
                                        @else
                                            <span class="badge bg-secondary">Unknown</span>
                                        @endif
                                    </p>

                                    {{ $step->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') }}
                                    @if (!$loop->last)
                                        <div class="position-absolute start-0 top-100 translate-middle-y border-start border-2"
                                            style="height: 30px; left: 6px;"></div>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        @if ($approvers->last()?->logPriorities->last()?->priority)
                            <div class="d-flex align-items-center position-relative mb-4" style="padding-left: 30px;">
                                <div class="position-absolute bg-primary rounded-circle"
                                    style="width: 12px; height: 12px; left: 5px;">
                                </div>
                                <div>
                                    <p class="fw-bold mb-1">Next Approver:
                                        @foreach ($approvers->last()?->logPriorities as $log)
                                            {{ $log->priority->user->name }} ,
                                        @endforeach
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                @php
                    $cartItems = App\Models\Payment::with('paymentNote')->where('sl_no', request()->slno)->get();
                @endphp
                @foreach ($cartItems as $item)
                    @php
                        $documents = [];

                        if (isset($item->paymentNote) && isset($item->paymentNote->greenNote)) {
                            $documents = App\Models\SupportingDoc::where(
                                'green_note_id',
                                $item->paymentNote->greenNote->id,
                            )->get();
                        }
                    @endphp

                    @if (!empty($item->paymentNote) && !empty($item->paymentNote->greenNote))
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Attached Supporting Docs <a class="btn btn-primary"
                                            href="{{ route('backend.note.view.pdf', $item->paymentNote->greenNote->id) }}">View
                                            Green Note</a> |
                                        <a class="btn btn-primary"
                                            href="{{ route('backend.payment-note.show', $item->paymentNote->id) }}">
                                            <i class="bi bi-eye"></i> View Payment Note
                                        </a>
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
                                                            $extension = pathinfo(
                                                                $document->file_path,
                                                                PATHINFO_EXTENSION,
                                                            );
                                                        @endphp

                                                        @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                                                            <img src="{{ asset('notes/documents/' . $document->file_path) }}"
                                                                alt="Document Image" width="70" height="70">
                                                        @else
                                                            <a href="{{ asset('notes/documents/' . $document->file_path) }}"
                                                                target="_blank"><i
                                                                    class="bi bi-file-earmark-text-fill"></i></a>
                                                        @endif
                                                    </td>
                                                    <td>{{ $document->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') }}
                                                    </td>
                                                    <td>{{ $document->user->name }}</td>
                                                    <td> <a href="{{ asset('notes/documents/' . $document->file_path) }}"
                                                            download>
                                                            <i class="bi bi-download"></i>

                                                        </a>
                                                        @if (\Auth::id() == 1)
                                                            |
                                                            <form
                                                                action="{{ route('backend.documents.destroy', $document->id) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-none btn-sm delete-btn"><i
                                                                        class="bi bi-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                    <!-- End Table with stripped rows -->
                                </div>
                            </div>
                        </div>
                    @elseif (!empty($item->paymentNote) && !empty($item->paymentNote->reimbursementNote))
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Attach Files
                                        <a class="btn btn-primary"
                                            href="{{ route('backend.reimbursement-note.view.pdf', $item->paymentNote->reimbursementNote->id) }}">View
                                            Reimbursement
                                            Note </a> |
                                        <a class="btn btn-primary"
                                            href="{{ route('backend.payment-note.show', $item->paymentNote->id) }}">
                                            <i class="bi bi-eye"></i> View Payment Note
                                        </a>
                                    </h5>
                                    @if ($item->paymentNote->reimbursementNote->file_path)
                                        <table class="table table-bordered mt-2">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Preview</th>
                                                    <th>Download</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach (json_decode($item->paymentNote->reimbursementNote->file_path, true) as $key => $file)
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
                                                                <img src="{{ asset('storage/rn/' . $file) }}"
                                                                    alt="Preview" width="50">
                                                            @elseif (strtolower($extension) == 'pdf')
                                                                <a href="{{ asset('storage/rn/' . $file) }}"
                                                                    target="_blank">View
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
                @endforeach
            </div>
        </div>
    </section>
@endsection
@push('script')
    <script>
        // var formData = $('.mybids-filter-form').serializeArray();
        // formData.push({ name: "viewType", value: viewType });
        // $('.mybids-filter-form').serialize()
        var formData = $('#tplForm').serializeArray();
        formData.push({
            name: "formId",
            value: 'tplForm'
        })
        formData.push({
            name: "outputId",
            value: 'tplTableData'
        })
        getTemplateView($('#tplForm').attr('action'), formData);
        $("#slno:input").on("keyup change, ready", function(e) {
            var formData = $('#tplForm').serializeArray();
            formData.push({
                name: "formId",
                value: 'tplForm'
            })
            formData.push({
                name: "outputId",
                value: 'tplTableData'
            })
            getTemplateView($('#tplForm').attr('action'), formData);
            /* $.ajax({
                url: '{{ "backend.templates.'+slno" }}',
                type: 'post',
                dataType: 'json',
                data: formData,
                contentType: 'application/json',
                success: function(response) {
                    console.log(response)
                    },
                    response: JSON.stringify(person)
                    }); */
        })

        $(document).on("click", ".templateGeneratePDF", function(e) {
            e.preventDefault();
            /* var formData = [];
            formData.push({
                name: "slno",
                value: $("#slno:input").val()
            }) */

            var formData = $('#tplForm').serializeArray();
            // formData.push({
            //     name: "formId",
            //     value: 'tplForm'
            // })
            // formData.push({
            //     name: "outputId",
            //     value: 'tplTableData'
            // })
            templateGeneratePDFAjax($(this).data('url'), formData);
        });
    </script>

    <script>
        document.getElementById('showRemarksBtn').addEventListener('click', function() {
            document.getElementById('remarksSection').style.display = 'block'; // Show remarks section
            this.style.display = 'none'; // Hide the initial button
        });
    </script>
    <!-- JS: Disable + Spinner on submit -->
    <script>
        // Approve button logic
        document.getElementById('approveForm').addEventListener('submit', function() {
            const btn = document.getElementById('approveBtn');
            const spinner = document.getElementById('approveSpinner');
            btn.disabled = true;
            spinner.classList.remove('d-none');
        });

        // Show remarks when Reject clicked
        document.getElementById('showRemarksBtn').addEventListener('click', function() {
            document.getElementById('remarksSection').style.display = 'block';
            this.style.display = 'none';
        });

        // Reject submit logic
        document.getElementById('rejectForm').addEventListener('submit', function() {
            const btn = document.getElementById('rejectSubmitBtn');
            const spinner = document.getElementById('rejectSpinner');
            btn.disabled = true;
            spinner.classList.remove('d-none');
        });
    </script>
@endpush
