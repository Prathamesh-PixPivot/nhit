<?php

namespace App\Http\Controllers\backend\Ticket;

use App\Http\Controllers\Controller;
use App\Mail\CommentMail;
use App\Mail\TicketMail;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TicketCommentController extends Controller
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
        try {
            $request->validate([
                'ticket_id' => 'required|exists:tickets,id',
                'content' => 'required|string',
                'parent_id' => 'nullable|exists:comments,id',
            ]);

            $comment = TicketComment::create([
                'ticket_id' => $request->ticket_id,
                'user_id' => auth()->id(),
                'content' => $request->content,
                'parent_id' => $request->parent_id,
            ]);
            $ticket = Ticket::with('user')->findOrFail($request->ticket_id);

            $data = [
                'on' => 'ticket',
                'ticket_name' => '<strong>Ticket:</strong> ' . $ticket->name ?? '-',
                'short_name' => config('app.short_name'),
                'comment_by' => '<strong>Commented by:</strong> ' . auth()->user()->name,
                'comment_content' => '<strong>Comment:</strong> ' . $request->content ?? 'No content',
            ];

            // Get admin user
            $adminUser = User::role('ticket')->first();

            // ✅ Send mail based on who commented
            if (auth()->id() === $adminUser->id) {
                if ($ticket->user && $ticket->user->email) {
                    $data['name'] = $ticket->user->name ?? '-';
                    Mail::to($ticket->user->email)->send(new CommentMail($data));
                }
            } else {
                if ($adminUser && $adminUser->email) {
                    $data['name'] = $adminUser->name ?? '-';
                    Mail::to($adminUser->email)->send(new CommentMail($data));
                }
            }

            return back()->with('success', 'Comment added successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add comment. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TicketComment $ticketComment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TicketComment $ticketComment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $ticketComment = TicketComment::findOrFail($id);

            if (auth()->id() !== $ticketComment->user_id) {
                abort(403, 'Unauthorized action.');
            }

            $request->validate(['content' => 'required|string']);

            $ticketComment->update(['content' => $request->content]);

            $ticket = Ticket::with('user')->findOrFail($ticketComment->ticket_id);

            $data = [
                'on' => 'ticket',
                'ticket_name' => '<strong>Ticket:</strong> ' . $ticket->name ?? '-',
                'comment_by' => '<strong>Commented by:</strong> ' . auth()->user()->name,
                'comment_content' => '<strong>Comment:</strong> ' . $request->content ?? 'No content',
            ];

            // Get admin user
            $adminUser = User::role('ticket')->first();

            // ✅ Send mail based on who commented
            if (auth()->id() === $adminUser->id) {
                if ($ticket->user && $ticket->user->email) {
                    $data['name'] = $ticket->user->name ?? '-';
                    Mail::to($ticket->user->email)->send(new CommentMail($data));
                }
            } else {
                if ($adminUser && $adminUser->email) {
                    $data['name'] = $adminUser->name ?? '-';
                    Mail::to($adminUser->email)->send(new CommentMail($data));
                }
            }
            return back()->with('success', 'Comment updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update comment. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $ticketComment = TicketComment::findOrFail($id);

            if (auth()->id() !== $ticketComment->user_id) {
                abort(403, 'Unauthorized action.');
            }

            $ticketComment->delete();

            return back()->with('success', 'Comment deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete comment. Please try again.');
        }
    }
}
