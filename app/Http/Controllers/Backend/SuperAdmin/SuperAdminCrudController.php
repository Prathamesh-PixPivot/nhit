<?php

namespace App\Http\Controllers\Backend\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\GreenNote;
use App\Models\PaymentNote;
use App\Models\Vendor;
use App\Models\VendorAccount;
use App\Models\User;
use App\Models\Department;
use App\Services\PaymentNoteService;
use App\Services\GreenNoteService;
use App\Services\VendorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SuperAdminCrudController extends Controller
{
    protected $paymentNoteService;
    protected $greenNoteService;
    protected $vendorService;

    public function __construct(
        PaymentNoteService $paymentNoteService,
        GreenNoteService $greenNoteService,
        VendorService $vendorService
    ) {
        // Ensure only SuperAdmin can access
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasRole('Super Admin')) {
                abort(403, 'SuperAdmin access required.');
            }
            return $next($request);
        });

        $this->paymentNoteService = $paymentNoteService;
        $this->greenNoteService = $greenNoteService;
        $this->vendorService = $vendorService;
    }

    /**
     * SuperAdmin Dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_green_notes' => GreenNote::count(),
            'total_payment_notes' => PaymentNote::count(),
            'total_vendors' => Vendor::count(),
            'total_users' => User::count(),
            'draft_payment_notes' => PaymentNote::where('is_draft', true)->count(),
            'held_green_notes' => GreenNote::where('status', 'H')->count(),
            'pending_approvals' => GreenNote::where('status', 'P')->count(),
        ];

        return view('backend.superadmin.dashboard', compact('stats'));
    }

    /**
     * Payment Notes Management
     */
    public function paymentNotes()
    {
        $paymentNotes = PaymentNote::with(['greenNote', 'reimbursementNote', 'user', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('backend.superadmin.payment-notes.index', compact('paymentNotes'));
    }

    public function createPaymentNote()
    {
        $greenNotes = GreenNote::where('status', 'A')
            ->with(['supplier', 'department'])
            ->orderBy('created_at', 'desc')
            ->get();

        $users = User::active()->orderBy('name')->get();

        return view('backend.superadmin.payment-notes.create', compact('greenNotes', 'users'));
    }

    public function storePaymentNote(Request $request)
    {
        $request->validate([
            'green_note_id' => 'nullable|exists:green_notes,id',
            'reimbursement_note_id' => 'nullable|exists:reimbursement_notes,id',
            'user_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:1000',
            'recommendation_of_payment' => 'required|string|max:1000',
            'is_draft' => 'boolean',
            'add_particulars' => 'nullable|array',
            'less_particulars' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            $paymentNote = PaymentNote::create([
                'green_note_id' => $request->green_note_id,
                'reimbursement_note_id' => $request->reimbursement_note_id,
                'user_id' => $request->user_id,
                'created_by' => auth()->id(),
                'note_no' => PaymentNote::generateOrderNumber(),
                'subject' => $request->subject,
                'recommendation_of_payment' => $request->recommendation_of_payment,
                'add_particulars' => $request->add_particulars,
                'less_particulars' => $request->less_particulars,
                'status' => $request->is_draft ? 'D' : 'P',
                'is_draft' => $request->is_draft ?? false,
                'auto_created' => false,
            ]);

            DB::commit();

            return redirect()
                ->route('backend.superadmin.payment-notes.show', $paymentNote)
                ->with('success', 'Payment note created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create payment note: ' . $e->getMessage()]);
        }
    }

    public function showPaymentNote(PaymentNote $paymentNote)
    {
        $paymentNote->load(['greenNote', 'reimbursementNote', 'user', 'createdBy', 'paymentApprovalLogs']);
        return view('backend.superadmin.payment-notes.show', compact('paymentNote'));
    }

    public function editPaymentNote(PaymentNote $paymentNote)
    {
        $greenNotes = GreenNote::where('status', 'A')
            ->with(['supplier', 'department'])
            ->orderBy('created_at', 'desc')
            ->get();

        $users = User::active()->orderBy('name')->get();

        return view('backend.superadmin.payment-notes.edit', compact('paymentNote', 'greenNotes', 'users'));
    }

    public function updatePaymentNote(Request $request, PaymentNote $paymentNote)
    {
        $request->validate([
            'subject' => 'required|string|max:1000',
            'recommendation_of_payment' => 'required|string|max:1000',
            'add_particulars' => 'nullable|array',
            'less_particulars' => 'nullable|array',
            'status' => 'required|in:D,P,A,R,S',
        ]);

        try {
            $paymentNote->update([
                'subject' => $request->subject,
                'recommendation_of_payment' => $request->recommendation_of_payment,
                'add_particulars' => $request->add_particulars,
                'less_particulars' => $request->less_particulars,
                'status' => $request->status,
                'is_draft' => $request->status === 'D',
            ]);

            return redirect()
                ->route('backend.superadmin.payment-notes.show', $paymentNote)
                ->with('success', 'Payment note updated successfully.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update payment note: ' . $e->getMessage()]);
        }
    }

    public function destroyPaymentNote(PaymentNote $paymentNote)
    {
        try {
            $paymentNote->delete();

            return redirect()
                ->route('backend.superadmin.payment-notes.index')
                ->with('success', 'Payment note deleted successfully.');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Failed to delete payment note: ' . $e->getMessage()]);
        }
    }

    /**
     * Green Notes Management
     */
    public function greenNotes()
    {
        $greenNotes = GreenNote::with(['department', 'vendor', 'supplier', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('backend.superadmin.green-notes.index', compact('greenNotes'));
    }

    public function createGreenNote()
    {
        $departments = Department::active()->orderBy('name')->get();
        $vendors = Vendor::active()->orderBy('vendor_name')->get();
        $users = User::active()->orderBy('name')->get();

        return view('backend.superadmin.green-notes.create', compact('departments', 'vendors', 'users'));
    }

    public function showGreenNote(GreenNote $greenNote)
    {
        $greenNote->load(['department', 'vendor', 'supplier', 'user', 'approvalLogs', 'paymentNotes']);
        return view('backend.superadmin.green-notes.show', compact('greenNote'));
    }

    public function editGreenNote(GreenNote $greenNote)
    {
        $departments = Department::active()->orderBy('name')->get();
        $vendors = Vendor::active()->orderBy('vendor_name')->get();
        $users = User::active()->orderBy('name')->get();

        return view('backend.superadmin.green-notes.edit', compact('greenNote', 'departments', 'vendors', 'users'));
    }

    public function destroyGreenNote(GreenNote $greenNote)
    {
        try {
            $greenNote->delete();

            return redirect()
                ->route('backend.superadmin.green-notes.index')
                ->with('success', 'Green note deleted successfully.');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Failed to delete green note: ' . $e->getMessage()]);
        }
    }

    /**
     * Vendors Management
     */
    public function vendors()
    {
        $vendors = Vendor::with(['accounts'])
            ->orderBy('vendor_name')
            ->paginate(50);

        return view('backend.superadmin.vendors.index', compact('vendors'));
    }

    public function createVendor()
    {
        return view('backend.superadmin.vendors.create');
    }

    public function storeVendor(Request $request)
    {
        $request->validate([
            'vendor_name' => 'required|string|max:255',
            'vendor_email' => 'nullable|email|max:255',
            'vendor_mobile' => 'nullable|string|max:20',
            'vendor_type' => 'nullable|string|max:100',
            'gstin' => 'nullable|string|max:15|unique:vendors,gstin',
            'pan' => 'nullable|string|max:10|unique:vendors,pan',
            'msme_classification' => 'nullable|string|max:100',
        ]);

        try {
            $vendor = $this->vendorService->createVendor($request->all());

            return redirect()
                ->route('backend.superadmin.vendors.show', $vendor)
                ->with('success', 'Vendor created successfully.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create vendor: ' . $e->getMessage()]);
        }
    }

    public function showVendor(Vendor $vendor)
    {
        $vendor->load(['accounts']);
        return view('backend.superadmin.vendors.show', compact('vendor'));
    }

    public function editVendor(Vendor $vendor)
    {
        return view('backend.superadmin.vendors.edit', compact('vendor'));
    }

    public function updateVendor(Request $request, Vendor $vendor)
    {
        $request->validate([
            'vendor_name' => 'required|string|max:255',
            'vendor_email' => 'nullable|email|max:255',
            'vendor_mobile' => 'nullable|string|max:20',
            'vendor_type' => 'nullable|string|max:100',
            'gstin' => 'nullable|string|max:15|unique:vendors,gstin,' . $vendor->id,
            'pan' => 'nullable|string|max:10|unique:vendors,pan,' . $vendor->id,
            'msme_classification' => 'nullable|string|max:100',
            'active' => 'boolean',
        ]);

        try {
            $vendor->update($request->all());

            return redirect()
                ->route('backend.superadmin.vendors.show', $vendor)
                ->with('success', 'Vendor updated successfully.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update vendor: ' . $e->getMessage()]);
        }
    }

    public function destroyVendor(Vendor $vendor)
    {
        try {
            $vendor->delete();

            return redirect()
                ->route('backend.superadmin.vendors.index')
                ->with('success', 'Vendor deleted successfully.');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Failed to delete vendor: ' . $e->getMessage()]);
        }
    }

    /**
     * Users Management
     */
    public function users()
    {
        $users = User::with(['roles', 'department'])
            ->orderBy('name')
            ->paginate(50);

        return view('backend.superadmin.users.index', compact('users'));
    }

    /**
     * System Statistics
     */
    public function systemStats()
    {
        $stats = [
            'database' => [
                'green_notes' => GreenNote::count(),
                'payment_notes' => PaymentNote::count(),
                'vendors' => Vendor::count(),
                'vendor_accounts' => VendorAccount::count(),
                'users' => User::count(),
            ],
            'status_breakdown' => [
                'green_notes' => [
                    'draft' => GreenNote::where('status', 'D')->count(),
                    'pending' => GreenNote::where('status', 'P')->count(),
                    'approved' => GreenNote::where('status', 'A')->count(),
                    'rejected' => GreenNote::where('status', 'R')->count(),
                    'held' => GreenNote::where('status', 'H')->count(),
                ],
                'payment_notes' => [
                    'draft' => PaymentNote::where('status', 'D')->count(),
                    'pending' => PaymentNote::where('status', 'P')->count(),
                    'approved' => PaymentNote::where('status', 'A')->count(),
                    'rejected' => PaymentNote::where('status', 'R')->count(),
                ],
            ],
        ];

        return view('backend.superadmin.stats', compact('stats'));
    }
}
