<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Central domain routes - Only accessible on central domain(s)
foreach (config('tenancy.central_domains') as $domain) {
    Route::middleware('web')
        ->domain($domain)
        ->group(function () {
            Route::get('/', App\Livewire\Homepage::class)->name('home');
        });
}

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
