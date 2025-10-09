<?php

namespace App\Services;

use App\Models\Vendor;
use App\Models\VendorAccount;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BankingDetailsService
{
    /**
     * Get banking details for auto-population in travel/expense forms
     */
    public function getBankingDetailsForUser(User $user, $vendorId = null)
    {
        try {
            $bankingDetails = [];

            // If vendor ID is provided, get vendor's banking details
            if ($vendorId) {
                $vendor = Vendor::find($vendorId);
                if ($vendor) {
                    $bankingDetails['vendor'] = $this->getVendorBankingDetails($vendor);
                }
            }

            // Get user's personal banking details if available
            $bankingDetails['user'] = $this->getUserBankingDetails($user);

            // Get frequently used banking details
            $bankingDetails['frequent'] = $this->getFrequentlyUsedBankingDetails($user);

            return $bankingDetails;

        } catch (\Exception $e) {
            Log::error("Failed to get banking details for user {$user->id}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get vendor's banking details with multiple accounts
     */
    public function getVendorBankingDetails(Vendor $vendor)
    {
        $details = [
            'vendor_name' => $vendor->vendor_name,
            'vendor_code' => $vendor->vendor_code,
            'primary_account' => null,
            'all_accounts' => []
        ];

        // Get primary account
        $primaryAccount = $vendor->primaryAccount;
        if ($primaryAccount) {
            $details['primary_account'] = [
                'id' => $primaryAccount->id,
                'account_name' => $primaryAccount->account_name,
                'account_number' => $primaryAccount->account_number,
                'name_of_bank' => $primaryAccount->name_of_bank,
                'branch_name' => $primaryAccount->branch_name,
                'ifsc_code' => $primaryAccount->ifsc_code,
                'swift_code' => $primaryAccount->swift_code,
                'account_type' => $primaryAccount->account_type,
            ];
        } else {
            // Fallback to vendor's direct banking details
            $details['primary_account'] = [
                'account_name' => $vendor->vendor_name,
                'account_number' => $vendor->account_number,
                'name_of_bank' => $vendor->name_of_bank,
                'ifsc_code' => $vendor->ifsc_code,
            ];
        }

        // Get all active accounts
        $allAccounts = $vendor->activeAccounts;
        foreach ($allAccounts as $account) {
            $details['all_accounts'][] = [
                'id' => $account->id,
                'account_name' => $account->account_name,
                'account_number' => $account->account_number,
                'name_of_bank' => $account->name_of_bank,
                'branch_name' => $account->branch_name,
                'ifsc_code' => $account->ifsc_code,
                'swift_code' => $account->swift_code,
                'account_type' => $account->account_type,
                'is_primary' => $account->is_primary,
            ];
        }

        return $details;
    }

    /**
     * Get user's personal banking details
     */
    public function getUserBankingDetails(User $user)
    {
        // This would depend on your user model structure
        // Assuming you have banking details in user profile
        return [
            'account_name' => $user->name,
            'account_number' => $user->account_number ?? null,
            'name_of_bank' => $user->bank_name ?? null,
            'ifsc_code' => $user->ifsc_code ?? null,
            'branch_name' => $user->branch_name ?? null,
        ];
    }

    /**
     * Get frequently used banking details by the user
     */
    public function getFrequentlyUsedBankingDetails(User $user)
    {
        $cacheKey = "frequent_banking_details_user_{$user->id}";
        
        return Cache::remember($cacheKey, 3600, function () use ($user) {
            // This would analyze user's payment history to find frequently used accounts
            // For now, returning empty array - you can implement based on your requirements
            return [];
        });
    }

    /**
     * Auto-populate banking details in forms
     */
    public function autoPopulateBankingDetails($formType, $userId, $vendorId = null, $additionalParams = [])
    {
        $user = User::find($userId);
        if (!$user) {
            return [];
        }

        $bankingDetails = $this->getBankingDetailsForUser($user, $vendorId);

        // Customize based on form type
        switch ($formType) {
            case 'travel_expense':
                return $this->formatForTravelExpense($bankingDetails, $additionalParams);
            
            case 'payment_note':
                return $this->formatForPaymentNote($bankingDetails, $additionalParams);
            
            case 'reimbursement':
                return $this->formatForReimbursement($bankingDetails, $additionalParams);
            
            default:
                return $bankingDetails;
        }
    }

    /**
     * Format banking details for travel expense forms
     */
    private function formatForTravelExpense($bankingDetails, $params)
    {
        $formatted = [
            'beneficiary_name' => '',
            'account_number' => '',
            'bank_name' => '',
            'ifsc_code' => '',
            'branch_name' => '',
        ];

        // Prioritize vendor details if available
        if (!empty($bankingDetails['vendor']['primary_account'])) {
            $account = $bankingDetails['vendor']['primary_account'];
            $formatted = [
                'beneficiary_name' => $account['account_name'],
                'account_number' => $account['account_number'],
                'bank_name' => $account['name_of_bank'],
                'ifsc_code' => $account['ifsc_code'],
                'branch_name' => $account['branch_name'] ?? '',
            ];
        } elseif (!empty($bankingDetails['user']['account_number'])) {
            // Fallback to user's details
            $user = $bankingDetails['user'];
            $formatted = [
                'beneficiary_name' => $user['account_name'],
                'account_number' => $user['account_number'],
                'bank_name' => $user['name_of_bank'],
                'ifsc_code' => $user['ifsc_code'],
                'branch_name' => $user['branch_name'] ?? '',
            ];
        }

        return $formatted;
    }

    /**
     * Format banking details for payment note forms
     */
    private function formatForPaymentNote($bankingDetails, $params)
    {
        // For payment notes, we typically use vendor's banking details
        if (!empty($bankingDetails['vendor']['primary_account'])) {
            return $bankingDetails['vendor']['primary_account'];
        }

        return [];
    }

    /**
     * Format banking details for reimbursement forms
     */
    private function formatForReimbursement($bankingDetails, $params)
    {
        // For reimbursements, we typically use user's personal banking details
        if (!empty($bankingDetails['user']['account_number'])) {
            return $bankingDetails['user'];
        }

        return [];
    }

    /**
     * Validate banking details
     */
    public function validateBankingDetails($details)
    {
        $errors = [];

        if (empty($details['account_number'])) {
            $errors[] = 'Account number is required';
        }

        if (empty($details['ifsc_code'])) {
            $errors[] = 'IFSC code is required';
        } elseif (!preg_match('/^[A-Z]{4}0[A-Z0-9]{6}$/', $details['ifsc_code'])) {
            $errors[] = 'Invalid IFSC code format';
        }

        if (empty($details['name_of_bank'])) {
            $errors[] = 'Bank name is required';
        }

        if (empty($details['account_name'])) {
            $errors[] = 'Account holder name is required';
        }

        return $errors;
    }

    /**
     * Get IFSC code details from external API (if available)
     */
    public function getIFSCDetails($ifscCode)
    {
        try {
            // You can integrate with IFSC API services like:
            // - https://ifsc.razorpay.com/
            // - https://bank-ifsc-api.herokuapp.com/
            
            $cacheKey = "ifsc_details_{$ifscCode}";
            
            return Cache::remember($cacheKey, 86400, function () use ($ifscCode) {
                // Mock implementation - replace with actual API call
                return [
                    'bank' => 'Bank Name',
                    'branch' => 'Branch Name',
                    'address' => 'Branch Address',
                    'city' => 'City',
                    'state' => 'State',
                    'contact' => 'Contact Number',
                ];
            });

        } catch (\Exception $e) {
            Log::error("Failed to get IFSC details for {$ifscCode}: " . $e->getMessage());
            return null;
        }
    }
}
