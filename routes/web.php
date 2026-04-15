<?php

use App\Livewire\Admin\Courses\Analyze as CoursesAnalyze;
use App\Livewire\Admin\Courses\ContentEditor;
use App\Livewire\Admin\Courses\ContentViewer;
use App\Livewire\Admin\Courses\Create as CoursesCreate;
use App\Livewire\Admin\Courses\Edit as CoursesEdit;
use App\Livewire\Admin\Courses\Index as CoursesIndex;
use App\Livewire\Admin\Courses\QuizEditor;
use App\Livewire\Admin\Courses\Show as CoursesShow;
use App\Livewire\Admin\Courses\Structure as CoursesStructure;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\Enrollments as AdminEnrollments;
use App\Livewire\Admin\Enrollments\Create as EnrollmentBatchCreate;
use App\Livewire\Admin\Enrollments\Show as EnrollmentBatchShow;
use App\Livewire\Admin\Users\Index as AdminUsersIndex;
use App\Livewire\Admin\Users\Profile as AdminUserProfile;
use App\Livewire\Faculty\Certificates;
use App\Livewire\Faculty\CoursePlayer;
use App\Livewire\Faculty\Courses;
use App\Livewire\Faculty\Dashboard as FacultyDashboard;
use App\Livewire\Faculty\Notifications;
use App\Livewire\Faculty\Profile;
use App\Livewire\Faculty\Streaks;
use App\Livewire\Faculty\Suggestions;
use App\Livewire\Public\Courses as PublicCourses;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('courses', PublicCourses::class)->name('courses');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('faculty.dashboard');
    })->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', AdminDashboard::class)->name('dashboard');
        Route::get('users', AdminUsersIndex::class)->name('users.index');
        Route::get('users/datatable', \App\Http\Controllers\Admin\UsersDataTableController::class)->name('users.datatable');
        Route::get('users/{user}', AdminUserProfile::class)->name('users.profile');
        Route::get('enrollments', AdminEnrollments::class)->name('enrollments.index');
        Route::get('enrollments/datatable', \App\Http\Controllers\Admin\EnrollmentsDataTableController::class)->name('enrollments.datatable');
        Route::get('enrollments/create', EnrollmentBatchCreate::class)->name('enrollments.create');
        Route::get('enrollments/{batchKey}', EnrollmentBatchShow::class)->name('enrollments.show');

        Route::prefix('courses')->name('courses.')->group(function () {
            Route::get('/', CoursesIndex::class)->name('index');
            Route::get('/datatable', \App\Http\Controllers\Admin\CoursesDataTableController::class)->name('index.datatable');
            Route::get('/create', CoursesCreate::class)->name('create');
            Route::delete('/{course}', \App\Http\Controllers\Admin\CourseDeleteController::class)->name('destroy');
            Route::get('/{course}', CoursesShow::class)->name('show');
            Route::get('/{course}', CoursesShow::class)->name('show');
            Route::get('/{course}/structure', CoursesStructure::class)->name('structure');
            Route::get('/{course}/analyze', CoursesAnalyze::class)->name('analyze');
            Route::get('/{course}/analyze/datatable', \App\Http\Controllers\Admin\CourseAnalyzeEnrollmentsDataTableController::class)->name('analyze.datatable');
            Route::get('/{course}/enroll', EnrollmentBatchCreate::class)->name('enroll');
            Route::get('/{course}/edit', CoursesEdit::class)->name('edit');
            Route::get('/content/{contentId}', ContentViewer::class)->name('content.show');
            Route::get('/{course}/content/{contentId}/edit', ContentEditor::class)->name('content.edit');
            Route::get('/{course}/content/{content}/quiz', QuizEditor::class)
                ->defaults('placement', 'content')
                ->name('content.quiz.edit');
            Route::get('/{course}/content/{content}/end-quiz', QuizEditor::class)
                ->defaults('placement', 'end')
                ->name('content.end-quiz.edit');
            Route::get('/{course}/content/{content}/timestamped-quiz/create', QuizEditor::class)
                ->defaults('placement', 'timestamped')
                ->name('content.timestamped-quiz.create');
            Route::get('/{course}/content/{content}/timestamped-quiz/{quiz}', QuizEditor::class)
                ->defaults('placement', 'timestamped')
                ->name('content.timestamped-quiz.edit');
        });
    });

    Route::prefix('faculty')->name('faculty.')->group(function () {
        Route::get('dashboard', FacultyDashboard::class)->name('dashboard');
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
