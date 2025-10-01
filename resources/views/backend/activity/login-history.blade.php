@extends('backend.layouts.app')
@section('content')
    

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                {{--<div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Payments</h5>
                         <div class="row mb-3">
                            <div class="form-group">
                                <label><strong>Sl No. :</strong></label>
                                <select id='status' class="form-control" style="width: 200px">
                                    <option value="">--Select Sl No--</option>
                                    @foreach ($sl_no_filter as $sl_no)
                                        <option value="{{ $sl_no->sl_no }}">{{ $sl_no->sl_no }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="form-group">
                                <label><strong>Sl No. :</strong></label>
                                <select id='status' class="form-control" style="width: 200px">
                                    <option value="">--PDF Generate Type--</option>
                                    <option value="sbi">SBI</option>
                                    <option value="mf-sbi">MF SBI</option>
                                    <option value="mf-axis">MF AXIS</option>
                                    <option value="mf-kotak">MF KOTAK</option>
                                </select>
                            </div>
                        </div> 
                        <div class="row mb-3">
                            <div class="col-sm-10">
                                <button type="submit" class="btn btn-primary">Generate Pdf</button>
                            </div>
                        </div>
                    </div>
                </div>--}}

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">USer Login History</h5>
                        <table class="table table-bordered login_history_datatable" id="login_history_datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Login At</th>
                                    <th>Login IP</th>
                                    <th>User Agent</th>
                                    <th>Created At</th>
                                    {{-- <th width="100px">Action</th> --}}
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </section>
@endsection
@push('script')
    <script type="text/javascript">
        $(function() {
            var table = $('#login_history_datatable').DataTable({
                ordering: false,
                processing: true,
                serverSide: true,
                ajax: "{{ route('backend.activity.loginHistory') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'last_login_at',
                        name: 'last_login_at'
                    },
                    {
                        data: 'last_login_ip',
                        name: 'last_login_ip'
                    },
                    {
                        data: 'user_agent',
                        name: 'user_agent'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    /* {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }, */
                ]
            });
        });
    </script>
@endpush
