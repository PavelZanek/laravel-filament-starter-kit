<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\Blameable;
use Carbon\CarbonImmutable;
use Database\Factories\UserFactory;
use Exception;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Override;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property int|null $deleted_by_id
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property CarbonImmutable|null $deleted_at
 * @property-read User|null $createdBy
 * @property-read User|null $deletedBy
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Role> $roles
 * @property-read int|null $roles_count
 * @property-read User|null $updatedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Workspace> $workspaces
 * @property-read int|null $workspaces_count
 *
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static Builder<static>|User newModelQuery()
 * @method static Builder<static>|User newQuery()
 * @method static Builder<static>|User onlyTrashed()
 * @method static Builder<static>|User permission($permissions, $without = false)
 * @method static Builder<static>|User query()
 * @method static Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static Builder<static>|User whereCreatedAt($value)
 * @method static Builder<static>|User whereCreatedById($value)
 * @method static Builder<static>|User whereDeletedAt($value)
 * @method static Builder<static>|User whereDeletedById($value)
 * @method static Builder<static>|User whereEmail($value)
 * @method static Builder<static>|User whereEmailVerifiedAt($value)
 * @method static Builder<static>|User whereId($value)
 * @method static Builder<static>|User whereName($value)
 * @method static Builder<static>|User wherePassword($value)
 * @method static Builder<static>|User whereRememberToken($value)
 * @method static Builder<static>|User whereUpdatedAt($value)
 * @method static Builder<static>|User whereUpdatedById($value)
 * @method static Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|User withoutPermission($permissions)
 * @method static Builder<static>|User withoutRole($roles, $guard = null)
 * @method static Builder<static>|User withoutTrashed()
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable implements FilamentUser, HasDefaultTenant, HasTenants, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use Blameable, HasFactory, HasRoles, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @throws Exception
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'auth') {
            return true;
        }

        if ($panel->getId() === 'app'
            && $this->hasAnyRole(Role::SUPER_ADMIN, Role::ADMIN, Role::AUTHENTICATED)
        ) {
            return true;
        }

        return $panel->getId() === 'admin' && $this->hasAnyRole(Role::SUPER_ADMIN, Role::ADMIN);
    }

    /**
     * @return BelongsToMany<Workspace, User>
     */
    public function workspaces(): BelongsToMany
    {
        /** @var BelongsToMany<Workspace, User> */
        return $this->belongsToMany(Workspace::class);
    }

    public function canAccessTenant(Model $tenant): bool
    {
        if (! $tenant instanceof Workspace) {
            return false;
        }

        return $this->workspaces->contains($tenant);
    }

    /**
     * @return Collection<int, Workspace>
     */
    public function getTenants(Panel $panel): Collection
    {
        return $this->workspaces;
    }

    public function getDefaultTenant(Panel $panel): ?Model
    {
        return $this->workspaces->first();
    }

    public function getActiveTenant(): ?Model
    {
        return Filament::getTenant() ?? $this->getDefaultTenant(Filament::getPanel('app'));
    }

    public function usersPanel(): ?string
    {
        if (Auth::user()?->hasAnyRole(Role::SUPER_ADMIN, Role::ADMIN)) {
            return Filament::getPanel('admin')->getUrl();
        }

        return Filament::getPanel('app')->getUrl($this->getDefaultTenant(Filament::getPanel('app')));
    }

    #[Override]
    protected static function booted(): void
    {
        self::deleted(function (User $user): void {
            $user->update(['email' => $user->email.'-deleted-'.$user->id]);
        });

        self::restoring(function (User $user): void {
            $user->update(['email' => str_replace('-deleted-'.$user->id, '', $user->email)]);
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
            'deleted_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
