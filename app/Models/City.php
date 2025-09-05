<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelPackageTools\Concerns\Package\HasTranslations;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class City extends Model
{
    use HasTranslations, CentralConnection;

    protected $table = 'cities';
    public $translatable = ['name'];
}
