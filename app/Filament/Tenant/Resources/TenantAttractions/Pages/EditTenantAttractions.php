<?php

namespace App\Filament\Resources\TenantAttractions\Pages;

use App\Filament\Resources\TenantAttractions\TenantAttractionsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTenantAttractions extends EditRecord
{
    protected static string $resource = TenantAttractionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
