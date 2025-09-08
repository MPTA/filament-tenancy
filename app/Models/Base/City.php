<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class City extends Model
{
    use HasTranslations, CentralConnection, HasUuids;

    protected $table = 'cities';
    public $translatable = ['name'];

    protected $fillable = ['name', 'code', 'province_id'];

}
