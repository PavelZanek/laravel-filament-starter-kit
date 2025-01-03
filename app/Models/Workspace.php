<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Workspace extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * @return BelongsToMany<User, Workspace>
     */
    public function users(): BelongsToMany
    {
        /** @var BelongsToMany<User, Workspace> */
        return $this->belongsToMany(User::class);
    }
}
