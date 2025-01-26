<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $localSeeders = [];
        if (app()->environment('local')) {
            $localSeeders = [
                FakeUserSeeder::class,
                PostSeeder::class,
            ];
        }

        $this->call([
            RoleSeeder::class,
            ShieldSeeder::class,
            UserSeeder::class,
            ...$localSeeders,
        ]);
    }
}
