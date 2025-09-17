<?php

namespace App\Filament\Base\Resources\BorderPoints\Pages;

use App\Filament\Base\Resources\BorderPoints\BorderPointResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBorderPoint extends EditRecord
{
    protected static string $resource = BorderPointResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
