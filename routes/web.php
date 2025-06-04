<?php

use App\Livewire\LaundryService\CreatePage as LaundryServiceCreatePage;
use App\Livewire\LaundryService\Table as LaundryServiceTable;
use App\Livewire\LaundryService\UpdatePage as LaundryServiceUpdatePage;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\User\CreatePage;
use App\Livewire\User\Table;
use App\Livewire\User\UpdatePage;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('', Table::class)->name('table');
        Route::get('create', CreatePage::class)->name('create');
        Route::get('{user}/edit', UpdatePage::class)->name('edit');
    });

    Route::prefix('laundry-services')->name('laundry-services.')->group(function () {
        Route::get('', LaundryServiceTable::class)->name('table');
        Route::get('create', LaundryServiceCreatePage::class)->name('create');
        Route::get('{user}/edit', LaundryServiceUpdatePage::class)->name('edit');
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('profile', Profile::class)->name('profile');
        Route::get('password', Password::class)->name('password');
        Route::get('appearance', Appearance::class)->name('appearance');
    });
});

require __DIR__ . '/auth.php';
