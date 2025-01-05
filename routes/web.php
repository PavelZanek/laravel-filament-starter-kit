<?php

declare(strict_types=1);

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('homepage');

Route::get('/app/login', fn () => redirect(Filament::getPanel('auth')->route('auth.login')));
Route::get('/admin/login', fn () => redirect(Filament::getPanel('auth')->route('auth.login')));
