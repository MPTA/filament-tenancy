<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\HasDatabase;

class TenantUser extends Model
{
    use HasDatabase;
    
    protected $table = 'users';

    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'email_verified_at',
        'remember_token'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
