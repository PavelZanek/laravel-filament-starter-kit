<?php

declare(strict_types=1);

use App\Models\Content\Category;
use App\Models\Content\Comment;
use App\Models\Content\Post;
use App\Models\Content\Tag;

test('to array', function (): void {
    $record = Post::factory()->create()->fresh();

    expect(array_keys($record->toArray()))->toEqual([
        'id',
        'name',
        'slug',
        'published_at',
        'content',
        'created_at',
        'updated_at',
        'deleted_at',
    ]);
});

it('may have comments', function (): void {
    $record = Post::factory()->has(Comment::factory()->count(3))->create();

    expect($record->comments)->toHaveCount(3);
});

it('may have categories', function (): void {
    $record = Post::factory()->hasAttached(Category::factory()->count(3))->create();

    expect($record->categories)->toHaveCount(3);
});

it('may have tags', function (): void {
    $record = Post::factory()->hasAttached(Tag::factory()->count(3))->create();

    expect($record->tags)->toHaveCount(3);
});
