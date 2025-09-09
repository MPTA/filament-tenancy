<?php

namespace App\Filament\Base\Resources\Attractions\Pages;

use App\Filament\Base\Resources\Attractions\AttractionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditAttraction extends EditRecord
{
    use Translatable;
    protected static string $resource = AttractionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
