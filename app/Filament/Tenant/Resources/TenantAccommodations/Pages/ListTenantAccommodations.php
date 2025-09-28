<?php

namespace App\Filament\Tenant\Resources\TenantAccommodations\Pages;

use App\Filament\Tenant\Resources\TenantAccommodations\TenantAccommodationResource;
use App\Models\TenantSetting;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListTenantAccommodations extends ListRecords
{
    protected static string $resource = TenantAccommodationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action - accommodations are read-only
        ];
    }

    protected function getTableQuery(): Builder
    {
        // Get tenant's country from settings
        $tenantSettings = TenantSetting::first();
        $countryId = $tenantSettings?->country_id;

        if (!$countryId) {
            // If no country is set, return empty query
            return $this->getResource()::getEloquentQuery()->where('id', '=', '0');
        }

        // Filter accommodations by tenant's country
        return $this->getResource()::getEloquentQuery()
            ->where('country_id', $countryId)
            ->where('is_active', true) // Only show active accommodations
            ->withCount('tenantPrices'); // Add count of tenant prices
    }
}
