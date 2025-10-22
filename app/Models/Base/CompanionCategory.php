<?php

namespace App\Models\Base;

use App\Enums\CompanionCategoryEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class CompanionCategory extends Model
{
    use HasTranslations, CentralConnection, HasUuids;

    protected $table = 'companion_categories';
    public $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'category_type',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'category_type' => CompanionCategoryEnum::class,
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->getTranslation('name', 'en') ?? 'companion-category');
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('name') && empty($model->slug)) {
                $model->slug = Str::slug($model->getTranslation('name', 'en') ?? 'companion-category');
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
}
