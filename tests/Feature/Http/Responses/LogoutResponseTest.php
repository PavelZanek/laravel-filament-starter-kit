<?php

declare(strict_types=1);

use App\Http\Responses\LogoutResponse;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Http\Request;

it('redirects to login URL if auth panel has login', function () {
    $authPanel = Mockery::mock(Panel::class); // Správný typ mocku
    $authPanel->shouldReceive('hasLogin')->once()->andReturn(true);
    $authPanel->shouldReceive('getLoginUrl')->once()->andReturn('/login');

    Filament::shouldReceive('getPanel')->with('auth')->andReturn($authPanel);

    $response = (new LogoutResponse)->toResponse(new Request);

    expect($response->getTargetUrl())->toBe(url('/login'));
});

it('redirects to auth panel URL if auth panel does not have login', function () {
    $authPanel = Mockery::mock(Panel::class); // Správný typ mocku
    $authPanel->shouldReceive('hasLogin')->once()->andReturn(false);
    $authPanel->shouldReceive('getUrl')->once()->andReturn('/auth-panel');

    Filament::shouldReceive('getPanel')->with('auth')->andReturn($authPanel);

    $response = (new LogoutResponse)->toResponse(new Request);

    expect($response->getTargetUrl())->toBe(url('/auth-panel'));
});

it('redirects to homepage route if auth panel URL is null', function () {
    $authPanel = Mockery::mock(Panel::class); // Správný typ mocku
    $authPanel->shouldReceive('hasLogin')->once()->andReturn(false);
    $authPanel->shouldReceive('getUrl')->once()->andReturn(null);

    Filament::shouldReceive('getPanel')->with('auth')->andReturn($authPanel);

    $response = (new LogoutResponse)->toResponse(new Request);

    expect($response->getTargetUrl())->toBe(route('homepage'));
});
