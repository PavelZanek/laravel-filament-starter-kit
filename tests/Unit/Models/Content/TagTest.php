<?php

declare(strict_types=1);

use App\Models\Content\Post;
use App\Models\Content\Tag;

test('to array', function (): void {
    $record = Tag::factory()->create()->fresh();

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
    $record = Tag::factory()->has(Post::factory()->count(3))->create();

    expect($record->posts)->toHaveCount(3);
});
