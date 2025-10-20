<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Middleware\FilamentAuthenticateRedirect;
use App\Http\Responses\RegisterResponse;
use App\Models\Role;
use App\Models\User;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Carbon\CarbonImmutable;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Http\Responses\Contracts\LogoutResponse;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\HtmlString;
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
            RegisterResponse::class
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
        $this->app->bind(
            Authenticate::class,
            FilamentAuthenticateRedirect::class,
        );

        Gate::before(function (User $user): ?bool {
            return $user->hasRole(Role::SUPER_ADMIN) ? true : null;
        });

        // Authenticate::redirectUsing(fn (): string => Filament::getPanel('auth')->route('auth.login'));

        // $this->configureCommands();
        $this->configureModels();
        $this->configureUrl();
        $this->configureDates();
        $this->configureVite();
        $this->configureLanguageSwitch();

        FilamentView::registerRenderHook(
            PanelsRenderHook::SCRIPTS_AFTER,
            fn (): HtmlString => new HtmlString('
                <script>document.addEventListener("scroll-to-top", () => window.scrollTo(0, 0))</script>
            '),
        );
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
     * Configure the application's dates.
     */
    private function configureDates(): void
    {
        Date::use(CarbonImmutable::class);
    }

    /*
     * Configure the application's Vite.
     */
    private function configureVite(): void
    {
        Vite::useAggressivePrefetching();
    }

    /*
     * Configure the Language Switch plugin.
     */
    private function configureLanguageSwitch(): void
    {
        // @codeCoverageIgnoreStart
        if (! $this->app->runningInConsole()) {
            LanguageSwitch::configureUsing(function (LanguageSwitch $switch): void {
                $switch->locales([
                    'cs',
                    'en',
                ])
                    ->flags([
                        'cs' => asset('flags/cz.svg'),
                        'en' => asset('flags/gb.svg'),
                    ])
                    ->visible(outsidePanels: true);
            });
        }
        // @codeCoverageIgnoreEnd
    }
}
