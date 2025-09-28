<?php

namespace App\Filament\Tenant\Resources\TenantAttractions\Pages;

use App\Filament\Tenant\Resources\TenantAttractions\TenantAttractionsResource;
use App\Models\Tenants\TenantAttraction;
use App\Models\Tenants\TenantSubAttraction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ViewTenantAttraction extends ViewRecord
{
    protected static string $resource = TenantAttractionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No header actions needed for read-only attractions
        ];
    }

    public function saveTenantPricing(): void
    {
        try {
            $tenantId = tenant('id');
            $attractionId = $this->record->id;
            
            // Get data from the component data
            $data = $this->data ?? [];
            
            // Debug: Log the data
            Log::info('Form data:', $data);
            
            // Save main attraction pricing
            $priceRecord = TenantAttraction::firstOrNew([
                'attraction_id' => $attractionId,
            ]);

            $priceRecord->fill([
                'currency_id' => $data['currency_id'] ?? null,
                'local_price' => $data['tenant_local_price'] ?? null,
                'foreigner_price' => $data['tenant_foreigner_price'] ?? null,
                'additional_content' => isset($data['tenant_additional_content']) && $data['tenant_additional_content'] ? json_decode($data['tenant_additional_content'], true) : null,
                'creator_user_id' => Auth::id(),
            ]);
            $priceRecord->save();

            // Save sub-attraction pricing if provided
            if (isset($data['sub_attractions']) && is_array($data['sub_attractions'])) {
                foreach ($data['sub_attractions'] as $subAttractionData) {
                    if (isset($subAttractionData['id'])) {
                        $subAttractionId = $subAttractionData['id'];
                        
                        $subPriceRecord = TenantSubAttraction::firstOrNew([
                            'sub_attraction_id' => $subAttractionId,
                        ]);

                        $subPriceRecord->fill([
                            'local_price' => $subAttractionData['tenant_local_price'] ?? null,
                            'foreigner_price' => $subAttractionData['tenant_foreigner_price'] ?? null,
                            'additional_content' => isset($subAttractionData['tenant_additional_content']) && $subAttractionData['tenant_additional_content'] ? json_decode($subAttractionData['tenant_additional_content'], true) : null,
                            'creator_user_id' => Auth::id(),
                        ]);
                        $subPriceRecord->save();
                    }
                }
            }

            Notification::make()
                ->title('Pricing saved successfully!')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error saving pricing!')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
