<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\LogoutResponse as BaseLogout;
use Illuminate\Http\RedirectResponse;

final class LogoutResponse extends BaseLogout
{
    public function toResponse($request): RedirectResponse
    {
        $authUrl = Filament::getPanel('auth')->hasLogin()
            ? Filament::getPanel('auth')->getLoginUrl()
            : Filament::getPanel('auth')->getUrl();

        return $authUrl ? redirect()->to($authUrl) : redirect()->route('homepage');
    }
}
