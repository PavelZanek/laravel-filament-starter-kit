<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
final class UserFactory extends Factory
{
    use WithoutModelEvents;

    /**
     * The current password being used by the factory.
     */
    private static ?string $password; // @phpstan-ignore-line

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => self::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes): array => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Attach workspaces to the user.
     */
    public function withWorkspaces(int $count = 1): static
    {
        return $this->afterCreating(function (User $user) use ($count): void {
            $workspaces = Workspace::factory()
                ->count($count)
                ->sequence(fn (Sequence $sequence): array => ['name' => 'Workspace '.($sequence->index + 1)])
                ->create();

            $user->workspaces()->attach($workspaces);
        });
    }

    /**
     * Attach role to the user.
     */
    public function withRole(string $roleName = Role::AUTHENTICATED, string $guardName = Role::GUARD_NAME_WEB): static
    {
        return $this->afterCreating(function (User $user) use ($roleName, $guardName): void {
            $role = Role::query()
                ->where('name', $roleName)
                ->where('guard_name', $guardName)
                ->first();

            if (! $role) {
                $role = Role::factory()->create([
                    'name' => $roleName,
                    'guard_name' => $guardName,
                ]);
            }

            $user->roles()->sync($role);
        });
    }
}
