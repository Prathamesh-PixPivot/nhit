@extends('backend.layouts.app')
@section('content')
    <style>
        .table-container {
            width: 100%;
            margin: 20px auto;
            border-radius: 10px;
            overflow: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        .table-header {
            display: flex;
            align-items: center;
            padding: 10px;
            background-color: #f5f5f5;
            border-bottom: 1px solid #ddd;
        }

        .search-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
        }

        .search-wrapper input {
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            margin-right: 10px;
        }

        .view-options {
            display: flex;
            align-items: center;
        }

        .view-options label {
            margin-right: 10px;
            font-size: 16px;
        }

        .view-options select {
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f0f0f0;
            cursor: pointer;
            position: relative;
        }

        th i {
            margin-left: 5px;
        }

        td {
            max-width: 300px;
            /* Increased maximum width for better visibility of long addresses */
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        td img {
            width: 50px;
            /* Square width */
            height: 50px;
            /* Square height */
            border-radius: 5px;
            /* Square corners */
            display: block;
            margin: 0 auto;
            /* Centering the image */
        }
    </style>
    <form class="" action="{{ route('backend.payments.updatePaymentRequest', $slno) }}" method="post"
        id="requestPaymentForm" name="requestForm">

        @csrf
        @method('PUT')
        @if (!empty($cartItems))
            <div class="table-container">
                <table id="employee-table" class="table-responsive-full sort-table">
                    <thead>
                        <tr>
                            <th>Template Type</th>
                            <th>Project <i class="fas fa-sort"></i></th>
                            <th>Account Full Name<i class="fas fa-sort"></i></th>
                            {{-- <th>From Account Type<i class="fas fa-sort"></i></th> --}}
                            <th>Full Account Number <i class="fas fa-sort"></i></th>
                            <th>Payment To<i class="fas fa-sort"></i></th>
                            {{-- <th>To Account Type <i class="fas fa-sort"></i></th> --}}
                            <th>Benificiary Name <i class="fas fa-sort"></i></th>
                            <th>Account Number <i class="fas fa-sort"></i></th>
                            <th>Name Of Bank <i class="fas fa-sort"></i></th>
                            <th>Amount <i class="fas fa-sort"></i></th>
                            <th>Purpose <i class="fas fa-sort"></i></th>
                            <th>Action <i class="fas fa-sort"></i></th>
                        </tr>
                    </thead>
                    <tbody id="table-body">



                        @for ($i = 0; $i < count($cartItems); $i++)
                            <input type="hidden" name="vendor[{{ $i }}][id]" value="{{ $cartItems[$i]['id'] }}">
                            <!-- Employee data will be inserted here -->

                            {{-- <fieldset class="scheduler-border"> 
                      <div class="list-group-item justify-content-between align-items-center"> --}}
                            {{-- <tr>
                    <td colspan="12">
                        <legend class="scheduler-border">Request Details : #{{$i+1}}</legend>
                        <div class="border p-3 d-flex justify-content-end">
                            <span class="badge badge-primary badge-pill delete-item" data-index="{{$i}}">X</span></li>
                        </div>
                    </td>
                     </tr> --}}
                            <tr>
                                <td>
                                    <div class="col-md-12 p-2">
                                        <div class="col-md-12">
                                            <input name="vendor[{{ $i }}][template_type]"
                                                class="template_type @error('template_type') is-invalid @enderror"
                                                id="template_type" value="{{ $cartItems[$i]['template_type'] ?? '' }}"
                                                readonly>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12 p-2">
                                        <div class="col-md-12">
                                            <input name="vendor[{{ $i }}][project]"
                                                class="project @error('project') is-invalid @enderror" id="project"
                                                value="{{ $cartItems[$i]['project'] ?? '' }}" readonly>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-6 p-2 p-10 left-align">
                                        <div class="col-md-12">
                                            <input id="account_full_name"
                                                class="account_full_name @error('account_full_name') is-invalid @enderror"
                                                name="vendor[{{ $i }}][account_full_name]"
                                                value="{{ $cartItems[$i]['account_full_name'] ?? '' }}" data-index="0"
                                                readonly>
                                        </div>
                                    </div>
                                </td>
                                {{-- <td>
                            <div class="col-md-6 p-2 p-10 left-align">
                                <div class="col-md-12">
                                    <input type="text" class="@error('from_account_type') is-invalid @enderror"
                                        id="from_account_type"
                                        value="{{ $cartItems[$i]['from_account_type'] ?? '' }}"
                                        name="vendor[{{ $i }}][from_account_type]" data-index="0" readonly>
                                </div>
                            </div>
                        </td> --}}
                                <td>
                                    <div class="col-md-12">
                                        <input type="text" class="@error('full_account_number') is-invalid @enderror"
                                            id="full_account_number"
                                            value="{{ $cartItems[$i]['full_account_number'] ?? '' }}"
                                            name="vendor[{{ $i }}][full_account_number]" data-index="0"
                                            readonly>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-6 p-2 p-10 left-align">
                                        <div class="col-md-12">
                                            <input type="text" class="@error('to') is-invalid @enderror" id="to"
                                                value="{{ $cartItems[$i]['to'] ?? '' }}"
                                                name="vendor[{{ $i }}][to]" data-index="0" readonly>
                                        </div>
                                    </div>
                                </td>
                                {{-- <td>
                            <div class="col-md-6 p-2 p-10 left-align">
                                <div class="col-md-12">
                                    <input type="text" class="@error('to_account_type') is-invalid @enderror"
                                        id="to_account_type" value="{{ $cartItems[$i]['to_account_type'] ?? '' }}"
                                        name="vendor[{{ $i }}][to_account_type]" readonly>
                                </div>
                            </div>
                        </td> --}}
                                <td>
                                    <div class="col-md-6 p-2 p-10 left-align">
                                        <div class="col-md-12">
                                            <input type="text" class="@error('benificiary_name') is-invalid @enderror"
                                                id="benificiary_name"
                                                value="{{ $cartItems[$i]['name_of_beneficiary'] ?? '' }}"
                                                name="vendor[{{ $i }}][name_of_beneficiary]" readonly>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-6 p-2 p-10 left-align">
                                        <div class="col-md-12">
                                            <input type="text" class="@error('account_number') is-invalid @enderror"
                                                id="account_number" value="{{ $cartItems[$i]['account_number'] ?? '' }}"
                                                name="vendor[{{ $i }}][account_number]" readonly>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-6 p-2 p-10 left-align">
                                        <div class="col-md-12">
                                            <input type="text" class="@error('name_of_bank') is-invalid @enderror"
                                                id="name_of_bank" value="{{ $cartItems[$i]['name_of_bank'] ?? '' }}"
                                                name="vendor[{{ $i }}][name_of_bank]" readonly>
                                        </div>
                                    </div>
                                </td>
                                <input type="hidden" class="@error('name_of_bank') is-invalid @enderror" id="ifsc_code"
                                    value="{{ $cartItems[$i]['ifsc_code'] ?? '' }}"
                                    name="vendor[{{ $i }}][ifsc_code]" readonly>

                                <td>
                                    <div class="col-md-6 p-2 p-10 left-align">
                                        <div class="col-md-12">
                                            <input type="number" class="@error('amount') is-invalid @enderror"
                                                id="amount" value="{{ $cartItems[$i]['amount'] ?? '' }}"
                                                name="vendor[{{ $i }}][amount]">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12 p-2">
                                        <div class="col-md-12">
                                            <textarea name="vendor[{{ $i }}][purpose]" id="" class="@error('purpose') is-invalid @enderror"
                                                id="purpose">{{ $cartItems[$i]['purpose'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12 p-2">
                                        <div class="col-md-12">
                                            <span class="badge badge-primary badge-pill delete-item"
                                                data-index="{{ $i }}"><a
                                                    href="{{ route('backend.payments.deleteRequestItem', [$slno, $cartItems[$i]['id']]) }}"
                                                    style="color: #000;">X</a></span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            {{--  </div>
         </fieldset> --}}
                        @endfor
                    </tbody>
                </table>
            </div>

            @if (auth()->user()->id == $cartItems[0]['user_id'] && $cartItems[0]['status'] == 'D')
                <div class="row mb-3">
                    <div class="col-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select form-control" id="status" name="status">
                            <option value="D"{{ old('status', $cartItems[0]['status']) == 'D' ? 'selected' : '' }}>
                                Draft </option>
                            <option value="S"{{ old('status', $cartItems[0]['status']) == 'S' ? 'selected' : '' }}>
                                Sent for Approval</option>
                        </select>
                        @error('status')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-3">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-select form-control" id="type" name="type">
                            <option value="">select an option</option>
                            <option value="I"> Internal </option>
                            <option value="E"> External </option>
                        </select>
                        @error('type')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            @endif




            {{-- @endif --}}
            <input type="submit" class="col-md-2 offset-md-0 btn btn-primary btn-sm request-form-submit" value="Update"
                style="background: #6c757d; color: #fff;">
        @endif
    </form>

    @foreach ($cartItems as $item)
        @php
            $documents = [];

            if (isset($item->paymentNote) && isset($item->paymentNote->greenNote)) {
                $documents = App\Models\SupportingDoc::where('green_note_id', $item->paymentNote->greenNote->id)->get();
            }
        @endphp

        @if (!empty($item->paymentNote) && !empty($item->paymentNote->greenNote))
            <div class="col-lg-12 mt-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Attached Supporting Docs <a class="btn btn-primary"
                                href="{{ route('backend.note.view.pdf', $item->paymentNote->greenNote->id) }}">View
                                Green Note</a>
                            | <a class="btn btn-primary"
                                href="{{ route('backend.payment-note.show', $item->paymentNote->id) }}">
                                <i class="bi bi-eye"></i> View Payment Note
                            </a>
                        </h5>
                        <!-- Vertical Form -->
                        <!-- Table with stripped rows -->
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>
                                        S no.
                                    </th>
                                    <th>File name</th>
                                    <th>File</th>
                                    <th data-type="date" data-format="DD/MM/YYYY">Upload Date</th>
                                    <th>Uploaded By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($documents as $index => $document)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $document->name }}</td>
                                        <td>
                                            @php
                                                $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
                                            @endphp

                                            @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                                                <img src="{{ asset('notes/documents/' . $document->file_path) }}"
                                                    alt="Document Image" width="70" height="70">
                                            @else
                                                <a href="{{ asset('notes/documents/' . $document->file_path) }}"
                                                    target="_blank"><i class="bi bi-file-earmark-text-fill"></i></a>
                                            @endif
                                        </td>
                                        <td>{{ $document->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') }}
                                        </td>
                                        <td>{{ $document->user->name }}</td>
                                        <td> <a href="{{ asset('notes/documents/' . $document->file_path) }}" download>
                                                <i class="bi bi-download"></i>

                                            </a>
                                            @if (\Auth::id() == 1)
                                                |
                                                <form action="{{ route('backend.documents.destroy', $document->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-none btn-sm delete-btn"><i
                                                            class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                        <!-- End Table with stripped rows -->
                    </div>
                </div>
            </div>
        @elseif (!empty($item->paymentNote) && !empty($item->paymentNote->reimbursementNote))
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Attach Files
                            <a class="btn btn-primary"
                                href="{{ route('backend.reimbursement-note.view.pdf', $item->paymentNote->reimbursementNote->id) }}">View
                                Reimbursement
                                Note </a>
                            | <a class="btn btn-primary"
                                href="{{ route('backend.payment-note.show', $item->paymentNote->id) }}">
                                <i class="bi bi-eye"></i> View Payment Note
                            </a>
                        </h5>
                        @if ($item->paymentNote->reimbursementNote->file_path)
                            <table class="table table-bordered mt-2">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Preview</th>
                                        <th>Download</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (json_decode($item->paymentNote->reimbursementNote->file_path, true) as $key => $file)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                @php
                                                    $extension = pathinfo($file, PATHINFO_EXTENSION);
                                                    $isImage = in_array(strtolower($extension), [
                                                        'jpg',
                                                        'jpeg',
                                                        'png',
                                                        'gif',
                                                        'webp',
                                                    ]);
                                                @endphp

                                                @if ($isImage)
                                                    <img src="{{ asset('storage/rn/' . $file) }}" alt="Preview"
                                                        width="50">
                                                @elseif (strtolower($extension) == 'pdf')
                                                    <a href="{{ asset('storage/rn/' . $file) }}" target="_blank">View
                                                        PDF</a>
                                                @else
                                                    <span>N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ asset('storage/rn/' . $file) }}" download
                                                    class="btn btn-sm btn-primary">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        @else
        @endif
    @endforeach


@endsection
@push('style')
    <style>
        button,
        input:not([type='number']),
        optgroup,
        select {
            background: #f0f0f0;
            border: none;
            padding: 10px;
        }
    </style>
@endpush
