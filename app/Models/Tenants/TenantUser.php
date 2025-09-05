<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\HasDatabase;

class TenantUser extends Model
{
    use HasDatabase;
    protected $table = 'users';
}
