<?php

namespace App\Filament\Tenant\Resources\Experiences\Pages;

use App\Filament\Tenant\Resources\Experiences\ExperienceResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditExperience extends EditRecord
{
    use Translatable;
    protected static string $resource = ExperienceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
