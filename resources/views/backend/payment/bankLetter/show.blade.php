@extends('backend.layouts.app')
@section('content')
    <div class="pagetitle">
        <h1>View Approval Rules</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item active">View Approval Rules</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Approval Rules</h5>
                        <div class="bg-white shadow-md rounded-lg p-6">
                            <h2 class="text-lg font-semibold mb-4">Approval Flow Details</h2>



                            <div class="mb-4">
                                <label class="block text-sm font-medium">Minimum Amount (Lakhs)</label>
                                <input type="number" name="min_amount" value="{{ $step->min_amount }}" class="form-control"
                                    readonly>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium">Maximum Amount (Lakhs)</label>
                                <input type="number" name="max_amount" value="{{ $step->max_amount }}" class="form-control"
                                    readonly>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium">Approvers</label>
                                <div id="approvers-container">
                                    @foreach ($step->approvers as $approver)
                                        <div class="flex gap-2 mt-1 approver-row">
                                            <select name="approvers[{{ $loop->index }}][reviewer_id]" class="form-input">
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        {{ $user->id == $approver->reviewer_id ? 'selected' : '' }}>
                                                        {{ $user->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <select name="approvers[{{ $loop->index }}][approver_level]"
                                                class="form-input">
                                                <option value="1"
                                                    {{ $approver->approver_level == 1 ? 'selected' : '' }}>Approver 1
                                                </option>
                                                <option value="2"
                                                    {{ $approver->approver_level == 2 ? 'selected' : '' }}>Approver 2
                                                </option>
                                            </select>
                                        </div>
                                    @endforeach
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('script')
@endpush
