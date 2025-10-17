<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;
use Override;

final class LoginResponse extends \Filament\Auth\Http\Responses\LoginResponse
{
    #[Override]
    public function toResponse($request): RedirectResponse|Redirector // @pest-ignore-type
    {
        return auth()->user()?->usersPanel()
            ? redirect()->to(auth()->user()->usersPanel())
            : redirect()->intended(Filament::getUrl());
    }
}
