<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BankingDetailsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BankingDetailsController extends Controller
{
    protected $bankingDetailsService;

    public function __construct(BankingDetailsService $bankingDetailsService)
    {
        $this->bankingDetailsService = $bankingDetailsService;
    }

    /**
     * Get banking details for auto-population
     */
    public function getBankingDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'form_type' => 'required|in:travel_expense,payment_note,reimbursement',
            'vendor_id' => 'nullable|exists:vendors,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = $request->user_id ?? auth()->id();
            $formType = $request->form_type;
            $vendorId = $request->vendor_id;
            $additionalParams = $request->except(['form_type', 'vendor_id', 'user_id']);

            $bankingDetails = $this->bankingDetailsService->autoPopulateBankingDetails(
                $formType,
                $userId,
                $vendorId,
                $additionalParams
            );

            return response()->json([
                'success' => true,
                'data' => $bankingDetails
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get banking details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vendor's all banking accounts
     */
    public function getVendorAccounts(Request $request, $vendorId)
    {
        try {
            $vendor = \App\Models\Vendor::findOrFail($vendorId);
            $bankingDetails = $this->bankingDetailsService->getVendorBankingDetails($vendor);

            return response()->json([
                'success' => true,
                'data' => $bankingDetails
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get vendor accounts: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate banking details
     */
    public function validateBankingDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_number' => 'required|string',
            'ifsc_code' => 'required|string',
            'name_of_bank' => 'required|string',
            'account_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $errors = $this->bankingDetailsService->validateBankingDetails($request->all());

            return response()->json([
                'success' => count($errors) === 0,
                'valid' => count($errors) === 0,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get IFSC code details
     */
    public function getIFSCDetails(Request $request, $ifscCode)
    {
        try {
            $details = $this->bankingDetailsService->getIFSCDetails($ifscCode);

            if ($details) {
                return response()->json([
                    'success' => true,
                    'data' => $details
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'IFSC code not found or invalid'
                ], 404);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get IFSC details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's banking details
     */
    public function getUserBankingDetails(Request $request)
    {
        try {
            $user = auth()->user();
            $vendorId = $request->vendor_id;

            $bankingDetails = $this->bankingDetailsService->getBankingDetailsForUser($user, $vendorId);

            return response()->json([
                'success' => true,
                'data' => $bankingDetails
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get user banking details: ' . $e->getMessage()
            ], 500);
        }
    }
}
