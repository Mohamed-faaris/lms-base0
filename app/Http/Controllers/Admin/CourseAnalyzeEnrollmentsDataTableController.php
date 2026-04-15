<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Progress;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CourseAnalyzeEnrollmentsDataTableController extends Controller
{
    public function __invoke(Request $request, Course $course)
    {
        $contentIds = $course->loadMissing('topics.modules.contents')
            ->topics
            ->flatMap->modules
            ->flatMap->contents
            ->pluck('id');

        $query = $course->enrollments()
            ->with(['user'])
            ->orderByDesc('enrolled_at');

        return DataTables::of($query)
            ->addColumn('user', function ($enrollment) {
                return $enrollment->user?->name ?? 'Unknown';
            })
            ->addColumn('enrolled_at', function ($enrollment) {
                return $enrollment->enrolled_at?->format('M d, Y') ?? 'N/A';
            })
            ->addColumn('progress', function ($enrollment) use ($contentIds) {
                $totalContent = $contentIds->count();

                if ($totalContent === 0) {
                    return 0;
                }

                $completedContent = Progress::query()
                    ->where('user_id', $enrollment->user_id)
                    ->whereIn('content_id', $contentIds)
                    ->whereNotNull('completed_at')
                    ->count();

                return (int) round(($completedContent / $totalContent) * 100);
            })
            ->addColumn('status', function ($enrollment) use ($contentIds) {
                $totalContent = $contentIds->count();

                if ($totalContent === 0) {
                    return 'Not Started';
                }

                $completedContent = Progress::query()
                    ->where('user_id', $enrollment->user_id)
                    ->whereIn('content_id', $contentIds)
                    ->whereNotNull('completed_at')
                    ->count();

                $progress = (int) round(($completedContent / $totalContent) * 100);

                return $progress === 100 ? 'Completed' : ($progress > 0 ? 'In Progress' : 'Not Started');
            })
            ->toJson();
    }
}
