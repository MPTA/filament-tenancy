<?php

namespace App\Filament\Base\Resources\MealCategories\Pages;

use App\Filament\Base\Resources\MealCategories\MealCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMealCategory extends CreateRecord
{
    protected static string $resource = MealCategoryResource::class;
}
