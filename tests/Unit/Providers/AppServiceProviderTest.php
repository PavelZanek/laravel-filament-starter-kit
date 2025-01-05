<?php

declare(strict_types=1);

use App\Http\Responses\LoginResponse as CustomLoginResponse;
use App\Http\Responses\LogoutResponse as CustomLogoutResponse;
use App\Http\Responses\RegisterResponse as CustomRegisterResponse;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Illuminate\Support\Facades\App;

it('registers singleton bindings', function (): void {
    $loginResponse = App::make(LoginResponse::class);
    $registrationResponse = App::make(RegistrationResponse::class);
    $logoutResponse = App::make(LogoutResponse::class);

    expect($loginResponse)->toBeInstanceOf(CustomLoginResponse::class)
        ->and($registrationResponse)->toBeInstanceOf(CustomRegisterResponse::class)
        ->and($logoutResponse)->toBeInstanceOf(CustomLogoutResponse::class);
});
