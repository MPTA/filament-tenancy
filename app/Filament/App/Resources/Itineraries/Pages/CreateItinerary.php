<?php

namespace App\Filament\App\Resources\Itineraries\Pages;

use App\Filament\App\Resources\Itineraries\ItineraryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateItinerary extends CreateRecord
{
    protected static string $resource = ItineraryResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Validate accommodation_city and accommodation relationship
        if (isset($data['days'])) {
            foreach ($data['days'] as $index => $dayData) {
                if (!empty($dayData['accommodation_city_id']) && empty($dayData['accommodation_id'])) {
                    \Filament\Notifications\Notification::make()
                        ->title('Accommodation Required')
                        ->body("Day " . ($index + 1) . ": Please select an accommodation when you have selected an accommodation city.")
                        ->danger()
                        ->send();
                    
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        "days.{$index}.accommodation_id" => 'Accommodation is required when Accommodation City is selected.',
                    ]);
                }
            }
        }
        
        return $data;
    }
}
