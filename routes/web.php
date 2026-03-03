<?php

use App\Livewire\Faculty\Dashboard;
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

    Route::get('dashboard/faculty', Dashboard::class)->name('faculty.dashboard');
});

require __DIR__.'/settings.php';
