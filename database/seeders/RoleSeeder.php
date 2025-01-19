<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

final class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Role::query()->updateOrCreate([
            'name' => Role::SUPER_ADMIN,
            'guard_name' => Role::GUARD_NAME_WEB,
            'is_default' => true,
        ]);
        Role::query()->updateOrCreate([
            'name' => Role::ADMIN,
            'guard_name' => Role::GUARD_NAME_WEB,
            'is_default' => true,
        ]);
        Role::query()->updateOrCreate([
            'name' => Role::AUTHENTICATED,
            'guard_name' => Role::GUARD_NAME_WEB,
            'is_default' => true,
        ]);
    }
}
