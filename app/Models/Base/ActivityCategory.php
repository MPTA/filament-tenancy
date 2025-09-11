<?php

namespace App\Models\Base;

use App\Enums\ActivityCategoryTypeEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class ActivityCategory extends Model
{
    use HasUuids, CentralConnection, HasTranslations;

    protected $fillable = [
        'name',
        'slug',
        'type',
    ];

    protected $casts = [
        'name' => 'array',
        'type' => ActivityCategoryTypeEnum::class,
    ];

    protected $translatable = [
        'name',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }


    /**
     * Scope a query to filter by type.
     */
    public function scopeOfType($query, ActivityCategoryTypeEnum $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to search categories.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('slug', 'like', "%{$search}%");
        });
    }
}
