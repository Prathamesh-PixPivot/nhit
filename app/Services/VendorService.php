<?php

namespace App\Services;

use App\Models\Vendor;
use App\Models\VendorAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VendorService
{
    /**
     * Create a new vendor with auto-generated code
     */
    public function createVendor(array $vendorData)
    {
        try {
            DB::beginTransaction();

            // Create vendor (code will be auto-generated in the model)
            $vendor = Vendor::create($vendorData);

            // Create primary account if banking details are provided
            if (!empty($vendorData['account_number']) && !empty($vendorData['name_of_bank'])) {
                $this->createVendorAccount($vendor, [
                    'account_name' => $vendor->vendor_name,
                    'account_number' => $vendorData['account_number'],
                    'name_of_bank' => $vendorData['name_of_bank'],
                    'ifsc_code' => $vendorData['ifsc_code'] ?? '',
                    'is_primary' => true,
                    'is_active' => true,
                ]);
            }

            DB::commit();

            Log::info("Vendor created successfully. ID: {$vendor->id}, Code: {$vendor->vendor_code}");

            return $vendor;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to create vendor. Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a new account for a vendor
     */
    public function createVendorAccount(Vendor $vendor, array $accountData)
    {
        try {
            DB::beginTransaction();

            // If this is being set as primary, unset other primary accounts
            if ($accountData['is_primary'] ?? false) {
                VendorAccount::where('vendor_id', $vendor->id)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }

            $account = VendorAccount::create(array_merge($accountData, [
                'vendor_id' => $vendor->id
            ]));

            DB::commit();

            Log::info("Vendor account created successfully. Vendor ID: {$vendor->id}, Account ID: {$account->id}");

            return $account;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to create vendor account. Vendor ID: {$vendor->id}. Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update vendor account
     */
    public function updateVendorAccount(VendorAccount $account, array $accountData)
    {
        try {
            DB::beginTransaction();

            // If this is being set as primary, unset other primary accounts
            if (($accountData['is_primary'] ?? false) && !$account->is_primary) {
                VendorAccount::where('vendor_id', $account->vendor_id)
                    ->where('id', '!=', $account->id)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }

            $account->update($accountData);

            DB::commit();

            Log::info("Vendor account updated successfully. Account ID: {$account->id}");

            return $account;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to update vendor account. Account ID: {$account->id}. Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete vendor account
     */
    public function deleteVendorAccount(VendorAccount $account)
    {
        try {
            DB::beginTransaction();

            $vendorId = $account->vendor_id;
            $wasPrimary = $account->is_primary;

            $account->delete();

            // If the deleted account was primary, set another account as primary
            if ($wasPrimary) {
                $nextAccount = VendorAccount::where('vendor_id', $vendorId)
                    ->where('is_active', true)
                    ->first();

                if ($nextAccount) {
                    $nextAccount->update(['is_primary' => true]);
                }
            }

            DB::commit();

            Log::info("Vendor account deleted successfully. Account ID: {$account->id}");

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to delete vendor account. Account ID: {$account->id}. Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get vendor's banking details for payment processing
     */
    public function getVendorBankingDetails(Vendor $vendor, $accountId = null)
    {
        if ($accountId) {
            $account = VendorAccount::where('vendor_id', $vendor->id)
                ->where('id', $accountId)
                ->where('is_active', true)
                ->first();
        } else {
            $account = $vendor->primaryAccount;
        }

        if ($account) {
            return [
                'account_name' => $account->account_name,
                'account_number' => $account->account_number,
                'name_of_bank' => $account->name_of_bank,
                'branch_name' => $account->branch_name,
                'ifsc_code' => $account->ifsc_code,
                'swift_code' => $account->swift_code,
            ];
        }

        // Fallback to vendor's direct banking details (backward compatibility)
        return [
            'account_name' => $vendor->vendor_name,
            'account_number' => $vendor->account_number,
            'name_of_bank' => $vendor->name_of_bank,
            'branch_name' => null,
            'ifsc_code' => $vendor->ifsc_code,
            'swift_code' => null,
        ];
    }

    /**
     * Generate new vendor code manually
     */
    public function generateVendorCode(string $vendorName, string $vendorType = null)
    {
        return Vendor::generateVendorCode($vendorName, $vendorType);
    }

    /**
     * Update vendor code
     */
    public function updateVendorCode(Vendor $vendor, string $newCode = null)
    {
        try {
            DB::beginTransaction();

            if ($newCode) {
                // Check if code is already taken
                $existingVendor = Vendor::where('vendor_code', $newCode)
                    ->where('id', '!=', $vendor->id)
                    ->first();

                if ($existingVendor) {
                    throw new \Exception("Vendor code '{$newCode}' is already taken");
                }

                $vendor->update([
                    'vendor_code' => $newCode,
                    'code_auto_generated' => false
                ]);
            } else {
                // Regenerate code
                $newCode = Vendor::generateVendorCode($vendor->vendor_name, $vendor->vendor_type);
                $vendor->update([
                    'vendor_code' => $newCode,
                    'code_auto_generated' => true
                ]);
            }

            DB::commit();

            Log::info("Vendor code updated successfully. Vendor ID: {$vendor->id}, New Code: {$newCode}");

            return $vendor;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to update vendor code. Vendor ID: {$vendor->id}. Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get all active accounts for a vendor
     */
    public function getVendorAccounts(Vendor $vendor)
    {
        return $vendor->activeAccounts()->orderBy('is_primary', 'desc')->get();
    }

    /**
     * Activate/Deactivate vendor account
     */
    public function toggleAccountStatus(VendorAccount $account)
    {
        try {
            DB::beginTransaction();

            $newStatus = !$account->is_active;
            
            // If deactivating a primary account, set another account as primary
            if (!$newStatus && $account->is_primary) {
                $nextAccount = VendorAccount::where('vendor_id', $account->vendor_id)
                    ->where('id', '!=', $account->id)
                    ->where('is_active', true)
                    ->first();

                if ($nextAccount) {
                    $nextAccount->update(['is_primary' => true]);
                }
                
                $account->update(['is_primary' => false]);
            }

            $account->update(['is_active' => $newStatus]);

            DB::commit();

            Log::info("Vendor account status toggled. Account ID: {$account->id}, New Status: " . ($newStatus ? 'Active' : 'Inactive'));

            return $account;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to toggle vendor account status. Account ID: {$account->id}. Error: " . $e->getMessage());
            throw $e;
        }
    }
}
