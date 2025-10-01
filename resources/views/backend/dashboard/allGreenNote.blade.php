@extends('backend.layouts.app')
@section('content')
    <div class="pagetitle">
        <h1>Only Pending Green Note</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                <li class="breadcrumb-item active">Green Notes</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        @can(['create-note'])
                            <a href="{{ route('backend.note.create') }}" class="btn btn-outline-success btn-sm my-2"><i
                                    class="bi bi-plus-circle"></i> Add New</a>
                        @endcan


                        <h5 class="card-title">Only Pending Green Notes</h5>

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
                                        <td>{{ $note->vendor->project ?? '-' }}</td>
                                        <td>{{ $note->supplier->vendor_name ?? '-' }}</td>
                                        <td>{{ \App\Helpers\Helper::formatIndianNumber($note->invoice_value) ?? '-' }}</td>
                                        <td>{{ $note->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?? '-' }}
                                        </td>
                                        <td>
                                            @php
                                                $statusLabels = [
                                                    'D' => '<span class="badge bg-dark">Draft</span>',
                                                    'PMPL' => '<span class="badge bg-info">Sent for PMC</span>',
                                                    'S' => '<span class="badge bg-secondary">Sent for Approval</span>',
                                                    'P' => '<span class="badge bg-warning">Pending</span>',
                                                    'A' => '<span class="badge bg-success">Approved</span>',
                                                    'R' => '<span class="badge bg-danger">Rejected</span>',
                                                    'B' => '<span class="badge bg-black">RTGS/NEFT Created</span>',
                                                    'PNA' =>
                                                        '<span class="badge bg-info">Payment Note Approved </span>',
                                                    'PA' => '<span class="badge bg-black">Payment Approved </span>',
                                                ];
                                            @endphp
                                            @php
                                                // $latestPaymentNote = $note->paymentNote->last();
                                                $latestPaymentNote = optional($note->paymentNotes)->last();
                                                // dd($note->paymentNotes);
                                                $latestLog =
                                                    $latestPaymentNote &&
                                                    $latestPaymentNote->paymentApprovalLogs->isNotEmpty()
                                                        ? $latestPaymentNote->paymentApprovalLogs->last()
                                                        : null;

                                            @endphp
                                            @if (in_array($note->status, ['B', 'PNA', 'PA']))
                                                {!! $statusLabels[$note->status] ?? '' !!}
                                            @elseif ($latestPaymentNote && $latestLog?->status != 'R')
                                                <span class="badge bg-warning">Payment Note Processed </span>
                                            @else
                                                {!! $statusLabels[$note->status] ?? '-' !!}
                                            @endif

                                            {{-- @if ($note->status != 'A') --}}
                                            @if (!in_array($note->status, ['A', 'B', 'PNA', 'PA']))
                                                @if ($note->approvalLogs->last()?->approvalStep->nextOnApprove && $note->approvalLogs->last()?->status == 'A')
                                                    <p class="mb-1 text-xs">Next Approver:
                                                        {{ $note->approvalLogs->last()->approvalStep->nextOnApprove->name }}
                                                    </p>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $userRoles = auth()->user()->getRoleNames();
                                                $lastStep = $note->approvalLogs->last();
                                                $showButton = false;

                                                if (
                                                    $lastStep &&
                                                    $lastStep->approvalStep->next_on_approve == auth()->user()->id &&
                                                    $lastStep->status == 'A' &&
                                                    $lastStep->reviewer_id !== auth()->user()->id
                                                ) {
                                                    $showButton = true;
                                                }
                                                // dd($lastStep->reviewer_id == auth()->user()->id);
                                            @endphp
                                            @can(['edit-note'])
                                                @if (
                                                    (auth()->check() && (($note->status == 'D' || $note->status == 'PMPL') && auth()->user()->id == $note->user_id)) ||
                                                        $userRoles->contains('Hr And Admin') ||
                                                        $userRoles->contains('Qs'))
                                                    @if ($showButton)
                                                        <a href="{{ route('backend.note.edit', $note->id) }}">
                                                            <i class="bi bi-pencil-square"></i>

                                                        </a> |
                                                    @endif
                                                    @if ((!$lastStep && $note->status == 'D') || ($lastStep && $lastStep->status == 'PMPL'))
                                                        <a href="{{ route('backend.note.edit', $note->id) }}">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </a> |
                                                    @endif
                                                @endif
                                            @endcan
                                            <a href="{{ route('backend.note.show', $note->id) }}"><i
                                                    class="bi bi-eye"></i></a>
                                            @can(['create-payment-note'])
                                                {{-- @if ($note->status == 'A' && !$note->paymentNote) --}}
                                                @if ($note->status == 'A')
                                                    @if (!$latestPaymentNote || ($latestLog && $latestLog->status == 'R'))
                                                        | <a href="{{ route('backend.note.create.payment.note', $note->id) }}"><i
                                                                class="bi bi-file-earmark-ppt"></i></a>
                                                    @endif
                                                @endif
                                            @endcan

                                            @can(['delete-note'])
                                                |
                                                <form action="{{ route('backend.note.destroy', $note->id) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-none"
                                                        onclick="return confirm('Are you sure?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
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
@endpush
