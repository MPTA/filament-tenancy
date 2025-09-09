<?php

namespace App\Filament\Base\Resources\RoomTypes\Pages;

use App\Filament\Base\Resources\RoomTypes\RoomTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditRoomType extends EditRecord
{
    use Translatable;
    protected static string $resource = RoomTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
