<?php

use App\Http\Controllers\ProgressController;
use App\Livewire\Admin\Courses\Analyze as CoursesAnalyze;
use App\Livewire\Admin\Courses\ContentEditor;
use App\Livewire\Admin\Courses\ContentViewer;
use App\Livewire\Admin\Courses\Create as CoursesCreate;
use App\Livewire\Admin\Courses\Edit as CoursesEdit;
use App\Livewire\Admin\Courses\EndQuizCreator;
use App\Livewire\Admin\Courses\EndQuizViewer;
use App\Livewire\Admin\Courses\Index as CoursesIndex;
use App\Livewire\Admin\Courses\ModuleQuizCreator;
use App\Livewire\Admin\Courses\ModuleQuizViewer;
use App\Livewire\Admin\Courses\Show as CoursesShow;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\Enrollments as AdminEnrollments;
use App\Livewire\Admin\Enrollments\Create as EnrollmentBatchCreate;
use App\Livewire\Admin\Enrollments\Show as EnrollmentBatchShow;
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
    Route::post('/progress/update', [ProgressController::class, 'update']);

    Route::get('dashboard', function () {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('faculty.dashboard');
    })->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', AdminDashboard::class)->name('dashboard');
        Route::get('enrollments', AdminEnrollments::class)->name('enrollments.index');
        Route::get('enrollments/create', EnrollmentBatchCreate::class)->name('enrollments.create');
        Route::get('enrollments/{batchKey}', EnrollmentBatchShow::class)->name('enrollments.show');

        Route::prefix('courses')->name('courses.')->group(function () {
            Route::get('/', CoursesIndex::class)->name('index');
            Route::get('/create', CoursesCreate::class)->name('create');
            Route::get('/{course}', CoursesShow::class)->name('show');
            Route::get('/{course}/analyze', CoursesAnalyze::class)->name('analyze');
            Route::get('/{course}/enroll', EnrollmentBatchCreate::class)->name('enroll');
            Route::get('/{course}/edit', CoursesEdit::class)->name('edit');
            Route::get('/content/{contentId}', ContentViewer::class)->name('content.show');
            Route::get('/{course}/content/{contentId}/edit', ContentEditor::class)->name('content.edit');

            Route::get('/{course}/end-quiz/create', EndQuizCreator::class)->name('end-quiz.create');
            Route::get('/{course}/end-quiz/{endQuiz}/edit', EndQuizCreator::class)->name('end-quiz.edit');
            Route::get('/end-quiz/{endQuizId}', EndQuizViewer::class)->name('end-quiz.show');

            Route::get('/{course}/module-quiz/create', ModuleQuizCreator::class)->name('module-quiz.create');
            Route::get('/{course}/module-quiz/{moduleQuiz}/edit', ModuleQuizCreator::class)->name('module-quiz.edit');
            Route::get('/module-quiz/{moduleQuizId}', ModuleQuizViewer::class)->name('module-quiz.show');
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
