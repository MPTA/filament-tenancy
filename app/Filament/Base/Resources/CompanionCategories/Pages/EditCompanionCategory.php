<?php

namespace App\Filament\Base\Resources\CompanionCategories\Pages;

use App\Filament\Base\Resources\CompanionCategories\CompanionCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditCompanionCategory extends EditRecord
{
    use Translatable;
    protected static string $resource = CompanionCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
