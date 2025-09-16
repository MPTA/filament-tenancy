<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Currency extends Model
{
    use HasTranslations, CentralConnection, HasUuids;

    protected $table = 'currencies';
    
    public $translatable = ['name'];

    protected $fillable = [
        'name',
        'code',
        'symbol',
    ];

    /**
     * Get the quotations using this currency.
     */
    public function quotations(): HasMany
    {
        return $this->hasMany(\App\Models\Tenants\Quotation::class);
    }

}
