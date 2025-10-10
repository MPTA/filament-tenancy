<?php

namespace App\Models\Tenants;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class TenantUser extends User
{
    use BelongsToTenant;
    
    protected $table = 'users';
    
    /**
     * Override the contact relationship to use TenantContact.
     */
    public function contact(): HasOne
    {
        return $this->hasOne(TenantContact::class, 'user_id');
    }
}
