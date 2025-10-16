<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/quotation-view.css') }}">
    
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('print-page', () => {
                window.print();
            });
        });
    </script>
    
    <style>
        @media print {
            /* Force light mode and remove dark overlays */
            html,
            html *,
            body,
            body * {
                color-scheme: light !important;
                filter: none !important;
            }
            
            /* Remove all dark mode classes and pseudo elements */
            .dark,
            .dark *,
            [class*="dark"],
            [class*="dark"] *,
            html::before,
            html::after,
            body::before,
            body::after,
            *::before,
            *::after {
                background: none !important;
                background-color: transparent !important;
                filter: none !important;
                opacity: 1 !important;
                box-shadow: none !important;
            }
            
            /* Hide all Filament UI elements */
            .fi-sidebar,
            .fi-topbar,
            .fi-breadcrumbs,
            .fi-page-actions,
            .fi-header,
            .fi-tabs,
            .fi-main > header,
            button,
            nav,
            aside {
                display: none !important;
            }
            
            /* Page setup */
            @page {
                margin: 15mm 12mm;
                size: A4;
            }
            
            /* Body setup */
            html,
            body {
                background: white !important;
                background-color: white !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            /* Show quotation container */
            .quotation-container {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 20px !important;
                margin: 0 !important;
                background: white !important;
            }
            
            /* Restore specific backgrounds */
            .quotation-container .offer-group-section-header {
                background: #dc2626 !important;
                background-color: #dc2626 !important;
            }
            
            .quotation-container .offer-features {
                background: #f9fafb !important;
                background-color: #f9fafb !important;
            }
            
            .quotation-container .itinerary-table th,
            .quotation-container .pricing-table th {
                background: #f3f4f6 !important;
                background-color: #f3f4f6 !important;
            }
            
            /* Ensure colored backgrounds are preserved */
            .quotation-container,
            .quotation-container * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
    
    <div class="quotation-container" wire:poll.10s="checkCompletionStatus">
        {{-- Header with Logo and Title --}}
        <div class="quotation-header">
            <div class="header-top">
                <div class="company-logo">
                    @php
                        $logo = tenant()->settings?->logo;
                        // If logo is an array, get the first item
                        if (is_array($logo)) {
                            $logo = !empty($logo) ? $logo[0] : null;
                        }
                        
                        // Get company name based on locale
                        $locale = app()->getLocale();
                        $companyName = ($locale === 'en') 
                            ? (tenant()->settings?->company_name ?? tenant()->name ?? __('customer-view.messages.mpta_default'))
                            : (tenant()->settings?->company_local_name ?? tenant()->settings?->company_name ?? tenant()->name ?? __('customer-view.messages.mpta_default'));
                    @endphp
                    
                    @if($logo)
                        <img src="{{ asset('storage/' . $logo) }}" alt="Company Logo" class="logo-image">
                    @else
                        <div class="logo-placeholder">
                            <div class="logo-text">{{ $companyName }}</div>
                            <div class="logo-subtitle">{{ __('customer-view.logo_subtitle') }}</div>
                        </div>
                    @endif
                </div>
                <div class="quotation-title">
                    <h1>{{ $companyName }}</h1>
                    <div class="quotation-subtitle">{{ __('customer-view.tour_quotation') }}</div>
                </div>
                <div class="quotation-details">
                    <div class="detail-row">
                        <span class="label">{{ __('customer-view.number') }}</span>
                        <span class="value">{{ $record->quotation->number }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">{{ __('customer-view.issue_date') }}</span>
                        <span class="value">{{ $record->quotation->created_at->format('Y-m-d H:i:s') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">{{ __('customer-view.expire_date') }}</span>
                        <span class="value">{{ $record->quotation->expire_date?->format('Y-m-d H:i:s') ?? __('customer-view.messages.na') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Customer Section --}}
        <div class="customer-section">
            <h2>{{ __('customer-view.customer') }} {{ $record->quotation->inquiry->contact->full_name ?? __('customer-view.messages.na') }}</h2>
            @if($tripStartDate && $tripEndDate)
                <p>{{ __('customer-view.quotation_valid_dates', ['start_date' => $tripStartDate->format('d-M-Y'), 'end_date' => $tripEndDate->format('d-M-Y')]) }}</p>
            @endif
        </div>

        {{-- Itinerary Section --}}
        @if($record->itinerary && count($itineraryDays) > 0)
            <div class="itinerary-section">
                <table class="itinerary-table">
                    <thead>
                        <tr>
                            <th>{{ __('customer-view.table_headers.day') }}</th>
                            <th>{{ __('customer-view.table_headers.city') }}</th>
                            <th>{{ __('customer-view.table_headers.activity') }}</th>
                            <th>{{ __('customer-view.table_headers.meals') }}</th>
                            <th>{{ __('customer-view.table_headers.hotel') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($itineraryDays as $day)
                            <tr>
                                {{-- Day Column --}}
                                <td class="day-cell">
                                    <div class="day-number">#{{ $day['day_number'] }}</div>
                                    @if($day['date'])
                                        <div class="day-date">{{ $day['date']->format('d-M-Y') }}</div>
                                    @endif
                                    <div class="day-icons">
                                        @if($day['has_vehicle'])
                                            <span class="day-icon vehicle-icon" title="{{ __('customer-view.tooltips.vehicle') }}">🚗</span>
                                        @endif
                                        @if($day['has_companion'])
                                            <span class="day-icon companion-icon" title="{{ __('customer-view.tooltips.guide_companion') }}">👤</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- City Column --}}
                                <td class="city-cell">
                                    @if($day['accommodation_city'])
                                        {{ $day['accommodation_city']->name }}
                                    @elseif($day['current_city'])
                                        {{ $day['current_city']->name }}
                                    @else
                                        -
                                    @endif
                                </td>

                                {{-- Activity Column --}}
                                <td class="activity-cell">
                                    {{-- Transportation --}}
                                    @if(count($day['tickets']) > 0)
                                        @foreach($day['tickets'] as $ticket)
                                            <div class="transport-activity">
                                                @php
                                                    $transportIcon = '🚌'; // Default
                                                    if ($ticket['transport_mode']) {
                                                        $transportIcon = match($ticket['transport_mode']) {
                                                            \App\Enums\TransportModeEnum::AIR => '✈️',
                                                            \App\Enums\TransportModeEnum::TRAIN => '🚂',
                                                            \App\Enums\TransportModeEnum::LAND => '🚌',
                                                            default => '🚌',
                                                        };
                                                    }
                                                @endphp
                                                <span class="transport-icon">{{ $transportIcon }}</span>
                                                <span class="transport-text">
                                                    {{ $ticket['from_city']->name ?? 'N/A' }} → {{ $ticket['to_city']->name ?? 'N/A' }}
                                                    @if($ticket['class'])
                                                        <span class="ticket-class">({{ $ticket['class']->label() }})</span>
                                                    @endif
                                                    @if($ticket['departure_time'] || $ticket['arrival_time'])
                                                        <div class="ticket-time">
                                                            @if($ticket['departure_time'])
                                                                {{ \Carbon\Carbon::parse($ticket['departure_time'])->format('H:i') }}
                                                            @endif
                                                            @if($ticket['departure_time'] && $ticket['arrival_time'])
                                                                -
                                                            @endif
                                                            @if($ticket['arrival_time'])
                                                                {{ \Carbon\Carbon::parse($ticket['arrival_time'])->format('H:i') }}
                                                            @endif
                                                        </div>
                                                    @endif
                                                </span>
                                            </div>
                                        @endforeach
                                    @endif

                                    {{-- Attractions --}}
                                    @if(count($day['attractions']) > 0)
                                        @foreach($day['attractions'] as $attraction)
                                            <div class="attraction-activity">
                                                <span class="attraction-icon">🏛️</span>
                                                @if($day['accommodation_city'] || $day['current_city'])
                                                    <strong>{{ $day['accommodation_city']->name ?? $day['current_city']->name }}:</strong>
                                                @endif
                                                <span class="attraction-bracket">[ {{ $attraction['attraction']->name }} ]</span>
                                                @if($attraction['is_outview'])
                                                    <span class="outview-text">{{ __('customer-view.out_view') }}</span>
                                                @endif
                                                @if(count($attraction['sub_attractions']) > 0)
                                                    @foreach($attraction['sub_attractions'] as $subAttr)
                                                        <div class="sub-attraction">
                                                            <span class="sub-bracket">[ {{ $subAttr->name }} ]</span>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif

                                    {{-- Experiences --}}
                                    @if(count($day['experiences']) > 0)
                                        @foreach($day['experiences'] as $exp)
                                            <div class="experience-activity">
                                                <span class="experience-icon">🎯</span>
                                                @if($exp->city)
                                                    <strong>{{ $exp->city->name }}:</strong>
                                                @elseif($day['accommodation_city'] || $day['current_city'])
                                                    <strong>{{ $day['accommodation_city']->name ?? $day['current_city']->name }}:</strong>
                                                @endif
                                                {{ $exp->name }}
                                            </div>
                                        @endforeach
                                    @endif

                                    {{-- If no activities --}}
                                    @if(count($day['tickets']) == 0 && count($day['attractions']) == 0 && count($day['experiences']) == 0)
                                        <span class="no-activity">-</span>
                                    @endif

                                    {{-- Day Description --}}
                                    @if($day['description'])
                                        <div class="day-description">
                                            @if(is_array($day['description']))
                                                {{ $day['description'][app()->getLocale()] ?? $day['description']['en'] ?? '' }}
                                            @else
                                                {{ $day['description'] }}
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                {{-- Meals Column --}}
                                <td class="meals-cell">
                                    @php
                                        $hasMeals = collect($day['meals'])->filter()->count() > 0;
                                    @endphp
                                    
                                    @if($hasMeals)
                                        @if($day['meals']['breakfast'])
                                            <div class="meal-item">
                                                <span class="meal-type">B:</span>
                                                {{ $day['meals']['breakfast']['type']->name ?? 'Buffet breakfast (BB)' }}
                                            </div>
                                        @endif
                                        @if($day['meals']['lunch'])
                                            <div class="meal-item">
                                                <span class="meal-type">L:</span>
                                                {{ $day['meals']['lunch']['type']->name ?? 'Iran Standard Local' }}
                                            </div>
                                        @endif
                                        @if($day['meals']['dinner'])
                                            <div class="meal-item">
                                                <span class="meal-type">D:</span>
                                                {{ $day['meals']['dinner']['type']->name ?? 'Dinner' }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="no-meals">-</span>
                                    @endif
                                </td>

                                {{-- Hotel Column --}}
                                <td class="hotel-cell">
                                    @if($day['accommodation'])
                                        <div class="hotel-info">
                                            <div class="hotel-name">{{ $day['accommodation']->name }}</div>
                                            @if($day['accommodation_star_rating'])
                                                <div class="hotel-rating">
                                                    -{{ $day['accommodation_star_rating']->value }} Stars
                                                    <div class="stars">
                                                        @for($i = 0; $i < $day['accommodation_star_rating']->value; $i++)
                                                            ⭐
                                                        @endfor
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="no-hotel">{{ __('customer-view.no_hotel') }}</div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                {{-- Legend for Icons --}}
                <div class="itinerary-legend">
                    <span class="legend-item">
                        <span class="legend-icon">🚗</span>
                        <span class="legend-text">{{ __('customer-view.legend.vehicle_included') }}</span>
                    </span>
                    <span class="legend-item">
                        <span class="legend-icon">👤</span>
                        <span class="legend-text">{{ __('customer-view.legend.guide_included') }}</span>
                    </span>
                </div>
            </div>
        @endif

        {{-- Offers Section --}}
        @if($record->quotationOfferGroups && $record->quotationOfferGroups->count() > 0)
            <div class="offers-section">
                <h2>{{ __('customer-view.offers.title') }}</h2>
                
                {{-- Offer Groups Sections --}}
                @foreach($record->quotationOfferGroups as $offerGroupIndex => $offerGroup)
                    @php
                        $firstOffer = $offerGroup->quotationOffers->first();
                        
                        // Get all room categories and their prices for the first offer
                        $roomPrices = collect();
                        if ($firstOffer && $firstOffer->quotationOfferPrices) {
                            foreach($firstOffer->quotationOfferPrices as $price) {
                                $roomPrices->put($price->roomCategory->name ?? 'Unknown', $price);
                            }
                        }
                        
                        // Get companions for this offer group
                        $companions = $offerGroup->quotationOfferGroupCompanions;
                    @endphp
                    
                    @if($firstOffer && $roomPrices->count() > 0)
                        {{-- Offer Group Section --}}
                        <div class="offer-group-section">
                            {{-- Section Header with Companions --}}
                            <div class="offer-group-section-header">
                                <h3 class="section-title">{{ __('customer-view.offers.option', ['number' => $offerGroupIndex + 1]) }}</h3>
                                @if($companions->count() > 0)
                                    <div class="companions-list">
                                        <strong>{{ __('customer-view.offers.tour_guides') }}</strong>
                                        @foreach($companions as $companion)
                                            <span class="companion-item">
                                                @if(is_array($companion->companionType->name))
                                                    {{ $companion->companionType->name[app()->getLocale()] ?? $companion->companionType->name['en'] ?? __('customer-view.offers.guide_default') }}
                                                @else
                                                    {{ $companion->companionType->name }}
                                                @endif
                                                @if($companion->companionType->companionCategory)
                                                    <span class="companion-category">
                                                        ({{ is_array($companion->companionType->companionCategory->name) ? ($companion->companionType->companionCategory->name[app()->getLocale()] ?? $companion->companionType->companionCategory->name['en']) : $companion->companionType->companionCategory->name }})
                                                    </span>
                                                @endif
                                                @if(!$loop->last), @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- Section Content --}}
                            <div class="offer-group-content">
                                {{-- Offer Group Features --}}
                                <div class="offer-features">
                                    <h4 class="features-title">{{ __('customer-view.offers.included_companion_costs') }}</h4>
                                    <div class="features-grid">
                                        {{-- Driver --}}
                                        <div class="feature-item">
                                            <span class="feature-icon">🚗</span>
                                            <span class="feature-text">
                                                {{ __('customer-view.offers.driver') }}
                                                <span class="feature-detail">
                                                    {{ __('customer-view.offers.meal') }} 
                                                    @if($offerGroup->is_include_driver_meal)
                                                        {{ __('customer-view.offers.included') }}{{ $offerGroup->is_driver_same_meal ? ' ' . __('customer-view.offers.with_group') : '' }}
                                                    @else
                                                        {{ __('customer-view.offers.not_included') }}
                                                    @endif
                                                    | {{ __('customer-view.offers.hotel') }} 
                                                    @if($offerGroup->is_include_driver_hotel)
                                                        {{ __('customer-view.offers.included') }}
                                                        @if($offerGroup->is_driver_stay_same_hotel)
                                                            ({{ $offerGroup->driverRoomCategory->name ?? __('customer-view.offers.same_hotel') }})
                                                        @endif
                                                    @else
                                                        {{ __('customer-view.offers.not_included') }}
                                                    @endif
                                                </span>
                                            </span>
                                        </div>

                                        {{-- Companions --}}
                                        @foreach($companions as $companion)
                                            {{-- Companion Info --}}
                                            <div class="feature-item">
                                                <span class="feature-icon">👤</span>
                                                <span class="feature-text">
                                                    {{ is_array($companion->companionType->name) ? ($companion->companionType->name[app()->getLocale()] ?? $companion->companionType->name['en']) : $companion->companionType->name }}
                                                    <span class="feature-detail">
                                                        {{ __('customer-view.offers.meal') }} {{ $companion->is_same_meal ? __('customer-view.offers.with_group_no_paren') : __('customer-view.offers.separate') }}
                                                        | {{ __('customer-view.offers.hotel') }} 
                                                        @if($companion->is_stay_same_hotel)
                                                            {{ __('customer-view.offers.with_group_no_paren') }}
                                                            @if($companion->roomCategory)
                                                                ({{ $companion->roomCategory->name }})
                                                            @endif
                                                        @else
                                                            {{ __('customer-view.offers.separate') }}
                                                        @endif
                                                    </span>
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Pricing Table --}}
                                <table class="pricing-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('customer-view.pricing_table.offer_number') }}</th>
                                            <th>{{ __('customer-view.pricing_table.pax_qty') }}</th>
                                            <th>{{ __('customer-view.pricing_table.vehicle') }}</th>
                                            <th>{{ __('customer-view.pricing_table.leader_bed') }}</th>
                                            @foreach($roomPrices as $roomName => $price)
                                                <th>{{ $roomName }} ({{ $record->quotation->currency->code ?? 'USD' }})</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="offer-number">{{ $firstOffer->full_number }}</td>
                                            <td class="pax-qty">{{ $firstOffer->pax_qty }} + {{ $firstOffer->leaders_qty }}</td>
                                            <td class="vehicle-info">
                                                <div class="vehicle-name">{{ $firstOffer->vehicleType->name ?? __('customer-view.messages.na') }}</div>
                                                <div class="drivers-count">{{ __('customer-view.offers.drivers', ['count' => $firstOffer->drivers_qty ?? 1]) }}</div>
                                            </td>
                                            <td class="leader-bed">{{ __('customer-view.offers.twin') }}</td>
                                            @foreach($roomPrices as $roomName => $price)
                                                <td class="room-price">
                                                    {{ $price->quotation_currency_symbol }}{{ $price->formatted_quotation_currency_price }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        {{-- Quotation Description --}}
        @if($record->quotation->description)
            <div class="quotation-description-section">
                <h3 class="description-title">{{ __('customer-view.description.title') }}</h3>
                <div class="description-content">
                    @if(is_array($record->quotation->description))
                        {{ $record->quotation->description[app()->getLocale()] ?? $record->quotation->description['en'] ?? '' }}
                    @else
                        {{ $record->quotation->description }}
                    @endif
                </div>
            </div>
        @endif

        {{-- Signature Section --}}
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-title">{{ __('customer-view.signature.title') }}</div>
                <div class="signature-space">
                    @php
                        $signature = tenant()->settings?->signature;
                        // If signature is an array, get the first item
                        if (is_array($signature)) {
                            $signature = !empty($signature) ? $signature[0] : null;
                        }
                    @endphp
                    
                    @if($signature)
                        <img src="{{ asset('storage/' . $signature) }}" alt="Company Signature" class="signature-image">
                    @endif
                </div>
                <div class="signature-line"></div>
                <div class="signature-label">{{ $companyName }}</div>
            </div>
        </div>
    </div>
</x-filament-panels::page>