<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelPackageTools\Concerns\Package\HasTranslations;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Country extends Model
{
    use HasTranslations, CentralConnection;

    protected $table = 'cities';
    public $translatable = ['name'];
}
