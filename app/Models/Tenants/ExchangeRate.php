<?php

namespace App\Models\Tenants;

use App\Models\Base\Currency;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ExchangeRate extends Model
{
    use HasTranslations;
    
    protected $translatable = ['name'];
    protected $fillable = ['from_currency_id', 'to_currency_id', 'rate'];

    public function fromCurrency(){
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }

    public function toCurrency(){
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }
}
