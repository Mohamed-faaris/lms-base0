<?php

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
});

require __DIR__.'/settings.php';
