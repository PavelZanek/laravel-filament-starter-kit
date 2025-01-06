<?php

declare(strict_types=1);

namespace App\Providers;

use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
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
                'cs',
                'en',
            ])
                ->flags([
                    'cs' => __('common.flags.cs'),
                    'en' => __('common.flags.en'),
                ]);
        });
        // @codeCoverageIgnoreEnd

        Authenticate::redirectUsing(fn (): string => Filament::getPanel('auth')->route('auth.login'));

        $this->configureCommands();
        $this->configureModels();
        $this->configureUrl();
        $this->configureVite();
    }

    /*
     * Configure the application's commands.
     */
    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands($this->app->isProduction());
    }

    /*
     * Configure the application's models.
     */
    private function configureModels(): void
    {
        Model::shouldBeStrict();
    }

    /*
     * Configure the application's URL.
     */
    private function configureUrl(): void
    {
        URL::forceHttps($this->app->isProduction());
    }

    /*
     * Configure the application's Vite.
     */
    private function configureVite(): void
    {
        Vite::useAggressivePrefetching();
    }
}
