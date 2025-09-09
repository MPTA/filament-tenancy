<?php

namespace App\Filament\Base\Resources\Attractions\Pages;

use App\Filament\Base\Resources\Attractions\AttractionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAttraction extends ViewRecord
{
    protected static string $resource = AttractionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
