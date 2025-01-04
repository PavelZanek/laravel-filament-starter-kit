<?php

declare(strict_types=1);

use App\Http\Responses\LoginResponse;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

it('redirects to usersPanel if user has one', function () {
    $user = Mockery::mock();
    $user->shouldReceive('usersPanel')->twice()->andReturn('/user-panel');

    Auth::shouldReceive('user')->andReturn($user);

    $response = (new LoginResponse)->toResponse(new Request);

    expect($response->getTargetUrl())->toBe(url('/user-panel'));
});

it('redirects to Filament URL if user has no usersPanel', function () {
    $user = Mockery::mock();
    $user->shouldReceive('usersPanel')->once()->andReturn(null);

    Auth::shouldReceive('user')->andReturn($user);

    Filament::shouldReceive('getUrl')->once()->andReturn('/dashboard');

    $response = (new LoginResponse)->toResponse(new Request);

    expect($response->getTargetUrl())->toBe(url('/dashboard'));
});
