<?php

namespace App\Imports;

use App\Helpers\Helper;
use App\Models\Vendor;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class VendorsImport implements ToModel, WithHeadingRow, WithValidation, WithCalculatedFormulas
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // $date = (!empty($row['date']) && !is_null($row['date'])) ? Carbon::parse($row['date'])->toDateTimeString() : null;
        // dd($date, $row['date'], $row, Carbon::parse($row['date']));
        // dd(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date']), $row['date'], Carbon::parse($row['date'])->toDateTimeString());
        /* $helper = new Helper;
        $country_id = $helper->getCountry($row['country_name'], 'name')->id;
        $state_id = $helper->getState($row['state'], 'name')->id;
        $city_id = $helper->getCity($row['city'], 'name')->id;
        dd($row, $helper->getCountry('india', 'name'), $country_id, $state_id, $city_id); */

        return new Vendor([
            's_no' => $row['s_no'],
            'from_account_type' => isset($row['from_account_type']) && !empty($row['from_account_type']) ? $row['from_account_type'] : null,
            'project' => $row['project'],
            'account_name' => isset($row['account_name']) ? $row['benificiary_name'] : null,
            'short_name' => isset($row['short_name']) ? $row['short_name'] : null,
            // 'parent' => $row['parent'],
            'account_number' => isset($row['account_number']) ? $row['account_number'] : null,
            'name_of_bank' => $row['name_of_bank'],
            // 'name_of_bank' => $row['name_of_bank'],
            'ifsc_code_id' => isset($row['ifsc_code_id']) ? $row['ifsc_code_id'] : null,
            'ifsc_code' => $row['ifsc_code'] ?? null,
            'vendor_code' => $row['vendor_code'] ?? null,
            'vendor_type' => isset($row['vendor_type']) ? $row['vendor_type'] : null,
            'vendor_name' => $row['vendor_name'] ?? null,
            // 'date' =>  (!empty($row['date']) && !is_null($row['date'])) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date']) : null,
            'vendor_nick_name' => isset($row['vendor_nick_name']) ? $row['vendor_nick_name'] : null,
            'email' => isset($row['e_mail']) ? $row['e_mail'] : null,
            'mobile' => isset($row['mobile']) ? $row['mobile'] : null,
            'gstin' => $row['gstin'] ?? null,
            'pan' => $row['pan'] ?? null,
            'country_id' => $row['country_name'] ?? null,
            'state_id' => $row['state'] ?? null,
            'city_id' => $row['city'] ?? null,
            'country_name' => $row['country_name'] ?? null,
            'state_name' => $row['state'] ?? null,
            'city_name' => $row['city'] ?? null,
            'msme' => $row['msme'] ?? null,
            'msme_registration_number' => $row['msme_registration_number'] ?? null,
            'msme_start_date' => isset($row['msme_start_date']) && !empty($row['msme_start_date']) && !is_null($row['msme_start_date']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['msme_start_date']) : null,
            'msme_end_date' => isset($row['msme_end_date']) && !empty($row['msme_end_date']) && !is_null($row['msme_end_date']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['msme_end_date']) : null,
            'material_nature' => $row['material_nature'] ?? null,
            'gst_defaulted' => $row['gst_defaulted'] ?? null,
            'section_206AB_verified' => $row['section_206ab_verified'] ?? null,
            'benificiary_name' => $row['benificiary_name'] ?? null,
            'remarks_address' => $row['remarks_in_address_details'] ?? null,
            'common_bank_details' => $row['common_bank_details_required_for_location_level_or_not'] ?? null,
            'income_tax_type' => $row['income_tax_type'] ?? null,
            'active' => 'Y' ?? null,
            'date_added' => isset($row['date_added']) && !empty($row['date_added']) && !is_null($row['date_added']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date_added']) : null,
            'last_updated' => isset($row['last_updated']) && !empty($row['last_updated']) && !is_null($row['last_updated']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['last_updated']) : null,
            // 'created_at' => $row['last_updated'] ?? null,
            // 'updated_at' => $row['last_updated'] ?? null,
        ]);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function rules(): array
    {
        return [];
        return [
            'gstin' => 'required',
            'pan' => 'required',
        ];
    }
}
