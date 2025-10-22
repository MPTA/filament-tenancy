<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class SubAttraction extends Model
{
    use HasTranslations, CentralConnection, HasUuids;

    protected $table = 'sub_attractions';
    public $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'description',
        'latitude',
        'longitude',
        'attraction_id',
        'price',
        'local_price',
        'foreigner_price',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'price' => 'decimal:2',
        'local_price' => 'decimal:2',
        'foreigner_price' => 'decimal:2',
    ];

    /**
     * Get the attraction that owns the sub attraction.
     */
    public function attraction(): BelongsTo
    {
        return $this->belongsTo(Attraction::class);
    }

    /**
     * Get the tenant sub attractions for this sub attraction.
     */
    public function tenantSubAttractions(): HasMany
    {
        return $this->hasMany(\App\Models\Tenants\TenantSubAttraction::class);
    }
}
