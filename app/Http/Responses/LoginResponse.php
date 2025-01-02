<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;
use Filament\Http\Responses\Auth\LoginResponse as BaseLogin;

class LoginResponse extends BaseLogin
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        return redirect()->to(auth()->user()->usersPanel());
    }
}
