<?php

namespace App\Filament\Base\Resources\Countries\Pages;

use App\Filament\Base\Resources\Countries\CountryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditCountry extends EditRecord
{
    use Translatable;
    protected static string $resource = CountryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
