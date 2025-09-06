<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ExchangeRate extends Model
{
    use HasTranslations;
    
    protected $translatable = ['name'];
    protected $fillable = ['name', 'code', 'symbol'];
}
