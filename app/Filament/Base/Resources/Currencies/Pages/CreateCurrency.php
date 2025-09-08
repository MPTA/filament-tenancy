<?php

namespace App\Filament\Base\Resources\Currencies\Pages;

use App\Filament\Base\Resources\Currencies\CurrencyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCurrency extends CreateRecord
{
    protected static string $resource = CurrencyResource::class;
}
