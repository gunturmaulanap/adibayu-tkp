<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Livewire\Users\UserIndex;
use App\Livewire\Users\UserEdit;
use App\Livewire\Users\UserShow;
use App\Livewire\Users\UserCreate;
use App\Livewire\Items\ItemIndex;
use App\Livewire\Items\ItemEdit;
use App\Livewire\Items\ItemShow;
use App\Livewire\Items\ItemCreate;
use App\Livewire\Roles\RoleIndex;
use App\Livewire\Roles\RoleEdit;
use App\Livewire\Roles\RoleShow;
use App\Livewire\Roles\RoleCreate;
use App\Livewire\Sales\SaleCreate;
use App\Livewire\Sales\SaleIndex;
use App\Livewire\Sales\SaleShow;
use App\Livewire\Sales\SaleEdit;
use App\Livewire\Payments\PaymentIndex;
use App\Livewire\Payments\PaymentCreate;
use App\Livewire\Payments\PaymentEdit;
use App\Livewire\Payments\PaymentShow;

Route::get('/', function () {
    return view('welcome');
})->name('home');



Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get("dashboard", function () {
        return view('dashboard');
    })->name("dashboard")->middleware('permission:dashboard.view');

    Route::get("users", UserIndex::class)->name("users.index")->middleware('permission:user.view|user.create|user.edit|user.delete');
    Route::get("users/create", UserCreate::class)->name("users.create")->middleware('permission:user.create');
    Route::get("users/{id}/edit", UserEdit::class)->name("users.edit")->middleware('permission:user.edit');
    Route::get("users/{id}", UserShow::class)->name("users.show")->middleware('permission:user.view');

    Route::get("items", ItemIndex::class)->name("items.index")->middleware('permission:item.view|item.create|item.edit|item.delete');
    Route::get("items/create", ItemCreate::class)->name("items.create")->middleware('permission:item.create');
    Route::get("items/{id}/edit", ItemEdit::class)->name("items.edit")->middleware('permission:item.edit');
    Route::get("items/{id}", ItemShow::class)->name("items.show")->middleware('permission:item.view');

    Route::get("roles", RoleIndex::class)->name("roles.index")->middleware('permission:role.view|role.create|role.edit|role.delete');
    Route::get("roles/create", RoleCreate::class)->name("roles.create")->middleware('permission:role.create');
    Route::get("roles/{id}/edit", RoleEdit::class)->name("roles.edit")->middleware('permission:role.edit');
    Route::get("roles/{id}", RoleShow::class)->name("roles.show")->middleware('permission:role.view');

    Route::get("sales", SaleIndex::class)->name("sales.index")->middleware('permission:sale.view|sale.create|sale.edit|sale.delete');
    Route::get("sales/create", SaleCreate::class)->name("sales.create")->middleware('permission:sale.create');
    Route::get("sales/{sale}", SaleShow::class)->name("sales.show")->middleware('permission:sale.view');
    Route::get("sales/{id}/edit", SaleEdit::class)->name("sales.edit")->middleware('permission:sale.edit');


    Route::get('payments', PaymentIndex::class)->name('payments.index')->middleware('permission:payment.view|payment.create|payment.edit|payment.delete');
    Route::get('payments/create', PaymentCreate::class)->name('payments.create')->middleware('permission:payment.create');
    Route::get('payments/{id}/edit', PaymentEdit::class)->name('payments.edit')->middleware('permission:payment.edit');
    Route::get('payments/{id}', PaymentShow::class)->name('payments.show')->middleware('permission:payment.view');


    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');



    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

require __DIR__ . '/auth.php';
