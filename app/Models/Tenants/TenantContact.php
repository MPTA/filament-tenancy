<?php

namespace App\Models\Tenants;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class TenantContact extends Contact
{
    use BelongsToTenant;

    protected $table = 'contacts';

    /**
     * Get the user that owns the contact.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}