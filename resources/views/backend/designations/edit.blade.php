@extends('backend.layouts.app')
@section('content')
    <div class="pagetitle">
        <h1>Designation</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item active">Designation</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Edit Designation</h5>
                        <!-- Vertical Form -->
                        <form action="{{ route('backend.designations.update', $designation->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    value="{{ $designation->name }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control" required>{{ $designation->description }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary  button-with-spinner">
                                <span>Submit</span>
                                <div class="spinner-border spinner-border-sm" role="status"></div>
                            </button>
                        </form>
                        <!-- Vertical Form -->
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
