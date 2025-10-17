<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate as BaseAuthenticate;
use Illuminate\Http\Request;
use Override;

/**
 * Redirect unauthenticated users of any Filament panel
 * to the login page of the 'auth' panel.
 */
class FilamentAuthenticateRedirect extends BaseAuthenticate
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  Request  $request
     */
    #[Override]
    protected function redirectTo($request): ?string // @pest-ignore-type
    {
        if ($request->expectsJson()) {
            return null;
        }

        return Filament::getPanel('auth')->route(\Illuminate\Auth\Events\Login::class);
    }
}
