@extends('backend.layouts.app')
@section('content')
    <div class="pagetitle">
        <h1>All Payment Note</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                <li class="breadcrumb-item active">Payment Notes</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">All Payment Notes</h5>
                        <ul class="nav nav-tabs mb-3">
                            <li class="nav-item">
                                <a class="nav-link {{ request('status') === 'all' ? 'active' : '' }}"
                                    href="{{ route('backend.payment-note.index', ['status' => 'all']) }}">All</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request('status') === null || request('status') === 'S' ? 'active' : '' }}"
                                    href="{{ route('backend.payment-note.index', ['status' => 'S']) }}">Pending</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request('status') === 'A' ? 'active' : '' }}"
                                    href="{{ route('backend.payment-note.index', ['status' => 'A']) }}">Approved</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request('status') === 'R' ? 'active' : '' }}"
                                    href="{{ route('backend.payment-note.index', ['status' => 'R']) }}">Rejected</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request('status') === 'D' ? 'active' : '' }}"
                                    href="{{ route('backend.payment-note.index', ['status' => 'D']) }}">Draft</a>
                            </li>
                        </ul>

                        <form id="bulkBankLetterForm" action="{{ route('backend.payments.createBankLetter') }}"
                            method="POST">
                            @csrf
                            <input type="hidden" name="note_ids" id="selectedNoteIds"> {{-- This will contain comma-separated IDs --}}
                        </form>
                        <!-- Table with stripped rows -->
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>S no.</th>
                                    <th>Project Name</th>
                                    <th>Vendor Name</th>
                                    <th>Invoice Value</th>
                                    <th data-type="date" data-format="DD/MM/YYYY">Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($notes as $index => $note)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if ($note->greenNote)
                                                {{ $note->greenNote->vendor->project ?? '-' }}
                                            @elseif ($note->reimbursementNote)
                                                {{ $note->reimbursementNote->project->project ?? '-' }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if ($note->greenNote)
                                                {{ $note->greenNote->supplier->vendor_name ?? '-' }}
                                            @elseif ($note->reimbursementNote)
                                                {{ $note->reimbursementNote->selectUser ? $note->reimbursementNote->selectUser->name : $note->reimbursementNote->user->name }}
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <td>{{ \App\Helpers\Helper::formatIndianNumber($note->net_payable_round_off) ?? '-' }}
                                        </td>
                                        <td>{{ $note->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?? '-' }}
                                        </td>
                                        <td>
                                            @php
                                                $userRoles = auth()->user()->getRoleNames();
                                                $statusLabels = [
                                                    'D' => '<span class="badge bg-dark">Draft</span>',
                                                    'P' => '<span class="badge bg-warning">Pending</span>',
                                                    'A' => '<span class="badge bg-success">Approved</span>',
                                                    'R' => '<span class="badge bg-danger">Rejected</span>',
                                                    'S' => '<span class="badge bg-secondary">Sent for Approval</span>',
                                                    'B' => '<span class="badge bg-black">RTGS/NEFT Created</span>',
                                                    'PA' => '<span class="badge bg-black">Payment Approved </span>',
                                                    'PD' => '<span class="badge bg-info">Paid</span>',
                                                ];
                                            @endphp
                                            {!! $statusLabels[$note->status] ?? '-' !!}
                                            @if ($note->status == 'PD' && $note->utr_date)
                                                <div class="text-muted" style="font-size: 12px;">
                                                    <strong>UTR Date :</strong>
                                                    {{ \Carbon\Carbon::parse($note->utr_date)->format('d-m-Y') ?? '-' }},
                                                    <br>
                                                    <strong>UTR Number :</strong>
                                                    {{ $note->utr_no ?? '-' }}
                                                </div>
                                            @endif
                                            @if (!in_array($note->status, ['A', 'B', 'PA', 'PD']))
                                                {{-- Show next approver based on approval rules --}}
                                                @php
                                                    $nextApprovers = collect();

                                                    // Check if there are any pending approval logs (status = 'P')
                                                    $pendingLogs = $note->paymentApprovalLogs->where('status', 'P');

                                                    if ($pendingLogs->isNotEmpty()) {
                                                        // Show approvers from the most recent pending log
                                                        $lastPendingLog = $pendingLogs->last();
                                                        foreach ($lastPendingLog->logPriorities as $logPriority) {
                                                            if ($logPriority->priority && $logPriority->priority->user) {
                                                                $nextApprovers->push($logPriority->priority->user);
                                                            }
                                                        }
                                                    }

                                                    // If no pending approvers, find the appropriate approval step and determine next level
                                                    if ($nextApprovers->isEmpty()) {
                                                        $approvalStep = \App\Models\PaymentNoteApprovalStep::where('min_amount', '<=', $note->net_payable_round_off)
                                                            ->where(function ($query) use ($note) {
                                                                $query->where('max_amount', '>=', $note->net_payable_round_off)
                                                                      ->orWhereNull('max_amount');
                                                            })
                                                            ->orderBy('min_amount', 'desc') // Get the most specific step
                                                            ->with('approvers.user')
                                                            ->first();

                                                        if ($approvalStep) {
                                                            // Determine the current pending level based on approved logs
                                                            $approvedLogsCount = $note->paymentApprovalLogs->where('status', 'A')->count();
                                                            $currentLevel = $approvedLogsCount + 1; // Current pending level

                                                            // Get approvers for the current level (where it's pending)
                                                            $currentLevelApprovers = $approvalStep->approvers->where('approver_level', $currentLevel);

                                                            if ($currentLevelApprovers->isNotEmpty()) {
                                                                foreach ($currentLevelApprovers as $approver) {
                                                                    if ($approver->user) {
                                                                        $nextApprovers->push($approver->user);
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                @endphp

                                                @if ($nextApprovers->isNotEmpty())
                                                    <p class="mb-1">
                                                        Next Approver:
                                                        @foreach ($nextApprovers as $index => $approver)
                                                            {{ $approver->name }}@if ($index < $nextApprovers->count() - 1), @endif
                                                        @endforeach
                                                    </p>
                                                @else
                                                    <p class="mb-1 text-muted">
                                                        <small>
                                                            @if ($note->paymentApprovalLogs->where('status', 'A')->count() > 0)
                                                                All configured approvers have approved
                                                            @else
                                                                No approvers configured
                                                            @endif
                                                        </small>
                                                    </p>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if ($note->canBeEditedBy(auth()->id()))
                                                <a href="{{ route('backend.payment-note.edit', $note->id) }}" 
                                                   class="text-primary" 
                                                   title="Edit Payment Note">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a> |
                                            @endif

                                            <a href="{{ route('backend.payment-note.show', $note->id) }}"
                                               class="text-info"
                                               title="View Payment Note">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            @if ($userRoles->contains('PN User') && $note->status == 'A')
                                                | <input type="checkbox" class="note-checkbox" value="{{ $note->id }}">
                                                <a href="javascript:void(0);" onclick="submitSelectedNotes()">
                                                    <i class="bi bi-file-earmark-check"></i>
                                                </a>
                                            @endif

                                            @can(['delete-payment-note'])
                                                | <form action="{{ route('backend.payment-note.destroy', $note->id) }}"
                                                    method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-none"
                                                        onclick="return confirm('Are you sure?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                            @if (in_array($note->status, ['A', 'B', 'PA']) && $userRoles->contains('PN User'))
                                                | <button class="btn btn-none open-update-modal"
                                                    data-id="{{ $note->id }}">
                                                    <i class="bi bi-calendar2-plus"></i>
                                                </button>
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
        </div>
    </section>

    <!-- Update UTR and Date Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="updateForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header d-flex justify-content-between">
                        <h5 class="modal-title">Update Date & UTR No</h5>
                        <button type="button" class="close btn btn-danger" data-bs-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="note_id" id="modal_note_id">

                        <div class="form-group">
                            <label for="utr_no">UTR No</label>
                            <input type="text" class="form-control" name="utr_no" id="utr_no" required>
                        </div>

                        <div class="form-group">
                            <label for="utr_date">Date</label>
                            <input type="date" class="form-control" name="utr_date" id="utr_date" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="fullscreenLoader"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(255, 255, 255, 0.8); z-index: 9999;
            justify-content: center; align-items: center;">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>


@endsection
@push('script')
    <script>
        $(document).on('click', '.open-update-modal', function() {
            let id = $(this).data('id');
            $('#modal_note_id').val(id);
            $('#utr_no').val('');
            $('#utr_date').val('');
            $('#updateModal').modal('show');
        });

        $('#updateForm').submit(function(e) {
            e.preventDefault();

            $('#fullscreenLoader').css('display', 'flex');
            $.ajax({
                url: "{{ route('backend.payment-note.updateUtr') }}",
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {

                        $('#fullscreenLoader').hide();
                        $('#updateModal').modal('hide');
                        // Use console.log or custom modal instead of alert
                        console.log('Updated successfully!');
                        location.reload();
                    } else {
                        $('#fullscreenLoader').hide();
                        $('#updateModal').modal('hide');
                        // Use console.log or custom modal instead of alert
                        console.log('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    $('#fullscreenLoader').hide();
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        // Better error display logic needed
                        $.each(errors, function(field, messages) {
                            let input = $('[name=' + field + ']');
                            input.after('<div class="text-danger">' + messages[0] + '</div>');
                        });
                    } else {
                        console.log('Something went wrong!');
                    }
                }
            });
        });


        function submitSelectedNotes() {
            const selectedCheckboxes = document.querySelectorAll('.note-checkbox:checked');
            const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

            if (selectedIds.length === 0) {
                // Use a custom message box instead of alert
                console.log('Please select at least one note.');
                return;
            }

            document.getElementById('selectedNoteIds').value = selectedIds.join(',');
            document.getElementById('bulkBankLetterForm').submit();
        }
    </script>
@endpush
