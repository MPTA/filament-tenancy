<?php

namespace App\Models\Tenants;

use App\Enums\InquiryTypeEnum;
use App\Models\Base\Currency;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Inquiry extends Model
{
    use HasUuids, BelongsToTenant, HasTranslations;

    protected $fillable = [
        'type',
        'title',
        'description',
        'reference',
        'number',
        'tenant_id',
        'creator_user_id',
        'contact_id',
        'attachments',
        'requested_currency_id',
        'published_at',
    ];

    protected $casts = [
        'type' => InquiryTypeEnum::class,
        'title' => 'array',
        'description' => 'array',
        'attachments' => 'array',
        'published_at' => 'datetime',
    ];

    protected $translatable = [
        'title',
        'description',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'number';
    }

    /**
     * Get the user who created this inquiry.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_user_id');
    }

    /**
     * Get the contact for this inquiry.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(TenantContact::class);
    }

    /**
     * Get the requested currency for this inquiry.
     */
    public function requestedCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'requested_currency_id');
    }

    /**
     * Scope a query to filter by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to filter by contact.
     */
    public function scopeByContact($query, $contactId)
    {
        return $query->where('contact_id', $contactId);
    }

    /**
     * Scope a query to filter by creator.
     */
    public function scopeByCreator($query, $creatorId)
    {
        return $query->where('creator_user_id', $creatorId);
    }

    /**
     * Scope a query to filter published inquiries.
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    /**
     * Scope a query to filter unpublished inquiries.
     */
    public function scopeUnpublished($query)
    {
        return $query->whereNull('published_at');
    }

    /**
     * Scope a query to search inquiries.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title->en', 'like', "%{$search}%")
              ->orWhere('title->fa', 'like', "%{$search}%")
              ->orWhere('description->en', 'like', "%{$search}%")
              ->orWhere('description->fa', 'like', "%{$search}%")
              ->orWhere('number', 'like', "%{$search}%")
              ->orWhere('reference', 'like', "%{$search}%");
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
     * Get the formatted attachments count.
     */
    public function getAttachmentsCountAttribute(): int
    {
        return is_array($this->attachments) ? count($this->attachments) : 0;
    }

    /**
     * Check if inquiry is published.
     */
    public function getIsPublishedAttribute(): bool
    {
        return !is_null($this->published_at);
    }

    /**
     * Get the display status.
     */
    public function getStatusAttribute(): string
    {
        return $this->is_published ? 'Published' : 'Draft';
    }
}
