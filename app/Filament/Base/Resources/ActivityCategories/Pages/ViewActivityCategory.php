<?php

namespace App\Filament\Base\Resources\ActivityCategories\Pages;

use App\Filament\Base\Resources\ActivityCategories\ActivityCategoryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewActivityCategory extends ViewRecord
{
    protected static string $resource = ActivityCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
