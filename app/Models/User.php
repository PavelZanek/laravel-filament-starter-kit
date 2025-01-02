<?php

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

class User extends Authenticatable implements FilamentUser, MustVerifyEmail, HasTenants
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

    /**
     * @param \Filament\Panel $panel
     * @return bool
     * @throws \Exception
     */
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

    public function workspaces(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class);
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->workspaces->contains($tenant);
    }

    public function getTenants(Panel $panel): array|Collection
    {
        return $this->workspaces;
    }

    public function usersPanel(): string
    {
        return match (auth()->user()->role) {
            'superadmin', 'admin' => Filament::getPanel('admin')->getUrl(),
            default => Filament::getPanel('app')->getUrl(),
        };
    }
}
