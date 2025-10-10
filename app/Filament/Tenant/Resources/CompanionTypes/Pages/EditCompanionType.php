<?php

namespace App\Filament\Tenant\Resources\CompanionTypes\Pages;

use App\Filament\Tenant\Resources\CompanionTypes\CompanionTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditCompanionType extends EditRecord
{
    use Translatable;
    protected static string $resource = CompanionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
