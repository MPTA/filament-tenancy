<?php

namespace App\Filament\Base\Resources\Accommodations\Pages;

use App\Filament\Base\Resources\Accommodations\AccommodationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditAccommodation extends EditRecord
{
    use Translatable;
    protected static string $resource = AccommodationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
