<?php

declare(strict_types=1);

namespace App\Models;

use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

final class User extends Authenticatable implements FilamentUser, HasTenants, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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

    public function canAccessPanel(Panel $panel): bool
    {
        //        if ($panel->getId() === 'auth') {
        //            return true;
        //        }
        //
        //        if ($panel->getId() === 'app' && $this->role->name === 'authenticated_user') {
        //            return true;
        //        }
        //
        //        if ($panel->getId() === 'admin' && in_array($this->role->name, ['superadmin', 'admin'])) {
        //            return true;
        //        }
        //
        //        return false;

        return true;
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

    public function usersPanel(): ?string
    {
        return match (auth()->user()?->email) {
            'zanek.pavel@gmail.com', 'admin' => Filament::getPanel('admin')->getUrl(),
            default => Filament::getPanel('app')->getUrl(),
        };
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
