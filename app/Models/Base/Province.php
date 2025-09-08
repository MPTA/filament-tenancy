<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Province extends Model
{
    use HasTranslations, CentralConnection, HasUuids;

    protected $table = 'provinces';
    public $translatable = ['name'];
    protected $fillable = ['name', 'code', 'country_id'];
}
