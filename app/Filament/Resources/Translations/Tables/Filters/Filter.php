<?php

namespace App\Filament\Resources\Translations\Tables\Filters;

abstract class Filter
{
    abstract public static function make(): \Filament\Tables\Filters\BaseFilter;
}
