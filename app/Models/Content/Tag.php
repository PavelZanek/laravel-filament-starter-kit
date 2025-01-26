<?php

declare(strict_types=1);

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperTag
 */
class Tag extends Model
{
    /** @use HasFactory<\Database\Factories\Content\TagFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * @return MorphToMany<Post, $this>
     */
    public function posts(): MorphToMany
    {
        return $this->morphedByMany(Post::class, 'taggable');
    }
}
