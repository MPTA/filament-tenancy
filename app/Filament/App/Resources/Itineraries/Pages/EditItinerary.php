<?php

namespace App\Filament\App\Resources\Itineraries\Pages;

use App\Enums\ActivityCategoryTypeEnum;
use App\Enums\VehicleUsageModeEnum;
use App\Filament\App\Resources\Itineraries\ItineraryResource;
use App\Filament\App\Resources\QuotationItineraries\QuotationItineraryResource;
use App\Models\Base\CompanionCategory;
use App\Models\Tenants\QuotationItinerary;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditItinerary extends EditRecord
{
    protected static string $resource = ItineraryResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load itinerary with optimized relationships using static method
        $itinerary = \App\Models\Tenants\Itinerary::getForFormEdit($this->getRecord()->id);
        
        if ($itinerary) {
            // Transform days data for form using optimized method
            $data['days'] = $itinerary->getFormattedDaysData();
        }
        
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Handle days manually
        if (isset($data['days'])) {
            $days = $data['days'];
            unset($data['days']); // Remove from main data
            
            // Use database transaction to ensure data consistency
            \Illuminate\Support\Facades\DB::transaction(function () use ($days) {
                $itinerary = $this->getRecord();
                
                // Delete all existing days (cascade will handle related data)
                $itinerary->days()->delete();
                
                // Process each day
                foreach ($days as $index => $dayData) {
                    // Add day_number automatically based on index
                    $dayData['day_number'] = $index + 1;
                    
                    // Handle vehicle fields conversion
                    $vehicleUsageMode = null;
                    $vehicleHours = null;
                    
                    // If has_vehicle is true, set to FULL_DAY with 0 hours
                    if (isset($dayData['has_vehicle']) && $dayData['has_vehicle']) {
                        $vehicleUsageMode = VehicleUsageModeEnum::FULL_DAY->value;
                        $vehicleHours = 0;
                    }
                    
                    // Create ItineraryDay
                    $itineraryDay = \App\Models\Tenants\Itinerary::find($itinerary->id)->days()->create([
                        'day_number' => $dayData['day_number'],
                        'current_city_id' => $dayData['current_city_id'],
                        'accommodation_city_id' => $dayData['accommodation_city_id'],
                        'accommodation_id' => $dayData['accommodation_id'] ?? null,
                        'accommodation_star_rating' => $dayData['accommodation_star_rating'] ?? null,
                        'vehicle_usage_mode' => $vehicleUsageMode,
                        'vehicle_hours' => $vehicleHours,
                        'description' => $dayData['description'] ?? null,
                        'creator_user_id' => \Illuminate\Support\Facades\Auth::user()->id,
                    ]);
                    
                    // Create tour guide companion if has_tour_guide is true
                    if (isset($dayData['has_tour_guide']) && $dayData['has_tour_guide']) {
                        $this->createTourGuideCompanion($itineraryDay);
                    }
                    
                    // Process meals
                    $this->processMeals($itineraryDay, $dayData);
                    
                    // Process attractions
                    $this->processAttractions($itineraryDay, $dayData);
                    
                    // Process tickets
                    $this->processTickets($itineraryDay, $dayData);
                    
                    // Process experiences
                    $this->processExperiences($itineraryDay, $dayData);
                }
            });
        }
        
        return $data;
    }
    
    private function processMeals($itineraryDay, $dayData)
    {
        $meals = ['breakfast', 'lunch', 'dinner'];
        
        foreach ($meals as $mealType) {
            if (!empty($dayData[$mealType])) {
                $activity = $itineraryDay->activities()->create([
                    'city_id' => $dayData['current_city_id'],
                    'description' => ucfirst($mealType) . ' meal',
                    'creator_user_id' => \Illuminate\Support\Facades\Auth::user()->id,
                    'activity_category_id' => \App\Models\Base\ActivityCategory::where('type', ActivityCategoryTypeEnum::MEAL->value)->first()->id,
                ]);
                
                $activity->meal()->create([
                    'meal_type_id' => $dayData[$mealType],
                    'meal_part' => $this->getMealPart($mealType),
                    // maybe need to remove this field  in future because we will calculate it based on the hotel rate type include breakfast, lunch, dinner
                    'is_included_with_hotel' => false,
                ]);
            }
        }
    }
    
    private function processAttractions($itineraryDay, $dayData)
    {
        if (!empty($dayData['attractions'])) {
            foreach ($dayData['attractions'] as $attractionData) {
                $activity = $itineraryDay->activities()->create([
                    'city_id' => $attractionData['city_id'],
                    'description' => 'Visit attraction',
                    'activity_category_id' => \App\Models\Base\ActivityCategory::where('type', ActivityCategoryTypeEnum::ATTRACTION->value)->first()->id,
                    'creator_user_id' => \Illuminate\Support\Facades\Auth::user()->id,
                ]);
                
                $attractionActivity = $activity->attraction()->create([
                    'attraction_id' => $attractionData['attraction_id'],
                    'is_outview' => $attractionData['is_outview'] ?? false,
                ]);
                
                // Process sub-attractions
                if (!empty($attractionData['sub_attractions'])) {
                    foreach ($attractionData['sub_attractions'] as $subAttractionId) {
                        $attractionActivity->subAttractions()->create([
                            'sub_attraction_id' => $subAttractionId,
                        ]);
                    }
                }
            }
        }
    }
    
    private function processTickets($itineraryDay, $dayData)
    {
        if (!empty($dayData['tickets'])) {
            foreach ($dayData['tickets'] as $ticketData) {
                $activity = $itineraryDay->activities()->create([
                    'city_id' => $ticketData['from_city_id'],
                    'start_time' => $ticketData['departure_time'],
                    'end_time' => $ticketData['arrival_time'],
                    'description' => 'Transport ticket',
                    'activity_category_id' => \App\Models\Base\ActivityCategory::where('type', ActivityCategoryTypeEnum::TICKET->value)->first()->id,
                    'creator_user_id' => \Illuminate\Support\Facades\Auth::user()->id,
                ]);
                
                $activity->ticket()->create([
                    'to_city_id' => $ticketData['to_city_id'],
                    'class' => $ticketData['class'],
                    'transport_number' => $ticketData['transport_number'],
                    'transport_mode' => $ticketData['transport_mode'],
                ]);
            }
        }
    }
    
    private function processExperiences($itineraryDay, $dayData)
    {
        if (!empty($dayData['experiences'])) {
            foreach ($dayData['experiences'] as $experienceData) {
                $activity = $itineraryDay->activities()->create([
                    'city_id' => $experienceData['city_id'],
                    'description' => 'Experience activity',
                    'activity_category_id' => \App\Models\Base\ActivityCategory::where('type', ActivityCategoryTypeEnum::EXPERIENCE->value)->first()->id,
                    'creator_user_id' => \Illuminate\Support\Facades\Auth::user()->id,
                ]);
                
                $activity->experience()->create([
                    'experience_id' => $experienceData['experience_id'],
                ]);
            }
        }
    }
    
    
    private function getMealPart($mealType)
    {
        return match($mealType) {
            'breakfast' => \App\Enums\MealPartEnum::BREAKFAST,
            'lunch' => \App\Enums\MealPartEnum::LUNCH,
            'dinner' => \App\Enums\MealPartEnum::DINNER,
            default => \App\Enums\MealPartEnum::LUNCH
        };
    }
    
    private function createTourGuideCompanion($itineraryDay)
    {
      
        // Create tour guide companion
        // Because we have a tour guide companion category in the enum we choose tourguide type from the enum, then find 
        // companion category id from type of enum and use it to create the tour guide companion
        $tourGuideCategory = \App\Enums\CompanionCategoryEnum::TOUR_GUIDE;
        $itineraryDay->companions()->create([
            'companion_category_id' => CompanionCategory::where('category_type', $tourGuideCategory)->first()->id,
            'hire_mode' => \App\Enums\HireModeEnum::DAILY,
            'quantity' => 1,
            'description' => 'Professional tour guide for the day',
            'creator_user_id' => \Illuminate\Support\Facades\Auth::user()->id,
        ]);
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->url(function () {
                // Check if this itinerary belongs to a quotation
                if ($this->record->itineraryable_type === QuotationItinerary::class) {
                    $quotationItinerary = $this->record->itineraryable;
                    if ($quotationItinerary?->quotation) {
                        // Redirect to the quotation edit page
                        return QuotationItineraryResource::getUrl('view', ['record' => $quotationItinerary]) . '?tab=itinerary%3A%3Atab';
                    }
                }
                
                // Default redirect to itinerary list
                return static::getResource()::getUrl('index');
            });
    }
}
