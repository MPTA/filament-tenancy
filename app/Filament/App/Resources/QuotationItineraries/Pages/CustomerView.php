<?php

namespace App\Filament\App\Resources\QuotationItineraries\Pages;

use App\Filament\App\Resources\QuotationItineraries\QuotationItineraryResource;
use App\Models\Tenants\QuotationItinerary;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;

class CustomerView extends Page
{
    protected static string $resource = QuotationItineraryResource::class;

    protected string $view = 'filament.app.resources.quotation-itineraries.pages.customer-view';
    
    public QuotationItinerary $record;
    
    public array $itineraryDays = [];
    public ?Carbon $tripStartDate = null;
    public ?Carbon $tripEndDate = null;

    public function mount($record): void
    {
        // If $record is already a model instance, use it; otherwise find it
        if ($record instanceof QuotationItinerary) {
            $this->record = $record;
        } else {
            $this->record = QuotationItinerary::findOrFail($record);
        }
        
        // Validate that itinerary and breakdown are complete
        if (!$this->record->itinerary) {
            \Filament\Notifications\Notification::make()
                ->title('Itinerary Not Found')
                ->body('The itinerary must be created before viewing the customer quotation.')
                ->danger()
                ->persistent()
                ->send();
            
            $this->redirect(QuotationItineraryResource::getUrl('view', ['record' => $this->record->id]));
            return;
        }

        if (!$this->record->itinerary->is_complete) {
            \Filament\Notifications\Notification::make()
                ->title('Itinerary Incomplete')
                ->body('The itinerary must be completed before viewing the customer quotation.')
                ->warning()
                ->persistent()
                ->send();
            
            $this->redirect(QuotationItineraryResource::getUrl('view', ['record' => $this->record->id]));
            return;
        }

        if (!$this->record->breakdown) {
            \Filament\Notifications\Notification::make()
                ->title('Breakdown Not Found')
                ->body('The breakdown must be created before viewing the customer quotation.')
                ->danger()
                ->persistent()
                ->send();
            
            $this->redirect(QuotationItineraryResource::getUrl('view', ['record' => $this->record->id]));
            return;
        }

        if (!$this->record->breakdown->is_completed) {
            \Filament\Notifications\Notification::make()
                ->title('Breakdown Incomplete')
                ->body('The breakdown must be completed before viewing the customer quotation.')
                ->warning()
                ->persistent()
                ->send();
            
            $this->redirect(QuotationItineraryResource::getUrl('view', ['record' => $this->record->id]));
            return;
        }
        
        // Eager load all necessary relationships to prevent N+1 queries
        $this->record->load([
            'quotation.currency',
            'quotation.inquiry.contact',
            'quotation.inquiry.inquiryItinerary',
            'quotation.creator',
            'itinerary.days.currentCity',
            'itinerary.days.accommodationCity',
            'itinerary.days.accommodation',
            'itinerary.days.activities.activityCategory',
            'itinerary.days.activities.city',
            'itinerary.days.activities.meal.mealType',
            'itinerary.days.activities.ticket.toCity',
            'itinerary.days.activities.attraction.attraction',
            'itinerary.days.activities.attraction.subAttractions.subAttraction',
            'itinerary.days.activities.experience.experience',
            'transportations',
            'quotationOfferGroups.quotationOffers.quotationOfferPrices.roomCategory',
            'quotationOfferGroups.quotationOffers.vehicleType',
            'quotationOfferGroups.quotationOffers.leaderRoomCategory',
            'quotationOfferGroups.quotationOfferGroupCompanions.companionType.companionCategory',
        ]);

        $this->calculateTripDates();
        $this->prepareItineraryDays();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewQuotation')
                ->label('View Quotation')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->url(fn () => QuotationItineraryResource::getUrl('view', ['record' => $this->record->id]) . '?tab=offers%3A%3Atab')
                ->openUrlInNewTab(false),
            
            Action::make('refresh')
                ->label('Refresh')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(function () {
                    $this->record->refresh();
                    $this->calculateTripDates();
                    $this->prepareItineraryDays();
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Refreshed')
                        ->body('Page data has been refreshed successfully.')
                        ->success()
                        ->send();
                }),
            
            Action::make('print')
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->color('primary')
                ->tooltip('Print quotation')
                ->action(function () {
                    // Always refresh and validate on each click
                    $this->record->refresh();
                    $this->record->load(['itinerary', 'breakdown']);
                    
                    if (!$this->record->itinerary) {
                        \Filament\Notifications\Notification::make()
                            ->title('Cannot Print')
                            ->body('The itinerary must be created first.')
                            ->danger()
                            ->persistent()
                            ->send();
                        return;
                    }
                    
                    if (!$this->record->itinerary->is_complete) {
                        \Filament\Notifications\Notification::make()
                            ->title('Cannot Print')
                            ->body('The itinerary must be completed before printing.')
                            ->warning()
                            ->persistent()
                            ->send();
                        return;
                    }
                    
                    if (!$this->record->breakdown) {
                        \Filament\Notifications\Notification::make()
                            ->title('Cannot Print')
                            ->body('The breakdown must be created first.')
                            ->danger()
                            ->persistent()
                            ->send();
                        return;
                    }
                    
                    if (!$this->record->breakdown->is_completed) {
                        \Filament\Notifications\Notification::make()
                            ->title('Cannot Print')
                            ->body('The breakdown must be completed before printing.')
                            ->warning()
                            ->persistent()
                            ->send();
                        return;
                    }
                    
                    // If all validations pass, dispatch browser event to print
                    $this->dispatch('print-page');
                }),
            
            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->tooltip('Download as PDF')
                ->action(function () {
                    // Always refresh and validate on each click
                    $this->record->refresh();
                    $this->record->load(['itinerary', 'breakdown']);
                    
                    if (!$this->record->itinerary) {
                        \Filament\Notifications\Notification::make()
                            ->title('Cannot Export PDF')
                            ->body('The itinerary must be created first.')
                            ->danger()
                            ->persistent()
                            ->send();
                        return;
                    }
                    
                    if (!$this->record->itinerary->is_complete) {
                        \Filament\Notifications\Notification::make()
                            ->title('Cannot Export PDF')
                            ->body('The itinerary must be completed before exporting.')
                            ->warning()
                            ->persistent()
                            ->send();
                        return;
                    }
                    
                    if (!$this->record->breakdown) {
                        \Filament\Notifications\Notification::make()
                            ->title('Cannot Export PDF')
                            ->body('The breakdown must be created first.')
                            ->danger()
                            ->persistent()
                            ->send();
                        return;
                    }
                    
                    if (!$this->record->breakdown->is_completed) {
                        \Filament\Notifications\Notification::make()
                            ->title('Cannot Export PDF')
                            ->body('The breakdown must be completed before exporting.')
                            ->warning()
                            ->persistent()
                            ->send();
                        return;
                    }
                    
                    // If all validations pass, generate PDF
                    return $this->generatePdf();
                }),
        ];
    }

    /**
     * Check if itinerary and breakdown are complete
     */
    protected function isComplete(): bool
    {
        // Refresh to get latest data
        $this->record->loadMissing(['itinerary', 'breakdown']);
        
        return $this->record->itinerary && 
               $this->record->itinerary->is_complete && 
               $this->record->breakdown && 
               $this->record->breakdown->is_completed;
    }

    /**
     * Check completion status periodically (called by wire:poll)
     */
    public function checkCompletionStatus(): void
    {
        $this->record->refresh();
        $this->record->load(['itinerary', 'breakdown']);
        
        if (!$this->record->itinerary || 
            !$this->record->itinerary->is_complete || 
            !$this->record->breakdown || 
            !$this->record->breakdown->is_completed) {
            
            \Filament\Notifications\Notification::make()
                ->title('Data Changed')
                ->body('The itinerary or breakdown is no longer complete. Redirecting...')
                ->warning()
                ->send();
            
            $this->redirect(QuotationItineraryResource::getUrl('view', ['record' => $this->record->id]));
        }
    }

    /**
     * Generate PDF and download
     */
    protected function generatePdf()
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.quotation-customer', [
            'record' => $this->record,
            'itineraryDays' => $this->itineraryDays,
            'tripStartDate' => $this->tripStartDate,
            'tripEndDate' => $this->tripEndDate,
        ]);
        
        $pdf->setPaper('a4', 'portrait');
        
        $filename = 'Quotation-' . $this->record->quotation->number . '.pdf';
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    /**
     * Calculate trip start date from transportations or inquiry dates
     */
    protected function calculateTripDates(): void
    {
        if (!$this->record->itinerary) {
            return;
        }

        // Use the calculated_entry_date accessor which handles priority automatically
        $this->tripStartDate = $this->record->calculated_entry_date;
        
        // Calculate trip end date from last day number
        if ($this->tripStartDate) {
            $lastDay = $this->record->itinerary->days->sortByDesc('day_number')->first();
            if ($lastDay) {
                $this->tripEndDate = $this->tripStartDate->copy()->addDays($lastDay->day_number - 1);
            }
        } else {
            // Fallback to first ticket in itinerary activities
            foreach ($this->record->itinerary->days as $day) {
                $ticketActivity = $day->activities->first(function ($activity) {
                    return $activity->activityCategory?->type === \App\Enums\ActivityCategoryTypeEnum::TICKET;
                });

                if ($ticketActivity && $ticketActivity->start_time) {
                    $this->tripStartDate = Carbon::parse($ticketActivity->start_time);
                    
                    // Calculate trip end date from last day
                    $lastDay = $this->record->itinerary->days->sortByDesc('day_number')->first();
                    if ($lastDay) {
                        $this->tripEndDate = $this->tripStartDate->copy()->addDays($lastDay->day_number - 1);
                    }
                    break;
                }
            }
        }
    }

    /**
     * Prepare itinerary days with calculated dates
     */
    protected function prepareItineraryDays(): void
    {
        if (!$this->record->itinerary) {
            return;
        }

        $this->itineraryDays = [];

        // Sort days by day_number
        $days = $this->record->itinerary->days->sortBy('day_number');

        foreach ($days as $day) {
            $dayData = [
                'day_number' => $day->day_number,
                'date' => null,
                'current_city' => $day->currentCity,
                'accommodation_city' => $day->accommodationCity,
                'accommodation' => $day->accommodation,
                'accommodation_star_rating' => $day->accommodation_star_rating,
                'description' => $day->description,
                'has_transport' => false,
                'has_vehicle' => $day->vehicle_usage_mode !== null,
                'vehicle_usage_mode' => $day->vehicle_usage_mode,
                'has_companion' => $day->companion_hire_mode !== null,
                'companion_hire_mode' => $day->companion_hire_mode,
                'meals' => [
                    'breakfast' => null,
                    'lunch' => null,
                    'dinner' => null,
                ],
                'tickets' => [],
                'attractions' => [],
                'experiences' => [],
            ];

            // Calculate date for this day if trip start date is known
            if ($this->tripStartDate) {
                $dayData['date'] = $this->tripStartDate->copy()->addDays($day->day_number - 1);
            }

            // Process activities
            foreach ($day->activities as $activity) {
                $activityType = $activity->activityCategory?->type;

                switch ($activityType) {
                    case \App\Enums\ActivityCategoryTypeEnum::MEAL:
                        if ($activity->meal && $activity->meal->mealType) {
                            $mealPart = $activity->meal->meal_part?->value ?? 'lunch';
                            $dayData['meals'][$mealPart] = [
                                'type' => $activity->meal->mealType,
                                'meal_part' => $mealPart,
                            ];
                        }
                        break;

                    case \App\Enums\ActivityCategoryTypeEnum::TICKET:
                        if ($activity->ticket) {
                            $dayData['has_transport'] = true;
                            $dayData['tickets'][] = [
                                'from_city' => $activity->city,
                                'to_city' => $activity->ticket->toCity,
                                'class' => $activity->ticket->class,
                                'transport_mode' => $activity->ticket->transport_mode,
                                'departure_time' => $activity->start_time,
                                'arrival_time' => $activity->end_time,
                            ];
                        }
                        break;

                    case \App\Enums\ActivityCategoryTypeEnum::ATTRACTION:
                        if ($activity->attraction && $activity->attraction->attraction) {
                            $subAttractions = [];
                            foreach ($activity->attraction->subAttractions as $subAttr) {
                                if ($subAttr->subAttraction) {
                                    $subAttractions[] = $subAttr->subAttraction;
                                }
                            }

                            $dayData['attractions'][] = [
                                'attraction' => $activity->attraction->attraction,
                                'sub_attractions' => $subAttractions,
                                'is_outview' => $activity->attraction->is_outview ?? false,
                            ];
                        }
                        break;

                    case \App\Enums\ActivityCategoryTypeEnum::EXPERIENCE:
                        if ($activity->experience && $activity->experience->experience) {
                            $dayData['experiences'][] = $activity->experience->experience;
                        }
                        break;
                }
            }

            $this->itineraryDays[] = $dayData;
        }
    }

    public function getTitle(): string
    {
        return 'Quotation View - ' . $this->record->quotation->number;
    }
}

