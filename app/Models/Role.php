<?php

declare(strict_types=1);

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Override;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * @mixin IdeHelperRole
 */
class Role extends SpatieRole
{
    /** @use HasFactory<\Database\Factories\RoleFactory> */
    use HasFactory;

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
            // @codeCoverageIgnoreStart
            throw new Exception('Default roles cannot be deleted', 403);
            // @codeCoverageIgnoreEnd
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
        ];
    }
}
