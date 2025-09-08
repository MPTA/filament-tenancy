<?php

namespace App\Filament\Base\Resources\Provinces\Pages;

use App\Filament\Base\Resources\Provinces\ProvinceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProvinces extends ListRecords
{
    protected static string $resource = ProvinceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
