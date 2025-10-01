<?php

namespace App\Http\Controllers\backend\SupportingDoc;

use App\Http\Controllers\Controller;
use App\Models\SupportingDoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SupportingDocController extends Controller
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
        $validated = $request->validate([
            'green_note_id' => 'required|exists:green_notes,id',
            'name' => 'required|string',
            'file_path' => 'required|mimes:pdf,doc,docx,xls,xlsx,csv|max:10240000',
        ]);

        if ($request->hasFile('file_path')) {
            $fileName = time() . '_' . $request->file('file_path')->getClientOriginalName();
            $filePath = $request->file('file_path')->move(public_path('notes/documents'), $fileName);
            $validated['file_path'] = $fileName;
        }

        SupportingDoc::create([
            'green_note_id' => $validated['green_note_id'],
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'file_path' => $validated['file_path'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Document uploaded successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(SupportingDoc $supportingDoc)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupportingDoc $supportingDoc)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SupportingDoc $supportingDoc)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupportingDoc $supportingDoc, $id)
    {
        $document = SupportingDoc::findOrFail($id);
        // Check if file exists and delete it
        $filePath = public_path('notes/documents/' . $document->file_path);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        // Delete record from database
        $document->delete();
        return redirect()->back()->with('success', 'Document deleted successfully!');
    }
}
