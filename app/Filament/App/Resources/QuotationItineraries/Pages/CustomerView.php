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
        
        // Eager load all necessary relationships to prevent N+1 queries
        $this->record->load([
            'quotation.currency',
            'quotation.inquiry.contact',
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
            Action::make('print')
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->color('primary')
                ->openUrlInNewTab(false)
                ->extraAttributes([
                    'onclick' => 'window.print(); return false;',
                ]),
        ];
    }

    /**
     * Calculate trip start date from first ticket
     */
    protected function calculateTripDates(): void
    {
        if (!$this->record->itinerary) {
            return;
        }

        // Find first day with a ticket to determine trip start date
        $firstTicketDay = null;
        $firstTicketActivity = null;

        foreach ($this->record->itinerary->days as $day) {
            $ticketActivity = $day->activities->first(function ($activity) {
                return $activity->activityCategory?->type === \App\Enums\ActivityCategoryTypeEnum::TICKET;
            });

            if ($ticketActivity && $ticketActivity->start_time) {
                $firstTicketDay = $day;
                $firstTicketActivity = $ticketActivity;
                break;
            }
        }

        if ($firstTicketDay && $firstTicketActivity->start_time) {
            // Use the ticket's start time as the trip start date
            $this->tripStartDate = Carbon::parse($firstTicketActivity->start_time);
            
            // Calculate trip end date from last day
            $lastDay = $this->record->itinerary->days->sortByDesc('day_number')->first();
            if ($lastDay) {
                $this->tripEndDate = $this->tripStartDate->copy()->addDays($lastDay->day_number - 1);
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

