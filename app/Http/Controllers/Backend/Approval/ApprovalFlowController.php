<?php

namespace App\Http\Controllers\Backend\Approval;

use App\Http\Controllers\Controller;
use App\Mail\NoteStatusChangeMail;
use App\Mail\TestMail;
use App\Mail\UpdateRoleMail;
use App\Models\ApprovalFlow;
use App\Models\ApprovalLog;
use App\Models\ApprovalStep;
use App\Models\Department;
use App\Models\GreenNote;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ApprovalFlowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $projects = Project::with('approvalFlows.approvalSteps')->get();
        // return response()->json($projects);
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
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'name' => 'required|string|max:255',
                'vendor_id' => 'required',
                'department_id' => 'required',
                'approvers' => 'required|array',
                'amounts' => 'required|array',
            ]);
            $approvalFlow = ApprovalFlow::create([
                'vendor_id' => $validated['vendor_id'],
                'department_id' => $validated['department_id'],
                'user_id' => $validated['user_id'],
                'name' => $validated['name'],
            ]);

            if (!$approvalFlow) {
                throw new \Exception('Failed to create Approval Flow.');
            }
            activity('Role Created')
                ->performedOn($approvalFlow)
                ->causedBy(auth()->user())
                ->event('created')
                ->withProperties([
                    'approvalFlow_id' => $approvalFlow->id,
                    'approvalFlow_name' => $approvalFlow->name,
                    'created_by' => auth()->user()->name,
                    'created_by_email' => auth()->user()->email,
                ])
                ->log("Approval Flow '{$approvalFlow->name}' created by " . auth()->user()->name);

            $approvers = $request->approvers;
            $amounts = $request->amounts;
            $step = 1;
            foreach ($approvers as $index => $approver) {
                $amount = $amounts[$index] ?? null;

                // if (!empty($approver) && !empty($amount)) {
                if (!is_null($approver) && $approver !== '' && $amount !== null && $amount !== '') {
                    ApprovalStep::create([
                        'approval_flow_id' => $approvalFlow->id,
                        'step' => $step++,
                        'next_on_approve' => $approver,
                        'amount' => $amount,
                        'next_on_reject' => $approver,
                    ]);
                }
            }
            return back()->with('success', 'Approval saved successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong.' . $e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ApprovalFlow $approvalFlow, $id)
    {
        $approvalFlow = ApprovalFlow::with('approvalSteps')->findOrFail($id);
        return view('backend.greenNote.rule.show', compact('approvalFlow'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ApprovalFlow $approvalFlow, $id)
    {
        $approvalFlow = ApprovalFlow::with('approvalSteps')->findOrFail($id);
        $filteredItems = Vendor::selectRaw('id,project, COUNT(*) as total_records')->whereNotNull('project')->where('project', '!=', '')->groupBy('project')->orderBy('project', 'asc')->where('active', 'Y')->get();
        $filteredVendorItems = Vendor::where('active', 'Y')->get();
        $departments = Department::all();
        $users = User::all();
        $auditorUsers = User::role('Auditor')->get();

        return view('backend.greenNote.rule.edit', compact('approvalFlow', 'filteredItems', 'auditorUsers', 'filteredVendorItems', 'departments', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ApprovalFlow $approvalFlow, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'vendor_id' => 'required',
                'department_id' => 'required',
                'approvers' => 'required|array',
                'amounts' => 'required|array',
            ]);
            // dd($request->all());

            $approvalFlow = ApprovalFlow::findOrFail($id);

            $approvalFlow->update([
                'vendor_id' => $validated['vendor_id'],
                'department_id' => $validated['department_id'],
                'name' => $validated['name'],
            ]);
            $userRole = auth()->user();

            if ($userRole->id === '1' && $userRole->hasRole('Super Admin Live')) {
                // $recipients = ['daliyatimmy@nhit.co.in', 'ravinderkumar@nhit.co.in', 'rinkal@nhit.co.in'];
                $recipients = ['dharmendrameel@nhit.co.in', 'ravinderkumar@nhit.co.in', '	ravivij@nhit.co.in'];
                $recipients = config('notifications.approval_flow_update_recipients');

                $data = [
                    'role_name' => $approvalFlow->name,
                    'updated_by' => auth()->user()->email,
                ];

                // Send Mail to Multiple Recipients
                Mail::to($recipients)->send(new UpdateRoleMail($data));
            }
            activity('Approval Flow Updated')
                ->performedOn($approvalFlow)
                ->causedBy(auth()->user())
                ->event('updated')
                ->withProperties([
                    'approvalFlow_id' => $approvalFlow->id,
                    'approvalFlow_name' => $approvalFlow->name,
                    'updated_by' => auth()->user()->name,
                    'updated_by_email' => auth()->user()->email,
                ])
                ->log("Approval Flow '{$approvalFlow->name}' updated by " . auth()->user()->name);
            // Get existing approval steps
            $existingSteps = $approvalFlow->approvalSteps()->get();
            $amounts = array_values($request->amounts);
            foreach ($validated['approvers'] as $index => $approver) {
                if ($approver) {
                    $stepNumber = $index + 1;
                    // Find existing step
                    $step = $existingSteps->where('step', $stepNumber)->first();

                    if ($step) {
                        // Update existing step
                        $step->update([
                            'next_on_approve' => $approver,
                            'next_on_reject' => $approver,
                            'amount' => isset($amounts[$index]) ? $amounts[$index] : 0,
                        ]);
                    } else {
                        // Create new step if it doesn't exist
                        ApprovalStep::create([
                            'approval_flow_id' => $approvalFlow->id,
                            'step' => $stepNumber,
                            'next_on_approve' => $approver,
                            'next_on_reject' => $approver,
                            'amount' => isset($amounts[$index]) ? $amounts[$index] : 0,
                        ]);
                    }
                }
            }
            $approvers = array_filter($validated['approvers']);
            $maxStep   = count($approvers);

            $approvalFlow->approvalSteps()
                ->where('step', '>', $maxStep)
                ->delete();


            return redirect()->route('backend.note.rule')->with('success', 'Approval Flow updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApprovalFlow $approvalFlow, $id)
    {
        $approvalFlow = ApprovalFlow::findOrFail($id);
        $approvalStepIds = ApprovalStep::where('approval_flow_id', $approvalFlow->id)->pluck('id');
        ApprovalLog::whereIn('approval_step_id', $approvalStepIds)->delete();
        ApprovalStep::whereIn('id', $approvalStepIds)->delete();
        $approvalFlow->delete();
        return redirect()->back()->with('success', 'Approval Step and related records deleted successfully.');
    }
    public function approvalStepDestroy(ApprovalFlow $approvalFlow, $id)
    {
        $approvalStep = ApprovalStep::findOrFail($id);

        ApprovalLog::where('approval_step_id', $id)->delete();

        $approvalStep->delete();
        return redirect()->back()->with('success', 'Approval Step and related records deleted successfully.');
    }
    public function storeApprovalStep(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'approval_flow_id' => 'required|exists:approval_flows,id',
            'step' => 'nullable|integer',
            'user_id' => 'nullable|exists:users,id',
            'next_on_approve' => 'nullable|integer',
            'next_on_reject' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // $approvalStep = ApprovalStep::create($request->all());
        $lastStep = ApprovalStep::where('approval_flow_id', $request->approval_flow_id)->orderBy('step', 'desc')->first();

        // If there is a previous step, increment; otherwise, start at 1
        $stepNumber = $lastStep ? $lastStep->step + 1 : 1;

        // Create the new approval step
        $approvalStep = ApprovalStep::create([
            'approval_flow_id' => $request->approval_flow_id,
            'step' => $stepNumber,
            'user_id' => $request->user_id,
            'next_on_approve' => $request->next_on_approve,
            'next_on_reject' => $request->next_on_reject,
        ]);

        return redirect()->back()->with('success', 'Approval Step created successfully');
    }
    public function approvalLogUpdate(Request $request, $id)
    {
        // Find the Green Note
        $greenNote = GreenNote::findOrFail($id);
        // dd($request->all());
        $currentApprovalStep = ApprovalLog::where('green_note_id', $greenNote->id)->whereNot('status', 'PMPL')->latest()->first();
        $userAlreadySubmitted = ApprovalLog::where('green_note_id', $greenNote->id)
            ->where('reviewer_id', auth()->id())
            ->exists();

        if ($userAlreadySubmitted) {
            return redirect()->back()->with('error', 'You have already submitted your approval.');
        }

        if (!$currentApprovalStep) {
            return redirect()->back()->with('error', 'Approval log not found.');
        }
        $existingLogsCount = ApprovalLog::where('green_note_id', $greenNote->id)->whereNot('status', 'PMPL')->count();

        $nextStep = $existingLogsCount + 1;
        // Get the next step based on next_on_approve
        $nextApprovalStep = ApprovalStep::where('approval_flow_id', $currentApprovalStep->approvalStep->approval_flow_id)->where('step', $nextStep)->first();

        if ($request->status == 'A') {
            // if ($nextApprovalStep && $greenNote->total_amount >= $nextApprovalStep->amount) {
            if ($nextApprovalStep && $greenNote->invoice_value >= $nextApprovalStep->amount) {
                $data = [
                    'updated_by' => auth()->user()->email,
                    'subject' => 'Expense Approval Note for ' . $greenNote->supplier->vendor_name ?? null . ' of Rs ' . $greenNote->invoice_value . ' has been Generated',
                    'approver_name' => $nextApprovalStep->nextOnApprove->name ?? 'Approver',
                    'maker' => $greenNote->user->name . ' has generated a Expense Approval Note No. ' . $greenNote->formatted_order_no . ' for ' . $greenNote->supplier->vendor_name ?? null . ' of Rs ' . $greenNote->invoice_value . ' for ' . $greenNote->vendor->project . ' & due for your approval.',
                    'end' => 'Login to the panel for review & Approve/Reject.',
                ];
                // Mail Send
                // Mail::to('goseg75168@doishy.com')->send(new NoteStatusChangeMail($data));

                Mail::to($nextApprovalStep->nextOnApprove->email)->send(new NoteStatusChangeMail($data));
                Mail::to($greenNote->user->email)->send(new NoteStatusChangeMail($data));
                ApprovalLog::create([
                    'approval_step_id' => $nextApprovalStep->id,
                    'green_note_id' => $greenNote->id,
                    'reviewer_id' => auth()->id(),
                    'status' => $request->status,
                    'comments' => null,
                ]);

                return redirect()->back()->with('success', 'Approval log created for the next step.');
            } else {
                $data = [
                    'updated_by' => auth()->user()->email,
                    'subject' => 'Expense Approval Note for ' . $greenNote->supplier->vendor_name . ' of Rs ' . $greenNote->invoice_value . ' is Approved & due for review / Payment',
                    'approver_name' => 'Maker',
                    'maker' => $greenNote->user->name . ' has generated a Expenses Approval Note No. ' . $greenNote->formatted_order_no . ' for ' . $greenNote->supplier->vendor_name ?? null . ' of Rs ' . $greenNote->invoice_value . ' for ' . $greenNote->vendor->project . '& due for your review / Payment.',
                    'end' => 'Login to the panel for review & process. ',
                ];
                // Mail Send
                $users = User::role('PN User')->get();
                foreach ($users as $key => $value) {
                    Mail::to($value->email)->send(new NoteStatusChangeMail($data));
                }

                // Mail::to('goseg75168@doishy.com')->send(new NoteStatusChangeMail($data));
                Mail::to($greenNote->user->email)->send(new NoteStatusChangeMail($data));
                ApprovalLog::create([
                    'approval_step_id' => $currentApprovalStep->approval_step_id,
                    'green_note_id' => $greenNote->id,
                    'reviewer_id' => auth()->id(),
                    'status' => $request->status,
                    'comments' => null,
                ]);

                $greenNote->status = 'A';
                $greenNote->save();
                return redirect()->route('backend.note.index')->with('success', 'Final step reached. No further approvals needed.');
            }
        } else {
            // dd($currentApprovalStep->approvalStep->nextOnApprove->name);
            $data = [
                'updated_by' => auth()->user()->email,
                'subject' => 'Expense Approval Note for ' . $greenNote->supplier->vendor_name ?? null . ' of Rs ' . $greenNote->invoice_value . ' has been Rejected',
                'approver_name' => $greenNote->user->name ?? 'Approver',
                'maker' => $currentApprovalStep->approvalStep->nextOnApprove->name . ' has Rejected the Expense Approval Note No. ' . $greenNote->formatted_order_no . ' for ' . $greenNote->supplier->vendor_name ?? null . ' of Rs ' . $greenNote->invoice_value . ' for ' . $greenNote->vendor->project,
                'rejection' => $request->remarks ?? null,
                'end' => 'Login to the panel for review & Re-process',
            ];
            // Mail Send
            // Mail::to('goseg75168@doishy.com')->send(new NoteStatusChangeMail($data));
            Mail::to($greenNote->user->email)->send(new NoteStatusChangeMail($data));
            ApprovalLog::create([
                'approval_step_id' => $currentApprovalStep->approval_step_id,
                'green_note_id' => $greenNote->id,
                'reviewer_id' => auth()->id(),
                'status' => $request->status,
                'comments' => $request->remarks,
            ]);

            $greenNote->status = 'R';
            $greenNote->save();
            return redirect()->route('backend.note.index')->with('success', 'Approval has been rejected successfully with remarks.');
        }
    }

    public function sendApproval(Request $request, $id)
    {
        // Creating the note entry
        $greenNote = GreenNote::find($id);
        // dd($request->all());
        $approvalFlow = ApprovalFlow::where('vendor_id', $request->vendor_id)->where('department_id', $request->department_id)->first();

        if (!$approvalFlow) {
            return redirect()->back()->with('success', 'Approval flow not found.');
        }
        $existingLogsCount = ApprovalLog::where('green_note_id', $greenNote->id)->whereNot('status', 'PMPL')->count();

        // Get the next step based on existing logs
        $nextStep = $existingLogsCount + 1;
        // dd($nextStep);
        // Find Step 1 of the Approval Flow
        $approvalStep = ApprovalStep::where('approval_flow_id', $approvalFlow->id)->where('step', $nextStep)->first();

        if (!$approvalStep) {
            return redirect()->back()->with('success', 'Approval step 1 not found.');
        } else {
            $data = [
                'updated_by' => auth()->user()->email,
                'subject' => 'Green Note (' . $greenNote->formatted_order_no . ') is due for review',
                'approver_name' => $nextApprovalStep->nextOnApprove->name ?? 'Approver',
                'maker' => $greenNote->user->name . ' generated a Green Note' . '(' . $greenNote->formatted_order_no . ') is due for review',
                'end' => 'Login to the panel for review & process. ',
            ];

            // Mail Send
            // Mail::to('goseg75168@doishy.com')->send(new NoteStatusChangeMail($data));
            if ($nextApprovalStep->nextOnApprove->email !== auth()->user()->email) {
                Mail::to($nextApprovalStep->nextOnApprove->email)->send(new NoteStatusChangeMail($data));
            }
            Mail::to($greenNote->user->email)->send(new NoteStatusChangeMail($data));
            // dd(1);
        }
        // dd($greenNote->id);
        // Create Approval Log
        ApprovalLog::create([
            'approval_step_id' => $approvalStep->id,
            'green_note_id' => $greenNote->id,
            'reviewer_id' => auth()->id(),
            'status' => 'P',
            'comments' => null,
        ]);

        return redirect()->back()->with('success', 'Updated successfully');
    }
}
