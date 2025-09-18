<?php

namespace App\Filament\Tenant\Resources\MealTypes\Pages;

use App\Filament\Tenant\Resources\MealTypes\MealTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMealType extends EditRecord
{
    protected static string $resource = MealTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
