<?php

namespace App\Filament\Resources\Translations\Actions;

abstract class Action
{
    abstract public static function make(): \Filament\Actions\Action;
}
