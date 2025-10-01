@extends('backend.layouts.app')
@section('content')
    <div class="pagetitle">
        <h1>Only Pending Payment Note</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                <li class="breadcrumb-item active"> Only Pending Payment Notes</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Only Pending Payment Notes</h5>
                        <form id="bulkBankLetterForm" action="{{ route('backend.payments.createBankLetter') }}"
                            method="POST">
                            @csrf
                            <input type="hidden" name="note_ids" id="selectedNoteIds"> {{-- This will contain comma-separated IDs --}}
                        </form>
                        <!-- Table with server-side DataTables -->
                        <table class="table table-bordered" id="payment_notes_dt">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Project Name</th>
                                    <th>Vendor/Employee</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <!-- End Table with stripped rows -->

                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
@push('script')
    <script type="text/javascript">
        function submitSelectedNotes() {
            const selectedCheckboxes = document.querySelectorAll('.note-checkbox:checked');
            const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);
            if (selectedIds.length === 0) {
                alert('Please select at least one note.');
                return;
            }
            document.getElementById('selectedNoteIds').value = selectedIds.join(',');
            document.getElementById('bulkBankLetterForm').submit();
        }

        $(function () {
            $('#payment_notes_dt').DataTable({
                processing: true,
                serverSide: true,
                deferRender: true,
                pageLength: 25,
                ajax: {
                    url: '{{ route('backend.dashboard.api.paymentNotes') }}'
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'project_name', name: 'project_name' },
                    { data: 'vendor_name', name: 'vendor_name' },
                    { data: 'amount', name: 'amount' },
                    { data: 'date', name: 'created_at' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });
    </script>
@endpush
