<?php

namespace App\Http\Controllers\Backend\VendorAccount;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\VendorAccount;
use App\Services\VendorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class VendorAccountController extends Controller
{
    protected $vendorService;

    public function __construct(VendorService $vendorService)
    {
        $this->vendorService = $vendorService;
    }

    /**
     * Display a listing of vendor accounts
     */
    public function index(Vendor $vendor)
    {
        $accounts = $this->vendorService->getVendorAccounts($vendor);
        
        return view('backend.vendor.accounts.index', compact('vendor', 'accounts'));
    }

    /**
     * Show the form for creating a new vendor account
     */
    public function create(Vendor $vendor)
    {
        return view('backend.vendor.accounts.create', compact('vendor'));
    }

    /**
     * Store a newly created vendor account
     */
    public function store(Request $request, Vendor $vendor)
    {
        $request->validate([
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'account_type' => 'nullable|string|max:50',
            'name_of_bank' => 'required|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'ifsc_code' => 'required|string|max:20',
            'swift_code' => 'nullable|string|max:20',
            'is_primary' => 'boolean',
            'remarks' => 'nullable|string',
        ]);

        try {
            $account = $this->vendorService->createVendorAccount($vendor, $request->all());

            return redirect()
                ->route('backend.vendor.accounts.index', $vendor)
                ->with('success', 'Vendor account created successfully.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create vendor account: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified vendor account
     */
    public function show(Vendor $vendor, VendorAccount $account)
    {
        return view('backend.vendor.accounts.show', compact('vendor', 'account'));
    }

    /**
     * Show the form for editing the specified vendor account
     */
    public function edit(Vendor $vendor, VendorAccount $account)
    {
        return view('backend.vendor.accounts.edit', compact('vendor', 'account'));
    }

    /**
     * Update the specified vendor account
     */
    public function update(Request $request, Vendor $vendor, VendorAccount $account)
    {
        $request->validate([
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'account_type' => 'nullable|string|max:50',
            'name_of_bank' => 'required|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'ifsc_code' => 'required|string|max:20',
            'swift_code' => 'nullable|string|max:20',
            'is_primary' => 'boolean',
            'remarks' => 'nullable|string',
        ]);

        try {
            $account = $this->vendorService->updateVendorAccount($account, $request->all());

            return redirect()
                ->route('backend.vendor.accounts.index', $vendor)
                ->with('success', 'Vendor account updated successfully.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update vendor account: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified vendor account
     */
    public function destroy(Vendor $vendor, VendorAccount $account)
    {
        try {
            $this->vendorService->deleteVendorAccount($account);

            return redirect()
                ->route('backend.vendor.accounts.index', $vendor)
                ->with('success', 'Vendor account deleted successfully.');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Failed to delete vendor account: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle account status (active/inactive)
     */
    public function toggleStatus(Vendor $vendor, VendorAccount $account)
    {
        try {
            $account = $this->vendorService->toggleAccountStatus($account);

            $status = $account->is_active ? 'activated' : 'deactivated';
            
            return redirect()
                ->route('backend.vendor.accounts.index', $vendor)
                ->with('success', "Vendor account {$status} successfully.");

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Failed to toggle account status: ' . $e->getMessage()]);
        }
    }

    /**
     * Set account as primary
     */
    public function setPrimary(Vendor $vendor, VendorAccount $account)
    {
        try {
            $this->vendorService->updateVendorAccount($account, ['is_primary' => true]);

            return redirect()
                ->route('backend.vendor.accounts.index', $vendor)
                ->with('success', 'Account set as primary successfully.');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Failed to set account as primary: ' . $e->getMessage()]);
        }
    }

    /**
     * Get vendor banking details for AJAX requests
     */
    public function getBankingDetails(Vendor $vendor, Request $request)
    {
        $accountId = $request->get('account_id');
        
        try {
            $bankingDetails = $this->vendorService->getVendorBankingDetails($vendor, $accountId);
            
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
}
