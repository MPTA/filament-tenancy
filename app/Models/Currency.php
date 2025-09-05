<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\LaravelPackageTools\Concerns\Package\HasTranslations;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Currency extends Model
{
    use HasTranslations, CentralConnection;

    protected $table = 'currencies';
    
    public $translatable = ['name'];

    protected $fillable = [
        'name',
        'code',
        'symbol',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all exchange rates where this currency is the source currency.
     */
    public function fromExchangeRates(): HasMany
    {
        return $this->hasMany(ExchangeRate::class, 'from_currency_id')
            ->using('tenant'); // Use tenant connection (current tenant schema)
    }

    /**
     * Get all exchange rates where this currency is the target currency.
     */
    public function toExchangeRates(): HasMany
    {
        return $this->hasMany(ExchangeRate::class, 'to_currency_id')
            ->using('tenant'); // Use tenant connection (current tenant schema)
    }

    /**
     * Scope to get only active currencies.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get currency by code.
     */
    public function scopeByCode($query, string $code)
    {
        return $query->where('code', strtoupper($code));
    }
}
