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
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // جلوگیری از حذف کانتکتی که به یوزر متصل است
        static::deleting(function ($contact) {
            if ($contact->user_id) {
                throw new \Exception('Cannot delete contact that is associated with a user. Please delete the user first.');
            }
        });
    }
    
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
        $firstName = trim((string) ($this->first_name ?? ''));
        $lastName = trim((string) ($this->last_name ?? ''));
        $company = trim((string) ($this->company ?? ''));

        // Combine first and last name (skip empty parts)
        $nameParts = array_values(array_filter([$firstName, $lastName], fn ($v) => $v !== ''));
        $name = implode(' ', $nameParts);

        // Build display with optional company, separated cleanly
        $displayParts = array_values(array_filter([$name, $company], fn ($v) => $v !== ''));
        $display = implode(' - ', $displayParts);

        // Fallbacks if everything is empty
        if ($display === '') {
            return $this->email ?: 'Unknown Contact';
        }

        return $display;
    }
}
