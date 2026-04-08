<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgressController extends Controller
{
    public function update(Request $request)
    {
        $user = auth()->user();
        $moduleId = $request->module_id;
        $seconds = (int) $request->seconds;
        $duration = (int) $request->duration;

        $currentProgress = DB::table('progress')
            ->where('user_id', $user->id)
            ->where('content_id', $moduleId)
            ->value('progress_seconds') ?? 0;

        $newSeconds = max($currentProgress, $seconds);

        DB::table('progress')
            ->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'content_id' => $moduleId,
                ],
                [
                    'progress_seconds' => $newSeconds,
                    'video_duration' => $duration,
                    'last_watched_at' => now(),
                ]
            );

        return response()->json(['success' => true]);
    }
}
