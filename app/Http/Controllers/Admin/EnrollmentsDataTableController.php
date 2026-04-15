<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class EnrollmentsDataTableController extends Controller
{
    public function __invoke(Request $request)
    {
        // Get enrollments grouped by batch
        $query = Enrollment::query()
            ->with(['course', 'enrolledBy'])
            ->when($request->search, function ($builder) use ($request) {
                $builder->where(function ($searchQuery) use ($request) {
                    $searchQuery
                        ->whereHas('user', function ($userQuery) use ($request) {
                            $userQuery
                                ->where('name', 'like', '%'.$request->search.'%')
                                ->orWhere('email', 'like', '%'.$request->search.'%');
                        })
                        ->orWhereHas('course', function ($courseQuery) use ($request) {
                            $courseQuery
                                ->where('title', 'like', '%'.$request->search.'%')
                                ->orWhere('slug', 'like', '%'.$request->search.'%');
                        })
                        ->orWhere('batch_id', 'like', '%'.$request->search.'%');
                });
            })
            ->when($request->course_id, function ($query, $courseId) {
                $query->where('course_id', $courseId);
            })
            ->orderByDesc('enrolled_at');

        // Transform to batch data
        $enrollments = $query->get()->map(function ($enrollment) {
            $batchKey = $enrollment->batch_id
                ? $enrollment->batch_id
                : 'legacy-'.$enrollment->course_id.'-'.$enrollment->enrolled_by.'-'.($enrollment->enrolled_at?->timestamp ?? 0);

            return (object) [
                'batch_key' => $batchKey,
                'batch_id' => $enrollment->batch_id,
                'course_id' => $enrollment->course_id,
                'course' => $enrollment->course,
                'enrolled_by' => $enrollment->enrolled_by,
                'enrolled_by_name' => $enrollment->enrolledBy?->name ?? 'Unknown',
                'enrolled_at' => $enrollment->enrolled_at,
                'deadline' => $enrollment->deadline,
            ];
        })->groupBy('batch_key')->map(function ($group) {
            $first = $group->first();
            $learnerNames = $group->pluck('course')->pluck('title')->filter()->take(3)->implode(', ');

            return (object) [
                'batch_key' => $first->batch_key,
                'batch_id' => $first->batch_id,
                'course_id' => $first->course_id,
                'course_title' => $first->course?->title ?? 'Unknown',
                'enrolled_by' => $first->enrolled_by_name,
                'enrolled_at' => $first->enrolled_at,
                'deadline' => $first->deadline,
                'learners_count' => $group->count(),
            ];
        })->values();

        return DataTables::of(collect($enrollments))
            ->addColumn('batch_id', function ($batch) {
                return '<a href="'.route('admin.enrollments.show', $batch->batch_key).'" class="font-mono text-xs bg-zinc-100 dark:bg-zinc-900 px-2 py-1 rounded text-zinc-700 dark:text-zinc-200 hover:bg-zinc-200 dark:hover:bg-zinc-800">'.$batch->batch_key.'</a>';
            })
            ->addColumn('course', function ($batch) {
                return '<a href="'.route('admin.courses.show', $batch->course_id).'" class="font-medium text-zinc-900 dark:text-zinc-100 hover:text-blue-600 dark:hover:text-blue-400">'.$batch->course_title.'</a>';
            })
            ->addColumn('learners', function ($batch) {
                return '<div><div class="font-medium text-zinc-900 dark:text-zinc-100">'.$batch->learners_count.' user'.($batch->learners_count === 1 ? '' : 's').'</div></div>';
            })
            ->addColumn('enrolled_by', function ($batch) {
                return $batch->enrolled_by;
            })
            ->addColumn('enrolled_at', function ($batch) {
                return $batch->enrolled_at?->format('M d, Y') ?? 'N/A';
            })
            ->addColumn('deadline', function ($batch) {
                if (! $batch->deadline) {
                    return '<span class="text-zinc-600 dark:text-zinc-300">No deadline</span>';
                }
                $now = now()->timestamp;
                $daysLeft = ceil(($batch->deadline - $now) / 86400);
                if ($daysLeft < 0) {
                    return '<span class="text-red-600 dark:text-red-400 font-medium">'.abs($daysLeft).' days overdue</span>';
                } elseif ($daysLeft <= 3) {
                    return '<span class="text-amber-600 dark:text-amber-400 font-medium">'.$daysLeft.' days left</span>';
                }

                return '<span class="text-zinc-600 dark:text-zinc-300">'.$daysLeft.' days left</span>';
            })
            ->rawColumns(['batch_id', 'course', 'learners', 'deadline'])
            ->toJson();
    }
}
