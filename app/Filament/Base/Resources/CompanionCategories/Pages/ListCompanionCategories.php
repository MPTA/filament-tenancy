<?php

namespace App\Filament\Base\Resources\CompanionCategories\Pages;

use App\Filament\Base\Resources\CompanionCategories\CompanionCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompanionCategories extends ListRecords
{
    protected static string $resource = CompanionCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
