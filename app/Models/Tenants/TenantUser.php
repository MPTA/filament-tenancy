<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class TenantUser extends Model
{
    use BelongsToTenant;
    protected $table = 'users';
}
