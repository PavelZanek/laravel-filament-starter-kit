<?php

declare(strict_types=1);

namespace App\Filament\Pages\Tenancy;

use App\Models\Workspace;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;
use Override;

final class RegisterWorkspace extends RegisterTenant
{
    public static function getLabel(): string
    {
        return __('common.workspaces.labels.register');
    }

    #[Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('common.workspaces.fields.name'))
                    ->required(),
            ]);
    }

    #[Override]
    protected function handleRegistration(array $data): Workspace
    {
        $workspace = Workspace::query()->create($data);

        $workspace->users()->attach(auth()->user());

        return $workspace;
    }
}
