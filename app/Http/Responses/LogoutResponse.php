<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Override;

final class LogoutResponse extends \Filament\Auth\Http\Responses\LogoutResponse
{
    #[Override]
    public function toResponse($request): RedirectResponse // @pest-ignore-type
    {
        $authUrl = Filament::getPanel('auth')->hasLogin()
            ? Filament::getPanel('auth')->getLoginUrl()
            : Filament::getPanel('auth')->getUrl();

        return $authUrl ? redirect()->to($authUrl) : redirect()->route('homepage');
    }
}
