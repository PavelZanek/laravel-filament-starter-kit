<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

final class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->withWorkspaces(3)->create([
            'name' => 'Pavel',
            'email' => 'zanek.pavel@gmail.com',
        ]);
    }
}
