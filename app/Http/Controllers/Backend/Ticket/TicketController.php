<?php

namespace App\Http\Controllers\backend\Ticket;

use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\TicketMail;
use App\Models\TicketStatusLog;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Ticket::with('user');
        $userRoles = auth()->user()->getRoleNames();

        if (auth()->user()->id != 1 && !$userRoles->contains('ticket')) {
            $query->where('user_id', auth()->id());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('error', 'like', "%{$request->search}%")->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        $tickets = $query->latest()->paginate(10);

        return view('backend.tickets.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.tickets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'number' => 'required',
            'error' => 'required',
            'description' => 'required',
            'entity_name' => 'required',
            'priority' => 'required|in:L,M,H',
            'attachment' => 'nullable|array',
            'attachment.*' => 'file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:20480',
        ]);
        $filenames = [];

        if ($request->hasFile('attachment')) {
            foreach ($request->file('attachment') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/bugs', $filename);
                $filenames[] = $filename;
            }
        }

        $validated['user_id'] = auth()->id();
        $validated['attachments'] = json_encode($filenames);

        $ticket = Ticket::create($validated);
        if ($ticket->status !== 'O') {
            TicketStatusLog::create([
                'ticket_id' => $ticket->id,
                'status' => 'O',
                'changed_by' => auth()->id(),
            ]);
        }
        $priorityMap = [
            'L' => 'Low',
            'M' => 'Medium',
            'H' => 'High',
        ];

        $statusMap = [
            'O' => 'Open',
            'IP' => 'In Progress',
            'R' => 'Resolved',
            'C' => 'Closed',
        ];
        $data = [
            'name' => $request->name ?? '-',
            'ticket_id' => $ticket->id,
            'short_name' => config('app.short_name'),
            'priority' => $priorityMap[$ticket->priority] ?? 'Unknown',
            'status' => $statusMap['O'] ?? 'Unknown',
            'error' => $ticket->error,
        ];
        $adminUser = User::role('ticket')->first();
        if ($adminUser) {
            Mail::to($adminUser->email)->send(new TicketMail($data));
        }

        // 📩 Send mail to Ticket Creator
        // if ($ticket->user && $ticket->user->email) {
        //     Mail::to($ticket->user->email)->send(new TicketMail($data));
        // }
        return redirect()->route('backend.tickets.index')->with('success', 'Ticket created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $ticket = Ticket::findOrFail($id);
        return view('backend.tickets.show', compact('ticket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $ticket = Ticket::findOrFail($id);
        return view('backend.tickets.edit', compact('ticket'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:O,IP,R,C',
        ]);

        $ticket = Ticket::findOrFail($id);
        if ($ticket->status !== $request->status) {
            // Save log only when status is actually changed
            TicketStatusLog::create([
                'ticket_id' => $ticket->id,
                'status' => $request->status,
                'changed_by' => auth()->id(),
            ]);
        }

        $ticket->status = $request->status;
        $ticket->save();
        $priorityMap = [
            'L' => 'Low',
            'M' => 'Medium',
            'H' => 'High',
        ];

        $statusMap = [
            'O' => 'Open',
            'IP' => 'In Progress',
            'R' => 'Resolved',
            'C' => 'Closed',
        ];
        $data = [
            'name' => $ticket->name ?? '-',
            'ticket_id' => $ticket->id,
            'priority' => $priorityMap[$ticket->priority] ?? 'Unknown',
            'error' => $ticket->error,
            'status' => $statusMap[$ticket->status] ?? 'Unknown',
        ];
        $adminUser = User::role('ticket')->first();
        if ($adminUser) {
            Mail::to($adminUser->email)->send(new TicketMail($data));
        }

        // 📩 Send mail to Ticket Creator
        if ($ticket->user && $ticket->user->email) {
            Mail::to($ticket->user->email)->send(new TicketMail($data));
        }
        return redirect()->route('backend.tickets.index')->with('success', 'Ticket status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        $ticket = Ticket::findOrFail($id);

        // 1. Delete files from storage
        $attachments = json_decode($ticket->attachments, true) ?? [];
        foreach ($attachments as $file) {
            $path = 'public/bugs/' . $file;
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
        }

        // 2. Delete all comments (including replies)
        $ticket->comments()->delete();
        $ticket->statusLogs()->delete();
        // 3. Delete the ticket itself
        $ticket->delete();

        DB::commit();

        return redirect()->route('backend.tickets.index')->with('success', 'Ticket deleted successfully.');
    }
    public function downloadAll($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $attachments = json_decode($ticket->attachments, true) ?? [];

            if (empty($attachments)) {
                return back()->with('error', 'No attachments found.');
            }

            // Ensure the temp folder exists
            $tempDir = storage_path('app/public/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $zipFileName = 'ticket_' . $ticket->id . '_attachments.zip';
            $zipFullPath = $tempDir . '/' . $zipFileName;

            $zip = new ZipArchive();

            if ($zip->open($zipFullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                foreach ($attachments as $file) {
                    $fullPath = storage_path('app/public/bugs/' . $file);
                    if (file_exists($fullPath)) {
                        // Optional: add file in a sub-folder like 'attachments/'
                        $zip->addFile($fullPath, basename($file));
                    } else {
                        \Log::warning("File not found while zipping: $fullPath");
                    }
                }
                $zip->close();
            } else {
                return back()->with('error', 'Could not create zip.');
            }

            return response()->download($zipFullPath)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            \Log::error('Download zip failed: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}
