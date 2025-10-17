<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\Blameable;
use Carbon\CarbonImmutable;
use Database\Factories\RoleFactory;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property bool $is_default
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property int|null $deleted_by_id
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property CarbonImmutable|null $deleted_at
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read User|null $updatedBy
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 *
 * @method static \Database\Factories\RoleFactory factory($count = null, $state = [])
 * @method static Builder<static>|Role newModelQuery()
 * @method static Builder<static>|Role newQuery()
 * @method static Builder<static>|Role onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role permission($permissions, $without = false)
 * @method static Builder<static>|Role query()
 * @method static Builder<static>|Role whereCreatedAt($value)
 * @method static Builder<static>|Role whereCreatedById($value)
 * @method static Builder<static>|Role whereDeletedAt($value)
 * @method static Builder<static>|Role whereDeletedById($value)
 * @method static Builder<static>|Role whereGuardName($value)
 * @method static Builder<static>|Role whereId($value)
 * @method static Builder<static>|Role whereIsDefault($value)
 * @method static Builder<static>|Role whereName($value)
 * @method static Builder<static>|Role whereUpdatedAt($value)
 * @method static Builder<static>|Role whereUpdatedById($value)
 * @method static Builder<static>|Role withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role withoutPermission($permissions)
 * @method static Builder<static>|Role withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Role extends SpatieRole
{
    /** @use HasFactory<RoleFactory> */
    use Blameable, HasFactory, SoftDeletes;

    /**
     * Default user roles
     */
    public const string SUPER_ADMIN = 'super_admin';

    public const string ADMIN = 'admin';

    public const string AUTHENTICATED = 'authenticated';

    public const array ROLES = [
        self::SUPER_ADMIN => 'Super Admin',
        self::ADMIN => 'Admin',
        self::AUTHENTICATED => 'Authenticated',
    ];

    /**
     * Guard names
     */
    public const string GUARD_NAME_WEB = 'web';

    public const string GUARD_NAME_API = 'api';

    public const array GUARD_NAMES = [
        self::GUARD_NAME_WEB => 'Web',
        self::GUARD_NAME_API => 'API',
    ];

    #[Override]
    protected static function booted(): void
    {
        self::deleting(function (Role $role): void {
            if ($role->is_default) {
                throw new Exception('Default roles cannot be deleted', 403);
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'deleted_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
