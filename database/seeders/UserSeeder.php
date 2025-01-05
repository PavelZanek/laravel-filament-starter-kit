<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

final class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->withWorkspaces(3)->create([
            'name' => 'Pavel',
            'email' => 'zanek.pavel@gmail.com',
        ]);
    }
}
