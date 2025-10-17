<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Roles\Pages;

use App\Filament\Admin\Resources\Roles\RoleResource;
use App\Models\Role;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Override;

final class CreateRole extends CreateRecord
{
    /**
     * @var Collection<int, mixed>
     */
    public Collection $permissions;

    protected static string $resource = RoleResource::class;

    #[Override]
    protected function mutateFormDataBeforeCreate(array $data): array
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

    protected function afterCreate(): void
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
