@extends('backend.layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>Reimbursement Note</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item active">Reimbursement Note</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Select User to Create Reimbursement Note</h4>
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
                        <div class="row">
                            <div class="col-lg-4">
                                <form method="POST" action="{{ route('backend.reimbursement-note.create.user.select') }}">
                                    @csrf
                                    <label for="user_id">Select User</label>
                                    <select name="user_id" class="form-control select2" required>
                                        <option value="">-- Select User --</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->emp_id }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-primary mt-3">Proceed</button>
                                </form>
                                <!-- Vertical Form -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection

@push('script')
    <script>
        $('.select2').select2();
    </script>
@endpush
