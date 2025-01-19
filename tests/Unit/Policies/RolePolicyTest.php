<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\Role;
use App\Models\User;
use App\Policies\RolePolicy;
use Mockery;

beforeEach(function (): void {
    $this->policy = new RolePolicy;
});

it('allows actions without role parameter when user has permission', function (string $method, string $permission): void {
    $user = Mockery::mock(User::class);
    $user->shouldReceive('can')
        ->with($permission)
        ->andReturn(true);

    $result = $this->policy->{$method}($user);
    expect($result)->toBeTrue();
})->with([
    ['viewAny',        'view_any_role'],
    ['create',         'create_role'],
    ['deleteAny',      'delete_any_role'],
    ['forceDeleteAny', '{{ ForceDeleteAny }}'],
    ['restoreAny',     '{{ RestoreAny }}'],
    ['reorder',        '{{ Reorder }}'],
]);

it('denies actions without role parameter when user lacks permission', function (string $method, string $permission): void {
    $user = Mockery::mock(User::class);
    $user->shouldReceive('can')
        ->with($permission)
        ->andReturn(false);

    $result = $this->policy->{$method}($user);
    expect($result)->toBeFalse();
})->with([
    ['viewAny',        'view_any_role'],
    ['create',         'create_role'],
    ['deleteAny',      'delete_any_role'],
    ['forceDeleteAny', '{{ ForceDeleteAny }}'],
    ['restoreAny',     '{{ RestoreAny }}'],
    ['reorder',        '{{ Reorder }}'],
]);

it('allows view when user has view_role permission', function (): void {
    $permission = 'view_role';
    $user = Mockery::mock(User::class);
    $user->shouldReceive('can')
        ->with($permission)
        ->andReturn(true);

    $role = new Role;
    $result = $this->policy->view($user, $role);
    expect($result)->toBeTrue();
});

it('denies view when user lacks view_role permission', function (): void {
    $permission = 'view_role';
    $user = Mockery::mock(User::class);
    $user->shouldReceive('can')
        ->with($permission)
        ->andReturn(false);

    $role = new Role;
    $result = $this->policy->view($user, $role);
    expect($result)->toBeFalse();
});

foreach ([
    'replicate' => '{{ Replicate }}',
] as $method => $permission) {
    it("allows {$method} when user has permission", function () use ($method, $permission): void {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')
            ->with($permission)
            ->andReturn(true);

        $role = new Role(['is_default' => true]);
        $result = $this->policy->{$method}($user, $role);
        expect($result)->toBeTrue();
    });

    it("denies {$method} when user lacks permission", function () use ($method, $permission): void {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')
            ->with($permission)
            ->andReturn(false);

        $role = new Role(['is_default' => false]);
        $result = $this->policy->{$method}($user, $role);
        expect($result)->toBeFalse();
    });
}

foreach ([
    'update' => 'update_role',
    'delete' => 'delete_role',
    'forceDelete' => '{{ ForceDelete }}',
    'restore' => '{{ Restore }}',
] as $method => $permission) {
    it("allows {$method} when user has permission and role is not default", function () use ($method, $permission): void {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')
            ->with($permission)
            ->andReturn(true);

        $role = new Role(['is_default' => false]);
        $result = $this->policy->{$method}($user, $role);
        expect($result)->toBeTrue();
    });

    it("denies {$method} when role is default regardless of permission", function () use ($method, $permission): void {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')
            ->with($permission)
            ->andReturn(false);

        $role = new Role(['is_default' => true]);
        $result = $this->policy->{$method}($user, $role);
        expect($result)->toBeFalse();
    });
}

afterEach(function (): void {
    Mockery::close();
});
