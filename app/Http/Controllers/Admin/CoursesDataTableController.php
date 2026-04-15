<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Yajra\DataTables\DataTables;

class CoursesDataTableController extends Controller
{
    public function __invoke()
    {
        $courses = Course::query()->withCount('enrollments');

        return DataTables::of($courses)
            ->editColumn('created_at', function ($course) {
                return $course->created_at->format('M d, Y');
            })
            ->addColumn('actions', function ($course) {
                return view('livewire.admin.courses.partials.actions', ['course' => $course])->render();
            })
            ->rawColumns(['actions'])
            ->toJson();
    }
}
