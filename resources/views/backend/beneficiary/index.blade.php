@extends('backend.layouts.app')
@section('content')
    {{-- <div class="pagetitle">
        <h1>Blank Page</h1>
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
                {{-- <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Genrate PDF</h5>
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
                </div> --}}

                <div class="card">
                    <div class="card-body">
                        @can('create-role')
                            <a href="{{ route('backend.roles.create') }}" class="btn btn-outline-success btn-sm my-2"><i class="bi bi-plus-circle"></i> Add New</a>
                        @endcan
                        <h5 class="card-title">Manege Role</h5>
                        <table class="table table-bordered users_datatable" id="users_datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Roles</th>
                                    <th width="100px">Action</th>
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
            var table = $('#users_datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('backend.users.index') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'username',
                        name: 'username'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'roles',
                        name: 'roles.'
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
