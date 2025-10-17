<?php

declare(strict_types=1);

use App\Http\Middleware\FilamentAuthenticateRedirect;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\Request;

beforeEach(function (): void {
    // Mock the Auth factory required by the parent middleware constructor
    $this->authFactory = mock(AuthFactory::class);
});

it('returns null when request expects json', function (): void {
    // Mock request that expects JSON
    $request = mock(Request::class);
    $request->shouldReceive('expectsJson')->andReturn(true);

    $middleware = new FilamentAuthenticateRedirect($this->authFactory);

    // Call protected method redirectTo via closure binding
    $redirectTo = (function (Request $req): ?string {
        return $this->redirectTo($req);
    })->call($middleware, $request);

    expect($redirectTo)->toBeNull();
});

it('redirects to auth panel login when request is not json', function (): void {
    // Mock request that does not expect JSON
    $request = mock(Request::class);
    $request->shouldReceive('expectsJson')->andReturn(false);

    // Mock the Filament auth panel and its login route
    $panel = mock(Panel::class);
    $panel->shouldReceive('route')
        ->with(Illuminate\Auth\Events\Login::class)
        ->andReturn('/auth/login');

    Filament::shouldReceive('getPanel')
        ->with('auth')
        ->andReturn($panel);

    $middleware = new FilamentAuthenticateRedirect($this->authFactory);

    // Call protected method redirectTo via closure binding
    $redirectTo = (function (Request $req): ?string {
        return $this->redirectTo($req);
    })->call($middleware, $request);

    expect($redirectTo)->toBe('/auth/login');
});
