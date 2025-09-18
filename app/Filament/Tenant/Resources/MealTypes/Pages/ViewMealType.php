<?php

namespace App\Filament\Tenant\Resources\MealTypes\Pages;

use App\Filament\Tenant\Resources\MealTypes\MealTypeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMealType extends ViewRecord
{
    protected static string $resource = MealTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
