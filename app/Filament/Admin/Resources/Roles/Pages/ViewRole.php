<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Roles\Pages;

use App\Filament\Admin\Resources\Roles\RoleResource;
use App\Models\Role;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

final class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    protected function getActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn (): bool => auth()->user()?->can('update', $this->record)
                    && $this->record
                    && $this->record instanceof Role
                    && ! $this->record->is_default
                ),
        ];
    }
}
