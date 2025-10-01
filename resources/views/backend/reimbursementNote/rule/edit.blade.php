@extends('backend.layouts.app')
@section('content')
    <div class="pagetitle">
        <h1>Approval Rule</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item active">Approval Rule</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Edit Approval Rule</h5>
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
                        <!-- Vertical Form -->
                        <form action="{{ route('backend.payment-note-approval.update', $step->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            {{-- <div>
                                <label>Step Number:</label>
                                <input type="number" name="step" class="form-control"
                                    value="{{ old('step', $step->step) }}" required>
                            </div>

                            <div>
                                <label>Minimum Amount:</label>
                                <input type="text" name="min_amount" class="form-control"
                                    value="{{ old('min_amount', $step->min_amount) }}" required>
                            </div>

                            <div>
                                <label>Maximum Amount:</label>
                                <input type="text" name="max_amount" class="form-control"
                                    value="{{ old('max_amount', $step->max_amount) }}">
                            </div> --}}

                            <div>
                                <label>Approvers:</label>
                                <select name="reviewer_ids[]" multiple class="form-select form-control">
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ in_array($user->id, $step->reviewers->pluck('id')->toArray()) ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>

                        <!-- Vertical Form -->
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('script')
    {{-- // --}}
@endpush
