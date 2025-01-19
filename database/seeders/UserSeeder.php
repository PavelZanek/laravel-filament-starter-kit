<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

final class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->withWorkspaces(3)->create([
            'name' => 'Pavel',
            'email' => 'zanek.pavel@gmail.com',
        ]);
        $user->assignRole(Role::SUPER_ADMIN);
    }
}
