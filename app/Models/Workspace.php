<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\WorkspaceFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 *
 * @method static WorkspaceFactory factory($count = null, $state = [])
 * @method static Builder<static>|Workspace newModelQuery()
 * @method static Builder<static>|Workspace newQuery()
 * @method static Builder<static>|Workspace query()
 * @method static Builder<static>|Workspace whereCreatedAt($value)
 * @method static Builder<static>|Workspace whereId($value)
 * @method static Builder<static>|Workspace whereName($value)
 * @method static Builder<static>|Workspace whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
final class Workspace extends Model
{
    /** @use HasFactory<WorkspaceFactory> */
    use HasFactory;

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
