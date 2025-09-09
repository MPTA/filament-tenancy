<?php

namespace App\Filament\Base\Resources\MealCategories\Pages;

use App\Filament\Base\Resources\MealCategories\MealCategoryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMealCategory extends ViewRecord
{
    protected static string $resource = MealCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
