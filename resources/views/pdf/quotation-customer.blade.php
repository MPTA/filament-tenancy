<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Quotation {{ $record->quotation->number }}</title>
    <style>
        @page {
            margin: 15mm 12mm;
            size: A4 portrait;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Noto Sans', 'Arial', sans-serif;
            font-size: 9pt;
            color: #000000;
            line-height: 1.3;
            padding: 15mm 12mm;
        }
        
        /* Icons using Unicode symbols */
        .icon {
            font-weight: bold;
            font-size: 8pt;
            padding: 1px 3px;
            background: #f3f4f6;
            border-radius: 2px;
            margin-right: 3px;
        }
        
        /* Header Table Layout */
        .header-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        
        .header-table td {
            vertical-align: middle;
            padding: 5px;
            border: none;
        }
        
        .logo-placeholder {
            background: #dc2626;
            color: white;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            font-size: 9pt;
            width: 120px;
        }
        
        .company-name {
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
        }
        
        .quotation-subtitle {
            font-size: 12pt;
            color: #dc2626;
            font-weight: bold;
            text-align: center;
            margin-top: 3px;
        }
        
        .quotation-number-box {
            background: #dc2626;
            color: white;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .quotation-number-value {
            font-size: 13pt;
            font-family: 'Courier New', monospace;
        }
        
        .detail-item {
            font-size: 7pt;
            margin-bottom: 2px;
            text-align: right;
        }
        
        /* Customer Info */
        .customer-info {
            background: #f3f4f6;
            padding: 8px;
            margin-bottom: 12px;
            font-size: 8pt;
        }
        
        .customer-info strong {
            font-size: 10pt;
        }
        
        /* Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        
        .data-table th {
            background: #f3f4f6;
            border: 1px solid #000;
            padding: 5px 3px;
            text-align: left;
            font-size: 7pt;
            font-weight: bold;
        }
        
        .data-table td {
            border: 1px solid #000;
            padding: 5px 3px;
            font-size: 7pt;
            vertical-align: top;
        }
        
        .day-cell {
            width: 8%;
            text-align: center;
        }
        
        .city-cell {
            width: 12%;
        }
        
        .activity-cell {
            width: 35%;
        }
        
        .meals-cell {
            width: 20%;
        }
        
        .hotel-cell {
            width: 25%;
        }
        
        /* Activity content */
        .activity-line {
            margin-bottom: 3px;
            font-size: 7pt;
        }
        
        .meal-line {
            margin-bottom: 2px;
        }
        
        /* Offer Section */
        .offer-box {
            border: 2px solid #dc2626;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        
        .offer-header {
            background: #dc2626;
            color: white;
            padding: 8px;
            font-size: 11pt;
            font-weight: bold;
        }
        
        .offer-content {
            padding: 10px;
        }
        
        .features-box {
            background: #f9fafb;
            padding: 8px;
            margin-bottom: 10px;
            font-size: 7pt;
            border: 1px solid #d1d5db;
        }
        
        .feature-line {
            margin-bottom: 3px;
        }
        
        /* Description */
        .description-box {
            background: #f9fafb;
            border: 2px solid #d1d5db;
            padding: 12px;
            margin: 15px 0;
            page-break-inside: avoid;
        }
        
        .description-title {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 2px solid #dc2626;
        }
        
        .description-content {
            font-size: 8pt;
            line-height: 1.5;
        }
        
        /* Signature */
        .signature-box {
            border: 2px solid #d1d5db;
            padding: 12px;
            margin-top: 20px;
            text-align: center;
            page-break-inside: avoid;
        }
        
        .signature-title {
            font-size: 9pt;
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 8px;
        }
        
        .signature-space {
            height: 50px;
            border: 1px dashed #d1d5db;
            background: #fafafa;
            margin-bottom: 8px;
        }
        
        .signature-line {
            height: 1px;
            background: #000;
            margin: 8px 0;
        }
        
        .signature-label {
            font-size: 8pt;
        }
        
        .legend-box {
            background: #f9fafb;
            padding: 6px;
            font-size: 6pt;
            border: 1px solid #d1d5db;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td style="width: 20%;">
                @php
                    $logo = tenant()->settings?->logo;
                    if (is_array($logo)) {
                        $logo = !empty($logo) ? $logo[0] : null;
                    }
                @endphp
                
                @if($logo && file_exists(public_path('storage/' . $logo)))
                    <img src="{{ public_path('storage/' . $logo) }}" style="max-width: 100px; max-height: 60px;">
                @else
                    <div class="logo-placeholder">
                        {{ tenant()->settings?->company_name ?? tenant()->name ?? 'Company' }}
                    </div>
                @endif
            </td>
            <td style="width: 60%; text-align: center;">
                <div class="company-name">{{ tenant()->settings?->company_name ?? tenant()->name ?? 'Company Name' }}</div>
                <div class="quotation-subtitle">Tour Quotation</div>
            </td>
            <td style="width: 20%; text-align: right;">
                <div class="quotation-number-box">
                    <div style="font-size: 8pt;">Number#:</div>
                    <div class="quotation-number-value">{{ $record->quotation->number }}</div>
                </div>
                <div class="detail-item"><strong>Issue:</strong> {{ $record->quotation->created_at->format('Y-m-d') }}</div>
                <div class="detail-item"><strong>Expire:</strong> {{ $record->quotation->expire_date?->format('Y-m-d') ?? 'N/A' }}</div>
            </td>
        </tr>
    </table>

    {{-- Customer Info --}}
    <div class="customer-info">
        <strong>Customer:</strong> {{ $record->quotation->inquiry->contact->full_name ?? 'N/A' }}
        @if($tripStartDate && $tripEndDate)
            <br><strong>Travel Date:</strong> {{ $tripStartDate->format('d-M-Y') }} to {{ $tripEndDate->format('d-M-Y') }}
        @endif
    </div>

    {{-- Itinerary Table --}}
    @if($record->itinerary && count($itineraryDays) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th class="day-cell">Day</th>
                    <th class="city-cell">City</th>
                    <th class="activity-cell">Activity</th>
                    <th class="meals-cell">Meals</th>
                    <th class="hotel-cell">Hotel</th>
                </tr>
            </thead>
            <tbody>
                @foreach($itineraryDays as $day)
                    <tr>
                        <td class="day-cell">
                            <strong>#{{ $day['day_number'] }}</strong>
                            @if($day['date'])
                                <br><span style="font-size: 6pt;">{{ $day['date']->format('d-M-Y') }}</span>
                            @endif
                            @if($day['has_vehicle'] || $day['has_companion'])
                                <br>
                                @if($day['has_vehicle'])<span class="icon">⚙</span>@endif
                                @if($day['has_companion'])<span class="icon">◆</span>@endif
                            @endif
                        </td>
                        <td class="city-cell">
                            {{ $day['accommodation_city']->name ?? $day['current_city']->name ?? '-' }}
                        </td>
                        <td class="activity-cell">
                            @foreach($day['tickets'] as $ticket)
                                <div class="activity-line">
                                    @php
                                        $icon = match($ticket['transport_mode'] ?? null) {
                                            \App\Enums\TransportModeEnum::AIR => '✈',
                                            \App\Enums\TransportModeEnum::TRAIN => '⚡',
                                            \App\Enums\TransportModeEnum::LAND => '▶',
                                            default => '▶',
                                        };
                                    @endphp
                                    <span class="icon">{{ $icon }}</span> {{ $ticket['from_city']->name ?? '' }} → {{ $ticket['to_city']->name ?? '' }}
                                    @if($ticket['class'])
                                        ({{ $ticket['class']->label() }})
                                    @endif
                                </div>
                            @endforeach
                            
                            @foreach($day['attractions'] as $attraction)
                                <div class="activity-line">
                                    <span class="icon">★</span> <strong>{{ $day['accommodation_city']->name ?? $day['current_city']->name ?? '' }}:</strong> [ {{ $attraction['attraction']->name }} ]
                                </div>
                            @endforeach
                            
                            @foreach($day['experiences'] as $exp)
                                <div class="activity-line">
                                    <span class="icon">●</span> <strong>{{ $exp->city->name ?? $day['accommodation_city']->name ?? $day['current_city']->name ?? '' }}:</strong> {{ $exp->name }}
                                </div>
                            @endforeach
                            
                            @if(count($day['tickets']) == 0 && count($day['attractions']) == 0 && count($day['experiences']) == 0)
                                -
                            @endif
                            
                            @if($day['description'])
                                <div style="margin-top: 5px; padding: 4px; background: #f9fafb; border-left: 2px solid #dc2626; font-size: 6pt; font-style: italic;">
                                    {{ is_array($day['description']) ? ($day['description'][app()->getLocale()] ?? $day['description']['en'] ?? '') : $day['description'] }}
                                </div>
                            @endif
                        </td>
                        <td class="meals-cell">
                            @if($day['meals']['breakfast'])
                                <div class="meal-line"><strong>B:</strong> {{ $day['meals']['breakfast']['type']->name ?? 'Breakfast' }}</div>
                            @endif
                            @if($day['meals']['lunch'])
                                <div class="meal-line"><strong>L:</strong> {{ $day['meals']['lunch']['type']->name ?? 'Lunch' }}</div>
                            @endif
                            @if($day['meals']['dinner'])
                                <div class="meal-line"><strong>D:</strong> {{ $day['meals']['dinner']['type']->name ?? 'Dinner' }}</div>
                            @endif
                            @if(!$day['meals']['breakfast'] && !$day['meals']['lunch'] && !$day['meals']['dinner'])
                                -
                            @endif
                        </td>
                        <td class="hotel-cell">
                            @if($day['accommodation'])
                                <strong>{{ $day['accommodation']->name }}</strong>
                                @if($day['accommodation_star_rating'])
                                    <br><span style="font-size: 6pt;">{{ $day['accommodation_star_rating']->value }} Stars</span>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="legend-box">
            <span class="icon">⚙</span> Vehicle included | <span class="icon">◆</span> Tour guide/companion included
        </div>
    @endif

    {{-- Offers Section --}}
    @if($record->quotationOfferGroups && $record->quotationOfferGroups->count() > 0)
        <div style="page-break-before: always; margin-top: 20px;">
            <h2 style="font-size: 13pt; margin-bottom: 12px; font-weight: bold;">Offers</h2>
            
            @foreach($record->quotationOfferGroups as $offerGroupIndex => $offerGroup)
                @php
                    $firstOffer = $offerGroup->quotationOffers->first();
                    $roomPrices = collect();
                    if ($firstOffer && $firstOffer->quotationOfferPrices) {
                        foreach($firstOffer->quotationOfferPrices as $price) {
                            $roomPrices->put($price->roomCategory->name ?? 'Unknown', $price);
                        }
                    }
                    $companions = $offerGroup->quotationOfferGroupCompanions;
                @endphp
                
                @if($firstOffer && $roomPrices->count() > 0)
                    <div class="offer-box">
                        <div class="offer-header">
                            Option {{ $offerGroupIndex + 1 }}
                            @if($companions->count() > 0)
                                <br><span style="font-size: 8pt;">Tour Guides: 
                                @foreach($companions as $companion)
                                    {{ is_array($companion->companionType->name) ? ($companion->companionType->name[app()->getLocale()] ?? $companion->companionType->name['en']) : $companion->companionType->name }}@if(!$loop->last), @endif
                                @endforeach
                                </span>
                            @endif
                        </div>
                        
                        <div class="offer-content">
                            <div class="features-box">
                                <strong style="font-size: 8pt;">Included Companion Costs:</strong><br>
                                <span class="icon">⚙</span> <strong>Driver</strong> - Meal: {{ $offerGroup->is_include_driver_meal ? 'Included' : 'Not included' }} | Hotel: {{ $offerGroup->is_include_driver_hotel ? 'Included' : 'Not included' }}
                                @foreach($companions as $companion)
                                    <br><span class="icon">◆</span> <strong>{{ is_array($companion->companionType->name) ? ($companion->companionType->name[app()->getLocale()] ?? $companion->companionType->name['en']) : $companion->companionType->name }}</strong> - 
                                    Meal: {{ $companion->is_same_meal ? 'With group' : 'Separate' }} | Hotel: {{ $companion->is_stay_same_hotel ? 'With group' : 'Separate' }}
                                @endforeach
                            </div>
                            
                            <table class="data-table">
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
                                        <td style="font-size: 7pt; font-weight: bold;">{{ $firstOffer->full_number }}</td>
                                        <td>{{ $firstOffer->pax_qty }} + {{ $firstOffer->leaders_qty }}</td>
                                        <td>
                                            <strong>{{ $firstOffer->vehicleType->name ?? 'N/A' }}</strong>
                                            <br><span style="font-size: 6pt;">{{ $firstOffer->drivers_qty ?? 1 }} Driver(s)</span>
                                        </td>
                                        <td>Twin</td>
                                        @foreach($roomPrices as $price)
                                            <td style="text-align: center; font-weight: bold;">
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

    {{-- Description --}}
    @if($record->quotation->description)
        <div class="description-box">
            <div class="description-title">Description & Notes</div>
            <div class="description-content">
                {{ is_array($record->quotation->description) ? ($record->quotation->description[app()->getLocale()] ?? $record->quotation->description['en'] ?? '') : $record->quotation->description }}
            </div>
        </div>
    @endif

    {{-- Signature --}}
    <div class="signature-box">
        <div class="signature-title">Company Signature & Stamp</div>
        <div class="signature-space">
            @php
                $signature = tenant()->settings?->signature;
                if (is_array($signature)) {
                    $signature = !empty($signature) ? $signature[0] : null;
                }
            @endphp
            
            @if($signature && file_exists(public_path('storage/' . $signature)))
                <img src="{{ public_path('storage/' . $signature) }}" style="max-width: 100px; max-height: 40px;">
            @endif
        </div>
        <div class="signature-line"></div>
        <div class="signature-label">{{ tenant()->settings?->company_name ?? tenant()->name ?? 'Authorized Signature' }}</div>
    </div>
</body>
</html>
