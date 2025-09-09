<?php

namespace App\Filament\Base\Resources\MealCategories\Pages;

use App\Filament\Base\Resources\MealCategories\MealCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMealCategory extends EditRecord
{
    protected static string $resource = MealCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
