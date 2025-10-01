@extends('backend.layouts.app')
@section('content')
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">


                        <h5 class="card-title">Bank RTGS/NEFT</h5>
                        <table class="table table-bordered payment_datatable_sl" id="payment_datatable_sl">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>SL No.</th>
                                    <th>Template.</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th width="100px">Shortcut</th>
                                    <th width="100px">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <input type="hidden" id="hidden_slno" value="{{ implode(',', $notes->pluck('sl_no')->toArray()) }}">
        </div>
    </section>
@endsection
@push('script')
    <script type="text/javascript">
        $(function() {
            var table = $('#payment_datatable_sl').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('backend.dashboard.user.bank.letter.notes') }}",
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: $('#hidden_slno').val()
                    }
                },

                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'sl_no',
                        name: 'sl_no'
                    },
                    {
                        data: 'template_type',
                        name: 'template_type'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'shortcut_name',
                        name: 'shortcut_name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });
    </script>
@endpush
