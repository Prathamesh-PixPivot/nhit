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
                        <h5 class="card-title">Edit Approval Rule ({{ $approvalFlow->name }})</h5>
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
                        <form class="row g-3" action="{{ route('backend.approval.update', $approvalFlow->id) }}"
                            method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="col-6">
                                <label for="two" class="form-label">Name</label>
                                <input type="text" class="form-control" name="name" id="two" required
                                    value="{{ old('name', $approvalFlow->name) }}">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="vendor_id" class="form-label">Project Name</label>
                                <select class="form-select form-control" id="vendor_id" name="vendor_id" required>
                                    <option value="">Select Project</option>
                                    @foreach ($filteredItems as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('vendor_id', $approvalFlow->vendor_id) == $item->id ? 'selected' : '' }}>
                                            {{ $item->project }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('vendor_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="five" class="form-label">User Department</label>
                                <select class="form-select form-control" id="five" name="department_id" required>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ old('department_id', $approvalFlow->department_id) == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}</option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            @foreach ($approvalFlow->approvalSteps as $i => $step)
                                <div class="col-6">
                                    <div class="row">
                                        <div class="col-8">

                                            <label for="five" class="form-label">Select Next on Approver
                                                {{ $i + 1 }}</label>
                                            <select class="form-select form-control select2" id="five"
                                                name="approvers[]">
                                                <option value="">Select User</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        {{ old('approvers[]', $user->id) == $step->next_on_approve ? 'selected' : '' }}>
                                                        {{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('approvers[]')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-4">
                                            <label for="approver_2" class="form-label">Amount</label>
                                            <input type="number" class="form-control" name="amounts[]"
                                                value="{{ $step->amount }}">
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <div id="approver-container" class="col-6">
                                <div class="approver-row mb-2 row">
                                    <div class="col-6">
                                        <select name="approvers[]" class="form-select me-2 select2">
                                            <option value="">Select User</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <input type="number" name="amounts[]" class="form-control me-2"
                                            placeholder="Amount">
                                    </div>

                                    <button type="button" class="btn btn-success btn-sm add-btn me-1 col-1">+</button>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
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
        $(document).ready(function() {
            $(document).on('click', '.add-btn', function() {
                let newRow = `
                   <div class="col-12">
                <div class="approver-row mb-2 row">
                    <div class="col-6">
                    <select name="approvers[]" class="form-select me-2 select2">
                        <option value="">Select User</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                     </div>
                    <div class="col-4">
                    <input type="number" name="amounts[]" class="form-control me-2" placeholder="Amount">
                     </div>
                    <button type="button" class="btn btn-danger btn-sm remove-btn col-1">×</button>
                </div>
                </div>
            `;
                $('#approver-container').append(newRow);
                $('#approver-container .select2').last().select2({
                    width: 'resolve' // ensures proper width
                });
            });

            $(document).on('click', '.remove-btn', function() {
                $(this).closest('.approver-row').remove();
            });
        });
    </script>
    <script>
        $('.select2').select2();
    </script>
@endpush
