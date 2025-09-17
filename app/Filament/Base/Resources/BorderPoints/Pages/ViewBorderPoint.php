<?php

namespace App\Filament\Base\Resources\BorderPoints\Pages;

use App\Filament\Base\Resources\BorderPoints\BorderPointResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBorderPoint extends ViewRecord
{
    protected static string $resource = BorderPointResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
