<?php

declare(strict_types=1);

use App\Models\Content\Category;
use App\Models\Content\Post;

test('to array', function (): void {
    $record = Category::factory()->create()->fresh();

    expect(array_keys($record->toArray()))->toEqual([
        'id',
        'name',
        'slug',
        'created_at',
        'updated_at',
        'deleted_at',
    ]);
});

it('may have posts', function (): void {
    $record = Category::factory()->has(Post::factory()->count(3))->create();

    expect($record->posts)->toHaveCount(3);
});
