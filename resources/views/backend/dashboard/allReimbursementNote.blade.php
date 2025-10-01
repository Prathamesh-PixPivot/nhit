@extends('backend.layouts.app')
@section('content')
    <div class="pagetitle">
        <h1>Only Pending Reimbursement Note</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                <li class="breadcrumb-item active">Reimbursement Notes</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Only Pending Reimbursement Notes</h5>
                        <!-- Table with server-side DataTables -->
                        <table class="table table-bordered" id="reimbursements_dt">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Project Name</th>
                                    <th>Employee Name</th>
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
        $(function () {
            $('#reimbursements_dt').DataTable({
                processing: true,
                serverSide: true,
                deferRender: true,
                pageLength: 25,
                ajax: {
                    url: '{{ route('backend.dashboard.api.reimbursements') }}'
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'project_name', name: 'project_name' },
                    { data: 'employee_name', name: 'employee_name' },
                    { data: 'amount', name: 'amount' },
                    { data: 'date', name: 'created_at' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });
    </script>
@endpush
