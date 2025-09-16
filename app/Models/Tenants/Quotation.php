<?php

namespace App\Models\Tenants;

use App\Enums\QuotationTypeEnum;
use App\Models\Base\Currency;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Quotation extends Model
{
    use HasUuids, BelongsToTenant, HasTranslations;

    protected $fillable = [
        'number',
        'inquiry_id',
        'type',
        'currency_id',
        'exchange_rate',
        'description',
        'internal_note',
        'tenant_id',
        'creator_user_id',
        'expire_date',
    ];

    protected $casts = [
        'type' => QuotationTypeEnum::class,
        'exchange_rate' => 'decimal:4',
        'expire_date' => 'date',
    ];

    protected $translatable = [
        'description',
        'internal_note',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'number';
    }

    /**
     * Get the inquiry associated with this quotation.
     */
    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class);
    }

    /**
     * Get the currency for this quotation.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the user who created this quotation.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_user_id');
    }

    /**
     * Get the quotation itinerary for this quotation (one-to-one relationship).
     */
    public function quotationItinerary(): HasOne
    {
        return $this->hasOne(QuotationItinerary::class);
    }

    /**
     * Scope a query to filter by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to filter by inquiry.
     */
    public function scopeByInquiry($query, $inquiryId)
    {
        return $query->where('inquiry_id', $inquiryId);
    }

    /**
     * Scope a query to filter by creator.
     */
    public function scopeByCreator($query, $creatorId)
    {
        return $query->where('creator_user_id', $creatorId);
    }

    /**
     * Scope a query to filter by currency.
     */
    public function scopeByCurrency($query, $currencyId)
    {
        return $query->where('currency_id', $currencyId);
    }

    /**
     * Scope a query to filter expired quotations.
     */
    public function scopeExpired($query)
    {
        return $query->where('expire_date', '<', now()->toDateString());
    }

    /**
     * Scope a query to filter active quotations.
     */
    public function scopeActive($query)
    {
        return $query->where('expire_date', '>', now()->toDateString());
    }

    /**
     * Scope a query to search quotations.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('number', 'like', "%{$search}%")
              ->orWhere('description->en', 'like', "%{$search}%")
              ->orWhere('description->fa', 'like', "%{$search}%")
              ->orWhere('internal_note->en', 'like', "%{$search}%")
              ->orWhere('internal_note->fa', 'like', "%{$search}%");
        });
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Check if quotation is expired.
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expire_date && $this->expire_date->isPast();
    }

    /**
     * Get the display status.
     */
    public function getStatusAttribute(): string
    {
        return $this->is_expired ? 'Expired' : 'Active';
    }

    /**
     * Get the formatted exchange rate.
     */
    public function getFormattedExchangeRateAttribute(): string
    {
        return number_format((float) $this->exchange_rate, 4);
    }

    /**
     * Boot method to generate quotation number.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($quotation) {
            if (empty($quotation->number)) {
                $quotation->number = static::generateQuotationNumber();
            }
        });
    }

    /**
     * Generate a unique quotation number starting from 1000100.
     */
    protected static function generateQuotationNumber(): string
    {
        $lastQuotation = static::orderBy('number', 'desc')->first();
        
        if ($lastQuotation && is_numeric($lastQuotation->number)) {
            $nextNumber = (int) $lastQuotation->number + 1;
        } else {
            $nextNumber = 1000100;
        }

        return (string) $nextNumber;
    }
}
