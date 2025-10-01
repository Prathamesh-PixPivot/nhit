<?php

namespace App\Http\Controllers\Backend\Departments;

use App\Models\Department;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            return DataTables::eloquent(Department::query()->select('id', 'name', 'description'))
                ->addIndexColumn()
                ->addColumn('actions', function ($row) {
                    $edit = '<a href="' . route('backend.departments.edit', $row->id) . '" class="btn btn-sm btn-warning">Edit</a>';
                    $del = '<form action="' . route('backend.departments.destroy', $row->id) . '" method="POST" style="display:inline;">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</button></form>';
                    return $edit . ' ' . $del;
                })
                ->rawColumns(['actions'])
                ->toJson();
        }
        return view('backend.departments.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.departments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:500',
        ]);

        // Store the designation
        $department = Department::create($validData);
        activity('Department Created')
            ->performedOn($department)
            ->causedBy(auth()->user())
            ->event('created')
            ->withProperties([
                'department_id' => $department->id,
                'department_name' => $department->name,
                'created_by' => auth()->user()->name,
                'created_by_email' => auth()->user()->email,
            ])
            ->log("Department '{$department->name}' Created by " . auth()->user()->name);
        return redirect()->route('backend.departments.index')->with('success', 'Designation created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department, $id)
    {
        $department = Department::findOrFail($id);
        return view('backend.departments.edit', compact('department'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department, $id)
    {
        $department = Department::findOrFail($id);

        return view('backend.departments.edit', compact('department'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Department $department, $id)
    {
        $validData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:500',
        ]);
        $department = Department::findOrFail($id);

        $department->update($validData);
        activity('Department Updated')
            ->performedOn($department)
            ->causedBy(auth()->user())
            ->event('updated')
            ->withProperties([
                'department_id' => $department->id,
                'department_name' => $department->name,
                'updated_by' => auth()->user()->name,
                'updated_by_email' => auth()->user()->email,
            ])
            ->log("Department '{$department->name}' Updated by " . auth()->user()->name);
        return redirect()->route('backend.departments.index')->with('success', 'department updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department, $id)
    {
        $department = Department::findOrFail($id);

        $department->delete();

        return redirect()->route('backend.departments.index')->with('success', 'department deleted successfully!');
    }
}
