<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Roles\Pages;

use App\Filament\Admin\Resources\Roles\RoleResource;
use App\Models\Role;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Override;

final class EditRole extends EditRecord
{
    /**
     * @var Collection<int, mixed>
     */
    public Collection $permissions;

    protected static string $resource = RoleResource::class;

    #[Override]
    public static function canAccess(array $parameters = []): bool
    {
        /** @var Role $record */
        $record = $parameters['record'];

        return auth()->user()?->can('update', RoleResource::getModel())
            && ! $record->is_default;
    }

    protected function getActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (): bool => auth()->user()?->can('delete', $this->record)
                    && $this->record
                    && $this->record instanceof Role
                    && ! $this->record->is_default
                ),
        ];
    }

    #[Override]
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->permissions = collect($data)
            ->filter(function (mixed $permission, string $key): bool {
                return ! in_array($key, ['name', 'guard_name', 'select_all', Utils::getTenantModelForeignKey()]);
            })
            ->values()
            ->flatten()
            ->unique();

        if (Arr::has($data, Utils::getTenantModelForeignKey())) {
            // @codeCoverageIgnoreStart
            /** @var array<string, mixed> $result */
            $result = Arr::only($data, ['name', 'guard_name', Utils::getTenantModelForeignKey()]);

            return $result;
            // @codeCoverageIgnoreEnd
        }

        /** @var array<string, mixed> $result */
        $result = Arr::only($data, ['name', 'guard_name']);

        return $result;
    }

    private function afterSave(): void
    {
        if ($this->permissions->isNotEmpty()) {
            $this->record->syncPermissions($this->permissions);
        }
    }
}
