<?php

namespace App\Models\Base;

use App\Enums\RoomCategoryEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class RoomCategory extends Model
{
    use HasTranslations, CentralConnection, HasUuids;

    protected $table = 'room_categories';
    public $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'slug',
        'category',
        'capacity',
        'description',
        'is_active',
    ];

    protected $casts = [
        'category' => RoomCategoryEnum::class,
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->getTranslation('name', 'en') ?? 'room-type');
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('name') && empty($model->slug)) {
                $model->slug = Str::slug($model->getTranslation('name', 'en') ?? 'room-type');
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the quotation offer groups using this room category as companion room.
     */
    public function quotationOfferGroupsAsCompanionRoom(): HasMany
    {
        return $this->hasMany(\App\Models\Tenants\QuotationOfferGroup::class, 'companion_room_category_id');
    }

    /**
     * Get the quotation offer groups using this room category as driver room.
     */
    public function quotationOfferGroupsAsDriverRoom(): HasMany
    {
        return $this->hasMany(\App\Models\Tenants\QuotationOfferGroup::class, 'driver_room_category_id');
    }

    /**
     * Get the quotation offers using this room category as leader room.
     */
    public function quotationOffersAsLeaderRoom(): HasMany
    {
        return $this->hasMany(\App\Models\Tenants\QuotationOffer::class, 'leader_room_category_id');
    }

    /**
     * Get the quotation offer prices using this room category.
     */
    public function quotationOfferPrices(): HasMany
    {
        return $this->hasMany(\App\Models\Tenants\QuotationOfferPrice::class);
    }
}
