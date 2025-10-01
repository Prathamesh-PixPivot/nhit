@extends('backend.layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>Approval Rules</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item active">Approval Rules</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="row">
            @can(['create-rule'])
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Create Approval Rules</h5>
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

                            {{-- <form class="row g-3" action="{{ route('backend.approval.store') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

                            <div class="col-6">
                                <label for="two" class="form-label">Name</label>
                                <input type="text" class="form-control" name="name" id="two" required
                                    value="{{ old('name') }}">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="vendor_id" class="form-label">Project Name</label>
                                <select class="form-select form-control" id="vendor_id" name="vendor_id" required>
                                    <option value="">Select Project</option>
                                    @foreach ($filteredItems as $item)
                                        <option value="{{ $item->id }}">{{ $item->project }}
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
                                            {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}</option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="five" class="form-label">Select Approver 1</label>
                                <select class="form-select form-control" id="five" name="approvers[]" required>
                                    <option value="">Select User</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('approvers[]') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('approvers[]')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="five" class="form-label">HR & Admin (Only if User Department is
                                    Operations)</label>
                                <select class="form-select form-control" id="five" name="approvers[]">
                                    <option value="">Select User</option>

                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('approvers[]') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('approvers[]')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="five" class="form-label">QS (Only if User Department is Selected
                                    Operations)</label>
                                <select class="form-select form-control" id="five" name="approvers[]">
                                    <option value="">Select User</option>

                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('approvers[]') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('approvers[]')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="five" class="form-label">Approver 2 (Only if Invoice Value 1,00,000 and
                                    above)</label>
                                <select class="form-select form-control" id="five" name="approvers[]">
                                    <option value="">Select User</option>

                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('approvers[]') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('approvers[]')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="five" class="form-label">Concurrent Auditor (Only if Invoice Value 25,00,000
                                    and above)</label>
                                <select class="form-select form-control" id="five" name="approvers[]">
                                    <option value="">Select User</option>

                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('approvers[]') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('approvers[]')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="five" class="form-label">Approver 4 (Only for Invoice Value 25,00,000 and
                                    above)</label>
                                <select class="form-select form-control" id="five" name="approvers[]">
                                    <option value="">Select User</option>

                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('approvers[]') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('approvers[]')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="five" class="form-label">Approver 5 (Only if Invoice Value 50,00,000 and
                                    above)</label>
                                <select class="form-select form-control" id="five" name="approvers[]">
                                    <option value="">Select User</option>

                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('approvers[]') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('approvers[]')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form> --}}
                            <form class="row g-3" action="{{ route('backend.approval.store') }}" method="post"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                                <div class="col-6">
                                    <label for="two" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" id="two" required
                                        value="{{ old('name') }}">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <!-- Project Selection -->
                                <div class="col-6">
                                    <label for="vendor_id" class="form-label">Project Name</label>
                                    <select class="form-select form-control" id="vendor_id" name="vendor_id" required>
                                        <option value="">Select Project</option>
                                        @foreach ($filteredItems as $item)
                                            <option value="{{ $item->id }}">{{ $item->project }}</option>
                                        @endforeach
                                    </select>
                                    @error('vendor_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Department Selection -->
                                <div class="col-6" id="department_div">
                                    <label for="department_id" class="form-label">User Department</label>
                                    <select class="form-select form-control" id="department_id" name="department_id">
                                        <option value="">Select Department</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Approver 1 (Always Visible After Department Selection) -->
                                <div class="col-6 approver-section" id="approver_1_div">
                                    <div class="row">
                                        <div class="col-8">
                                            <label for="approver_1" class="form-label">Select Approver 1</label>
                                            <select class="form-select form-control " id="approver_1" name="approvers[]">
                                                <option value="">Select User</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <label for="approver_2" class="form-label">Amount</label>
                                            <input type="number" class="form-control" name="amounts[]">

                                        </div>
                                    </div>
                                </div>

                                <!-- HR & QS (Visible Only If Operations is Selected) -->
                                <div class="col-6 approver-section" id="hr_admin_div">
                                    <div class="row">
                                        <div class="col-8">
                                            <label for="hr_admin" class="form-label">HR & Admin (Only for
                                                Operations)</label>
                                            <select class="form-select form-control" id="hr_admin" name="approvers[]">
                                                <option value="">Select User</option>
                                                @foreach ($adminHrUsers as $user)
                                                    <option value="{{ $user->id }}">
                                                        {{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <label for="approver_2" class="form-label">Amount</label>
                                            <input type="number" class="form-control" name="amounts[]">

                                        </div>
                                    </div>
                                </div>

                                <div class="col-6 approver-section" id="qs_div">
                                    <div class="row">
                                        <div class="col-8">
                                            <label for="qs" class="form-label">QS (Only for Operations)</label>
                                            <select class="form-select form-control" id="qs" name="approvers[]">
                                                <option value="">Select User</option>
                                                @foreach ($qsUsers as $user)
                                                    <option value="{{ $user->id }}">
                                                        {{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <label for="approver_2" class="form-label">Amount</label>
                                            <input type="number" class="form-control" name="amounts[]">

                                        </div>
                                    </div>
                                </div>

                                <!-- Approver 2 (Only If Invoice > 1,00,000) -->
                                <div class="col-6 approver-section" id="approver_2_div">
                                    <div class="row">
                                        <div class="col-8">
                                            <label for="approver_2" class="form-label">Approver 2 (Only if Invoice
                                                Value >
                                                1,00,000)</label>
                                            <select class="form-select form-control" id="approver_2" name="approvers[]">
                                                <option value="">Select User</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">
                                                        {{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <label for="approver_2" class="form-label">Amount</label>
                                            <input type="number" class="form-control" name="amounts[]">

                                        </div>
                                    </div>
                                </div>

                                <!-- Approver 3, 4, 5 (Only if Invoice Value 25L, 50L, etc.) -->
                                <div class="col-6 approver-section" id="approver_3_div">
                                    <div class="row">
                                        <div class="col-8">
                                            <label for="approver_3" class="form-label">Approver 3 (Only if
                                                Invoice
                                                Value >
                                                2,50,000)</label>
                                            <select class="form-select form-control" id="approver_3" name="approvers[]">
                                                <option value="">Select User</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">
                                                        {{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <label for="approver_2" class="form-label">Amount</label>
                                            <input type="number" class="form-control" name="amounts[]">

                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 approver-section" id="concurrent_auditor_div">
                                    <div class="row">
                                        <div class="col-8">
                                            <label for="concurrent_auditor" class="form-label">Concurrent Auditor (Only if
                                                Invoice
                                                Value >
                                                25,00,000)</label>
                                            <select class="form-select form-control" id="concurrent_auditor"
                                                name="approvers[]">
                                                <option value="">Select User</option>
                                                @foreach ($auditorUsers as $user)
                                                    <option value="{{ $user->id }}">
                                                        {{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <label for="approver_2" class="form-label">Amount</label>
                                            <input type="number" class="form-control" name="amounts[]">

                                        </div>
                                    </div>
                                </div>

                                <div class="col-6 approver-section" id="approver_4_div">
                                    <div class="row">
                                        <div class="col-8">
                                            <label for="approver_4" class="form-label">Approver 4 (Only if Invoice
                                                Value >
                                                25,00,000)</label>
                                            <select class="form-select form-control" id="approver_4" name="approvers[]">
                                                <option value="">Select User</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">
                                                        {{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <label for="approver_2" class="form-label">Amount</label>
                                            <input type="number" class="form-control" name="amounts[]">

                                        </div>
                                    </div>
                                </div>

                                <div class="col-6 approver-section" id="approver_5_div">
                                    <div class="row">
                                        <div class="col-8">
                                            <label for="approver_5" class="form-label">Approver 5 (Only if Invoice
                                                Value >
                                                50,00,000)</label>
                                            <select class="form-select form-control" id="approver_5" name="approvers[]">
                                                <option value="">Select User</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">
                                                        {{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <label for="approver_2" class="form-label">Amount</label>
                                            <input type="number" class="form-control" name="amounts[]">

                                        </div>
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
        @endcan


        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">All Approval Rules</h5>
                        <!-- Table with stripped rows -->
                        <table class="table datatable table-responsive">
                            <thead>
                                <tr>
                                    <th class="border p-3 text-left">Name</th>
                                    <th class="border p-3 text-left">Project</th>
                                    <th class="border p-3 text-left">Department</th>
                                    <th class="border p-3 text-left">Steps</th>
                                    <th class="border p-3 text-left">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($approvalFlows as $flow)
                                    <tr class="">
                                        <td class="border p-3">{{ $flow->name ?? 'N/A' }}</td>
                                        <td class="border p-3">{{ $flow->vendor->project ?? 'N/A' }}</td>
                                        <td class="border p-3">{{ $flow->department->name ?? 'N/A' }}</td>
                                        <td class="border p-3">
                                            <ul class="list-disc pl-4">
                                                @foreach ($flow->approvalSteps as $step)
                                                    <li>Step {{ $step->step }} -
                                                        {{ $step->nextOnApprove->name }} --
                                                        ({{ \App\Helpers\Helper::formatIndianNumber($step->amount) }})
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td class="border p-3">
                                            @can(['edit-rule'])
                                                <a href="{{ route('backend.approval.edit', $flow->id) }}">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a> |
                                            @endcan

                                            <a href="{{ route('backend.approval.show', $flow->id) }}">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            {{-- @can(['delete-rule'])
                                                | <form action="{{ route('backend.approval.destroy', $flow->id) }}"
                                                    method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm"
                                                        onclick="return confirm('Are you sure?')">
                                                        <i class="bi bi-trash"></i>

                                                    </button>
                                                </form>
                                            @endcan --}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!-- End Table with stripped rows -->
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        $('.select2').select2();

        // document.addEventListener("DOMContentLoaded", function() {
        //     let projectSelect = document.getElementById("vendor_id");
        //     let departmentDiv = document.getElementById("department_div");
        //     let departmentSelect = document.getElementById("department_id");
        //     let hrAdminDiv = document.getElementById("hr_admin_div");
        //     let qsDiv = document.getElementById("qs_div");

        //     let approverDivs = [
        //         document.getElementById("approver_1_div"),
        //         document.getElementById("approver_2_div"),
        //         document.getElementById("approver_3_div"),
        //         document.getElementById("concurrent_auditor_div"),
        //         document.getElementById("approver_4_div"),
        //         document.getElementById("approver_5_div"),
        //     ];

        //     let approverSelects = [
        //         document.getElementById("approver_1"),

        //         document.getElementById("approver_2"),
        //         document.getElementById("approver_3"),
        //         document.getElementById("concurrent_auditor"),
        //         document.getElementById("approver_4"),
        //         document.getElementById("approver_5"),
        //     ];

        //     // Project Selection Enables Department
        //     projectSelect.addEventListener("change", function() {
        //         departmentDiv.style.display = this.value ? "block" : "none";
        //     });

        //     departmentSelect.addEventListener("change", function() {
        //         let selectedDepartment = this.value;
        //         approverDivs.forEach(div => div.style.display = "none");
        //         if (selectedDepartment == "1") {
        //             hrAdminDiv.style.display = "block";
        //             qsDiv.style.display = "block";
        //         } else {
        //             hrAdminDiv.style.display = "none";
        //             qsDiv.style.display = "none";
        //         }
        //         if (selectedDepartment) {
        //             approverDivs[0].style.display = "block";
        //         }
        //     });

        //     approverSelects.forEach((select, index) => {
        //         select.addEventListener("change", function() {
        //             if (this.value) {
        //                 let nextIndex = index + 1;
        //                 if (nextIndex < approverDivs.length) {
        //                     approverDivs[nextIndex].style.display = "block";
        //                 }
        //             }
        //         });
        //     });
        // });
    </script>
@endpush
