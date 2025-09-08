<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Country extends Model
{
    use HasTranslations, CentralConnection, HasUuids;

    protected $table = 'countries';
    public $translatable = ['name'];

    protected $fillable = ['name', 'code'];

}
