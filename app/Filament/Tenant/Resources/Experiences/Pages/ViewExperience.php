<?php

namespace App\Filament\Tenant\Resources\Experiences\Pages;

use App\Filament\Tenant\Resources\Experiences\ExperienceResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewExperience extends ViewRecord
{
    protected static string $resource = ExperienceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
