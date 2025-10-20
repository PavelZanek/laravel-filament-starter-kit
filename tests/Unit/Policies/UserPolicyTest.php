<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Policies\UserPolicy;
use Mockery;

beforeEach(function (): void {
    $this->policy = new UserPolicy;
});

it('allows actions when user has permission', function (string $method, string $permission, bool $requiresModel = false): void {
    $user = Mockery::mock(User::class);
    $user->shouldReceive('can')
        ->with($permission)
        ->andReturn(true);

    if ($requiresModel) {
        $model = Mockery::mock(User::class);
        $model->shouldReceive('hasRole')
            ->with('super_admin')
            ->andReturn(false);
        $result = $this->policy->{$method}($user, $model);
    } else {
        $result = $this->policy->{$method}($user);
    }

    expect($result)->toBeTrue();
})->with([
    ['viewAny',       'view_any_user', false],
    ['view',          'view_user', false],
    ['create',        'create_user', false],
    ['update',        'update_user', true],
    ['delete',        'delete_user', true],
    ['deleteAny',     'delete_any_user', false],
    ['forceDelete',   'force_delete_user', true],
    ['forceDeleteAny', 'force_delete_any_user', false],
    ['restore',       'restore_user', true],
    ['restoreAny',    'restore_any_user', false],
    ['replicate',     'replicate_user', true],
    ['reorder',       'reorder_user', false],
]);

it('denies actions when user lacks permission', function (string $method, string $permission, bool $requiresModel = false): void {
    $user = Mockery::mock(User::class);
    $user->shouldReceive('can')
        ->with($permission)
        ->andReturn(false);

    if ($requiresModel) {
        $model = Mockery::mock(User::class);
        $model->shouldReceive('hasRole')
            ->with('super_admin')
            ->andReturn(false);
        $result = $this->policy->{$method}($user, $model);
    } else {
        $result = $this->policy->{$method}($user);
    }

    expect($result)->toBeFalse();
})->with([
    ['viewAny',       'view_any_user', false],
    ['view',          'view_user', false],
    ['create',        'create_user', false],
    ['update',        'update_user', true],
    ['delete',        'delete_user', true],
    ['deleteAny',     'delete_any_user', false],
    ['forceDelete',   'force_delete_user', true],
    ['forceDeleteAny', 'force_delete_any_user', false],
    ['restore',       'restore_user', true],
    ['restoreAny',    'restore_any_user', false],
    ['replicate',     'replicate_user', true],
    ['reorder',       'reorder_user', false],
]);

it('denies operations on super admin users', function (string $method): void {
    $user = Mockery::mock(User::class);
    $user->shouldReceive('can')
        ->andReturn(true);

    $model = Mockery::mock(User::class);
    $model->shouldReceive('hasRole')
        ->with('super_admin')
        ->andReturn(true);

    $result = $this->policy->{$method}($user, $model);
    expect($result)->toBeFalse();
})->with([
    'update',
    'delete',
    'forceDelete',
    'restore',
    'replicate',
]);
