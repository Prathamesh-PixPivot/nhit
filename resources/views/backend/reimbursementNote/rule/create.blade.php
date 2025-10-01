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

                        <form class="row g-3" action="{{ route('backend.payment-note-approval.store') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">


                            <div class="col-6 approver-section" id="approver_1_div">
                                <label for="approver_1" class="form-label">(Only if Invoice Value > 2,50,000)</label>
                                <div class="row">
                                    <div class="col-4">
                                        <label for="approver_1" class="form-label">Select Approver 1 </label>
                                        <select class="form-select form-control" id="approver_1" name="reviewer_ids[0][]"
                                            multiple>
                                            <option value="">Select User</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <label for="approver_2" class="form-label">Minimum Amount</label>
                                        <select class="form-select form-control" id="amount" name="min_amount[]">
                                            <option value="">Select Amount</option>
                                            <option value="0">0</option>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <label for="approver_2" class="form-label">Minimum Amount</label>
                                        <select class="form-select form-control" id="amount" name="max_amount[]">
                                            <option value="">Select Amount</option>
                                            <option value="250000">2,50,000</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 approver-section" id="approver_2_div" style="display: none;">
                                <label for="approver_2" class="form-label">
                                    (Only if Invoice Value > 2,50,000 < 5000000 ) </label>
                                        <div class="row">
                                            <div class="col-4">
                                                <label for="approver_2" class="form-label">Approver 2</label>
                                                <select class="form-select form-control" id="approver_2" multiple
                                                    name="reviewer_ids[1][]">
                                                    <option value="">Select User</option>
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-4">
                                                <label for="approver_2" class="form-label">Minimum Amount</label>
                                                <select class="form-select form-control" id="amount" name="min_amount[]">
                                                    <option value="">Select Amount</option>
                                                    <option value="250000">2,50,000</option>
                                                </select>
                                            </div>
                                            <div class="col-4">
                                                <label for="approver_2" class="form-label">Minimum Amount</label>
                                                <select class="form-select form-control" id="amount" name="max_amount[]">
                                                    <option value="">Select Amount</option>
                                                    <option value="500000">5,00,000</option>
                                                </select>
                                            </div>
                                        </div>
                            </div>

                            <div class="col-6 approver-section" id="approver_3_div" style="display: none;">
                                <label for="approver_3" class="form-label">(Only if Invoice Value > 5000000)</label>
                                <div class="row">
                                    <div class="col-4">
                                        <label for="approver_3" class="form-label">Approver 3 </label>
                                        <select class="form-select form-control" id="approver_3" name="reviewer_ids[2][]"
                                            multiple>
                                            <option value="">Select User</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <label for="approver_2" class="form-label">Minimum Amount</label>
                                        <select class="form-select form-control" id="amount" name="min_amount[]">
                                            <option value="">Select Amount</option>
                                            <option value="500000">5,00,000</option>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <label for="approver_2" class="form-label">Minimum Amount</label>
                                        <select class="form-select form-control" id="amount" name="max_amount[]">
                                            <option value="">Select Amount</option>
                                            <option value="">Above</option>
                                        </select>
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


        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">All Approval Rules</h5>
                        <!-- Table with stripped rows -->
                        <table class="table datatable table-responsive">
                            <thead>
                                <tr>
                                    <th>Step</th>
                                    <th>Min Amount</th>
                                    <th>Max Amount</th>
                                    <th>Reviewers</th>
                                    <th class="border p-3 text-left">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($approvalSteps as $step)
                                    <tr>
                                        <td>{{ $step->step }}</td>
                                        <td>{{ number_format($step->min_amount, 2) }}</td>
                                        <td>
                                            {{ $step->max_amount ? number_format($step->max_amount, 2) : 'Above' }}
                                        </td>
                                        <td>
                                            @if ($step->reviewers->isNotEmpty())
                                                <ul>
                                                    @foreach ($step->reviewers as $reviewer)
                                                        <li>{{ $reviewer->name }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                No Reviewers Assigned
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('backend.payment-note-approval.edit', $step->id) }}"><i
                                                    class="bi bi-eye"></i></a>
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
