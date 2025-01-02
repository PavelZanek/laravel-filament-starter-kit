<?php

namespace App\Http\Responses;

use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Filament\Http\Responses\Auth\LogoutResponse as BaseLogout;

class LogoutResponse extends BaseLogout
{
    public function toResponse($request): RedirectResponse
    {
        return redirect()->to(
            Filament::getPanel('auth')->hasLogin()
                ? Filament::getPanel('auth')->getLoginUrl()
                : Filament::getPanel('auth')->getUrl(),
        );
    }
}
