<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Policies\UserPolicy;
use Mockery;

beforeEach(function (): void {
    $this->policy = new UserPolicy;
});

it('allows actions when user has permission', function (string $method, string $permission): void {
    $user = Mockery::mock(User::class);
    $user->shouldReceive('can')
        ->with($permission)
        ->andReturn(true);

    $result = $this->policy->{$method}($user);
    expect($result)->toBeTrue();
})->with([
    ['viewAny',       'view_any_user'],
    ['view',          'view_user'],
    ['create',        'create_user'],
    ['update',        'update_user'],
    ['delete',        'delete_user'],
    ['deleteAny',     'delete_any_user'],
    ['forceDelete',   'force_delete_user'],
    ['forceDeleteAny', 'force_delete_any_user'],
    ['restore',       'restore_user'],
    ['restoreAny',    'restore_any_user'],
    ['replicate',     'replicate_user'],
    ['reorder',       'reorder_user'],
]);

it('denies actions when user lacks permission', function (string $method, string $permission): void {
    $user = Mockery::mock(User::class);
    $user->shouldReceive('can')
        ->with($permission)
        ->andReturn(false);

    $result = $this->policy->{$method}($user);
    expect($result)->toBeFalse();
})->with([
    ['viewAny',       'view_any_user'],
    ['view',          'view_user'],
    ['create',        'create_user'],
    ['update',        'update_user'],
    ['delete',        'delete_user'],
    ['deleteAny',     'delete_any_user'],
    ['forceDelete',   'force_delete_user'],
    ['forceDeleteAny', 'force_delete_any_user'],
    ['restore',       'restore_user'],
    ['restoreAny',    'restore_any_user'],
    ['replicate',     'replicate_user'],
    ['reorder',       'reorder_user'],
]);
