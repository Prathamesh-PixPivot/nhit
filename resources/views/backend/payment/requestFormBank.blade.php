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
@if (!empty($cartItems))
    <div class="table-container">
        <table id="employee-table" class="table-responsive-full sort-table">
            <thead>
                <tr>
                    <th>Template Type</th>
                    <th>Project</th>
                    <th>From Account</th>
                    <th>Account Number</th>
                    <th>Beneficiary Name</th>
                    <th>Account Number</th>
                    <th>Name Of Bank</th>
                    <th>IFSC</th>
                    <th>Amount</th>
                    <th>Purpose</th>
                    {{-- <th>Action</th> --}}
                </tr>
            </thead>
            <tbody id="table-body">
                @foreach ($cartItems as $index => $note)
                    <tr>
                        <td>
                            <div class="col-md-12 p-1">
                                <input type="hidden" name="payment_note_id[]" value="{{ $note->id }}">
                                <label for="template_type" class="form-label">Template Type <span
                                        style="color: red;">*</span></label>
                                <div class="col-md-12">
                                    <select id="template_type" name="vendor[{{ $index }}][template_type]"
                                        class="form-control template_type @error('template_type') is-invalid @enderror"
                                        require tabindex="1">
                                        <option value="">--Select Options---</option>
                                        <option value="any-bank-internal-external-bulk"
                                            {{ old('template_type') == 'any-bank-internal-external-bulk' ? 'selected' : '' }}>
                                            Any Bulk</option>
                                        <option value="sbi-sbi-internal-external-bulk"
                                            {{ old('template_type') == 'sbi-sbi-internal-external-bulk' ? 'selected' : '' }}>
                                            SBI Bulk</option>
                                        <option value="anybank-onetomany-external-bulk"
                                            {{ old('template_type') == 'anybank-onetomany-external-bulk' ? 'selected' : '' }}>
                                            One To Many Bulk
                                        </option>
                                        <option value="anybank-internalexternal-single"
                                            {{ old('template_type') == 'anybank-internalexternal-single' ? 'selected' : '' }}>
                                            Any Single
                                        </option>
                                    </select>
                                    @error('template_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="col-md-12 p-1">
                                <div class="col-md-12 p-1">
                                    <label for="project" class="form-label">Project
                                        <span style="color: red;">*</span>
                                    </label>
                                    <div class="col-md-12">
                                        <select name="vendor[{{ $index }}][project]" id="project"
                                            class="form-control select-project @error('project') is-invalid @enderror"
                                            required tabindex="2" data-index="{{ $index }}">
                                        </select>

                                        @error('project')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="col-md-12 p-1">
                                <div class="col-md-12 p-1">
                                    <label for="from_account" class="form-label" data-pattern-text="">Payment
                                        From <span style="color: red;">*</span></label>
                                    <div class="col-md-12 from_account_dropdown"
                                        id="from_account_dropdown{{ $index }}">
                                        <select id="from_account{{ $index }}"
                                            class="form-control from_account @error('from_account') is-invalid @enderror"
                                            name="vendor[{{ $index }}][account_full_name]"
                                            data-index="{{ $index }}" tabindex="3">
                                        </select>
                                        @error('from_account')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    {{-- <input type="text"
                                        class="form-control @error('from_account_no') is-invalid @enderror from_account_no"
                                        id="from_account_no{{ $index }}"
                                        value="{{ old('from_account_no') ?? '' }}" name="from_account_no"
                                        data-index="{{ $index }}" readonly> --}}
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="col-md-12 p-1">
                                <input type="text"
                                    class="form-control @error('from_account_no') is-invalid @enderror from_account_no"
                                    id="from_account_no{{ $index }}" value="{{ old('from_account_no') ?? '' }}"
                                    name="vendor[{{ $index }}][full_account_number]"
                                    data-index="{{ $index }}" readonly>
                            </div>
                        </td>

                        <td>
                            <div class="col-md-12 p-1">
                                @if ($note->greenNote)
                                    <input type="text" class="@error('benificiary_name') is-invalid @enderror"
                                        id="benificiary_name"
                                        value="{{ $note->greenNote->supplier->benificiary_name ?? '-' }}"
                                        name="vendor[{{ $index }}][benificiary_name]" readonly>
                                @elseif ($note->reimbursementNote)
                                    <input type="text" class="@error('benificiary_name') is-invalid @enderror"
                                        id="benificiary_name"
                                        value="{{ $note->reimbursementNote->account_holder ?? '-' }}"
                                        name="vendor[{{ $index }}][benificiary_name]" readonly>
                                @else
                                    -
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="col-md-12 p-1">
                                @if ($note->greenNote)
                                    <input type="text" class="@error('account_number') is-invalid @enderror"
                                        id="account_number"
                                        value="{{ $note->greenNote->supplier->account_number ?? '-' }}"
                                        name="vendor[{{ $index }}][account_number]" readonly>
                                @elseif ($note->reimbursementNote)
                                    <input type="text" class="@error('account_number') is-invalid @enderror"
                                        id="account_number" value="{{ $note->reimbursementNote->bank_account ?? '-' }}"
                                        name="vendor[{{ $index }}][account_number]" readonly>
                                @else
                                    -
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="col-md-12 p-1">
                                @if ($note->greenNote)
                                    <input type="text" class="@error('name_of_bank') is-invalid @enderror"
                                        id="name_of_bank" value="{{ $note->greenNote->supplier->name_of_bank ?? '-' }}"
                                        name="vendor[{{ $index }}][name_of_bank]" readonly>
                                @elseif ($note->reimbursementNote)
                                    <input type="text" class="@error('name_of_bank') is-invalid @enderror"
                                        id="name_of_bank" value="{{ $note->reimbursementNote->bank_name ?? '-' }}"
                                        name="vendor[{{ $index }}][name_of_bank]" readonly>
                                @else
                                    -
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="col-md-12 p-1">
                                @if ($note->greenNote)
                                    <input type="text" class="@error('ifsc_code') is-invalid @enderror"
                                        id="ifsc_code" value="{{ $note->greenNote->supplier->ifsc_code ?? '-' }}"
                                        name="vendor[{{ $index }}][ifsc_code]" readonly>
                                @elseif ($note->reimbursementNote)
                                    <input type="text" class="@error('ifsc_code') is-invalid @enderror"
                                        id="ifsc_code" value="{{ $note->reimbursementNote->IFSC_code ?? '-' }}"
                                        name="vendor[{{ $index }}][ifsc_code]" readonly>
                                @else
                                    -
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="col-md-12 p-1">
                                <input type="number" class="@error('amount') is-invalid @enderror" id="amount"
                                    value="{{ $note->net_payable_round_off ?? '-' }}"
                                    name="vendor[{{ $index }}][amount]" readonly>
                            </div>
                        </td>
                        <td>
                            <div class="col-md-12 p-1">
                                {{-- <textarea name="vendor[{{ $index }}][purpose]" id="" class="@error('purpose') is-invalid @enderror"
                                    id="purpose"></textarea> --}}
                                <textarea name="vendor[{{ $index }}][purpose]" id="" class="@error('purpose') is-invalid @enderror"
                                    id="purpose">{{ $note->subject ?? '-' }}</textarea>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
