<?php

declare(strict_types=1);

use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

Route::get('/', function (): Illuminate\Contracts\View\Factory|Illuminate\Contracts\View\View {
    return view('welcome');
})->name('homepage');

Route::get('/app/login', fn (): RedirectResponse => redirect(Filament::getPanel('auth')->route('auth.login')));
Route::get('/admin/login', fn (): RedirectResponse => redirect(Filament::getPanel('auth')->route('auth.login')));
