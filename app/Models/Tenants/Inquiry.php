<?php

namespace App\Models\Tenants;

use App\Enums\InquiryTypeEnum;
use App\Models\Base\Currency;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Stancl\Tenancy\Database\TenantScope;

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

    /**
     * Get the inquiry itinerary for this inquiry (one-to-one relationship).
     */
    public function inquiryItinerary(): HasOne
    {
        return $this->hasOne(InquiryItinerary::class);
    }

    /**
     * Get the quotations for this inquiry (one-to-many relationship).
     */
    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    /**
     * Boot method to generate inquiry number and handle file cleanup.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($inquiry) {
            if (empty($inquiry->number)) {
                $inquiry->number = static::generateInquiryNumber();
            }
        });

        // Clean up removed attachment files when updating
        static::updating(function ($inquiry) {
            $oldAttachments = $inquiry->getOriginal('attachments') ?? [];
            $newAttachments = $inquiry->attachments ?? [];
            
            // Find files that were removed
            $removedFiles = array_diff($oldAttachments, $newAttachments);
            
            // Delete removed files from storage
            foreach ($removedFiles as $file) {
                if ($file && Storage::disk('local')->exists($file)) {
                    Storage::disk('local')->delete($file);
                }
            }
        });

        // Clean up all attachment files when deleting inquiry
        static::deleting(function ($inquiry) {
            $attachments = $inquiry->attachments ?? [];
            
            // Delete all attachment files from storage
            foreach ($attachments as $file) {
                if ($file && Storage::disk('local')->exists($file)) {
                    Storage::disk('local')->delete($file);
                }
            }
        });
    }

    /**
     * Generate a unique inquiry number starting from 2500100.
     * Numbers are globally unique across all tenants.
     */
    protected static function generateInquiryNumber(): string
    {
        // Query without tenant scope to get the highest number across all tenants
        $lastInquiry = static::query()
            ->withoutGlobalScope(TenantScope::class)
            ->orderBy('number', 'desc')
            ->first();
        
        if ($lastInquiry && is_numeric($lastInquiry->number)) {
            $nextNumber = (int) $lastInquiry->number + 1;
        } else {
            $nextNumber = 2500100;
        }

        return (string) $nextNumber;
    }

}
