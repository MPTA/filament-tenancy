<?php

namespace App\Filament\Base\Resources\RoomTypes\Pages;

use App\Filament\Base\Resources\RoomTypes\RoomTypeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRoomType extends ViewRecord
{
    protected static string $resource = RoomTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
