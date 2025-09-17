<?php

namespace App\Filament\Base\Resources\ActivityCategories\Pages;

use App\Filament\Base\Resources\ActivityCategories\ActivityCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateActivityCategory extends CreateRecord
{
    protected static string $resource = ActivityCategoryResource::class;
}
