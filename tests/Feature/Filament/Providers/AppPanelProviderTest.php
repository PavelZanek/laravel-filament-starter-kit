<?php

declare(strict_types=1);

use App\Filament\Pages\Tenancy\EditWorkspace;
use App\Filament\Pages\Tenancy\RegisterWorkspace;
use App\Models\Workspace;
use App\Providers\Filament\AppPanelProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

it('configures AppPanelProvider correctly', function (): void {
    $provider = new AppPanelProvider(app());
    $panel = new Panel;
    $configuredPanel = $provider->panel($panel);

    expect($configuredPanel->getId())->toBe('app')
        ->and($configuredPanel->getPath())->toBe('app')
        ->and($configuredPanel->isDefault())->toBeTrue()
        ->and($configuredPanel->getTenantModel())->toBe(Workspace::class)
        ->and($configuredPanel->getTenantRegistrationPage())->toBe(RegisterWorkspace::class)
        ->and($configuredPanel->getTenantProfilePage())->toBe(EditWorkspace::class)
        ->and($configuredPanel->getColors())->toEqual(['primary' => Color::Emerald]);

    $middleware = $configuredPanel->getMiddleware();
    expect($middleware)->toContain(VerifyCsrfToken::class);

    $authMiddleware = $configuredPanel->getAuthMiddleware();
    expect($authMiddleware)->toContain(Authenticate::class);
});
