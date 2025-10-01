<?php

namespace App\Helpers;

use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\{Account, Country, User, Vendor};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use DateInterval;
use DateTime;
use NumberFormatter;

use function PHPUnit\Framework\throwException;

class Helper
{
    function generateRandomNumber($prefix = null, $length = 4, $upper = false)
    {
        $number = str_shuffle((int) date('Ymd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(1000, 9999)));

        if (is_null($prefix)) {
            $res = substr($number, 0, $length);
        } else {
            $res = $prefix . substr($number, 0, $length);
        }
        return $res;
    }

    function generateRandomString($prefix = null, $length = 13, $upper = false)
    {
        $characters = time() . '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= sha1($characters[random_int(0, $charactersLength - 1)]);
        }
        if (is_null($prefix)) {
            $res = substr($randomString, 0, $length);
        } else {
            $res = $prefix . substr($randomString, 0, $length);
        }
        return $upper ? strtoupper($res) : $res;
    }

    function timeslots($duration = null, $cleanup = null, $start = null, $end = null, $is_24 = false)
    {
        $duration = $duration ?? 180;
        $cleanup = $cleanup ?? 30;
        $start = $start ?? '09:00';
        $end = $end ?? '24:00';
        $format = $is_24 ? 'H:i A' : 'h:i A';
        $start = new DateTime($start);
        $end = new DateTime($end);
        $interval = new DateInterval('PT' . $duration . 'M');
        $cleanupinterval = new DateInterval('PT' . $cleanup . 'M');
        $slots = [];

        for ($intStart = $start; $intStart < $end; $intStart->add($interval)->add($cleanupinterval)) {
            $endperiod = clone $intStart;
            $endperiod->add($interval);
            if ($endperiod > $end) {
                break;
            }
            $slots[] = $intStart->format($format) . '-' . $endperiod->format($format);
        }

        return $slots;
    }

    /**
     * Get Country
     */
    function getCountry($value, $field = null)
    {
        if ($field) {
            $country = Country::where($field, $value)->first();
        } else {
            $country = Country::where('id', $value)->first();
        }
        return $country;
    }
    /**
     * Get State
     */
    function getState($value, $field = null)
    {
        if ($field) {
            $country = Country::where($field, $value)->first();
        } else {
            $country = Country::where('id', $value)->first();
        }
        return $country;
    }
    /**
     * Get City
     */
    function getCity($value, $field = null)
    {
        if ($field) {
            $country = Country::where($field, $value)->first();
        } else {
            $country = Country::where('id', $value)->first();
        }
        return $country;
    }
    /**
     * Get City
     */
    function getFromAccount($temp_type = null)
    {
        /* if ($temp_type == 'mf-rtgs') {
            $internalAc = Vendor::whreIn('from_account_type', ['Internal', 'External'])->get();
        } else {
            $internalAc = Vendor::whereIn('from_account_type', ['Internal', 'External'])->get();
        } */
        $internalAc = Vendor::whereIn('from_account_type', ['Internal'])->get();
        return $internalAc;
    }
    /**
     * Get City
     */
    function getAllVendors($type = 'Salery')
    {
        /* if ($type) {
            $vendors = Vendor::whereNotIn('from_account_type', ['Internal', 'External'])->get();
        } else {
            $vendors = Vendor::get();
        } */
        $vendors = Vendor::get();
        return $vendors;
    }

    /**
     * Remove the specified resource from cart session.
     */
    public function checkTransferPermitted($request)
    {
        if ($request->from_account == 1) {
            $query = Account::query();
            $query->whereBetween('id', [3, 18]);
        }
        return $query->get();
    }

    /**
     * Here’s a simple custom function to achieve Indian number formatting:
     */
    public static function formatIndianNumber($number)
    {
        // Split the number into integer and decimal parts
        $parts = explode('.', (string) $number);
        $integerPart = $parts[0];

        // Ensure the decimal part has exactly two digits
        $decimalPart = isset($parts[1]) ? substr($parts[1], 0, 2) : '00'; // Get first two decimal digits

        // Round the decimal part to two digits
        $decimalPart = str_pad($decimalPart, 2, '0'); // Ensure two decimal places

        // Reverse the integer part for easier processing
        $integerPart = strrev($integerPart);
        $length = strlen($integerPart);
        $result = '';

        // Format the integer part with commas
        for ($i = 0; $i < $length; $i++) {
            if ($i == 3 || ($i > 3 && ($i - 3) % 2 == 0)) {
                $result .= ',';
            }
            $result .= $integerPart[$i];
        }

        // Reverse the formatted integer part and append the decimal part
        // return strrev($result) . '.' . $decimalPart;
        return strrev($result) . '.00';
    }
}
