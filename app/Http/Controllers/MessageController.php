<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Folder;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MessageController extends Controller
{
    //

    public function sendMessage(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'body' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|max:10240', // 10MB max size
        ]);

        // Check if a conversation exists between sender and recipient
        $conversation = Conversation::where(function ($q) use ($request) {
            $q->where('user_one_id', auth()->id())->where('user_two_id', $request->recipient_id);
        })->orWhere(function ($q) use ($request) {
            $q->where('user_one_id', $request->recipient_id)->where('user_two_id', auth()->id());
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_one_id' => auth()->id(),
                'user_two_id' => $request->recipient_id,
            ]);
        }

        // Upload attachment if provided
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments');
        }

        // Create the message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'recipient_id' => $request->recipient_id,
            'body' => $request->body,
            'attachment' => $attachmentPath,
        ]);

        return response()->json(['message' => 'Message sent successfully', 'data' => $message], 201);
    }

    public function inbox()
    {
        $conversations = Conversation::where('user_one_id', auth()->id())
            ->orWhere('user_two_id', auth()->id())
            ->with(['messages' => function ($q) {
                $q->orderBy('created_at', 'desc');
            }])
            ->get();

        return view('conversations/inbox', compact('conversations'));
    }

    /**
     * Show the form for composing a new message.
     * 
     * @return \Illuminate\View\View
     */
    public function compose()
    {
        // Get all users except the current authenticated user
        $users = User::where('id', '!=', auth()->id())->get();

        // Return the view with the list of users to select as the recipient
        return view('conversations/compose', compact('users'));
    }

    public function send(Request $request)
    {
        // Validate the request inputs
        $request->validate([
            'recipient_id' => 'required|exists:users,id', // This ensures the recipient exists
            'body' => 'required|string',
            'attachment' => 'nullable|file',
        ]);

        // Check if conversation exists or create a new one
        $conversation = Conversation::firstOrCreate([
            'user_one_id' => auth()->id(),
            'user_two_id' => $request->recipient_id,
        ]);

        // Create a new message
        $message = new Message();
        $message->conversation_id = $conversation->id;
        $message->sender_id = auth()->id();
        $message->recipient_id = $request->recipient_id; // Add recipient_id to the message
        $message->body = $request->body;

        // Handle file attachment if exists
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments', 'public');
            $message->attachment = $path;
        }

        // Save the message
        $message->save();

        // Redirect back to the conversation
        return redirect()->route('conversation.show', $conversation->id)
            ->with('success', 'Message sent successfully.');
    }

    public function reply(Request $request, $conversationId)
    {
        $request->validate([
            'body' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $conversation = Conversation::findOrFail($conversationId);

        // Ensure the current user is part of the conversation
        if ($conversation->user_one_id != auth()->id() && $conversation->user_two_id != auth()->id()) {
            abort(403);
        }

        // Upload attachment
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments');
        }

        // Get the recipient
        $recipientId = $conversation->user_one_id == auth()->id() ? $conversation->user_two_id : $conversation->user_one_id;

        // Create reply message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'recipient_id' => $recipientId,
            'body' => $request->body,
            'attachment' => $attachmentPath,
        ]);

       /*  $sender = User::where('id', $conversation->user_one_id)->first();
        $recipient = User::where('id', $conversation->user_two_id)->first();
        

        $emails = [$recipient->email];

        Mail::send('mails.test', [], function($message) use ($emails, $sender)
        {    
            $message->to($emails)->subject('This is test e-mail')
            // ->from($sender, 'Reply Guy'); 
            ->replyTo($sender->email, 'Reply Guy');
        });
        dd($sender, $recipient, $conversation->user_one_id,$conversation->user_two_id, Mail:: failures() ); */

        return response()->json(['message' => 'Reply sent successfully', 'data' => $message], 201);
    }

    /**
     * Display the conversation between the current user and another user.
     * 
     * @param int $id Conversation ID
     * @return \Illuminate\View\View
     */
    public function showConversation($id)
    {
        // Fetch the conversation by ID, with its messages and users
        $conversation = Conversation::with('messages', 'userOne', 'userTwo')->find($id);

        // Ensure the authenticated user is part of the conversation
        if (!empty($conversation) && $conversation->userOne->id !== auth()->id() && $conversation->userTwo->id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Return the conversation view
        return view('conversations/conversation', compact('conversation'));
    }

    public function markAsRead($id)
    {
        $message = Message::where('id', $id)->where('recipient_id', auth()->id())->firstOrFail();
        $message->update(['is_read' => true]);

        return response()->json(['message' => 'Message marked as read']);
    }


    public function deleteMessage($id)
    {
        $message = Message::where('id', $id)
            ->where('sender_id', auth()->id()) // Only sender can delete
            ->firstOrFail();

        // Delete the message
        $message->delete();

        return response()->json(['message' => 'Message deleted successfully']);
    }
    public function moveToTrash($id)
    {
        $message = Message::where('id', $id)
            ->where('recipient_id', auth()->id())
            ->firstOrFail();

        // Mark the message as trashed
        $message->update(['trashed_at' => now()]);

        return response()->json(['message' => 'Message moved to trash successfully']);
    }
    public function restoreFromTrash($id)
    {
        $message = Message::onlyTrashed()->where('id', $id)
            ->where('recipient_id', auth()->id())
            ->firstOrFail();

        $message->update(['trashed_at' => null]);

        return response()->json(['message' => 'Message restored from trash successfully']);
    }

    public function deleteFromTrash($id)
    {
        $message = Message::onlyTrashed()->where('id', $id)
            ->where('recipient_id', auth()->id())
            ->firstOrFail();

        $message->delete(); // Permanently deletes the message

        return response()->json(['message' => 'Message permanently deleted']);
    }
    public function createFolder(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $folder = Folder::create([
            'name' => $request->name,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Folder created successfully', 'data' => $folder]);
    }

    public function addMessageToFolder(Request $request, $folderId)
    {
        $folder = Folder::where('id', $folderId)->where('user_id', auth()->id())->firstOrFail();
        $message = Message::where('id', $request->message_id)->where('recipient_id', auth()->id())->firstOrFail();

        $folder->messages()->attach($message);

        return response()->json(['message' => 'Message added to folder successfully']);
    }

    public function removeMessageFromFolder(Request $request, $folderId)
    {
        $folder = Folder::where('id', $folderId)->where('user_id', auth()->id())->firstOrFail();
        $message = Message::where('id', $request->message_id)->where('recipient_id', auth()->id())->firstOrFail();

        $folder->messages()->detach($message);

        return response()->json(['message' => 'Message removed from folder successfully']);
    }

    public function createLabel(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $label = Label::create([
            'name' => $request->name,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Label created successfully', 'data' => $label]);
    }

    public function addLabelToMessage(Request $request, $labelId)
    {
        $label = Label::where('id', $labelId)->where('user_id', auth()->id())->firstOrFail();
        $message = Message::where('id', $request->message_id)->where('recipient_id', auth()->id())->firstOrFail();

        $label->messages()->attach($message);

        return response()->json(['message' => 'Label added to message successfully']);
    }

    public function removeLabelFromMessage(Request $request, $labelId)
    {
        $label = Label::where('id', $labelId)->where('user_id', auth()->id())->firstOrFail();
        $message = Message::where('id', $request->message_id)->where('recipient_id', auth()->id())->firstOrFail();

        $label->messages()->detach($message);

        return response()->json(['message' => 'Label removed from message successfully']);
    }
}
