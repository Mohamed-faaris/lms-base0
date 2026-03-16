<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    /**
     * Display a listing of the faculties.
     */
    public function index()
    {
        $faculties = Faculty::with(['college', 'department'])->latest()->paginate(10);
        return view('admin.faculties.index', compact('faculties'));
    }

    /**
     * Show the form for creating a new faculty.
     */
    public function create()
    {
        $colleges = College::where('status', 'active')->orderBy('college_name')->get();
        $departments = Department::where('status', 'active')->with('college')->orderBy('department_name')->get();
        return view('admin.faculties.create', compact('colleges', 'departments'));
    }

    /**
     * Store a newly created faculty in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'college_id' => 'required|exists:colleges,id',
            'department_id' => 'required|exists:departments,id',
            'faculty_name' => 'required|string|max:255',
            'faculty_email' => 'required|email|max:255|unique:faculties,faculty_email',
            'phone' => 'nullable|string|max:20',
            'designation' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        Faculty::create($validated);

        return redirect()->route('admin.faculties.index')
            ->with('success', 'Faculty created successfully.');
    }

    /**
     * Show the form for editing the specified faculty.
     */
    public function edit(Faculty $faculty)
    {
        $colleges = College::where('status', 'active')->orderBy('college_name')->get();
        $departments = Department::where('status', 'active')
            ->where('college_id', $faculty->college_id)
            ->with('college')
            ->orderBy('department_name')
            ->get();
        return view('admin.faculties.edit', compact('faculty', 'colleges', 'departments'));
    }

    /**
     * Update the specified faculty in storage.
     */
    public function update(Request $request, Faculty $faculty)
    {
        $validated = $request->validate([
            'college_id' => 'required|exists:colleges,id',
            'department_id' => 'required|exists:departments,id',
            'faculty_name' => 'required|string|max:255',
            'faculty_email' => 'required|email|max:255|unique:faculties,faculty_email,' . $faculty->id,
            'phone' => 'nullable|string|max:20',
            'designation' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $faculty->update($validated);

        return redirect()->route('admin.faculties.index')
            ->with('success', 'Faculty updated successfully.');
    }

    /**
     * Remove the specified faculty from storage.
     */
    public function destroy(Faculty $faculty)
    {
        $faculty->delete();

        return redirect()->route('admin.faculties.index')
            ->with('success', 'Faculty deleted successfully.');
    }
}
