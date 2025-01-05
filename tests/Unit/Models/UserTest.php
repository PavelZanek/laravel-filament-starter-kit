<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Workspace;
use Filament\Panel;

use function Pest\Laravel\actingAs;

test('to array', function (): void {
    $user = User::factory()->create()->fresh();

    expect(array_keys($user->toArray()))->toEqual([
        'id',
        'name',
        'email',
        'email_verified_at',
        'created_at',
        'updated_at',
    ]);
});

it('may have workspaces', function (): void {
    $user = User::factory()->hasWorkspaces(3)->create();

    expect($user->workspaces)->toHaveCount(3);
});

test('can access panel', function (): void {
    $user = User::factory()->withWorkspaces()->create();
    $panel = mock(Panel::class);

    expect($user->canAccessPanel($panel))->toBeTrue();
});

test('cannot access invalid tenant', function (): void {
    $user = User::factory()->create();
    $invalidTenant = new class extends Illuminate\Database\Eloquent\Model {};

    expect($user->canAccessTenant($invalidTenant))->toBeFalse();
});

test('can access valid tenant', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $user->workspaces()->attach($workspace);

    expect($user->canAccessTenant($workspace))->toBeTrue();
});

test('get tenants returns workspaces', function (): void {
    $user = User::factory()->create();
    $workspace1 = Workspace::factory()->create();
    $workspace2 = Workspace::factory()->create();

    $user->workspaces()->attach([$workspace1->id, $workspace2->id]);

    $panel = mock(Panel::class);

    expect($user->getTenants($panel))->toHaveCount(2)
        ->and($user->getTenants($panel)->pluck('id')->toArray())
        ->toContain($workspace1->id, $workspace2->id);
});

test('usersPanel returns admin panel for admin email', function (): void {
    $user = User::factory()->withWorkspaces()->create(['email' => 'zanek.pavel@gmail.com']);
    actingAs($user);

    expect($user->usersPanel())->toContain('/admin');
})->skip();

test('usersPanel returns app panel for default user', function (): void {
    $user = User::factory()->withWorkspaces()->create(['email' => 'user@example.com']);
    actingAs($user);

    expect($user->usersPanel())->toContain('/app');
});
