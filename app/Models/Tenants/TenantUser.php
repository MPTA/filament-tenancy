<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\HasDatabase;

class TenantUser extends Model
{
    protected $table = 'users';

    protected $fillable = ['name', 'email', 'password', 'tenant_id'];
}
