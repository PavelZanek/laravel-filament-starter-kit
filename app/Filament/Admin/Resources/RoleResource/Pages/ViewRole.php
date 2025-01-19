<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\RoleResource\Pages;

use App\Filament\Admin\Resources\RoleResource;
use App\Models\Role;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

final class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn (): bool => auth()->user()?->can('update', $this->record)
                    && $this->record
                    && $this->record instanceof Role
                    && ! $this->record->is_default
                ),
        ];
    }
}
