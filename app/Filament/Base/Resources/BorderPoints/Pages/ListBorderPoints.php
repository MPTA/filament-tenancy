<?php

namespace App\Filament\Base\Resources\BorderPoints\Pages;

use App\Filament\Base\Resources\BorderPoints\BorderPointResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBorderPoints extends ListRecords
{
    protected static string $resource = BorderPointResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
