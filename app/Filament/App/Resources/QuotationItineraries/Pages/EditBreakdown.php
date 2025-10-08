<?php

namespace App\Filament\App\Resources\QuotationItineraries\Pages;

use App\Filament\App\Resources\QuotationItineraries\QuotationItineraryResource;
use App\Filament\App\Resources\QuotationItineraries\Schemas\BreakdownForm;
use App\Models\Tenants\Breakdown;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditBreakdown extends EditRecord
{
    protected static string $resource = QuotationItineraryResource::class;

    public ?Breakdown $breakdown = null;

    public function mount(int|string $record): void
    {
        parent::mount($record);
        $this->breakdown = $this->record->breakdown;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('store')
                ->label('Store')
                ->action(function () {
                    try {
                        // Get form data
                        $data = $this->form->getState();
                        
                        // Save breakdown data
                        $this->mutateFormDataBeforeSave($data);
                        
                        // Refresh the breakdown to get updated data
                        $this->breakdown->refresh();
                        
                        Notification::make()
                            ->title('Breakdown saved successfully!')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error saving breakdown')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->color('success')
                ->icon('heroicon-o-check'),
            
            Actions\Action::make('save_and_close')
                ->label('Save and Close')
                ->action(function () {
                    $this->save();
                    $this->redirect($this->getRedirectUrl());
                })
                ->color('primary')
                ->icon('heroicon-o-check-circle'),
            
            Actions\Action::make('view_quotation')
                ->label('View Quotation')
                ->url(fn() => route('filament.app.resources.quotation-itineraries.view', $this->record) . '?tab=breakdown%3A%3Atab')
                ->icon('heroicon-o-eye')
                ->color('gray'),
        ];
    }

    public function getTitle(): string
    {
        return 'Edit Breakdown - ' . ($this->record->quotation->title ?? 'Quotation Itinerary');
    }

    public function form(Schema $schema): Schema
    {
        return BreakdownForm::configure($schema);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {

        if ($this->record->breakdown) {
            $this->record->breakdown->load([
                'vehicleTypes',
                'tickets',
                'meals',
                'experiences',
                'accommodations.rooms',
                'attractions.subAttractions',
                'companions',
                'expenses'
            ]);

            $breakdownData = $this->record->breakdown->toArray();
            
            $breakdownData['vehicleTypes'] = $this->record->breakdown->vehicleTypes->toArray();
            $breakdownData['tickets'] = $this->record->breakdown->tickets->toArray();
            $breakdownData['meals'] = $this->record->breakdown->meals->toArray();
            $breakdownData['experiences'] = $this->record->breakdown->experiences->toArray();
            $breakdownData['accommodations'] = $this->record->breakdown->accommodations->map(function($accommodation) {
                $data = $accommodation->toArray();
                $data['rooms'] = $accommodation->rooms->map(function($room) {
                    $roomData = $room->toArray();
                    // Convert price 0 to null for form display
                    if ($roomData['price'] == 0 || $roomData['price'] == 0.00) {
                        $roomData['price'] = null;
                    }
                    return $roomData;
                })->toArray();
                return $data;
            })->toArray();
            $breakdownData['attractions'] = $this->record->breakdown->attractions->map(function($attraction) {
                $data = $attraction->toArray();
                $data['subAttractions'] = $attraction->subAttractions->toArray();
                return $data;
            })->toArray();
            $breakdownData['companions'] = $this->record->breakdown->companions->toArray();
            $breakdownData['expenses'] = $this->record->breakdown->expenses->toArray();
            
            return $breakdownData;
        }
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->record->breakdown) {
            // Update main breakdown fields
            $breakdownData = collect($data)->except([
                'vehicleTypes', 'tickets', 'meals', 'experiences', 
                'accommodations', 'attractions', 'companions', 'expenses'
            ])->toArray();
            
            $this->record->breakdown->update($breakdownData);
            
            // Handle relationship data
            $this->handleRelationshipData($data);
        }
        return $data;
    }

    private function handleRelationshipData(array $data): void
    {
        $breakdown = $this->record->breakdown;
        
        DB::transaction(function () use ($breakdown, $data) {
            // Handle Vehicle Types - Use model methods
            if (isset($data['vehicleTypes'])) {
                $breakdown->vehicleTypes()->delete();
                    
                foreach ($data['vehicleTypes'] as $vehicleTypeData) {
                    $breakdown->vehicleTypes()->create($vehicleTypeData);
                }
            }
            
            // Handle Tickets - Use model methods
            if (isset($data['tickets'])) {
                $breakdown->tickets()->delete();
                    
                foreach ($data['tickets'] as $ticketData) {
                    $breakdown->tickets()->create($ticketData);
                }
            }
            
            // Handle Meals - Use model methods
            if (isset($data['meals'])) {
                $breakdown->meals()->delete();
                    
                foreach ($data['meals'] as $mealData) {
                    $breakdown->meals()->create($mealData);
                }
            }
            
            // Handle Experiences - Use model methods
            if (isset($data['experiences'])) {
                $breakdown->experiences()->delete();
                    
                foreach ($data['experiences'] as $experienceData) {
                    $breakdown->experiences()->create($experienceData);
                }
            }
            
            // Handle Accommodations with Rooms - Use model methods
            if (isset($data['accommodations'])) {
                $breakdown->accommodations()->delete();
                    
                foreach ($data['accommodations'] as $accommodationData) {
                    $roomsData = $accommodationData['rooms'] ?? [];
                    unset($accommodationData['rooms']);
                    
                    $accommodation = $breakdown->accommodations()->create($accommodationData);
                    
                    foreach ($roomsData as $roomData) {
                        // Convert null price back to 0 for database storage
                        if (!isset($roomData['price']) || $roomData['price'] === null) {
                            $roomData['price'] = 0.00;
                        }
                        $accommodation->rooms()->create($roomData);
                    }
                }
            }
            
            // Handle Attractions with Sub Attractions - Use model methods
            if (isset($data['attractions'])) {
                $breakdown->attractions()->delete();
                    
                foreach ($data['attractions'] as $attractionData) {
                    $subAttractionsData = $attractionData['subAttractions'] ?? [];
                    unset($attractionData['subAttractions']);
                    
                    $attraction = $breakdown->attractions()->create($attractionData);
                    
                    foreach ($subAttractionsData as $subAttractionData) {
                        $attraction->subAttractions()->create($subAttractionData);
                    }
                }
            }
            
            // Handle Companions - Use model methods
            if (isset($data['companions'])) {
                $breakdown->companions()->delete();
                    
                foreach ($data['companions'] as $companionData) {
                    $breakdown->companions()->create($companionData);
                }
            }
            
            // Handle Expenses - Use model methods
            if (isset($data['expenses'])) {
                $breakdown->expenses()->delete();
                    
                foreach ($data['expenses'] as $expenseData) {
                    $breakdown->expenses()->create($expenseData);
                }
            }
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]) . '?tab=breakdown%3A%3Atab';
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
