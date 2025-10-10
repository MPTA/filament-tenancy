<?php

namespace App\Models;

use App\Enums\ContactTypeEnum;
use App\Enums\GenderEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Contact extends Model
{
    use HasUuids, CentralConnection;
    protected $table = 'contacts';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'mobile',
        'country_code',
        'company',
        'type',
        'gender',
        'postal_address',
        'country_id',
        'user_id',
        'tenant_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => ContactTypeEnum::class,
        'gender' => GenderEnum::class,
    ];

    /**
     * Get the user that owns the contact.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the country that owns the contact.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Base\Country::class);
    }

    /**
     * Get the full name of the contact.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
