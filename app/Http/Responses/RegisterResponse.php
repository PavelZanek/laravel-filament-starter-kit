<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\RegistrationResponse;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

final class RegisterResponse extends RegistrationResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        return redirect()->to(auth()->user()->usersPanel());
    }
}
