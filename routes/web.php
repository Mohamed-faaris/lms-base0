<?php

use App\Http\Controllers\Admin\CourseAnalyzeEnrollmentsDataTableController;
use App\Http\Controllers\Admin\CourseDeleteController;
use App\Http\Controllers\Admin\CoursesDataTableController;
use App\Http\Controllers\Admin\EnrollmentsDataTableController;
use App\Http\Controllers\Admin\UsersDataTableController;
use App\Livewire\Admin\BehavioralAnalytics;
use App\Livewire\Admin\Courses\Analyze;
use App\Livewire\Admin\Courses\ContentEditor;
use App\Livewire\Admin\Courses\Create;
use App\Livewire\Admin\Courses\Edit;
use App\Livewire\Admin\Courses\Index as CoursesIndex;
use App\Livewire\Admin\Courses\QuizEditor;
use App\Livewire\Admin\Courses\Show as CoursesShow;
use App\Livewire\Admin\Courses\Structure;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\Enrollments;
use App\Livewire\Admin\Enrollments\Create as EnrollmentsCreate;
use App\Livewire\Admin\Enrollments\Show as EnrollmentsShow;
use App\Livewire\Admin\Users\Index as UsersIndex;
use App\Livewire\Admin\Users\Profile as UsersProfile;
use App\Livewire\Faculty\Certificates;
use App\Livewire\Faculty\CoursePlayer;
use App\Livewire\Faculty\Courses;
use App\Livewire\Faculty\Dashboard;
use App\Livewire\Faculty\Notifications;
use App\Livewire\Faculty\Profile;
use App\Livewire\Faculty\Streaks;
use App\Livewire\Faculty\Suggestions;
use App\Livewire\Public\CertificateVerify;
use App\Models\Certificate;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('certificates/verify/{certificate_id}', CertificateVerify::class)->name('certificates.verify');

Route::get('certificates/download/{certificate_id}', function ($certificate_id, ?string $format = 'png') {
    $certificate = Certificate::where('certificate_id', $certificate_id)->firstOrFail();
    $course = $certificate->course;
    $user = $certificate->user;

    $verificationUrl = route('certificates.verify', ['certificate_id' => $certificate_id]);

    $qrCode = new QrCode(
        data: $verificationUrl,
        size: 150,
        margin: 5
    );
    $writer = new SvgWriter;
    $qrSvg = $writer->write($qrCode)->getString();

    $html = view('certificates.download', [
        'certificate' => $certificate,
        'course' => $course,
        'user' => $user,
        'qrSvg' => $qrSvg,
        'verificationUrl' => $verificationUrl,
    ])->render();

    if ($format === 'pdf') {
        return response($html, 200, [
            'Content-Type' => 'text/html',
            'Content-Disposition' => 'attachment; filename="certificate-'.$certificate_id.'.html"',
        ]);
    }

    return response($html, 200, [
        'Content-Type' => 'text/html',
        'Content-Disposition' => 'attachment; filename="certificate-'.$certificate_id.'.html"',
    ]);
})->name('certificates.download');

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

    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('dashboard', AdminDashboard::class)->name('dashboard');

        Route::get('users', UsersIndex::class)->name('users.index');
        Route::get('users/datatable', UsersDataTableController::class)->name('users.datatable');
        Route::get('users/{user}', UsersProfile::class)->name('users.profile');

        Route::get('courses', CoursesIndex::class)->name('courses.index');
        Route::get('courses/datatable', CoursesDataTableController::class)->name('courses.index.datatable');
        Route::get('courses/create', Create::class)->name('courses.create');
        Route::get('courses/{course}', CoursesShow::class)->name('courses.show');
        Route::get('courses/{course}/edit', Edit::class)->name('courses.edit');
        Route::get('courses/{course}/analyze', Analyze::class)->name('courses.analyze');
        Route::get('courses/{course}/analyze/datatable', CourseAnalyzeEnrollmentsDataTableController::class)->name('courses.analyze.datatable');
        Route::get('courses/{course}/structure', Structure::class)->name('courses.structure');
        Route::get('courses/{course}/content/{module?}', ContentEditor::class)->name('courses.content');
        Route::get('courses/{course}/quiz/{quiz?}', QuizEditor::class)->name('courses.quiz');
        Route::delete('courses/{course}', CourseDeleteController::class)->name('courses.destroy');

        Route::get('enrollments', Enrollments::class)->name('enrollments.index');
        Route::get('enrollments/datatable', EnrollmentsDataTableController::class)->name('enrollments.datatable');
        Route::get('enrollments/create', EnrollmentsCreate::class)->name('enrollments.create');
        Route::get('enrollments/{batchId}', EnrollmentsShow::class)->name('enrollments.show');

        Route::get('behavioral-analytics', BehavioralAnalytics::class)->name('behavioral-analytics');
    });
});

require __DIR__.'/settings.php';
