<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Pages\EditProfile;
use App\Filament\Pages\Tenancy\EditWorkspace;
use App\Filament\Pages\Tenancy\RegisterWorkspace;
use App\Http\Middleware\ApplyTenantScopes;
use App\Models\Workspace;
use Exception;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Middleware\ShareErrorsFromSession;

final class AppPanelProvider extends PanelProvider
{
    /**
     * @throws Exception
     */
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('app')
            ->path('app')
            ->databaseTransactions()
            ->databaseNotifications()
            ->unsavedChangesAlerts()
            ->spa()
            ->login()
            // ->registration()
            // ->passwordReset()
            // ->emailVerification()
            // ->profile()
            ->userMenuItems([
                'profile' => MenuItem::make()->url(
                    fn (): string => EditProfile::getUrl(
                        tenant: Filament::getTenant() ?? Auth::user()?->workspaces->first()
                    )
                ),
            ])
            ->tenant(Workspace::class)
            ->tenantRegistration(RegisterWorkspace::class)
            ->tenantProfile(EditWorkspace::class)
            // ->tenantMiddleware([
            //     ApplyTenantScopes::class,
            // ], isPersistent: true)
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                // PanelsRenderHook::BODY_END,
                PanelsRenderHook::BODY_END,
                fn () => view('footer')
            );
    }
}
