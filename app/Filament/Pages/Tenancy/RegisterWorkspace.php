<?php

declare(strict_types=1);

namespace App\Filament\Pages\Tenancy;

use App\Models\Workspace;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;

final class RegisterWorkspace extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register workspace';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
            ]);
    }

    protected function handleRegistration(array $data): Workspace
    {
        $workspace = Workspace::query()->create($data);

        $workspace->users()->attach(auth()->user());

        return $workspace;
    }
}
