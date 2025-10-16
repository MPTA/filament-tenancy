<?php

namespace App\Filament\Resources\Translations\Actions;

use Filament\Actions;

class ViewAction extends Action
{
    public static function make(): Actions\Action
    {
        return Actions\ViewAction::make();
    }
}
