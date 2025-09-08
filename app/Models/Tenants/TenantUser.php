<?php

namespace App\Models\Tenants;

use App\Models\User;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class TenantUser extends User
{
    use BelongsToTenant;
    
    protected $table = 'users';
    
}
