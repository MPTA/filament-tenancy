<?php

namespace App\Filament\Tenant\Resources\CompanionTypes\Pages;

use App\Filament\Tenant\Resources\CompanionTypes\CompanionTypeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCompanionType extends ViewRecord
{
    protected static string $resource = CompanionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
