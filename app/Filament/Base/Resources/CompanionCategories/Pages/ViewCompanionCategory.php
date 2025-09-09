<?php

namespace App\Filament\Base\Resources\CompanionCategories\Pages;

use App\Filament\Base\Resources\CompanionCategories\CompanionCategoryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCompanionCategory extends ViewRecord
{
    protected static string $resource = CompanionCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
