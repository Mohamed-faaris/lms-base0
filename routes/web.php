<?php

use App\Livewire\Faculty\Dashboard;
use App\Livewire\Faculty\Courses;
use App\Livewire\Faculty\CoursePlayer;
use App\Livewire\Faculty\Streaks;
use App\Livewire\Faculty\Certificates;
use App\Livewire\Faculty\Notifications;
use App\Livewire\Faculty\Profile;
use App\Livewire\Faculty\Suggestions;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        $user = auth()->user();

        if ($user->role->value === 'faculty') {
            return redirect()->route('faculty.dashboard');
        }

        return redirect()->route('faculty.dashboard');
    })->name('dashboard');

    Route::prefix('faculty')->name('faculty.')->group(function () {
        Route::get('dashboard', Dashboard::class)->name('dashboard');
        Route::get('courses', Courses::class)->name('courses');
        Route::get('course-player/{course?}', CoursePlayer::class)->name('course-player');
        Route::get('streaks', Streaks::class)->name('streaks');
        Route::get('certificates', Certificates::class)->name('certificates');
        Route::get('notifications', Notifications::class)->name('notifications');
        Route::get('profile', Profile::class)->name('profile');
        Route::get('suggestions', Suggestions::class)->name('suggestions');
    });
});

require __DIR__.'/settings.php';
