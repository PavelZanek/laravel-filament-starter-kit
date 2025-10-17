<?php

declare(strict_types=1);

use App\Http\Middleware\FilamentAuthenticateRedirect;
use App\Providers\Filament\AppPanelProvider;

arch('controllers')
    ->expect('App\Http\Controllers')
    ->toExtendNothing()
    ->not->toBeUsed();

arch('middleware')
    ->expect('App\Http\Middleware')
    ->toHaveMethod('handle')
    ->toUse('Illuminate\Http\Request')
    ->not->toBeUsed()
    ->ignoring([
        //        'App\Providers\Filament\AppPanelProvider',
        AppPanelProvider::class,
        FilamentAuthenticateRedirect::class,
    ]);

// arch('requests')
//    ->expect('App\Http\Requests')
//    ->toExtend('Illuminate\Foundation\Http\FormRequest')
//    ->toHaveMethod('rules')
//    ->toBeUsedIn('App\Http\Controllers');
