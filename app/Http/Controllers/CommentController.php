<?php

namespace App\Http\Controllers;

use App\Mail\CommentMail;
use App\Models\ApprovalLog;
use App\Models\Comment;
use App\Models\GreenNote;
use App\Models\PaymentNote;
use App\Models\PaymentNoteApprovalLog;
use App\Models\ReimbursementNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CommentController extends Controller
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
            'note_id' => 'nullable|exists:green_notes,id',
            'payment_note_id' => 'nullable|exists:payment_notes,id',
            'reimbursement_note_id' => 'nullable|exists:reimbursement_notes,id',
            'comment' => 'required|string|max:500',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $noteTypes = [
            'green_note_id' => $request->note_id,
            'payment_note_id' => $request->payment_note_id,
            'reimbursement_note_id' => $request->reimbursement_note_id,
        ];

        $nonNullNotes = array_filter($noteTypes);

        if (count($nonNullNotes) !== 1) {
            return back()->withErrors(['note' => 'Exactly one note type ID must be provided.']);
        }

        // Create comment
        Comment::create([
            'green_note_id' => $request->note_id,
            'payment_note_id' => $request->payment_note_id,
            'reimbursement_note_id' => $request->reimbursement_note_id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
            'parent_id' => $request->parent_id,
        ]);

        $data = [
            'comment_by' => '<strong>Commented by:</strong> ' . auth()->user()->name,
            'comment_content' => '<strong>Comment:</strong> ' . ($request->comment ?? 'No content'),
            'short_name' => config('app.short_name'),
        ];

        $recipients = collect();

        if ($request->note_id) {
            $note = GreenNote::find($request->note_id);
            $data['on'] = 'Green Note';
            $data['ticket_name'] = '<strong>Green Note:</strong> ' . ($note->order_no ?? '-') . ' <strong> Date :</strong> ' . ($note->created_at->setTimezone('Asia/Kolkata')->format('d/m/Y h:i A') ?? '-');
            $data['project'] = '<strong>Project:</strong> ' . ($note->vendor->project ?? '-');
            $data['invoice_value'] = '<strong>Invoice Value:</strong> ' . ($note->total_amount ?? '-');
            $data['name_of_supplier'] = '<strong>Name Of Supplier:</strong> ' . ($note->supplier->vendor_name ?? '-');

            $noteUsers = ApprovalLog::where('green_note_id', $note->id)->with('reviewer')->get();

            foreach ($noteUsers as $log) {
                if ($log->reviewer && $log->reviewer->email) {
                    $recipients->push(['email' => $log->reviewer->email, 'name' => $log->reviewer->name]);
                }
            }

            if ($note->user && $note->user->email) {
                $recipients->push(['email' => $note->user->email, 'name' => $note->user->name]);
            }
        } elseif ($request->payment_note_id) {
            $payment = PaymentNote::find($request->payment_note_id);
            $data['on'] = 'Payment Note';
            $data['ticket_name'] = '<strong>Payment Note:</strong> ' . ($payment->note_no ?? '-');
            $data['project'] = '<strong>Project:</strong> ' . ($note->greenNote->vendor->project ?? '-');
            $data['invoice_value'] = '<strong>Invoice Value:</strong> ' . ($note->greenNote->total_amount ?? '-');
            $data['name_of_supplier'] = '<strong>Name Of Supplier:</strong> ' . ($note->greenNote->supplier->vendor_name ?? '-');

            $paymentUsers = PaymentNoteApprovalLog::where('payment_note_id', $payment->id)->with('reviewer')->get();

            foreach ($paymentUsers as $log) {
                if ($log->reviewer && $log->reviewer->email) {
                    $recipients->push(['email' => $log->reviewer->email, 'name' => $log->reviewer->name]);
                }
            }

            if ($payment->user && $payment->user->email) {
                $recipients->push(['email' => $payment->user->email, 'name' => $payment->user->name]);
            }
        } elseif ($request->reimbursement_note_id) {
            $reimbursement = ReimbursementNote::find($request->reimbursement_note_id);

            $totalPayable = $note->expenses->sum('bill_amount');
            $advanceAdjusted = $note->adjusted;
            $netPayable = $totalPayable - $advanceAdjusted;

            $data['on'] = 'Reimbursement Note';
            $data['ticket_name'] = '<strong>Reimbursement Note:</strong> ' . ($reimbursement->note_no ?? '-');

            $data['project'] = '<strong>Project:</strong> ' . ($note->project->project ?? '-');
            $data['invoice_value'] = '<strong>Invoice Value:</strong> ' . (Helper::formatIndianNumber($netPayable) ?? '-');
            $data['name_of_Supplier'] = '';

            if ($reimbursement->approver && $reimbursement->approver->email) {
                $recipients->push(['email' => $reimbursement->approver->email, 'name' => $reimbursement->approver->name]);
            }

            if ($reimbursement->user && $reimbursement->user->email) {
                $recipients->push(['email' => $reimbursement->user->email, 'name' => $reimbursement->user->name]);
            }

            if ($reimbursement->selectUser && $reimbursement->selectUser->email) {
                $recipients->push(['email' => $reimbursement->selectUser->email, 'name' => $reimbursement->selectUser->name]);
            }
        }
        // Mail::to('sovife3696@luxpolar.com')->send(new CommentMail($data));

        $recipients = $recipients->unique('email');
        foreach ($recipients as $recipient) {
            $data['name'] = $recipient['name'] ?? 'User';

            Mail::to($recipient['email'])->send(new CommentMail($data));
        }

        return redirect()->back()->with('success', 'Comment added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment, $id)
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You can only edit your own comments.');
        }

        $comment->update($validated);
        return redirect()->back()->with('success', 'Comment updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment, $id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You can only delete your own comments.');
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Comment deleted successfully');
    }
}
