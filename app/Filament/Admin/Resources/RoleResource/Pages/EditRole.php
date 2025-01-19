<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\RoleResource\Pages;

use App\Filament\Admin\Resources\RoleResource;
use App\Models\Role;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions;
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
            Actions\DeleteAction::make()
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

    protected function afterSave(): void
    {
        $permissionModels = collect();
        $this->permissions->each(function (mixed $permission) use ($permissionModels): void {
            // @codeCoverageIgnoreStart
            /** @var ?string $guardName */
            $guardName = $this->data['guard_name'] ?? null;
            $permissionModels->push(Utils::getPermissionModel()::firstOrCreate([
                'name' => $permission,
                'guard_name' => $guardName,
            ]));
            // @codeCoverageIgnoreEnd
        });

        /** @var Role $record */
        $record = $this->record;
        $record->syncPermissions($permissionModels);
    }
}
