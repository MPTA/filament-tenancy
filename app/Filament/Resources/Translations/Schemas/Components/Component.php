<?php

namespace App\Filament\Resources\Translations\Schemas\Components;

use Filament\Forms\Components\Field;

abstract class Component
{
    abstract public static function make(): Field;
}
