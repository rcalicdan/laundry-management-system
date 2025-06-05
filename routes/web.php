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

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth'])
//     ->name('dashboard');

Route::get('dashboard', App\Livewire\Dashboard\Page::class)->middleware(['auth'])->name('dashboard');

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
        Route::get('{laundryService}/edit', LaundryServiceUpdatePage::class)->name('edit');
    });

    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('', \App\Livewire\Customer\Table::class)->name('table');
        Route::get('create', \App\Livewire\Customer\CreatePage::class)->name('create');
        Route::get('{customer}/edit', \App\Livewire\Customer\UpdatePage::class)->name('edit');
        Route::get('{customer}/orders', App\Livewire\Customer\OrdersTable::class)->name('orders');
    });

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('', \App\Livewire\Orders\Table::class)->name('table');
        Route::get('create', \App\Livewire\Orders\CreatePage::class)->name('create');
        Route::get('create/{customer_id?}', \App\Livewire\Orders\CreatePage::class)->name('create.with-customer');
        Route::get('{order}', \App\Livewire\Orders\ShowPage::class)->name('show');
        Route::get('{order}/edit', \App\Livewire\Orders\UpdatePage::class)->name('edit');
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('profile', Profile::class)->name('profile');
        Route::get('password', Password::class)->name('password');
        Route::get('appearance', Appearance::class)->name('appearance');
    });
});

require __DIR__ . '/auth.php';
