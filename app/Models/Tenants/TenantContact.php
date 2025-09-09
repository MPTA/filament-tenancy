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
     * Override the connection to use tenant connection instead of central.
     */
    protected $connection = null; // Use default connection (tenant)
}