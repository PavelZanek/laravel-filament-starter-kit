<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Workspace;
use Filament\Panel;
use Illuminate\Support\Facades\Config;

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
    $user = User::factory()->withWorkspaces(3)->create();

    expect($user->workspaces)->toHaveCount(4); // 1 created in User::booted()
});

test('can access app panel', function (): void {
    $user = User::factory()->withWorkspaces()->create();
    $panel = mock(Panel::class)->shouldReceive('getId')->andReturn('app')->getMock();

    expect($user->canAccessPanel($panel))->toBeTrue();
});

test('can access admin panel', function (): void {
    Config::set('project.admin.allowed_email', 'allowed@example.com');

    $user = User::factory()->withWorkspaces()->create(['email' => 'allowed@example.com']);
    $panel = mock(Panel::class)->shouldReceive('getId')->andReturn('admin')->getMock();

    expect($user->canAccessPanel($panel))->toBeTrue();
});

test('denies user with disallowed email to view admin panel', function (): void {
    Config::set('project.admin.allowed_email', 'allowed@example.com');

    $user = User::factory()->withWorkspaces()->create(['email' => 'disallowed@example.com']);
    $panel = mock(Panel::class)->shouldReceive('getId')->andReturn('admin')->getMock();

    expect($user->canAccessPanel($panel))->toBeFalse();
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
    $user = User::factory()->create()->fresh();
    $workspace1 = $user->workspaces->first(); // created in User::booted()
    $workspace2 = Workspace::factory()->create();

    $user->workspaces()->attach([$workspace2->id]);

    $user->refresh();

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
