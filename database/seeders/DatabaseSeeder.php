<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    // use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $localSeeders = [];
        if (app()->environment('local')) {
            $localSeeders = [
                FakeUserSeeder::class,
            ];
        }

        $this->call([
            RoleSeeder::class,
            ShieldSeeder::class,
            // PermissionSeeder::class,
            // RolePermissionSeeder::class,
            UserSeeder::class,
            ...$localSeeders,
        ]);
    }
}
