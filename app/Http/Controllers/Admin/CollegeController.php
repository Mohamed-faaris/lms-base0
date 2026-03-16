<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;
use Illuminate\Http\Request;

class CollegeController extends Controller
{
    /**
     * Display a listing of the colleges.
     */
    public function index()
    {
        $colleges = College::withCount(['departments', 'faculties'])->latest()->paginate(10);
        return view('admin.colleges.index', compact('colleges'));
    }

    /**
     * Show the form for creating a new college.
     */
    public function create()
    {
        return view('admin.colleges.create');
    }

    /**
     * Store a newly created college in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'college_name' => 'required|string|max:255',
            'college_code' => 'required|string|max:10|unique:colleges,college_code',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        College::create($validated);

        return redirect()->route('admin.colleges.index')
            ->with('success', 'College created successfully.');
    }

    /**
     * Show the form for editing the specified college.
     */
    public function edit(College $college)
    {
        return view('admin.colleges.edit', compact('college'));
    }

    /**
     * Update the specified college in storage.
     */
    public function update(Request $request, College $college)
    {
        $validated = $request->validate([
            'college_name' => 'required|string|max:255',
            'college_code' => 'required|string|max:10|unique:colleges,college_code,' . $college->id,
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $college->update($validated);

        return redirect()->route('admin.colleges.index')
            ->with('success', 'College updated successfully.');
    }

    /**
     * Remove the specified college from storage.
     */
    public function destroy(College $college)
    {
        $college->delete();

        return redirect()->route('admin.colleges.index')
            ->with('success', 'College deleted successfully.');
    }
}
