<?php

declare(strict_types=1);

namespace App\Models;

use Exception;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Override;
use Spatie\Permission\Traits\HasRoles;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable implements FilamentUser, HasDefaultTenant, HasTenants, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

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

    public function usersPanel(): ?string
    {
        if (auth()->user()?->hasAnyRole(Role::SUPER_ADMIN, Role::ADMIN)) {
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
