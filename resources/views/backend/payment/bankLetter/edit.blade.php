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
                        <form action="{{ route('backend.bank-letter.update', $step->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label class="block text-sm font-medium">Minimum Amount (Lakhs)</label>
                                <input type="number" name="min_amount" value="{{ $step->min_amount }}"
                                    class="form-control">
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium">Maximum Amount (Lakhs)</label>
                                <input type="number" name="max_amount" value="{{ $step->max_amount }}"
                                    class="form-control">
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium">Approvers</label>
                                <div id="approvers-container">
                                    @foreach ($step->approvers as $approver)
                                        <div class="flex gap-2 mt-1 approver-row">
                                            <select name="old_approvers[{{ $approver->id }}][reviewer_id]"
                                                class="form-input">
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        {{ $user->id == $approver->reviewer_id ? 'selected' : '' }}>
                                                        {{ $user->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <select name="old_approvers[{{ $approver->id }}][approver_level]"
                                                class="form-input">
                                                <option value="1"
                                                    {{ $approver->approver_level == 1 ? 'selected' : '' }}>Approver 1
                                                </option>
                                                <option value="2"
                                                    {{ $approver->approver_level == 2 ? 'selected' : '' }}>Approver 2
                                                </option>
                                            </select>
                                            <button type="button"
                                                class="remove-approver btn btn-danger remove-old-approver">Remove</button>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" id="add-approver" class="btn btn-primary mt-2">Add
                                    Approver</button>
                            </div>
                            <input type="hidden" name="removed_approvers" id="removed_approvers">

                            <button type="submit" class="btn btn-primary">Update Rule</button>
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
    <script>
        document.getElementById('add-approver').addEventListener('click', function() {
            let container = document.getElementById('approvers-container');
            let index = container.children.length;
            if (index < 15) { // Max 3 Approvers Allowed
                let newRow = `<div class="flex gap-2 approver-row mt-1">
                    <select name="approvers[${index}][reviewer_id]" class="form-input">
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <select name="approvers[${index}][approver_level]" class="form-input">
                        <option value="1">Approver 1</option>
                        <option value="2">Approver 2</option>
                    </select>
                    <button type="button" class="remove-approver btn btn-danger">Remove</button>
                </div>`;
                container.insertAdjacentHTML('beforeend', newRow);
            } else {
                alert("You cannot add more than 15 approvers.");
            }
        });


        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-approver')) {
                event.target.parentElement.remove();
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            let removedApprovers = [];

            $(document).on('click', '.remove-old-approver', function() {
                const row = $(this).closest('.approver-row');

                // Get the approver ID from the input name attribute
                const inputName = row.find('select').first().attr('name');
                const matches = inputName.match(/\[([0-9]+)\]/);
                if (matches && matches[1]) {
                    const approverId = matches[1];
                    removedApprovers.push(approverId);

                    // Update the hidden input field
                    $('#removed_approvers').val(removedApprovers.join(','));
                }

            });
        });
    </script>

    <script>
        $('.select2').select2();
    </script>
@endpush
