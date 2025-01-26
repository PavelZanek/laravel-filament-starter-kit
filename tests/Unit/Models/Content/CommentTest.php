<?php

declare(strict_types=1);

use App\Models\Content\Comment;
use App\Models\Content\Post;

test('to array', function (): void {
    $record = Comment::factory()->for(Post::factory(), 'commentable')->create()->fresh();

    expect(array_keys($record->toArray()))->toEqual([
        'id',
        'user_id',
        'commentable_type',
        'commentable_id',
        'content',
        'created_at',
        'updated_at',
        'deleted_at',
    ]);
});

it('belongs to user and commentable', function (): void {
    $record = Comment::factory()->for(Post::factory(), 'commentable')->create();

    expect($record->user)->not()->toBeNull()
        ->and($record->user)->toBeInstanceOf(App\Models\User::class)
        ->and($record->commentable)->toBeInstanceOf(Post::class);
});
