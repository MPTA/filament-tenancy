<?php

namespace App\Filament\Base\Resources\MealCategories\Pages;

use App\Filament\Base\Resources\MealCategories\MealCategoryResource;
use Filament\Resources\Pages\CreateRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateMealCategory extends CreateRecord
{
    use Translatable;
    protected static string $resource = MealCategoryResource::class;
}
