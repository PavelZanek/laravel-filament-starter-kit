<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Filament\Auth\Http\Responses\RegistrationResponse;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;
use Override;

final class RegisterResponse extends RegistrationResponse
{
    #[Override]
    public function toResponse($request): RedirectResponse|Redirector // @pest-ignore-type
    {
        return auth()->user()?->usersPanel()
            ? redirect()->to(auth()->user()->usersPanel())
            : redirect()->intended(Filament::getUrl());
    }
}
