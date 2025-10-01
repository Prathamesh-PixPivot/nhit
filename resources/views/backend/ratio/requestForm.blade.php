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
{{-- @if (!empty($vendorItems)) --}}
<div class="table-container">
    <table id="employee-table" class="table-responsive-full sort-table">
        <thead>
            <tr>
                <th>Template Type</th>
                <th>Project <i class="fas fa-sort"></i></th>
                {{-- <th>Account Full Name<i class="fas fa-sort"></i></th> --}}
                <th>From Account Type<i class="fas fa-sort"></i></th>
                <th>Full Account Number <i class="fas fa-sort"></i></th>
                <th>Payment To<i class="fas fa-sort"></i></th>
                {{-- <th>To Account Type <i class="fas fa-sort"></i></th> --}}
                {{-- <th>Beneficiary Name <i class="fas fa-sort"></i></th> --}}
                <th>Account Number <i class="fas fa-sort"></i></th>
                {{-- <th>Name Of Bank <i class="fas fa-sort"></i></th> --}}
                <th>Amount <i class="fas fa-sort"></i></th>
                <th>Purpose <i class="fas fa-sort"></i></th>
                {{-- <th>Action <i class="fas fa-sort"></i></th> --}}
            </tr>
        </thead>
        <tbody id="table-body">

            @php
                $i = 0; // Initialize $i before the loop
            @endphp
            @foreach ($vendorItems as $index => $item)
                @php
                    $firstItem = @$item[0];
                    $lastItem = end($item);
                    // dd($lastItem);
                @endphp
                <td hidden>
                            <div class="col-md-6 p-2 p-10 left-align">
                                <div class="col-md-12">
                                    <input type="text" class="@error('ifsc_code') is-invalid @enderror"
                                        id="ifsc_code" value="{{ $lastItem['ifsc_code'] ?? '' }}"
                                        name="vendor[{{ $i }}][ifsc_code]" readonly>
                                </div>
                            </div>
                        </td>
                <tr>
                    <td>
                        <div class="col-md-12 p-2">
                            <div class="col-md-12">
                                <input type="hidden" name="vendor[{{ $i }}][template_type]"
                                    class="template_type @error('template_type') is-invalid @enderror"
                                    id="template_type" value="sbi-sbi-internal-external-bulk">
                                <input
                                    class="template_type @error('template_type') is-invalid @enderror"
                                    id="template_type" value="SBI Bulk" readonly>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="col-md-12 p-2">
                            <div class="col-md-12">
                                <input name="vendor[{{ $i }}][project]"
                                    class="project @error('project') is-invalid @enderror" id="project"
                                    value="{{ $index ?? 'N/A' }}" readonly>
                            </div>
                        </div>
                    </td>
                    <td class="d-none">
                        <div class="col-md-6 p-2 p-10 left-align">
                            <div class="col-md-12">
                                <input id="account_full_name"
                                    class="account_full_name @error('account_full_name') is-invalid @enderror"
                                    name="vendor[{{ $i }}][account_full_name]"
                                    value="{{ @$firstItem['vendor_name'] ?? 'N/A' }}" data-index="0" readonly>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="col-md-6 p-2 p-10 left-align">
                            <div class="col-md-12">
                                <input type="text" class="@error('from_account_type') is-invalid @enderror"
                                    id="from_account_type" value="{{ @$firstItem['vendor_name'] ?? 'N/A' }}"
                                    name="vendor[{{ $i }}][from_account_type]" data-index="0" readonly>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="col-md-12">
                            <input type="text" class="@error('full_account_number') is-invalid @enderror"
                                id="full_account_number" value="{{ @$firstItem['account_number'] ?? 'N/A' }}"
                                name="vendor[{{ $i }}][full_account_number]" data-index="0" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="col-md-6 p-2 p-10 left-align">
                            <div class="col-md-12">
                                <input type="text" class="@error('to') is-invalid @enderror" id="to"
                                    value="{{ @$lastItem['benificiary_name'] ?? 'N/A' }}"
                                    name="vendor[{{ $i }}][benificiary_name]" data-index="0" readonly>
                            </div>
                        </div>
                    </td>
                    <td class="d-none">
                        <div class="col-md-6 p-2 p-10 left-align">
                            <div class="col-md-12">
                                <input type="text" class="@error('to_account_type') is-invalid @enderror"
                                    id="to_account_type" value="{{ 'Internal' }}"
                                    name="vendor[{{ $i }}][to_account_type]" readonly>
                            </div>
                        </div>
                    </td>
                    <td class="d-none">
                        <div class="col-md-6 p-2 p-10 left-align">
                            <div class="col-md-12">
                                <input type="text" class="@error('benificiary_name') is-invalid @enderror"
                                    id="benificiary_name" value="{{ @$lastItem['vendor_name'] ?? 'N/A' }}"
                                    name="vendor[{{ $i }}][benificiary_name]" readonly>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="col-md-6 p-2 p-10 left-align">
                            <div class="col-md-12">
                                <input type="text" class="@error('account_number') is-invalid @enderror"
                                    id="account_number" value="{{ @$lastItem['account_number'] ?? 'N/A' }}"
                                    name="vendor[{{ $i }}][account_number]" readonly>
                            </div>
                        </div>
                    </td>
                    <td class="d-none">
                        <div class="col-md-6 p-2 p-10 left-align">
                            <div class="col-md-12">
                                <input type="text" class="@error('name_of_bank') is-invalid @enderror"
                                    id="name_of_bank" value="{{ @$lastItem['name_of_bank'] ?? 'N/A' }}"
                                    name="vendor[{{ $i }}][name_of_bank]" readonly>
                            </div>
                        </div>
                    </td>
                    
                    <td>
                        <div class="col-md-6 p-2 p-10 left-align">
                            <div class="col-md-12">
                                <input type="number" class="@error('amount') is-invalid @enderror" id="amount"
                                    value="{{ @$lastItem['calculatedAmount'] ?? 0 }}"
                                    name="vendor[{{ $i }}][amount]" readonly>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="col-md-12 p-2">
                            <div class="col-md-12">
                                <textarea name="vendor[{{ $i }}][purpose]" id="" class="@error('purpose') is-invalid @enderror"
                                    id="purpose"></textarea>
                            </div>
                        </div>
                    </td>
                </tr>
                @php
                    $i++;
                @endphp
            @endforeach


            {{-- @for ($index = 0; $index < 8; $index++)
                <tr>
                    <td>
                        <div class="col-md-12 p-2">
                            <div class="col-md-12">
                                <input name="vendor[{{ $i }}][template_type]"
                                    class="template_type @error('template_type') is-invalid @enderror"
                                    id="template_type" value="sbi" readonly>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="col-md-12 p-2">
                            <div class="col-md-12">
                                <input name="vendor[{{ $i }}][project]"
                                    class="project @error('project') is-invalid @enderror" id="project"
                                    value="{{ $vendorItems[$i]['project'] ?? 'N/A' }}" readonly>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="col-md-6 p-2 p-10 left-align">
                            <div class="col-md-12">
                                <input id="account_full_name"
                                    class="account_full_name @error('account_full_name') is-invalid @enderror"
                                    name="vendor[{{ $i }}][account_full_name]"
                                    value="{{ $vendorItems[$i]['account_full_name'] ?? 'N/A' }}" data-index="0"
                                    readonly>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="col-md-6 p-2 p-10 left-align">
                            <div class="col-md-12">
                                <input type="text" class="@error('from_account_type') is-invalid @enderror"
                                    id="from_account_type" value="ONMExp"
                                    name="vendor[{{ $i }}][from_account_type]" data-index="0" readonly>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="col-md-12">
                            <input type="text" class="@error('full_account_number') is-invalid @enderror"
                                id="full_account_number" value="{{ $vendorItems[$i]['full_account_number'] ?? 'N/A' }}"
                                name="vendor[{{ $i }}][full_account_number]" data-index="0" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="col-md-6 p-2 p-10 left-align">
                            <div class="col-md-12">
                                <input type="text" class="@error('to') is-invalid @enderror" id="to"
                                    value="{{ $vendorItems[$i]['to'] ?? 'N/A' }}" name="vendor[{{ $i }}][to]"
                                    data-index="0" readonly>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="col-md-6 p-2 p-10 left-align">
                            <div class="col-md-12">
                                <input type="text" class="@error('to_account_type') is-invalid @enderror"
                                    id="to_account_type" value="P1_AS Common Payments Pool account"
                                    name="vendor[{{ $i }}][to_account_type]" readonly>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="col-md-6 p-2 p-10 left-align">
                            <div class="col-md-12">
                                <input type="text" class="@error('benificiary_name') is-invalid @enderror"
                                    id="benificiary_name" value="{{ $vendorItems[$i]['benificiary_name'] ?? 'N/A' }}"
                                    name="vendor[{{ $i }}][benificiary_name]" readonly>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="col-md-6 p-2 p-10 left-align">
                            <div class="col-md-12">
                                <input type="text" class="@error('account_number') is-invalid @enderror"
                                    id="account_number" value="{{ $vendorItems[$i]['account_number'] ?? 'N/A' }}"
                                    name="vendor[{{ $i }}][account_number]" readonly>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="col-md-6 p-2 p-10 left-align">
                            <div class="col-md-12">
                                <input type="text" class="@error('name_of_bank') is-invalid @enderror"
                                    id="name_of_bank" value="{{ $vendorItems[$i]['name_of_bank'] ?? 'N/A' }}"
                                    name="vendor[{{ $i }}][name_of_bank]" readonly>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="col-md-6 p-2 p-10 left-align">
                            <div class="col-md-12">
                                <input type="number" class="@error('amount') is-invalid @enderror" id="amount"
                                    value="{{ $vendorItems[$i]['calculatedAmount'] ?? 0 }}"
                                    name="vendor[{{ $i }}][amount]" readonly>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="col-md-12 p-2">
                            <div class="col-md-12">
                                <textarea name="vendor[{{ $i }}][purpose]" id="" class="@error('purpose') is-invalid @enderror"
                                    id="purpose"></textarea>
                            </div>
                        </div>
                    </td>
                </tr>
            @endfor --}}
        </tbody>
    </table>
</div>
{{-- @endif --}}
