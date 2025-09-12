<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Language extends Model
{
    use HasUuids, CentralConnection;

    protected $fillable = [
        'code',
        'name',
        'native_name',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'code';
    }

    /**
     * Scope a query to filter by language code.
     */
    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    /**
     * Scope a query to search languages.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('code', 'like', "%{$search}%")
              ->orWhere('name', 'like', "%{$search}%")
              ->orWhere('native_name', 'like', "%{$search}%");
        });
    }

    /**
     * Get the formatted display name.
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->native_name} ({$this->name})";
    }
}
