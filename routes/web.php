<?php

use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\Settings;
use App\Livewire\Admin\Users;
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
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', AdminDashboard::class)->name('dashboard');
        Route::get('users', Users::class)->name('users');
        Route::get('settings', Settings::class)->name('settings');
    });
});

require __DIR__.'/settings.php';
