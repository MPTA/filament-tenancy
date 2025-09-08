<?php

namespace App\Models\Tenants;

use App\Models\User;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class TenantUser extends User implements FilamentUser
{
    use BelongsToTenant;
    
    protected $table = 'users';
    
    /**
     * Determine if the user can access the given panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
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

        // Admin panel - tenant users cannot access
        if ($panel->getId() === 'admin') {
            return false;
        }

        // App panel - all users can access
        if ($panel->getId() === 'app') {
            return true;
        }

        return false;
    }
}
