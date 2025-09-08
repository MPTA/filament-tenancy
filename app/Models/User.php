<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
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
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get the contact associated with the user.
     */
    public function contact(): HasOne
    {
        return $this->hasOne(Contact::class);
    }

    /**
     * Determine if the user can access the given panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Admin panel - only users with is_admin = true can access
        if ($panel->getId() === 'admin') {
            return $this->is_admin === true;
        }

        // Tenant panel - only users with tenant_id != null can access
        if ($panel->getId() === 'tenant-admin') {
            // Check if user belongs to current tenant
            if ($this->tenant_id === null) {
                return false;
            }
            
            // Check if user belongs to the current tenant context
            $currentTenant = tenant();
            if ($currentTenant && $this->tenant_id !== $currentTenant->id) {
                return false;
            }
            
            return true;
        }

        // Shared panel - both admin and tenant users can access
        if ($panel->getId() === 'shared') {
            return $this->is_admin === true || $this->tenant_id !== null;
        }

        // App panel - all users can access
        if ($panel->getId() === 'app') {
            return true;
        }

        return false;
    }
}
