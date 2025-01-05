<?php

declare(strict_types=1);

namespace App\Providers;

use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Illuminate\Support\ServiceProvider;
use Override;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[Override]
    public function register(): void
    {
        $this->app->singleton(
            LoginResponse::class,
            \App\Http\Responses\LoginResponse::class
        );

        $this->app->singleton(
            RegistrationResponse::class,
            \App\Http\Responses\RegisterResponse::class
        );

        $this->app->singleton(
            LogoutResponse::class,
            \App\Http\Responses\LogoutResponse::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // @codeCoverageIgnoreStart
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch): void {
            $switch->locales([
                // 'cs',
                'en',
            ])
                ->flags([
                    // 'cs' => __('common.flags.cs'),
                    'en' => __('common.flags.en'),
                ]);
        });
        // @codeCoverageIgnoreEnd

        Authenticate::redirectUsing(fn (): string => Filament::getPanel('auth')->route('auth.login'));
    }
}
