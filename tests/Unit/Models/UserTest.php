<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use App\Models\Workspace;
use Filament\Facades\Filament;
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
        'deleted_at',
    ]);
});

it('may have workspaces', function (): void {
    $user = User::factory()->withWorkspaces(3)->create();

    expect($user->workspaces)->toHaveCount(3);
});

test('can always access auth panel', function (): void {
    $user = User::factory()->create();
    $panel = mock(Panel::class)->shouldReceive('getId')->andReturn('auth')->getMock();

    expect($user->canAccessPanel($panel))->toBeTrue();
});

test('cannot access panel with unknown id', function (): void {
    $user = User::factory()->create();
    $panel = mock(Panel::class)->shouldReceive('getId')->andReturn('unknown')->getMock();

    expect($user->canAccessPanel($panel))->toBeFalse();
});

test('can access app panel', function (): void {
    $user = User::factory()->withWorkspaces()->withRole()->create();
    $panel = mock(Panel::class)->shouldReceive('getId')->andReturn('app')->getMock();

    expect($user->canAccessPanel($panel))->toBeTrue();
});

test('can access admin panel', function (string $role): void {
    $user = User::factory()->withWorkspaces()->withRole($role)->create();
    $panel = mock(Panel::class)->shouldReceive('getId')->andReturn('admin')->getMock();

    expect($user->canAccessPanel($panel))->toBeTrue();
})->with([Role::ADMIN, Role::SUPER_ADMIN]);

test('denies user with disallowed email to view admin panel', function (): void {
    $user = User::factory()->withWorkspaces()->withRole()->create();
    $panel = mock(Panel::class)->shouldReceive('getId')->andReturn('admin')->getMock();

    expect($user->canAccessPanel($panel))->toBeFalse();
});

test('cannot access invalid tenant', function (): void {
    $user = User::factory()->create();
    $invalidTenant = new class extends Illuminate\Database\Eloquent\Model {};

    expect($user->canAccessTenant($invalidTenant))->toBeFalse();
});

test('can access valid tenant', function (): void {
    $user = User::factory()->withWorkspaces()->withRole()->create();

    expect($user->canAccessTenant($user->workspaces()->first()))->toBeTrue();
});

test('get tenants returns workspaces', function (): void {
    $user = User::factory()->withWorkspaces()->withRole()->create();
    actingAs($user);

    $workspace1 = $user->workspaces->first();
    $workspace2 = Workspace::factory()->create();

    $user->workspaces()->attach([$workspace2->id]);

    $user->refresh();

    $panel = mock(Panel::class);

    expect($user->getTenants($panel))->toHaveCount(2)
        ->and($user->getTenants($panel)->pluck('id')->toArray())
        ->toContain($workspace1->id, $workspace2->id);
});

test('usersPanel returns admin panel for specific role', function (string $role): void {
    $user = User::factory()->withWorkspaces()->withRole($role)->create();
    actingAs($user);

    Filament::setTenant($user->getDefaultTenant(Filament::getPanel('app')));

    expect($user->usersPanel())->toContain('/admin');
})->with([Role::ADMIN, Role::SUPER_ADMIN]);

test('usersPanel returns app panel for authenticated role', function (): void {
    $user = User::factory()->withWorkspaces()->withRole()->create();
    actingAs($user);

    expect($user->usersPanel())->toContain('/app');
});

test('updates email on delete (soft delete)', function (): void {
    $user = User::factory()->create();
    $originalEmail = $user->email;
    $userId = $user->id;

    $user->delete();

    $trashedUser = User::withTrashed()->find($userId);

    expect($trashedUser->email)->toBe("{$originalEmail}-deleted-{$userId}");
});

test('restores email on restore', function (): void {
    $user = User::factory()->create();
    $originalEmail = $user->email;
    $userId = $user->id;
    $user->delete();

    $trashedUser = User::withTrashed()->find($userId);
    $trashedUser->restore();

    $restoredUser = User::query()->find($userId);

    expect($restoredUser->email)->toBe($originalEmail);
});
