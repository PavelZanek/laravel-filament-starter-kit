<?php

declare(strict_types=1);

namespace App\Filament\Auth;

use App\Models\Role;
use App\Models\Workspace;
use Filament\Pages\Auth\Register;
use Illuminate\Database\Eloquent\Model;
use Override;

final class CustomRegister extends Register
{
    /**
     * @param  array<string, mixed>  $data
     */
    #[Override]
    protected function handleRegistration(array $data): Model
    {
        /** @var \App\Models\User $user */
        $user = $this->getUserModel()::create($data);

        $user->roles()->attach(
            Role::query()
                ->where('name', Role::AUTHENTICATED)
                ->where('guard_name', Role::GUARD_NAME_WEB)
                ->first()
        );

        $user->workspaces()->attach(Workspace::query()->create([
            'name' => $user->name."'s Workspace",
        ]));

        return $user;
    }
}
