<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->withWorkspaces(3)->create([
            'name' => config()->string('project.super_admin_name'),
            'email' => config()->string('project.super_admin_email'),
            'password' => Hash::make(config()->string('project.super_admin_password')),
        ]);
        $user->assignRole(Role::SUPER_ADMIN);
    }
}
