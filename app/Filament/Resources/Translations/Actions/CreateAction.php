<?php

namespace App\Filament\Resources\Translations\Actions;

use Filament\Actions;

class CreateAction extends Action
{
    public static function make(): Actions\CreateAction
    {
        return Actions\CreateAction::make();
    }
}
