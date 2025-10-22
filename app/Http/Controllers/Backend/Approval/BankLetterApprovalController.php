<?php

namespace App\Http\Controllers\Backend\Approval;

use App\Http\Controllers\Controller;
use App\Models\BankLetterApprovalPriority;
use App\Models\BankLetterApprovalStep;
use App\Models\BankLetterApprovalLog;
use App\Models\Payment;
use App\Models\User;
use App\Services\BankLetterService;
use Illuminate\Http\Request;
use App\Mail\UpdateRoleMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BankLetterApprovalController extends Controller
{
    protected $bankLetterService;
    
    public function __construct(BankLetterService $bankLetterService)
    {
        $this->bankLetterService = $bankLetterService;
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getBankLettersDataTable($request);
        }
        
        $users = \App\Services\RoleService::getUsersWithRoles(['PN Approver', 'approver', 'reviewer']);
        $approvalSteps = BankLetterApprovalStep::with('approvers.user')->get();
        $stats = $this->bankLetterService->getBankLetterStats();
        $pendingApprovals = $this->bankLetterService->getPendingApprovalsForUser(auth()->id());

        return view('backend.payment.bankLetter.index', compact('users', 'approvalSteps', 'stats', 'pendingApprovals'));
    }
    
    /**
     * Get bank letters data for DataTable
     */
    private function getBankLettersDataTable($request)
    {
        $query = Payment::select('sl_no', 'project', 'status', 'created_at', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(*) as payments_count'))
            ->groupBy('sl_no', 'project', 'status', 'created_at')
            ->with(['user', 'bankLetterApprovalLogs']);
            
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('status_badge', function ($row) {
                $statusLabels = [
                    'S' => '<span class="badge bg-warning">Pending Approval</span>',
                    'A' => '<span class="badge bg-success">Approved</span>',
                    'R' => '<span class="badge bg-danger">Rejected</span>',
                    'P' => '<span class="badge bg-info">Processing</span>',
                ];
                return $statusLabels[$row->status] ?? '<span class="badge bg-secondary">Unknown</span>';
            })
            ->addColumn('formatted_amount', function ($row) {
                return '₹' . number_format($row->total_amount, 2);
            })
            ->addColumn('formatted_date', function ($row) {
                return $row->created_at->format('d/m/Y h:i A');
            })
            ->addColumn('action', function ($row) {
                $actions = '';
                
                // View action
                $actions .= '<a href="' . route('backend.bank-letter.show-letter', $row->sl_no) . '" class="btn btn-outline-info btn-sm me-1" title="View Bank Letter">';
                $actions .= '<i class="bi bi-eye"></i></a>';
                
                // Approval action for pending letters
                if ($row->status === 'S' && $this->canUserApprove($row->sl_no)) {
                    $actions .= '<a href="' . route('backend.bank-letter.approve-form', $row->sl_no) . '" class="btn btn-outline-primary btn-sm me-1" title="Review & Approve">';
                    $actions .= '<i class="bi bi-check-circle"></i></a>';
                }
                
                // Download action for approved letters
                if ($row->status === 'A') {
                    $actions .= '<a href="' . route('backend.bank-letter.download', $row->sl_no) . '" class="btn btn-outline-success btn-sm me-1" title="Download">';
                    $actions .= '<i class="bi bi-download"></i></a>';
                }
                
                return $actions;
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }
    
    /**
     * Check if user can approve this bank letter
     */
    private function canUserApprove($slNo)
    {
        $userApprovalPriorities = BankLetterApprovalPriority::where('reviewer_id', auth()->id())->pluck('id');
        
        return BankLetterApprovalLog::where('sl_no', $slNo)
            ->whereIn('priority_id', $userApprovalPriorities)
            ->where('status', 'P')
            ->exists();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = \App\Services\RoleService::getUsersWithRoles(['PN Approver', 'approver', 'reviewer']);
        $approvalSteps = BankLetterApprovalStep::with('approvers.user')->get();

        return view('backend.payment.bankLetter.create', compact('users', 'approvalSteps'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'approvers' => 'required|array',
        ]);
        $rule = BankLetterApprovalStep::create([
            'min_amount' => $request->min_amount,
            'max_amount' => $request->max_amount ?: null,
        ]);
        //
        foreach ($request->approvers as $approver) {
            if (!empty($approver['user_id']) && !empty($approver['approver_level'])) {
                BankLetterApprovalPriority::create([
                    'approval_step_id' => $rule->id,
                    'reviewer_id' => $approver['user_id'],
                    'approver_level' => $approver['approver_level'],
                ]);
            }
        }
        // Log activity
        activity('Bank Letter Approval Step Created')
            ->performedOn($rule)
            ->causedBy(auth()->user())
            ->withProperties([
                'min_amount' => $request->min_amount,
                'max_amount' => $request->max_amount,
                'approvers_count' => count($request->approvers)
            ])
            ->log('Bank letter approval step created with ' . count($request->approvers) . ' approvers');
            
        return redirect()->back()->with('success', 'Approval Step & Reviewers Assigned Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $users = \App\Services\RoleService::getUsersWithRoles(['PN Approver', 'approver', 'reviewer']);

        $step = BankLetterApprovalStep::with('approvers.user')->findOrFail($id);

        return view('backend.payment.bankLetter.show', compact('step', 'users'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $users = \App\Services\RoleService::getUsersWithRoles(['PN Approver', 'approver', 'reviewer']);

        $step = BankLetterApprovalStep::with('approvers.user')->findOrFail($id);

        return view('backend.payment.bankLetter.edit', compact('step', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $paymentRule = BankLetterApprovalStep::findOrFail($id);

        $request->validate([
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            // 'approvers' => 'required|array',
            'old_approvers' => 'nullable|array',
            'approvers' => 'nullable|array',
        ]);
        // Update rule
        $paymentRule->update([
            'min_amount' => $request->min_amount,
            'max_amount' => $request->max_amount ?: null,
        ]);
        // Delete old approvers
        // $paymentRule->approvers()->delete();
        // $removed = explode(',', $request->input('removed_approvers'));
        if ($request->filled('removed_approvers')) {
            $removed = explode(',', $request->input('removed_approvers'));
            $removed = array_map('intval', $removed);
            BankLetterApprovalPriority::whereIn('id', $removed)->delete();
        }
        // dd($removed);
        foreach ($request->old_approvers ?? [] as $id => $approver) {
            if (!empty($approver['reviewer_id']) && !empty($approver['approver_level'])) {
                $existing = BankLetterApprovalPriority::find($id);

                if ($existing) {
                    $existing->reviewer_id = $approver['reviewer_id'];
                    $existing->approver_level = $approver['approver_level'];
                    $existing->save();
                }
            }
        }

        foreach ($request->approvers ?? [] as $id => $approver) {
            if (!empty($approver['reviewer_id']) && !empty($approver['approver_level'])) {
                // Insert new
                BankLetterApprovalPriority::create([
                    'approval_step_id' => $paymentRule->id,
                    'reviewer_id' => $approver['reviewer_id'],
                    'approver_level' => $approver['approver_level'],
                ]);
            }
        }


        $userRole = auth()->user();

        // if ($userRole->id === '1' && $userRole->hasRole('Super Admin Live')) {
        //     $recipients = ['dharmendrameel@nhit.co.in', 'ravinderkumar@nhit.co.in', '	ravivij@nhit.co.in'];

        //     $data = [
        //         'updated_by' => auth()->user()->email,
        //     ];

        //     Mail::to($recipients)->send(new UpdateRoleMail($data));
        // }
        // activity('Payment Rule Updated')
        //     ->performedOn($paymentRule)
        //     ->causedBy(auth()->user())
        //     ->event('updated')
        //     ->withProperties([
        //         'paymentRule_id' => $paymentRule->id,
        //         'paymentRule_name' => 'New ',
        //         'updated_by' => auth()->user()->name,
        //         'updated_by_email' => auth()->user()->email,
        //     ])
        //     ->log("Payment Rule '{$paymentRule->id}' updated by " . auth()->user()->name);


        return redirect()->back()->with('success', 'Reviewers updated successfully!!');
    }

    /**
     * Show bank letter details
     */
    public function showLetter($slNo)
    {
        $payments = Payment::where('sl_no', $slNo)
            ->with(['paymentNote.greenNote.supplier', 'paymentNote.reimbursementNote.user', 'user'])
            ->get();
            
        if ($payments->isEmpty()) {
            return redirect()->back()->with('error', 'Bank letter not found.');
        }
        
        $approvalLogs = BankLetterApprovalLog::where('sl_no', $slNo)
            ->with(['reviewer', 'bankLetterApprovalPriority.user'])
            ->orderBy('created_at', 'asc')
            ->get();
            
        $totalAmount = $payments->sum('amount');
        $status = $payments->first()->status;
        
        return view('backend.payment.bankLetter.show-letter', compact('payments', 'approvalLogs', 'totalAmount', 'status', 'slNo'));
    }
    
    /**
     * Show approval form
     */
    public function approveForm($slNo)
    {
        $payments = Payment::where('sl_no', $slNo)
            ->with(['paymentNote.greenNote.supplier', 'paymentNote.reimbursementNote.user', 'user'])
            ->get();
            
        if ($payments->isEmpty()) {
            return redirect()->back()->with('error', 'Bank letter not found.');
        }
        
        // Check if user can approve
        if (!$this->canUserApprove($slNo)) {
            return redirect()->back()->with('error', 'You are not authorized to approve this bank letter.');
        }
        
        $approvalLogs = BankLetterApprovalLog::where('sl_no', $slNo)
            ->with(['reviewer', 'bankLetterApprovalPriority.user'])
            ->orderBy('created_at', 'asc')
            ->get();
            
        $totalAmount = $payments->sum('amount');
        
        return view('backend.payment.bankLetter.approve', compact('payments', 'approvalLogs', 'totalAmount', 'slNo'));
    }
    
    /**
     * Process approval/rejection
     */
    public function processApproval(Request $request, $slNo)
    {
        $request->validate([
            'status' => 'required|in:A,R',
            'comments' => 'nullable|string|max:1000'
        ]);
        
        try {
            $result = $this->bankLetterService->processApproval(
                $slNo,
                $request->status,
                $request->comments,
                auth()->id()
            );
            
            return redirect()->route('backend.bank-letter.index')
                ->with('success', $result['message']);
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Download bank letter
     */
    public function download($slNo)
    {
        $payments = Payment::where('sl_no', $slNo)
            ->where('status', 'A')
            ->with(['paymentNote.greenNote.supplier', 'paymentNote.reimbursementNote.user'])
            ->get();
            
        if ($payments->isEmpty()) {
            return redirect()->back()->with('error', 'Approved bank letter not found.');
        }
        
        // Generate and return downloadable bank letter
        return $this->generateBankLetterPDF($payments, $slNo);
    }
    
    /**
     * Generate PDF for bank letter
     */
    private function generateBankLetterPDF($payments, $slNo)
    {
        // This would typically use a PDF library like DomPDF or similar
        // For now, return a view that can be printed
        $totalAmount = $payments->sum('amount');
        
        return view('backend.payment.bankLetter.pdf', compact('payments', 'slNo', 'totalAmount'));
    }
    
    /**
     * Get dashboard data
     */
    public function dashboard()
    {
        $stats = $this->bankLetterService->getBankLetterStats();
        $pendingApprovals = $this->bankLetterService->getPendingApprovalsForUser(auth()->id());
        
        $recentBankLetters = Payment::select('sl_no', 'project', 'status', 'created_at', DB::raw('SUM(amount) as total_amount'))
            ->groupBy('sl_no', 'project', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        return view('backend.payment.bankLetter.dashboard', compact('stats', 'pendingApprovals', 'recentBankLetters'));
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $step = BankLetterApprovalStep::findOrFail($id);
            
            // Check if step is being used
            $isUsed = BankLetterApprovalLog::whereHas('bankLetterApprovalPriority', function($query) use ($id) {
                $query->where('approval_step_id', $id);
            })->exists();
            
            if ($isUsed) {
                return redirect()->back()->with('error', 'Cannot delete approval step as it is being used in bank letters.');
            }
            
            $step->approvers()->delete();
            $step->delete();
            
            return redirect()->back()->with('success', 'Approval step deleted successfully.');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting approval step: ' . $e->getMessage());
        }
    }
}
