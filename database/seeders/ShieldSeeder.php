<?php

declare(strict_types=1);

namespace Database\Seeders;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = $this->getRolesWithPermissions();
        $directPermissions = $this->getDirectPermissions();

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    /**
     * Get roles with their permissions configuration.
     */
    private function getRolesWithPermissions(): string
    {
        $adminPermissions = [
            // Role permissions
            'view_role',
            'view_any_role',
            'create_role',
            'update_role',
            'delete_role',
            'delete_any_role',
            'restore_role',
            'restore_any_role',
            'replicate_role',
            'reorder_role',
            'force_delete_role',
            'force_delete_any_role',

            // User permissions
            'view_user',
            'view_any_user',
            'create_user',
            'update_user',
            'delete_user',
            'delete_any_user',
            'restore_user',
            'restore_any_user',
            'replicate_user',
            'reorder_user',
            'force_delete_user',
            'force_delete_any_user',

            // Widget permissions
            'widget_UserStatsOverview',

            // Panel access permissions
            'access_admin_panel',
            'access_app_panel',
        ];

        $roles = [
            [
                'name' => 'super_admin',
                'guard_name' => 'web',
                'permissions' => $adminPermissions,
            ],
            [
                'name' => 'admin',
                'guard_name' => 'web',
                'permissions' => $adminPermissions,
            ],
            [
                'name' => 'authenticated',
                'guard_name' => 'web',
                'permissions' => [
                    'access_app_panel',
                ],
            ],
        ];

        return json_encode($roles);
    }

    /**
     * Get direct permissions configuration.
     */
    private function getDirectPermissions(): string
    {
        $permissions = [];

        return json_encode($permissions);
    }
}
