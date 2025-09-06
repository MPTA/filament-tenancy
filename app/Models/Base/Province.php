<?php

namespace App\Models\Base;


use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelPackageTools\Concerns\Package\HasTranslations;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Province extends Model
{
    use HasTranslations, CentralConnection;

    protected $table = 'provinces';
    public $translatable = ['name'];
    protected $fillable = ['name', 'code', 'country_id'];
}
