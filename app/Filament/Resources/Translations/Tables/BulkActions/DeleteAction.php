<?php

namespace App\Filament\Resources\Translations\Tables\BulkActions;

use Filament\Actions;

class DeleteAction extends Action
{
    public static function make(): Actions\DeleteBulkAction
    {
        return Actions\DeleteBulkAction::make();
    }
}
