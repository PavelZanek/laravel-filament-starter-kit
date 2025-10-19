<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Role;
use App\Models\User;
use Exception;

test('to array', function (): void {
    $user = Role::factory()->create()->fresh();

    expect(array_keys($user->toArray()))->toEqual([
        'id',
        'name',
        'guard_name',
        'is_default',
        'created_by_id',
        'updated_by_id',
        'deleted_by_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ]);
});

it('may have users', function (): void {
    $user = User::factory()->withRole()->create();

    expect($user->roles)->toHaveCount(1);
});

it('cannot delete default role', function (): void {
    $record = Role::factory()->create(['name' => 'Test', 'is_default' => true]);

    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Default roles cannot be deleted');
    $this->expectExceptionCode(403);

    $record->delete();
})->skip('Need to fix the test - sometimes it fails');
