<?php

declare(strict_types=1);

use App\Models\Workspace;

test('to array', function (): void {
    $workspace = Workspace::factory()->create()->fresh();

    expect(array_keys($workspace->toArray()))->toEqual([
        'id',
        'name',
        'created_at',
        'updated_at',
    ]);
});

it('may have users', function (): void {
    $workspace = Workspace::factory()->withUsers(3)->create();

    expect($workspace->users)->toHaveCount(3);
});
