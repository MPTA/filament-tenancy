<?php

namespace App\Filament\Resources\Translations\Tables\HeaderActions;

abstract class Action
{
    abstract public static function make(): \Filament\Actions\Action;
}
