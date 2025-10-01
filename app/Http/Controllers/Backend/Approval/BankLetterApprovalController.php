<?php

namespace App\Http\Controllers\Backend\Approval;

use App\Http\Controllers\Controller;
use App\Models\BankLetterApprovalPriority;
use App\Models\BankLetterApprovalStep;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\UpdateRoleMail;
use Illuminate\Support\Facades\Mail;

class BankLetterApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::role('PN Approver')->get();
        $approvalSteps = BankLetterApprovalStep::with('approvers.user')->get();

        return view('backend.payment.bankLetter.create', compact('users', 'approvalSteps'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
        return redirect()->back()->with('success', 'Approval Step & Reviewers Assigned Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $users = User::role('PN Approver')->get();

        $step = BankLetterApprovalStep::with('approvers.user')->findOrFail($id);

        return view('backend.payment.bankLetter.show', compact('step', 'users'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $users = User::role('PN Approver')->get();

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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
