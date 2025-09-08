<?php

namespace App\Filament\Base\Resources\Provinces\Pages;

use App\Filament\Base\Resources\Provinces\ProvinceResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProvince extends ViewRecord
{
    protected static string $resource = ProvinceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
