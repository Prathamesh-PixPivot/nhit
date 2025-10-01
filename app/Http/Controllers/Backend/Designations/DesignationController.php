<?php

namespace App\Http\Controllers\Backend\Designations;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDesignationRequest;
use App\Http\Requests\UpdateDesignationRequest;
use App\Models\Designation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class DesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            return DataTables::eloquent(Designation::query()->select('id', 'name', 'description'))
                ->addIndexColumn()
                ->addColumn('actions', function ($row) {
                    $edit = '<a href="' . route('backend.designations.edit', $row->id) . '" class="btn btn-sm btn-warning">Edit</a>';
                    $del = '<form action="' . route('backend.designations.destroy', $row->id) . '" method="POST" style="display:inline;">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</button></form>';
                    return $edit . ' ' . $del;
                })
                ->rawColumns(['actions'])
                ->toJson();
        }
        return view('backend.designations.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.designations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDesignationRequest $request): RedirectResponse
    {
        // Validation
        $validData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:500',
        ]);

        // Store the designation
        $designation = Designation::create($validData);
        activity('Designation Created')
            ->performedOn($designation)
            ->causedBy(auth()->user())
            ->event('created')
            ->withProperties([
                'designation_id' => $designation->id,
                'designation_name' => $designation->name,
                'created_by' => auth()->user()->name,
                'created_by_email' => auth()->user()->email,
            ])
            ->log("Designation '{$designation->name}' Created by " . auth()->user()->name);
        return redirect()->route('backend.designations.index')->with('success', 'Designation created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Designation $designation)
    {
        return view('backend.designations.show', compact('designation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Designation $designation, $id): View
    {
        $designation = Designation::findOrFail($id);
        return view('backend.designations.edit', compact('designation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Designation $designation, $id): RedirectResponse
    {
        $validData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:500',
        ]);
        $designation = Designation::findOrFail($id);

        $designation->update($validData);
        activity('Designation Updated')
            ->performedOn($designation)
            ->causedBy(auth()->user())
            ->event('updated')
            ->withProperties([
                'designation_id' => $designation->id,
                'designation_name' => $designation->name,
                'updated_by' => auth()->user()->name,
                'updated_by_email' => auth()->user()->email,
            ])
            ->log("Designation '{$designation->name}' Updated by " . auth()->user()->name);
        return redirect()->route('backend.designations.index')->with('success', 'Designation updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Designation $designation, $id)
    {
        $designation = Designation::findOrFail($id);

        $designation->delete();

        return redirect()->route('backend.designations.index')->with('success', 'Designation deleted successfully!');
    }
}
