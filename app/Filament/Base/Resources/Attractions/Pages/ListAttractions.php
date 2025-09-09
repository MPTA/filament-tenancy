<?php

namespace App\Filament\Base\Resources\Attractions\Pages;

use App\Filament\Base\Resources\Attractions\AttractionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAttractions extends ListRecords
{
    protected static string $resource = AttractionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
