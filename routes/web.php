<?php

use App\Livewire\Faculty\Dashboard;
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Dashboard as AdminDashboard;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('dashboard', function () {
        $user = auth()->user();

        if ($user->role->value === 'faculty') {
            return redirect()->route('faculty.dashboard');
        }

        return redirect()->route('faculty.dashboard');
    })->name('dashboard');

    // Faculty dashboard (unchanged)
    Route::get('dashboard/faculty', Dashboard::class)
        ->name('faculty.dashboard');

    // ✅ Admin dashboard added here (inside same middleware)
    Route::get('dashboard/admin', AdminDashboard::class)
        ->name('admin.dashboard');
});

require __DIR__.'/settings.php';