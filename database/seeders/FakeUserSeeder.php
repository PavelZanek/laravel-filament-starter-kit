<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

final class FakeUserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()
            ->withRole(Role::ADMIN)
            ->withWorkspaces(3)
            ->create([
                'name' => 'Admin User',
                'email' => 'admin@test.com',
            ]);

        User::factory()
            ->withRole()
            ->withWorkspaces(3)
            ->create([
                'name' => 'Authenticated User',
                'email' => 'authenticated@test.com',
            ]);

        User::factory(150)
            ->withRole()
            ->withWorkspaces()
            ->create()
            ->each(function (User $user): void {
                if (fake()->boolean(chanceOfGettingTrue: 10)) {
                    $user->roles()->sync(
                        Role::query()
                            ->where('name', Role::ADMIN)
                            ->where('guard_name', Role::GUARD_NAME_WEB)
                            ->firstOrFail()
                    );
                }

                if (fake()->boolean(chanceOfGettingTrue: 10)) {
                    $user->delete(); // Soft delete
                }
            });
    }
}
