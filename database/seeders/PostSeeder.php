<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Content\Category;
use App\Models\Content\Comment;
use App\Models\Content\Post;
use App\Models\Content\Tag;
use Illuminate\Database\Seeder;

final class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Post::factory()
            ->count(100)
            ->has(Comment::factory()->count(3))
            ->hasAttached(Category::factory()->count(3))
            ->hasAttached(Tag::factory()->count(3))
            ->create();
    }
}
