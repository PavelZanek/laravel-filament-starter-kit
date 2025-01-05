<?php

declare(strict_types=1);

namespace App\Filament\Pages\Tenancy;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;
use Override;

final class EditWorkspace extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return __('common.workspaces.labels.settings');
    }

    #[Override]
    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label(__('common.workspaces.fields.name'))
                ->required(),
        ]);
    }
}
