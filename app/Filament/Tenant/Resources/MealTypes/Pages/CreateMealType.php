<?php

namespace App\Filament\Tenant\Resources\MealTypes\Pages;

use App\Filament\Tenant\Resources\MealTypes\MealTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMealType extends CreateRecord
{
    protected static string $resource = MealTypeResource::class;
}
