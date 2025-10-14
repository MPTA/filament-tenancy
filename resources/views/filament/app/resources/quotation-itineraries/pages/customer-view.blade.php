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
                    @endphp
                    
                    @if($logo)
                        <img src="{{ asset('storage/' . $logo) }}" alt="Company Logo" class="logo-image">
                    @else
                        <div class="logo-placeholder">
                            <div class="logo-text">{{ tenant()->settings?->company_name ?? tenant()->name ?? 'MPTA' }}</div>
                            <div class="logo-subtitle">Travel & Tourism</div>
                        </div>
                    @endif
                </div>
                <div class="quotation-title">
                    <h1>{{ tenant()->settings?->company_name ?? tenant()->name ?? 'Company Name' }}</h1>
                    <div class="quotation-subtitle">Tour Quotation</div>
                </div>
                <div class="quotation-details">
                    <div class="detail-row">
                        <span class="label">Number#:</span>
                        <span class="value">{{ $record->quotation->number }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Issue Date:</span>
                        <span class="value">{{ $record->quotation->created_at->format('Y-m-d H:i:s') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Expire Date:</span>
                        <span class="value">{{ $record->quotation->expire_date?->format('Y-m-d H:i:s') ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Customer Section --}}
        <div class="customer-section">
            <h2>Customer: {{ $record->quotation->inquiry->contact->full_name ?? 'N/A' }}</h2>
            @if($tripStartDate && $tripEndDate)
                <p>This quotation is valid for specific travel date From {{ $tripStartDate->format('d-M-Y') }} To: {{ $tripEndDate->format('d-M-Y') }}</p>
            @endif
        </div>

        {{-- Itinerary Section --}}
        @if($record->itinerary && count($itineraryDays) > 0)
            <div class="itinerary-section">
                <table class="itinerary-table">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>City</th>
                            <th>Activity</th>
                            <th>Meals</th>
                            <th>Hotel</th>
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
                                            <span class="day-icon vehicle-icon" title="Vehicle">🚗</span>
                                        @endif
                                        @if($day['has_companion'])
                                            <span class="day-icon companion-icon" title="Guide/Companion">👤</span>
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
                                                    <span class="outview-text">*Out View*</span>
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
                                        <div class="no-hotel">No Hotel</div>
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
                        <span class="legend-text">Vehicle included for this day</span>
                    </span>
                    <span class="legend-item">
                        <span class="legend-icon">👤</span>
                        <span class="legend-text">Tour guide/companion included for this day</span>
                    </span>
                </div>
            </div>
        @endif

        {{-- Offers Section --}}
        @if($record->quotationOfferGroups && $record->quotationOfferGroups->count() > 0)
            <div class="offers-section">
                <h2>Offers</h2>
                
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
                                <h3 class="section-title">Option {{ $offerGroupIndex + 1 }}</h3>
                                @if($companions->count() > 0)
                                    <div class="companions-list">
                                        <strong>Tour Guides:</strong>
                                        @foreach($companions as $companion)
                                            <span class="companion-item">
                                                @if(is_array($companion->companionType->name))
                                                    {{ $companion->companionType->name[app()->getLocale()] ?? $companion->companionType->name['en'] ?? 'Guide' }}
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
                                    <h4 class="features-title">Included Companion Costs:</h4>
                                    <div class="features-grid">
                                        {{-- Driver --}}
                                        <div class="feature-item">
                                            <span class="feature-icon">🚗</span>
                                            <span class="feature-text">
                                                Driver
                                                <span class="feature-detail">
                                                    Meal: 
                                                    @if($offerGroup->is_include_driver_meal)
                                                        Included{{ $offerGroup->is_driver_same_meal ? ' (With group)' : '' }}
                                                    @else
                                                        Not included
                                                    @endif
                                                    | Hotel: 
                                                    @if($offerGroup->is_include_driver_hotel)
                                                        Included
                                                        @if($offerGroup->is_driver_stay_same_hotel)
                                                            ({{ $offerGroup->driverRoomCategory->name ?? 'Same hotel' }})
                                                        @endif
                                                    @else
                                                        Not included
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
                                                        Meal: {{ $companion->is_same_meal ? 'With group' : 'Separate' }}
                                                        | Hotel: 
                                                        @if($companion->is_stay_same_hotel)
                                                            With group
                                                            @if($companion->roomCategory)
                                                                ({{ $companion->roomCategory->name }})
                                                            @endif
                                                        @else
                                                            Separate
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
                                            <th>Offer #</th>
                                            <th>Pax Qty</th>
                                            <th>Vehicle</th>
                                            <th>Leader Bed</th>
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
                                                <div class="vehicle-name">{{ $firstOffer->vehicleType->name ?? 'N/A' }}</div>
                                                <div class="drivers-count">{{ $firstOffer->drivers_qty ?? 1 }} Driver(s)</div>
                                            </td>
                                            <td class="leader-bed">Twin</td>
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
                <h3 class="description-title">Description & Notes</h3>
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
                <div class="signature-title">Company Signature & Stamp</div>
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
                <div class="signature-label">{{ tenant()->settings?->company_name ?? tenant()->name ?? 'Authorized Signature' }}</div>
            </div>
        </div>
    </div>
</x-filament-panels::page>