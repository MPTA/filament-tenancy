<?php

namespace App\Filament\Resources\Translations\Actions;

use Filament\Actions;

class DeleteAction extends Action
{
    public static function make(): Actions\Action
    {
        return Actions\DeleteAction::make('deleteSelectedTranslation');
    }
}
