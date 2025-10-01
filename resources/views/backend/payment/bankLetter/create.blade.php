@extends('backend.layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>Bank Letter Approval Rules</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item active">Bank Letter Approval Rules</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        {{-- <div class="row">
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

                        <form class="row g-3" action="{{ route('backend.bank-letter.store') }}" method="post">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">


                            <div class="col-3 mb-4">
                                <label for="min_amount" class="block text-gray-700">Minimum Amount</label>
                                <select class="form-select form-control" id="amount" name="min_amount">
                                    <option value="">Select Amount</option>
                                    <option value="0">0</option>
                                    <option value="5000000">5,000,000</option>
                                </select>
                            </div>

                            <div class="col-3 mb-4">
                                <label for="max_amount" class="block text-gray-700">Maximum Amount (Leave empty for no
                                    limit)</label>
                                <select class="form-select form-control" id="amount" name="max_amount">
                                    <option value="">Select Amount</option>
                                    <option value="5000000">5,000,000</option>
                                    <option value="">Above</option>
                                </select>
                            </div>

                            <!-- Approvers -->
                            <div class="col-6 mb-4">
                                <h3 class="text-lg font-semibold">Approvers</h3>
                                <div id="approver-container">
                                    <div class="approver-row flex gap-2 mb-2">
                                        <select name="approvers[0][user_id]" class="p-2 border rounded-lg w-1/2">
                                            <option value="">Select User</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                        <select name="approvers[0][approver_level]" class="p-2 border rounded-lg w-1/2">
                                            <option value="1">Approver 1</option>
                                            <option value="2">Approver 2</option>
                                        </select>
                                    </div>
                                </div>

                                <button type="button" id="add-approver" class="mt-2 btn btn-primary">+ Add
                                    Approver</button>
                            </div>



                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                        <!-- Vertical Form -->
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">All Bank Letter Approval Rules</h5>
                        <!-- Table with stripped rows -->
                        <table class="table datatable table-responsive">
                            <thead>
                                <tr>
                                    <th class="border p-3 text-left">Approver Level</th>
                                    <th class="border p-3 text-left">Users</th>
                                    <th class="border p-3 text-left">Payment Range</th>
                                    <th class="border p-3 text-left">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($approvalSteps as $rule)
                                    @foreach ($rule->approvers->groupBy('approver_level') as $level => $approvers)
                                        <tr>
                                            <td class="border p-3">Approver {{ $level }}
                                                @if ($rule->min_amount == 0 && $rule->max_amount == 0)
                                                    (Internal)
                                                @else
                                                    (External)
                                                @endif
                                            </td>
                                            <td class="border p-3">
                                                {{ implode(', ', $approvers->pluck('user.name')->toArray()) }}
                                            </td>
                                            <td class="border p-3">

                                                @if ($rule->min_amount == 0 && $rule->max_amount == 0)
                                                    NO LIMIT
                                                @else
                                                    @if ($rule->max_amount == null)
                                                        {{ number_format($rule->min_amount) }} And Above
                                                    @else
                                                        {{ number_format($rule->min_amount) }} -
                                                        {{ number_format($rule->max_amount) }} Above
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="border p-3">
                                                @can(['bank-letter-edit-rule'])
                                                    <a href="{{ route('backend.bank-letter.edit', $rule->id) }}">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a> |
                                                @endcan
                                                <a href="{{ route('backend.bank-letter.show', $rule->id) }}">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
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
        document.addEventListener("DOMContentLoaded", function() {
            let approverIndex = 1;

            document.getElementById("add-approver").addEventListener("click", function() {
                let container = document.getElementById("approver-container");
                let newRow = document.createElement("div");
                newRow.classList.add("approver-row", "flex", "gap-2", "mb-2");
                newRow.innerHTML = `
                <select name="approvers[${approverIndex}][user_id]" class="p-2 border rounded-lg w-1/2">
                    <option value="">Select User</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                <select name="approvers[${approverIndex}][approver_level]" class="p-2 border rounded-lg w-1/2">
                    <option value="1">Approver 1</option>
                    <option value="2">Approver 2</option>
                </select>
                <button type="button" class="remove-approver btn btn-danger">X</button>
            `;
                container.appendChild(newRow);
                approverIndex++;
            });

            document.getElementById("approver-container").addEventListener("click", function(e) {
                if (e.target.classList.contains("remove-approver")) {
                    e.target.parentElement.remove();
                }
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            let approverDivs = [
                document.getElementById("approver_1_div"),
                document.getElementById("approver_2_div"),
                document.getElementById("approver_3_div"),
            ];

            let approverSelects = [
                document.getElementById("approver_1"),
                document.getElementById("approver_2"),
                document.getElementById("approver_3"),
            ];

            approverSelects.forEach((select, index) => {
                select.addEventListener("change", function() {
                    if (this.value) {
                        let nextIndex = index + 1;
                        if (nextIndex < approverDivs.length) {
                            approverDivs[nextIndex].style.display = "block";
                        }
                    }
                });
            });
        });
    </script>
@endpush
