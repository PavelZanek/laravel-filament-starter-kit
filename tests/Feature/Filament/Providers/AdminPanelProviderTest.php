<?php

declare(strict_types=1);

use App\Providers\Filament\AdminPanelProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

it('configures AdminPanelProvider correctly', function (): void {
    $provider = new AdminPanelProvider(app());
    $panel = new Panel;

    $configuredPanel = $provider->panel($panel);

    expect($configuredPanel->getId())->toBe('admin')
        ->and($configuredPanel->getPath())->toBe('admin')
        ->and($configuredPanel->getColors())->toEqual(['primary' => Color::Indigo])
        ->and($configuredPanel->getUserMenuItems())->toBeEmpty();

    $middleware = $configuredPanel->getMiddleware();
    expect($middleware)->toContain(VerifyCsrfToken::class)
        ->and($middleware)->toContain(AuthenticateSession::class);

    $plugins = $configuredPanel->getPlugins();
    expect($plugins)->toHaveCount(1);

    $authMiddleware = $configuredPanel->getAuthMiddleware();
    expect($authMiddleware)->toContain(Authenticate::class);
});
