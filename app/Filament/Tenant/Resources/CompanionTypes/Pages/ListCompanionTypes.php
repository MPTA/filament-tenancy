<?php

namespace App\Filament\Tenant\Resources\CompanionTypes\Pages;

use App\Filament\Tenant\Resources\CompanionTypes\CompanionTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompanionTypes extends ListRecords
{
    protected static string $resource = CompanionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
