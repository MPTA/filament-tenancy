<?php

namespace App\Filament\Tenant\Resources\TenantAttractions\Pages;

use App\Filament\Tenant\Resources\TenantAttractions\TenantAttractionsResource;
use App\Models\TenantSetting;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListTenantAttractions extends ListRecords
{
    protected static string $resource = TenantAttractionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action - attractions are read-only
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

        // Filter attractions by tenant's country
        return $this->getResource()::getEloquentQuery()
            ->where('country_id', $countryId)
            ->where('is_active', true); // Only show active attractions
    }
}