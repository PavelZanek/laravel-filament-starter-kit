<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workspace>
 */
final class WorkspaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
        ];
    }

    /**
     * Attach users to the workspace.
     */
    public function withUsers(int $count = 1): static
    {
        return $this->afterCreating(function (Workspace $workspace) use ($count): void {
            $users = User::factory()->count($count)->create();
            $workspace->users()->attach($users);
        });
    }
}
