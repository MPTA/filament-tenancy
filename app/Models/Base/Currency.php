<?php

namespace App\Models\Base;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\LaravelPackageTools\Concerns\Package\HasTranslations;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Currency extends Model
{
    use HasTranslations, CentralConnection;

    protected $table = 'currencies';
    
    public $translatable = ['name'];

    protected $fillable = [
        'name',
        'code',
        'symbol',
    ];

}
