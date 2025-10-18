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

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        $itineraryable = $this->record->itineraryable;
        
        if ($itineraryable instanceof QuotationItinerary) {
            return 'Edit Itinerary - Quotation ' . ($itineraryable->quotation?->number ?? 'N/A');
        } elseif ($itineraryable instanceof \App\Models\Tenants\Inquiry) {
            return 'Edit Itinerary - Inquiry ' . ($itineraryable->number ?? $itineraryable->id);
        }
        
        return 'Edit Itinerary';
    }

    public function getRecordTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        $itineraryable = $this->record->itineraryable;
        
        if ($itineraryable instanceof QuotationItinerary) {
            return 'Quotation ' . ($itineraryable->quotation?->number ?? 'N/A');
        } elseif ($itineraryable instanceof \App\Models\Tenants\Inquiry) {
            return 'Inquiry ' . ($itineraryable->number ?? $itineraryable->id);
        }
        
        return 'Itinerary';
    }

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
            $this->getSaveAction(),
            $this->getSaveAndCloseAction(),
            $this->getSaveAndCompleteAction(),
            $this->getViewQuotationAction(),
            // DeleteAction::make()
            //     ->icon('heroicon-o-trash'),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    /**
     * Save record without redirect
     */
    protected function saveRecord(): void
    {
        $data = $this->form->getState();
        
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
        
        // Handle days manually (same as mutateFormDataBeforeSave)
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
                    
                    // Handle companion fields conversion
                    $companionHireMode = null;
                    $companionHours = null;
                    
                    // If has_companion is true, set to DAILY
                    if (isset($dayData['has_companion']) && $dayData['has_companion']) {
                        $companionHireMode = 'daily';
                        $companionHours = null;
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
                        'companion_hire_mode' => $companionHireMode,
                        'companion_hours' => $companionHours,
                        'description' => $dayData['description'] ?? null,
                        'creator_user_id' => \Illuminate\Support\Facades\Auth::user()->id,
                    ]);
                    
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
        
        // Save the main record (only if there are other fields to save)
        if (!empty($data)) {
            $this->record->update($data);
        }
        
        // Regenerate breakdown if this itinerary belongs to a QuotationItinerary
        if ($this->record->itineraryable_type === QuotationItinerary::class) {
            $quotationItinerary = $this->record->itineraryable;
            if ($quotationItinerary) {
                $quotationItinerary->generateBreakdownFromItinerary();
            }
        }
        
        // Refresh the form data using the same method as mutateFormDataBeforeFill
        $this->form->fill($this->mutateFormDataBeforeFill($this->record->toArray()));
    }

    protected function getSaveAction(): \Filament\Actions\Action
    {
        return \Filament\Actions\Action::make('save')
            ->label('Save')
            ->color('success')
            ->icon('heroicon-o-check')
            ->action(function () {
                // Save without redirect
                $this->saveRecord();
                \Filament\Notifications\Notification::make()
                    ->title('Itinerary saved successfully!')
                    ->success()
                    ->send();
            })
            ->close(false);
    }

    protected function getSaveAndCompleteAction(): \Filament\Actions\Action
    {
        return \Filament\Actions\Action::make('saveAndComplete')
            ->label('Save and Complete')
            ->color('success')
            ->icon('heroicon-o-check-badge')
            ->requiresConfirmation()
            ->modalHeading('Complete Itinerary')
            ->modalDescription('Are you sure you want to complete this itinerary? This will save, mark it as complete, and redirect you to edit the breakdown.')
            ->modalSubmitActionLabel('Yes, Complete')
            ->action(function () {
                // Step 1: First validate without saving
                $data = $this->form->getState();
                
                // Check if there are days in the form data
                if (isset($data['days']) && !empty($data['days'])) {
                    $totalDays = count($data['days']);
                    $lastDayData = $data['days'][$totalDays - 1];
                    
                    // Check if last day has accommodation
                    if (!empty($lastDayData['accommodation_id']) || !empty($lastDayData['accommodation_city_id'])) {
                        \Filament\Notifications\Notification::make()
                            ->title('Cannot Complete Itinerary!')
                            ->body('The last day (Day ' . $totalDays . ') cannot have accommodation because it is the checkout day. Please edit the itinerary and remove the accommodation and accommodation city from the last day.')
                            ->danger()
                            ->persistent()
                            ->send();
                        
                        $this->halt();
                        return;
                    }
                }
                
                try {
                    // Step 2: Save the itinerary with validation
                    $this->save();
                    
                    // Step 3: Mark itinerary as complete
                    $this->record->refresh();
                    $this->record->update(['is_complete' => true]);
                    
                    // Step 4: Show success notification
                    \Filament\Notifications\Notification::make()
                        ->title('Itinerary completed successfully!')
                        ->body('You can now edit the breakdown.')
                        ->success()
                        ->send();
                    
                    // Step 5: Redirect to edit breakdown if this is a quotation itinerary
                    if ($this->record->itineraryable_type === QuotationItinerary::class) {
                        $quotationItinerary = $this->record->itineraryable;
                        if ($quotationItinerary && $quotationItinerary->breakdown) {
                            return redirect(QuotationItineraryResource::getUrl('edit-breakdown', ['record' => $quotationItinerary]));
                        }
                    }
                    
                    // Default redirect
                    return redirect($this->getRedirectUrl());
                } catch (\Exception $e) {
                    // Don't show generic error if it's a validation error
                    if (!($e instanceof \Illuminate\Validation\ValidationException)) {
                        \Filament\Notifications\Notification::make()
                            ->title('Error completing itinerary')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                    
                    throw $e;
                }
            })
            ->visible(fn() => $this->record->itineraryable_type === QuotationItinerary::class);
    }

    protected function getSaveAndCloseAction(): \Filament\Actions\Action
    {
        return \Filament\Actions\Action::make('saveAndClose')
            ->label('Save and Close')
            ->color('primary')
            ->icon('heroicon-o-check-circle')
            ->action(function () {
                // Save the record first
                $this->save();
                
                // Show success notification
                \Filament\Notifications\Notification::make()
                    ->title('Itinerary saved successfully!')
                    ->success()
                    ->send();
                
                // Then redirect
                $redirectUrl = $this->getRedirectUrl();
                return redirect($redirectUrl);
            });
    }
    
    protected function getRedirectUrl(): string
    {
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
    }
    protected function mutateFormDataBeforeSave(array $data): array
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
                    
                    // Handle companion fields conversion
                    $companionHireMode = null;
                    $companionHours = null;
                    
                    // If has_companion is true, set to DAILY
                    if (isset($dayData['has_companion']) && $dayData['has_companion']) {
                        $companionHireMode = 'daily';
                        $companionHours = null;
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
                        'companion_hire_mode' => $companionHireMode,
                        'companion_hours' => $companionHours,
                        'description' => $dayData['description'] ?? null,
                        'creator_user_id' => \Illuminate\Support\Facades\Auth::user()->id,
                    ]);
                    
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
            
            // Regenerate breakdown if this itinerary belongs to a QuotationItinerary
            if ($this->record->itineraryable_type === QuotationItinerary::class) {
                $quotationItinerary = $this->record->itineraryable;
                if ($quotationItinerary) {
                    $quotationItinerary->generateBreakdownFromItinerary();
                }
            }
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
            foreach ($dayData['tickets'] as $index => $ticketData) {
                // Validate required fields
                $errors = [];
                $dayNumber = $itineraryDay->day_number;
                
                if (empty($ticketData['transport_mode'])) {
                    $errors[] = __('app-itineraries.fields.mode');
                }
                if (empty($ticketData['from_city_id'])) {
                    $errors[] = __('app-itineraries.fields.from_city');
                }
                if (empty($ticketData['to_city_id'])) {
                    $errors[] = __('app-itineraries.fields.to_city');
                }
                if (empty($ticketData['class'])) {
                    $errors[] = __('app-itineraries.fields.class');
                }
                
                if (!empty($errors)) {
                    $errorFields = implode(', ', $errors);
                    \Filament\Notifications\Notification::make()
                        ->title('Ticket Validation Error')
                        ->body("Day {$dayNumber}, Ticket " . ($index + 1) . ": The following fields are required: {$errorFields}")
                        ->danger()
                        ->persistent()
                        ->send();
                    
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        "days." . ($dayNumber - 1) . ".tickets.{$index}" => "Please fill all required fields: {$errorFields}",
                    ]);
                }
                
                $activity = $itineraryDay->activities()->create([
                    'city_id' => $ticketData['from_city_id'],
                    'start_time' => $ticketData['departure_time'] ?? null,
                    'end_time' => $ticketData['arrival_time'] ?? null,
                    'description' => 'Transport ticket',
                    'activity_category_id' => \App\Models\Base\ActivityCategory::where('type', ActivityCategoryTypeEnum::TICKET->value)->first()->id,
                    'creator_user_id' => \Illuminate\Support\Facades\Auth::user()->id,
                ]);
                
                $activity->ticket()->create([
                    'to_city_id' => $ticketData['to_city_id'],
                    'class' => $ticketData['class'],
                    'transport_number' => $ticketData['transport_number'] ?? null,
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
    

    protected function getViewQuotationAction(): \Filament\Actions\Action
    {
        return \Filament\Actions\Action::make('view_quotation')
            ->label('View Quotation')
            ->url(fn() => $this->getQuotationUrl())
            ->icon('heroicon-o-eye')
            ->color('gray')
            ->visible(fn() => $this->record->itineraryable_type === QuotationItinerary::class);
    }
    
    protected function getQuotationUrl(): string
    {
        if ($this->record->itineraryable_type === QuotationItinerary::class) {
            $quotationItinerary = $this->record->itineraryable;
            if ($quotationItinerary?->quotation) {
                return QuotationItineraryResource::getUrl('view', ['record' => $quotationItinerary]) . '?tab=itinerary%3A%3Atab';
            }
        }
        
        return '#';
    }
}
