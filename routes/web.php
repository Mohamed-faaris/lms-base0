<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth', 'verified'])->group(function () {
    Volt::route('learner/my-learning', 'pages.learner.my-learning')
        ->name('learner.my-learning');

    Volt::route('learner/my-learning/{course}/{item?}', 'pages.learner.course-view')
        ->name('learner.my-learning.course');
});

require __DIR__.'/auth.php';
