<?php

declare(strict_types=1);

namespace Tests\Feature\Filament\Auth;

use App\Filament\Auth\CustomRegister;
use App\Models\Role;
use App\Models\User;
use ReflectionClass;

it('handles registration correctly using reflection', function (): void {
    Role::factory()->create([
        'name' => Role::AUTHENTICATED,
        'guard_name' => Role::GUARD_NAME_WEB,
        'is_default' => true,
    ]);

    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Password!123',
    ];

    $register = new CustomRegister;

    $reflection = new ReflectionClass($register);
    $method = $reflection->getMethod('handleRegistration');
    $method->setAccessible(true);

    /** @var User $user */
    $user = $method->invoke($register, $data);

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->toBe('Test User')
        ->and($user->email)->toBe('test@example.com')
        ->and($user->roles()->where('name', Role::AUTHENTICATED)->exists())->toBeTrue();

    $workspace = $user->workspaces()->first();
    expect($workspace)->not->toBeNull()
        ->and($workspace->name)->toBe("Test User's Workspace");
});
