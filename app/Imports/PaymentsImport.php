<?php

namespace App\Imports;

use App\Models\Payment;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class PaymentsImport implements ToModel, WithHeadingRow, WithValidation, WithCalculatedFormulas
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // dd("Payment", $row);
        // $date = (!empty($row['date']) && !is_null($row['date'])) ? Carbon::parse($row['date'])->toDateTimeString() : null;
        // dd($date, $row['date'], $row, Carbon::parse($row['date']));
        // dd(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date']), $row['date'], Carbon::parse($row['date'])->toDateTimeString());
        return new Payment([
            'sl_no' => $row['sl_no'],
            'ref_no' => $row['ref_no'],
            'template_type' => $row['template_type'] ?? null,
            'date' =>  (!empty($row['date']) && !is_null($row['date'])) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date']) : null,
            'project' => $row['project'],
            'account_full_name' => $row['account_full_name'],
            'from_account_type' => $row['from_account_type'],
            'full_account_number' => $row['full_account_number'],
            'to' => $row['to'],
            'to_account_type' => $row['to_account_type'],
            'name_of_beneficiary' => $row['name_of_beneficiary'],
            'account_number' => $row['account_number'],
            'name_of_bank' => $row['name_of_bank'],
            'ifsc_Code_id' => $row['ifsc_Code_id'] ?? null,
            'ifsc_code' => $row['ifsc_code'],
            'amount' => $row['amount'],
            'purpose' => $row['purpose'],
            'invoice_type' => 'invoice' ?? null,
        ]);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function rules(): array
    {
        return [
            // 'sl_no' => 'required',
            // 'ref_no' => 'required',
            // 'date' => 'required',
        ];
    }
}
