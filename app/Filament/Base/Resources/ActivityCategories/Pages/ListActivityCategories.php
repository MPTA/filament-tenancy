<?php

namespace App\Filament\Base\Resources\ActivityCategories\Pages;

use App\Filament\Base\Resources\ActivityCategories\ActivityCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListActivityCategories extends ListRecords
{
    protected static string $resource = ActivityCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
