<?php

namespace App\Http\Controllers\Backend\Approval;

use App\Http\Controllers\Controller;
use App\Models\PaymentNote;
use App\Models\PaymentNoteApprovalLog;
use App\Models\PaymentNoteApprovalPriority;
use App\Models\PaymentNoteApprovalStep;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\NoteStatusChangeMail;
use App\Mail\UpdateRoleMail;
use Illuminate\Support\Facades\Mail;

class PaymentNoteApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

        $rule = PaymentNoteApprovalStep::create([
            'min_amount' => $request->min_amount,
            'max_amount' => $request->max_amount ?: null,
        ]);
        //
        foreach ($request->approvers as $approver) {
            if (!empty($approver['user_id']) && !empty($approver['approver_level'])) {
                PaymentNoteApprovalPriority::create([
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
        $users = \App\Services\RoleService::getUsersWithRoles(['PN Approver', 'approver', 'reviewer']);

        $step = PaymentNoteApprovalStep::with('approvers.user')->findOrFail($id);

        return view('backend..paymentNote.rule.show', compact('step', 'users'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // $step = PaymentNoteApprovalStep::with('reviewers')->findOrFail($id);
        $users = \App\Services\RoleService::getUsersWithRoles(['PN Approver', 'approver', 'reviewer']);

        $step = PaymentNoteApprovalStep::with('approvers.user')->findOrFail($id);

        return view('backend..paymentNote.rule.edit', compact('step', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $paymentRule = PaymentNoteApprovalStep::findOrFail($id);

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
            PaymentNoteApprovalPriority::whereIn('id', $removed)->delete();
        }
        // dd($removed);
        foreach ($request->old_approvers ?? [] as $id => $approver) {
            if (!empty($approver['reviewer_id']) && !empty($approver['approver_level'])) {
                $existing = PaymentNoteApprovalPriority::find($id);

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
                PaymentNoteApprovalPriority::create([
                    'approval_step_id' => $paymentRule->id,
                    'reviewer_id' => $approver['reviewer_id'],
                    'approver_level' => $approver['approver_level'],
                ]);
            }
        }

        // foreach ($request->approvers as $approver) {
        //     if (!empty($approver['reviewer_id']) && !empty($approver['approver_level'])) {
        //         PaymentNoteApprovalPriority::create([
        //             'approval_step_id' => $paymentRule->id,
        //             'reviewer_id' => $approver['reviewer_id'],
        //             'approver_level' => $approver['approver_level'],
        //         ]);
        //     }
        // }
        $userRole = auth()->user();

        if ($userRole->id === '1' && $userRole->hasRole('Super Admin Live')) {
            // $recipients = ['daliyatimmy@nhit.co.in', 'ravinderkumar@nhit.co.in', 'rinkal@nhit.co.in'];
            $recipients = ['dharmendrameel@nhit.co.in', 'ravinderkumar@nhit.co.in', '	ravivij@nhit.co.in'];

            $data = [
                'updated_by' => auth()->user()->email,
            ];

            Mail::to($recipients)->send(new UpdateRoleMail($data));
        }
        activity('Payment Rule Updated')
            ->performedOn($paymentRule)
            ->causedBy(auth()->user())
            ->event('updated')
            ->withProperties([
                'paymentRule_id' => $paymentRule->id,
                'paymentRule_name' => 'New ',
                'updated_by' => auth()->user()->name,
                'updated_by_email' => auth()->user()->email,
            ])
            ->log("Payment Rule '{$paymentRule->id}' updated by " . auth()->user()->name);
        return redirect()->back()->with('success', 'Reviewers updated successfully!!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function approvalLogUpdate(Request $request, $id)
    {
        $note = PaymentNote::findOrFail($id);
        $existingLogsCount = PaymentNoteApprovalLog::where('payment_note_id', $note->id)->count();
        $existingLogsCountFirst = PaymentNoteApprovalLog::where('payment_note_id', $note->id)->first();
        $userAlreadySubmitted = PaymentNoteApprovalLog::where('payment_note_id', $note->id)
            ->where('reviewer_id', auth()->id())
            ->where('status', '!=', 'P')
            ->exists();

        if ($userAlreadySubmitted) {
            return redirect()->back()->with('error', 'You have already submitted your approval.');
        }

        $approvalStepNew = PaymentNoteApprovalPriority::find($existingLogsCountFirst->priority_id);
        $nextStep = $existingLogsCount + 1;
        $approvalStepCurrent = PaymentNoteApprovalPriority::where('approver_level', $existingLogsCount)->where('approval_step_id', $approvalStepNew->approval_step_id)->first();
        $approvalStep = PaymentNoteApprovalPriority::where('approver_level', $nextStep)->where('approval_step_id', $approvalStepNew->approval_step_id)->first();
        $allApprovalStep = PaymentNoteApprovalPriority::where('approver_level', $nextStep)->where('approval_step_id', $approvalStepNew->approval_step_id)->pluck('id')->toArray();

        if (!$approvalStepCurrent) {
            return redirect()->back()->with('success', 'Approval step 1 not found.');
        }

        if (!$approvalStep) {
            PaymentNoteApprovalLog::create([
                'payment_note_id' => $note->id,
                'priority_id' => $approvalStepCurrent->id,
                'reviewer_id' => auth()->id(),
                'status' => $request->status,
                'comments' => $request->remarks ?? null,
            ]);

            if ($note->greenNote) {
                $name = $note->greenNote->supplier->vendor_name ?? null;
                $nameProject = $note->greenNote->vendor->project;
            } elseif ($note->reimbursementNote) {
                $name = $note->reimbursementNote->project->project;
                $nameProject = $note->reimbursementNote->project->project;
            } else {
                $name = '';
                $nameProject = '';
            }

            $data = [
                'updated_by' => auth()->user()->email,
                'subject' => 'Payment Note for ' . $name . ' of Rs ' . $note->net_payable_round_off . ' is Approved & due for NEFT/RTGS.',
                'approver_name' => $approvalStepCurrent->user->name == auth()->user()->name ? 'Maker' : $approvalStepCurrent->user->name,
                'maker' => $approvalStepCurrent->user->name . ' has Approved the Payment Note No. ' . $note->note_no . ' for ' . $name . ' of Rs ' . $note->net_payable_round_off . ' for ' . $nameProject . ' kindly pay through RTGS/NEFT.',
                'end' => 'Login to the panel for review & process.',
            ];
            if ($note->greenNote) {
                $note->greenNote->status = 'PNA';
                $note->greenNote->save();
            }

            // Mail::to('girabo8955@forcrack.com')->send(new NoteStatusChangeMail($data));
            Mail::to($note->user->email)->send(new NoteStatusChangeMail($data));
            $note->status = $request->status;
            $note->save();
            return redirect()->route('backend.payment-note.index')->with('success', 'Final step reached. No further approvals needed.');
        }

        if ($request->status == 'A') {
            $log = PaymentNoteApprovalLog::create([
                'payment_note_id' => $note->id,
                'priority_id' => $approvalStep->id,
                'reviewer_id' => auth()->id(),
                'status' => $request->status,
                'comments' => $request->remarks ?? null,
            ]);
            if (!empty($allApprovalStep)) {
                $log->priorities()->attach($allApprovalStep);
            }
            if ($note->greenNote) {
                $name = $note->greenNote->supplier->vendor_name ?? null;
                $nameProject = $note->greenNote->vendor->project;
            } elseif ($note->reimbursementNote) {
                $name = $note->reimbursementNote->project->project;
                $nameProject = $note->reimbursementNote->project->project;
            } else {
                $name = '';
                $nameProject = '';
            }

            // Mail Send
            $data = [
                'updated_by' => auth()->user()->email,
                'subject' => 'Payment Note for ' . $name . 'of Rs ' . $note->net_payable_round_off . ' is due for Approval',
                'approver_name' => $approvalStep->user->name ?? 'Approver',
                'maker' => $note->user->name . ' generated a Payment Note ' . $note->note_no . ' for ' . $name . ' of Rs ' . $note->net_payable_round_off . ' for ' . $nameProject . ' & due for your review.',
                'end' => 'Login to the panel for review & Approval',
            ];

            // 📧 Mail for Maker/User
            $makerData = [
                'updated_by' => 'Maker',
                'subject' => 'Your Payment Note ' . $note->note_no . ' has been sent for Approval',
                'approver_name' => $approvalStep->user->name ?? 'Approver',
                'maker' => 'You created a Payment Note ' . $note->note_no . ' for ' . $name . ' of Rs ' . $note->net_payable_round_off . ' for ' . $nameProject . '. It has been forwarded to ' . ($approvalStep->user->name ?? 'Approver') . ' for approval.',
                'end' => 'You will be notified once it is reviewed.',
            ];
            // Mail Send
            // Mail::to('girabo8955@forcrack.com')->send(new NoteStatusChangeMail($data));
            if ($approvalStep->user->email !== auth()->user()->email) {
                Mail::to($approvalStep->user->email)->send(new NoteStatusChangeMail($data));
            }
            Mail::to($note->user->email)->send(new NoteStatusChangeMail($makerData));
            $note->status = 'S';
            $note->save();
            return redirect()->route('backend.payment-note.index')->with('success', 'Approval log created for the next step.');
        } else {
            PaymentNoteApprovalLog::create([
                'priority_id' => $approvalStepCurrent->id,
                'payment_note_id' => $note->id,
                'reviewer_id' => auth()->id(),
                'status' => $request->status,
                'comments' => $request->remarks ?? null,
            ]);
            // Mail Send

            if ($note->greenNote) {
                $name = $note->greenNote->supplier->vendor_name ?? null;
                $nameProject = $note->greenNote->vendor->project;
                $note->greenNote->status = 'A';
                $note->greenNote->save();
            } elseif ($note->reimbursementNote) {
                $name = $note->reimbursementNote->project->project;
                $nameProject = $note->reimbursementNote->project->project;
                $note->reimbursementNote->status = 'A';
                $note->reimbursementNote->save();
            } else {
                $name = '';
                $nameProject = '';
            }

            $data = [
                'updated_by' => auth()->user()->email,
                'subject' => 'Payment Note for' . $name . 'of Rs ' . $note->net_payable_round_off . ' has been Rejected',
                'approver_name' => $approvalStepCurrent->user->name ?? 'Approver',
                'maker' => $approvalStepCurrent->user->name . ' has Rejected The payment Note No. ' . $note->note_no . ' for ' . $name . ' of Rs ' . $note->net_payable_round_off . ' for ' . $nameProject,
                'rejection' => $request->remarks ?? null,
                'end' => 'Login to the panel for review & process.',
            ];
            // Mail Send
            // Mail::to('goseg75168@doishy.com')->send(new NoteStatusChangeMail($data));
            Mail::to($note->user->email)->send(new NoteStatusChangeMail($data));
            $note->status = 'R';
            $note->save();
            return redirect()->route('backend.payment-note.index')->with('success', 'Approval has been rejected successfully with remarks.');
        }
    }
}
