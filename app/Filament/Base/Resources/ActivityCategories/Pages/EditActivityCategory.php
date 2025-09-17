<?php

namespace App\Filament\Base\Resources\ActivityCategories\Pages;

use App\Filament\Base\Resources\ActivityCategories\ActivityCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditActivityCategory extends EditRecord
{
    protected static string $resource = ActivityCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
