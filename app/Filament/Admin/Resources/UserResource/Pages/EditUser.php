<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Override;

final class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    #[Override] // @phpstan-ignore-line
    public function getTitle(): string|Htmlable
    {
        return __('admin/user-resource.edit.title');
    }

    #[Override] // @phpstan-ignore-line
    public function getSubheading(): string|Htmlable|null
    {
        return __('admin/user-resource.edit.subheading');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->hidden(fn (User $record): bool => $record->hasRole(Role::SUPER_ADMIN) || $record->deleted_at),
            Actions\ForceDeleteAction::make()
                ->hidden(fn (User $record): bool => $record->hasRole(Role::SUPER_ADMIN)),
            Actions\RestoreAction::make()
                ->hidden(fn (User $record): bool => $record->hasRole(Role::SUPER_ADMIN)),
        ];
    }

    protected function getRedirectUrl(): string
    {
        /** @var string $url */
        $url = $this->getResource()::getUrl('index');

        return $url;
    }
}
