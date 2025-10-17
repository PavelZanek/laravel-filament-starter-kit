<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Filament\Admin\Resources\Users\UserResource;
use App\Models\Role;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Override;

final class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    #[Override]
    public function getTitle(): string
    {
        return __('admin/user-resource.edit.title');
    }

    #[Override]
    public function getSubheading(): string
    {
        return __('admin/user-resource.edit.subheading');
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->hidden(fn (User $record): bool => $record->hasRole(Role::SUPER_ADMIN) || $record->deleted_at),
            ForceDeleteAction::make()
                ->hidden(fn (User $record): bool => $record->hasRole(Role::SUPER_ADMIN)),
            RestoreAction::make()
                ->hidden(fn (User $record): bool => $record->hasRole(Role::SUPER_ADMIN)),
        ];
    }
}
