<?php

namespace App\Filament\Resources\Translations\Actions;

use Filament\Actions;

class EditAction extends Action
{
    public static function make(): Actions\Action
    {
        return Actions\EditAction::make();
    }
}
