<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the departments.
     */
    public function index()
    {
        $departments = Department::with(['college', 'faculties'])->latest()->paginate(10);
        return view('admin.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new department.
     */
    public function create()
    {
        $colleges = College::where('status', 'active')->orderBy('college_name')->get();
        return view('admin.departments.create', compact('colleges'));
    }

    /**
     * Store a newly created department in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'college_id' => 'required|exists:colleges,id',
            'department_name' => 'required|string|max:255',
            'department_code' => 'required|string|max:10|unique:departments,department_code',
            'hod_name' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        Department::create($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department created successfully.');
    }

    /**
     * Show the form for editing the specified department.
     */
    public function edit(Department $department)
    {
        $colleges = College::where('status', 'active')->orderBy('college_name')->get();
        return view('admin.departments.edit', compact('department', 'colleges'));
    }

    /**
     * Update the specified department in storage.
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'college_id' => 'required|exists:colleges,id',
            'department_name' => 'required|string|max:255',
            'department_code' => 'required|string|max:10|unique:departments,department_code,' . $department->id,
            'hod_name' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $department->update($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department updated successfully.');
    }

    /**
     * Remove the specified department from storage.
     */
    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department deleted successfully.');
    }
}
