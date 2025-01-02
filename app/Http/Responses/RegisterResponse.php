<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;
use Filament\Http\Responses\Auth\RegistrationResponse;

class RegisterResponse extends RegistrationResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        return redirect()->to(auth()->user()->usersPanel());
    }
}
