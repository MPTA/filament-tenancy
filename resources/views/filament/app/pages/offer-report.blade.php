<div>
    <style>
        .cost-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .cost-grid {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 8px;
        }
        
        .cost-item {
            display: contents;
        }
        
        .cost-label {
            font-weight: 500;
            color: #6b7280;
            padding: 6px 0;
            font-size: 14px;
        }
        
        .cost-value {
            font-weight: 600;
            color: #1f2937;
            text-align: right;
            padding: 6px 0;
            font-family: monospace;
            font-size: 14px;
        }
        
        .subtotal-row {
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: 1fr auto;
            background: #f3f4f6;
            padding: 10px 12px;
            border-radius: 6px;
            margin-top: 8px;
            font-weight: 700;
            color: #4f46e5;
            font-size: 15px;
        }
        
        .calculation-box {
            background: #eef2ff;
            padding: 16px;
            border-radius: 8px;
            margin: 16px 0;
        }
        
        .calc-step {
            display: grid;
            grid-template-columns: 1fr auto;
            padding: 8px 12px;
            background: white;
            margin-bottom: 8px;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .calc-step:last-child {
            margin-bottom: 0;
        }
        
        .calc-label {
            color: #4b5563;
            font-weight: 500;
        }
        
        .calc-value {
            font-weight: 700;
            color: #1f2937;
            font-family: monospace;
        }
        
        .final-price-box {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-top: 12px;
        }
        
        .final-price-label {
            font-size: 14px;
            margin-bottom: 8px;
            opacity: 0.9;
        }
        
        .final-price-value {
            font-size: 32px;
            font-weight: 900;
            font-family: monospace;
        }
        
        .room-category-card {
            background: #fafafa;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .room-category-title {
            font-size: 18px;
            font-weight: 700;
            color: #4f46e5;
            margin-bottom: 12px;
        }
        
        .hotel-details {
            background: white;
            padding: 12px;
            border-radius: 6px;
            margin-top: 12px;
            border: 1px solid #e5e7eb;
        }
        
        .hotel-item {
            display: grid;
            grid-template-columns: 1fr auto;
            padding: 6px 0;
            border-bottom: 2px solid #f3f4f6;
            font-size: 13px;
        }
        
        .hotel-item:last-child {
            border-bottom: none;
        }
        
        .hotel-name {
            font-weight: 600;
            color: #374151;
        }
        
        .hotel-cost {
            font-family: monospace;
            color: #6b7280;
        }
        
        .accordion {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            margin-bottom: 8px;
            overflow: hidden;
        }
        
        .accordion-header {
            background: #f9fafb;
            padding: 12px 16px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.2s;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
        }
        
        .accordion-header:hover {
            background: #f3f4f6;
        }
        
        .accordion-header-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .accordion-title {
            font-weight: 600;
            color: #1f2937;
        }
        
        .accordion-total {
            font-family: monospace;
            color: #4f46e5;
            font-weight: 700;
        }
        
        .accordion-icon {
            transition: transform 0.2s;
            color: #9ca3af;
        }
        
        .accordion-icon.open {
            transform: rotate(180deg);
        }
        
        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            background: white;
        }
        
        .accordion-content.open {
            max-height: none;
            transition: max-height 0.5s ease-in;
        }
        
        .accordion-body {
            padding: 16px;
            border-top: 2px solid #e5e7eb;
        }
        
        .detail-item {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 12px;
            padding: 10px 12px;
            background: #f9fafb;
            margin-bottom: 8px;
            border-radius: 4px;
            font-size: 13px;
            align-items: center;
        }
        
        .detail-item:last-child {
            margin-bottom: 0;
        }
        
        .detail-name {
            font-weight: 500;
            color: #374151;
        }
        
        .detail-qty {
            text-align: center;
            color: #6b7280;
            font-family: monospace;
        }
        
        .detail-price {
            text-align: right;
            color: #6b7280;
            font-family: monospace;
        }
        
        .detail-total {
            text-align: right;
            font-weight: 700;
            color: #1f2937;
            font-family: monospace;
        }
        
        .detail-header {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 12px;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            border-bottom: 2px solid #e5e7eb;
            margin-bottom: 8px;
        }
        
        .detail-header > div:nth-child(2),
        .detail-header > div:nth-child(3),
        .detail-header > div:nth-child(4) {
            text-align: right;
        }
        
        .empty-state {
            text-align: center;
            padding: 20px;
            color: #9ca3af;
            font-size: 13px;
            font-style: italic;
        }
        
        @media print {
            .print-button {
                display: none !important;
            }
            
            .accordion-content {
                max-height: none !important;
                display: block !important;
            }
            
            .cost-section,
            .room-category-card {
                page-break-inside: avoid;
            }
            
            body {
                background: white;
            }
        }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.toggleAccordion = function(id) {
                const content = document.getElementById('accordion-' + id);
                const icon = document.getElementById('icon-' + id);
                
                if (content && icon) {
                    content.classList.toggle('open');
                    icon.classList.toggle('open');
                }
            };
            
            window.printReport = function() {
                window.print();
            };
        });
    </script>

    @if($offer)
        <!-- Basic Information -->
        <div class="cost-section">
            <div class="section-title">
                <span>📋</span>
                <span>Basic Information</span>
            </div>
            <div class="cost-grid">
                <div class="cost-item">
                    <span class="cost-label">Vehicle Type</span>
                    <span class="cost-value">{{ $offer->vehicleType->name ?? 'N/A' }}</span>
                </div>
                <div class="cost-item">
                    <span class="cost-label">PAX Quantity</span>
                    <span class="cost-value">{{ $offer->pax_qty }}</span>
                </div>
                <div class="cost-item">
                    <span class="cost-label">Leaders Quantity</span>
                    <span class="cost-value">{{ $offer->leaders_qty }}</span>
                </div>
                <div class="cost-item">
                    <span class="cost-label">Drivers Quantity</span>
                    <span class="cost-value">{{ $offer->drivers_qty }}</span>
                </div>
                <div class="cost-item">
                    <span class="cost-label">Markup</span>
                    <span class="cost-value">{{ $offer->markup }}%</span>
                </div>
            </div>
        </div>

        <!-- Per Person Direct Costs -->
        <div class="cost-section">
            <div class="section-title">
                <span>👤</span>
                <span>Per Person Direct Costs</span>
            </div>
            
            @php
                $offerGroup = $offer->quotationOfferGroup;
                $breakdown = $offerGroup->quotationItinerary->breakdown;
                $breakdownCurrency = $breakdown->currency;
                $bCurrency = $breakdownCurrency ? ($breakdownCurrency->symbol ?? $breakdownCurrency->code) : '$';
                
                // Attractions
                $attractions = $offerGroup->quotationOfferGroupAttractions ?? collect();
                $attractionsTotal = $attractions->sum('price');
                
                // Create a map of attraction_id to city from breakdown
                $attractionCityMap = $breakdown->attractions->keyBy('attraction_id')->map(fn($ba) => $ba->city);
                
                // Calculate sub-attractions total for all attractions
                $subAttractionsTotal = $attractions->sum(function($attraction) {
                    return $attraction->subAttractions->sum('price');
                });
                
                // Meals
                $meals = $offerGroup->quotationOfferGroupMeals ?? collect();
                $mealsTotal = $meals->sum(fn($m) => $m->price * $m->qty);
                
                // Tickets
                $tickets = $offerGroup->quotationOfferGroupTickets ?? collect();
                $ticketsTotal = $tickets->sum('price');
                
                // Individual Expenses (per person)
                $expenses = $offerGroup->quotationOfferGroupExpenses()
                    ->where('charge_mode', \App\Enums\ChargeModeEnum::PER_PERSON)
                    ->get() ?? collect();
                $expensesTotal = $expenses->sum('price');
                
                // Experiences (per person) - filter by experience.charge_mode
                $experiences = ($offerGroup->quotationOfferGroupExperiences ?? collect())->filter(function($exp) {
                    return $exp->experience && 
                           $exp->experience->charge_mode === \App\Enums\ChargeModeEnum::PER_PERSON;
                });
                $experiencesTotal = $experiences->sum('price');
                
                $perPersonTotal = $attractionsTotal + $subAttractionsTotal + $mealsTotal + 
                                  $ticketsTotal + $expensesTotal + $experiencesTotal;
            @endphp
            
            <!-- Attractions & Sub-Attractions Accordion -->
            <div class="accordion" x-data="{ open: false }">
                <div class="accordion-header" @click="open = !open" style="cursor: pointer;">
                    <div class="accordion-header-left">
                        <span>🎭</span>
                        <span class="accordion-title">Attractions & Sub-Attractions</span>
                        <svg :class="{ 'open': open }" class="accordion-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="accordion-total">{{ $bCurrency }}{{ number_format($attractionsTotal + $subAttractionsTotal, 2) }}</span>
                </div>
                <div :class="{ 'open': open }" class="accordion-content">
                    <div class="accordion-body">
                        @if($attractions->count() > 0)
                            @foreach($attractions as $attraction)
                                <!-- Main Attraction -->
                                <div style="margin-bottom: 16px; padding: 12px; background: #f9fafb; border-radius: 6px; border-left: 3px solid #4f46e5;">
                                    <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 12px; align-items: center; margin-bottom: 8px;">
                                        <div style="font-weight: 600; color: #1f2937; font-size: 14px;">
                                            🎭 {{ $attraction->attraction->name ?? 'N/A' }}
                                        </div>
                                        <div style="color: #6b7280; font-size: 13px;">
                                            📍 {{ $attractionCityMap->get($attraction->attraction_id)?->name ?? 'N/A' }}
                                        </div>
                                        <div style="text-align: right; font-weight: 700; color: #4f46e5; font-family: monospace;">
                                            {{ $bCurrency }}{{ number_format($attraction->price, 2) }}
                                        </div>
                                    </div>
                                    
                                    <!-- Sub-Attractions for this attraction -->
                                    @if($attraction->subAttractions && $attraction->subAttractions->count() > 0)
                                        <div style="margin-top: 8px; padding-left: 16px; border-left: 2px solid #e5e7eb;">
                                            @foreach($attraction->subAttractions as $subAttraction)
                                                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 12px; padding: 6px 0; align-items: center; font-size: 13px;">
                                                    <div style="color: #6b7280;">
                                                        🎪 {{ $subAttraction->subAttraction->name ?? 'N/A' }}
                                                    </div>
                                                    <div style="text-align: right; font-weight: 600; color: #1f2937; font-family: monospace;">
                                                        {{ $bCurrency }}{{ number_format($subAttraction->price, 2) }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">No attractions</div>
                        @endif
                    </div>
                </div>
            </div>

            
            <!-- Meals Accordion -->
            <div class="accordion" x-data="{ open: false }">
                <div class="accordion-header" @click="open = !open" style="cursor: pointer;">
                    <div class="accordion-header-left">
                        <span>🍽️</span>
                        <span class="accordion-title">Meals</span>
                        <svg :class="{ 'open': open }" class="accordion-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="accordion-total">{{ $bCurrency }}{{ number_format($mealsTotal, 2) }}</span>
                </div>
                <div :class="{ 'open': open }" class="accordion-content">
                    <div class="accordion-body">
                        @if($meals->count() > 0)
                            <div class="detail-header">
                                <div>Meal Type</div>
                                <div>Qty</div>
                                <div>Unit Price</div>
                                <div>Total</div>
                            </div>
                            @foreach($meals as $meal)
                                <div class="detail-item">
                                    <div class="detail-name">
                                        {{ $meal->mealType?->name ?? 'N/A' }}
                                    </div>
                                    <div class="detail-qty">{{ $meal->qty }}</div>
                                    <div class="detail-price">{{ $bCurrency }}{{ number_format($meal->price, 2) }}</div>
                                    <div class="detail-total">{{ $bCurrency }}{{ number_format($meal->price * $meal->qty, 2) }}</div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">No meals</div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Tickets Accordion -->
            <div class="accordion" x-data="{ open: false }">
                <div class="accordion-header" @click="open = !open" style="cursor: pointer;">
                    <div class="accordion-header-left">
                        <span>🎫</span>
                        <span class="accordion-title">Tickets</span>
                        <svg :class="{ 'open': open }" class="accordion-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="accordion-total">{{ $bCurrency }}{{ number_format($ticketsTotal, 2) }}</span>
                </div>
                <div :class="{ 'open': open }" class="accordion-content">
                    <div class="accordion-body">
                        @if($tickets->count() > 0)
                            <div class="detail-header">
                                <div>Route</div>
                                <div>Class</div>
                                <div>-</div>
                                <div>Price</div>
                            </div>
                            @foreach($tickets as $ticket)
                                <div class="detail-item">
                                    <div class="detail-name">
                                        {{ $ticket->fromCity->name ?? 'N/A' }} → {{ $ticket->toCity->name ?? 'N/A' }}
                                    </div>
                                    <div class="detail-qty">
                                        {{ $ticket->class?->label() ?? 'N/A' }}
                                    </div>
                                    <div class="detail-price">-</div>
                                    <div class="detail-total">{{ $bCurrency }}{{ number_format($ticket->price, 2) }}</div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">No tickets</div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Expenses (Per Person) Accordion -->
            <div class="accordion" x-data="{ open: false }">
                <div class="accordion-header" @click="open = !open" style="cursor: pointer;">
                    <div class="accordion-header-left">
                        <span>💰</span>
                        <span class="accordion-title">Individual Expenses</span>
                        <svg :class="{ 'open': open }" class="accordion-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="accordion-total">{{ $bCurrency }}{{ number_format($expensesTotal, 2) }}</span>
                </div>
                <div :class="{ 'open': open }" class="accordion-content">
                    <div class="accordion-body">
                        @if($expenses->count() > 0)
                            <div class="detail-header">
                                <div>Description</div>
                                <div>Charge Mode</div>
                                <div>-</div>
                                <div>Price</div>
                            </div>
                            @foreach($expenses as $expense)
                                <div class="detail-item">
                                    <div class="detail-name">{{ $expense->description ?? 'N/A' }}</div>
                                    <div class="detail-qty">{{ $expense->charge_mode?->label() ?? 'Per Person' }}</div>
                                    <div class="detail-price">-</div>
                                    <div class="detail-total">{{ $bCurrency }}{{ number_format($expense->price, 2) }}</div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">No individual expenses</div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Experiences (Per Person) Accordion -->
            <div class="accordion" x-data="{ open: false }">
                <div class="accordion-header" @click="open = !open" style="cursor: pointer;">
                    <div class="accordion-header-left">
                        <span>✨</span>
                        <span class="accordion-title">Individual Experiences</span>
                        <svg :class="{ 'open': open }" class="accordion-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="accordion-total">{{ $bCurrency }}{{ number_format($experiencesTotal, 2) }}</span>
                </div>
                <div :class="{ 'open': open }" class="accordion-content">
                    <div class="accordion-body">
                        @if($experiences->count() > 0)
                            <div class="detail-header">
                                <div>Experience Name</div>
                                <div>City</div>
                                <div>Charge Mode</div>
                                <div>Price</div>
                            </div>
                            @foreach($experiences as $experience)
                                <div class="detail-item">
                                    <div class="detail-name">{{ $experience->experience->name ?? 'N/A' }}</div>
                                    <div class="detail-qty">{{ $experience->experience->city->name ?? 'N/A' }}</div>
                                    <div class="detail-price">{{ $experience->experience->charge_mode?->label() ?? 'Per Person' }}</div>
                                    <div class="detail-total">{{ $bCurrency }}{{ number_format($experience->price, 2) }}</div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">No individual experiences</div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="subtotal-row" style="margin-top: 16px;">
                <span>👤 Subtotal (Per Person Direct)</span>
                <span>{{ $bCurrency }}{{ number_format($perPersonTotal, 2) }}</span>
            </div>
        </div>

        <!-- Per Group Costs -->
        <div class="cost-section">
            <div class="section-title">
                <span>👥</span>
                <span>Per Group Costs (÷ {{ $offer->pax_qty }} PAX)</span>
            </div>
            
            <!-- Vehicle & Driver Costs Accordion -->
            <div class="accordion" x-data="{ open: false }">
                <div class="accordion-header" @click="open = !open" style="cursor: pointer;">
                    <div class="accordion-header-left">
                        <span>🚗</span>
                        <span class="accordion-title">Vehicle & Driver Costs ({{ $offer->drivers_qty }} driver{{ $offer->drivers_qty > 1 ? 's' : '' }})</span>
                        <svg :class="{ 'open': open }" class="accordion-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="accordion-total">{{ $bCurrency }}{{ number_format($offer->vehicle_cost + $offer->driver_meals_cost + $offer->driver_accommodations_cost, 2) }}</span>
                </div>
                <div :class="{ 'open': open }" class="accordion-content">
                    <div class="accordion-body">
                        <!-- Vehicle Costs -->
                        <div style="margin-bottom: 20px; padding: 12px; background: #f0f9ff; border-radius: 6px; border-left: 3px solid #0284c7;">
                            <div style="font-weight: 700; color: #0284c7; margin-bottom: 12px; font-size: 14px;">
                                🚗 Vehicle Costs
                            </div>
                            
                            @if($offer->vehicle_days_qty > 0 || $offer->vehicle_half_days_qty > 0 || $offer->vehicle_airport_transfers_qty > 0)
                                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px; padding: 8px 12px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #e0f2fe; margin-bottom: 8px;">
                                    <div>Type</div>
                                    <div style="text-align: center;">Qty</div>
                                    <div style="text-align: right;">Unit Price</div>
                                    <div style="text-align: right;">Total</div>
                                </div>
                                
                                @if($offer->vehicle_days_qty > 0)
                                    <div class="detail-item" style="background: white;">
                                        <div class="detail-name">Full Day</div>
                                        <div class="detail-qty">{{ $offer->vehicle_days_qty }}</div>
                                        <div class="detail-price">{{ $bCurrency }}{{ number_format($offer->vehicle_day_price, 2) }}</div>
                                        <div class="detail-total">{{ $bCurrency }}{{ number_format($offer->vehicle_days_qty * $offer->vehicle_day_price, 2) }}</div>
                                    </div>
                                @endif
                                
                                @if($offer->vehicle_half_days_qty > 0)
                                    <div class="detail-item" style="background: white;">
                                        <div class="detail-name">Half Day</div>
                                        <div class="detail-qty">{{ $offer->vehicle_half_days_qty }}</div>
                                        <div class="detail-price">{{ $bCurrency }}{{ number_format($offer->vehicle_half_day_price, 2) }}</div>
                                        <div class="detail-total">{{ $bCurrency }}{{ number_format($offer->vehicle_half_days_qty * $offer->vehicle_half_day_price, 2) }}</div>
                                    </div>
                                @endif
                                
                                @if($offer->vehicle_airport_transfers_qty > 0)
                                    <div class="detail-item" style="background: white;">
                                        <div class="detail-name">Airport Transfer / Hours</div>
                                        <div class="detail-qty">{{ $offer->vehicle_airport_transfers_qty }}</div>
                                        <div class="detail-price">{{ $bCurrency }}{{ number_format($offer->vehicle_airport_transfer_price, 2) }}</div>
                                        <div class="detail-total">{{ $bCurrency }}{{ number_format($offer->vehicle_airport_transfers_qty * $offer->vehicle_airport_transfer_price, 2) }}</div>
                                    </div>
                                @endif
                                
                                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px; padding: 10px 12px; background: #dbeafe; margin-top: 8px; border-radius: 4px; font-weight: 700; color: #0284c7;">
                                    <div style="grid-column: 1 / 4; text-align: right;">👥 Vehicle Subtotal:</div>
                                    <div style="text-align: right; font-family: monospace;">{{ $bCurrency }}{{ number_format($offer->vehicle_cost, 2) }}</div>
                                </div>
                            @else
                                <div class="empty-state">No vehicle costs</div>
                            @endif
                        </div>
                        
                        <!-- Driver Meals -->
                        @php
                            $driverMeals = $offer->quotationOfferDriverMeals ?? collect();
                        @endphp
                        
                        @if($driverMeals->count() > 0)
                            <div style="margin-bottom: 20px; padding: 12px; background: #fef3c7; border-radius: 6px; border-left: 3px solid #f59e0b;">
                                <div style="font-weight: 700; color: #f59e0b; margin-bottom: 12px; font-size: 14px;">
                                    🍽️ Driver Meals ({{ $offer->drivers_qty }} driver{{ $offer->drivers_qty > 1 ? 's' : '' }})
                                </div>
                                
                                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px; padding: 8px 12px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #fde68a; margin-bottom: 8px;">
                                    <div>Meal Type</div>
                                    <div style="text-align: center;">Qty</div>
                                    <div style="text-align: right;">Unit Price</div>
                                    <div style="text-align: right;">Total</div>
                                </div>
                                
                                @foreach($driverMeals as $meal)
                                    <div class="detail-item" style="background: white;">
                                        <div class="detail-name">{{ $meal->mealType?->name ?? 'Base Budget' }}</div>
                                        <div class="detail-qty">{{ $meal->qty }}</div>
                                        <div class="detail-price">{{ $bCurrency }}{{ number_format($meal->price, 2) }}</div>
                                        <div class="detail-total">{{ $bCurrency }}{{ number_format($meal->qty * $meal->price * $offer->drivers_qty, 2) }} <span style="font-size: 11px; color: #9ca3af;">({{ $meal->qty }} × {{ $offer->drivers_qty }} drivers)</span></div>
                                    </div>
                                @endforeach
                                
                                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px; padding: 10px 12px; background: #fde68a; margin-top: 8px; border-radius: 4px; font-weight: 700; color: #f59e0b;">
                                    <div style="grid-column: 1 / 4; text-align: right;">👥 Driver Meals Subtotal:</div>
                                    @php $driverMealsPerDriver = $driverMeals->sum(fn($m) => $m->qty * $m->price); @endphp
                                    <div style="text-align: right; font-family: monospace;">{{ $bCurrency }}{{ number_format($offer->driver_meals_cost, 2) }} <span style="font-size: 11px; color: #d97706;">({{ number_format($driverMealsPerDriver, 2) }} × {{ $offer->drivers_qty }} = {{ number_format($offer->driver_meals_cost, 2) }})</span></div>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Driver Accommodations -->
                        @php
                            $driverAccommodations = $offer->quotationOfferDriverAccommodations ?? collect();
                        @endphp
                        
                        @if($driverAccommodations->count() > 0)
                            <div style="margin-bottom: 20px; padding: 12px; background: #fce7f3; border-radius: 6px; border-left: 3px solid #ec4899;">
                                <div style="font-weight: 700; color: #ec4899; margin-bottom: 12px; font-size: 14px;">
                                    🏨 Driver Accommodations ({{ $offer->drivers_qty }} driver{{ $offer->drivers_qty > 1 ? 's' : '' }})
                                </div>
                                
                                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px; padding: 8px 12px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #fbcfe8; margin-bottom: 8px;">
                                    <div>Hotel / City</div>
                                    <div style="text-align: center;">Nights</div>
                                    <div style="text-align: right;">Night Price</div>
                                    <div style="text-align: right;">Total</div>
                                </div>
                                
                                @foreach($driverAccommodations as $accommodation)
                                    <div class="detail-item" style="background: white;">
                                        <div class="detail-name">
                                            @if($accommodation->accommodation_id)
                                                {{ $accommodation->accommodation->name ?? 'N/A' }}
                                            @else
                                                {{ $accommodation->city->name ?? 'Base Budget' }}
                                            @endif
                                        </div>
                                        <div class="detail-qty">{{ $accommodation->nights }}</div>
                                        <div class="detail-price">{{ $bCurrency }}{{ number_format($accommodation->night_price, 2) }}</div>
                                        <div class="detail-total">{{ $bCurrency }}{{ number_format($accommodation->nights * $accommodation->night_price * $offer->drivers_qty, 2) }} <span style="font-size: 11px; color: #9ca3af;">({{ $accommodation->nights }}n × {{ $offer->drivers_qty }} drivers)</span></div>
                                    </div>
                                @endforeach
                                
                                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px; padding: 10px 12px; background: #fbcfe8; margin-top: 8px; border-radius: 4px; font-weight: 700; color: #ec4899;">
                                    <div style="grid-column: 1 / 4; text-align: right;">👥 Driver Accommodations Subtotal:</div>
                                    @php $driverAccomPerDriver = $driverAccommodations->sum(fn($a) => $a->nights * $a->night_price); @endphp
                                    <div style="text-align: right; font-family: monospace;">{{ $bCurrency }}{{ number_format($offer->driver_accommodations_cost, 2) }} <span style="font-size: 11px; color: #db2777;">({{ number_format($driverAccomPerDriver, 2) }} × {{ $offer->drivers_qty }} = {{ number_format($offer->driver_accommodations_cost, 2) }})</span></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div style="height: 12px;"></div>
            
            <!-- Leader Costs Accordion -->
            <div class="accordion" x-data="{ open: false }">
                <div class="accordion-header" @click="open = !open" style="cursor: pointer;">
                    <div class="accordion-header-left">
                        <span>👨‍🏫</span>
                        <span class="accordion-title">Leader Costs ({{ $offer->leaders_qty }} leader{{ $offer->leaders_qty > 1 ? 's' : '' }})</span>
                        <svg :class="{ 'open': open }" class="accordion-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="accordion-total">{{ $bCurrency }}{{ number_format($offer->leader_meals_cost + $offer->leader_attractions_cost + $offer->leader_sub_attractions_cost + $offer->leader_experiences_cost + $offer->leader_tickets_cost + $offer->leader_expenses_cost + $offer->leader_accommodations_cost, 2) }}</span>
                </div>
                <div :class="{ 'open': open }" class="accordion-content">
                    <div class="accordion-body">
                        <!-- Leader Meals -->
                        @php
                            $leaderMeals = $offer->quotationOfferLeaderMeals ?? collect();
                        @endphp
                        
                        @if($leaderMeals->count() > 0)
                            <div style="margin-bottom: 20px; padding: 12px; background: #fef3c7; border-radius: 6px; border-left: 3px solid #f59e0b;">
                                <div style="font-weight: 700; color: #f59e0b; margin-bottom: 12px; font-size: 14px;">
                                    🍽️ Leader Meals
                                </div>
                                
                                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px; padding: 8px 12px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #fde68a; margin-bottom: 8px;">
                                    <div>Meal Type</div>
                                    <div style="text-align: center;">Qty</div>
                                    <div style="text-align: right;">Unit Price</div>
                                    <div style="text-align: right;">Total</div>
                                </div>
                                
                                @foreach($leaderMeals as $meal)
                                    <div class="detail-item" style="background: white;">
                                        <div class="detail-name">{{ $meal->mealType?->name ?? 'N/A' }}</div>
                                        <div class="detail-qty">{{ $meal->qty }}</div>
                                        <div class="detail-price">{{ $bCurrency }}{{ number_format($meal->price, 2) }}</div>
                                        <div class="detail-total">{{ $bCurrency }}{{ number_format($meal->qty * $meal->price * $offer->leaders_qty, 2) }} <span style="font-size: 11px; color: #9ca3af;">({{ $meal->qty }} × {{ $offer->leaders_qty }} leaders)</span></div>
                                    </div>
                                @endforeach
                                
                                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px; padding: 10px 12px; background: #fde68a; margin-top: 8px; border-radius: 4px; font-weight: 700; color: #f59e0b;">
                                    <div style="grid-column: 1 / 4; text-align: right;">👥 Leader Meals Subtotal:</div>
                                    @php $leaderMealsPerLeader = $leaderMeals->sum(fn($m) => $m->qty * $m->price); @endphp
                                    <div style="text-align: right; font-family: monospace;">{{ $bCurrency }}{{ number_format($offer->leader_meals_cost, 2) }} <span style="font-size: 11px; color: #d97706;">({{ number_format($leaderMealsPerLeader, 2) }} × {{ $offer->leaders_qty }} = {{ number_format($offer->leader_meals_cost, 2) }})</span></div>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Leader Attractions -->
                        @php
                            $leaderAttractions = $offer->quotationOfferLeaderAttractions ?? collect();
                        @endphp
                        
                        @if($leaderAttractions->count() > 0)
                            <div style="margin-bottom: 20px; padding: 12px; background: #f3e8ff; border-radius: 6px; border-left: 3px solid #a855f7;">
                                <div style="font-weight: 700; color: #a855f7; margin-bottom: 12px; font-size: 14px;">
                                    🎭 Leader Attractions & Sub-Attractions
                                </div>
                                
                                @foreach($leaderAttractions as $attraction)
                                    <div style="margin-bottom: 12px; padding: 10px; background: white; border-radius: 4px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <div style="font-weight: 600; color: #1f2937; font-size: 14px;">
                                                🎭 {{ $attraction->attraction->name ?? 'N/A' }}
                                            </div>
                                            <div style="text-align: right; font-weight: 700; color: #a855f7; font-family: monospace;">
                                                {{ $bCurrency }}{{ number_format($attraction->price, 2) }}
                                            </div>
                                        </div>
                                        
                                        <!-- Sub-Attractions for this leader attraction -->
                                        @if($attraction->subAttractions && $attraction->subAttractions->count() > 0)
                                            <div style="margin-top: 8px; padding-left: 16px; border-left: 2px solid #e5e7eb;">
                                                @foreach($attraction->subAttractions as $subAttraction)
                                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 6px 0; font-size: 13px;">
                                                        <div style="color: #6b7280; display: flex; align-items: center; gap: 8px;">
                                                            <span>🎪</span>
                                                            <span>{{ $subAttraction->subAttraction->name ?? 'N/A' }}</span>
                                                            <span style="color: #9ca3af; font-size: 11px;">(Qty: {{ $subAttraction->qty }})</span>
                                                        </div>
                                                        <div style="text-align: right; font-weight: 600; color: #1f2937; font-family: monospace;">
                                                            {{ $bCurrency }}{{ number_format($subAttraction->price, 2) }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                                
                                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px; padding: 10px 12px; background: #f3e8ff; margin-top: 8px; border-radius: 4px; font-weight: 700; color: #a855f7;">
                                    <div style="grid-column: 1 / 4; text-align: right;">👥 Leader Attractions & Sub-Attractions Subtotal:</div>
                                    @php $leaderAttractionsPerLeader = $leaderAttractions->sum(fn($a) => $a->price + $a->subAttractions->sum("price")); @endphp
                                    <div style="text-align: right; font-family: monospace;">{{ $bCurrency }}{{ number_format($offer->leader_attractions_cost + $offer->leader_sub_attractions_cost, 2) }} <span style="font-size: 11px; color: #9333ea;">({{ number_format($leaderAttractionsPerLeader, 2) }} × {{ $offer->leaders_qty }} = {{ number_format($offer->leader_attractions_cost + $offer->leader_sub_attractions_cost, 2) }})</span></div>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Leader Tickets -->
                        @php
                            $leaderTickets = $offer->quotationOfferLeaderTickets ?? collect();
                        @endphp
                        
                        @if($leaderTickets->count() > 0)
                            <div style="margin-bottom: 20px; padding: 12px; background: #dbeafe; border-radius: 6px; border-left: 3px solid #3b82f6;">
                                <div style="font-weight: 700; color: #3b82f6; margin-bottom: 12px; font-size: 14px;">
                                    🎫 Leader Tickets
                                </div>
                                
                                <div style="display: grid; grid-template-columns: 3fr 1fr 1fr; gap: 12px; padding: 8px 12px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #bfdbfe; margin-bottom: 8px;">
                                    <div>Route</div>
                                    <div style="text-align: center;">Class</div>
                                    <div style="text-align: right;">Price</div>
                                </div>
                                
                                @foreach($leaderTickets as $ticket)
                                    <div style="display: grid; grid-template-columns: 3fr 1fr 1fr; gap: 12px; padding: 8px 12px; font-size: 12px; background: white; border-bottom: 2px solid #eff6ff;">
                                        <span style="color: #6b7280;">
                                            {{ $ticket->fromCity->name ?? 'N/A' }} → {{ $ticket->toCity->name ?? 'N/A' }}
                                        </span>
                                        <span style="text-align: center; color: #6b7280; font-size: 11px;">{{ $ticket->class?->label() ?? 'N/A' }}</span>
                                        <span style="text-align: right; font-weight: 700; color: #1f2937; font-family: monospace;">{{ $bCurrency }}{{ number_format($ticket->price, 2) }}</span>
                                    </div>
                                @endforeach
                                
                                <div style="display: grid; grid-template-columns: 3fr 1fr 1fr; gap: 12px; padding: 10px 12px; background: #bfdbfe; margin-top: 8px; border-radius: 4px; font-weight: 700; color: #3b82f6;">
                                    <div style="grid-column: 1 / 3; text-align: right;">👥 Leader Tickets Subtotal:</div>
                                    @php $leaderTicketsPerLeader = $leaderTickets->sum("price"); @endphp
                                    <div style="text-align: right; font-family: monospace;">{{ $bCurrency }}{{ number_format($offer->leader_tickets_cost, 2) }} <span style="font-size: 11px; color: #2563eb;">({{ number_format($leaderTicketsPerLeader, 2) }} × {{ $offer->leaders_qty }} = {{ number_format($offer->leader_tickets_cost, 2) }})</span></div>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Leader Experiences -->
                        @php
                            $leaderExperiences = $offer->quotationOfferLeaderExperiences ?? collect();
                        @endphp
                        
                        @if($leaderExperiences->count() > 0)
                            <div style="margin-bottom: 20px; padding: 12px; background: #fce7f3; border-radius: 6px; border-left: 3px solid #ec4899;">
                                <div style="font-weight: 700; color: #ec4899; margin-bottom: 12px; font-size: 14px;">
                                    ✨ Leader Experiences
                                </div>
                                
                                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px; padding: 8px 12px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #fbcfe8; margin-bottom: 8px;">
                                    <div>Experience Name</div>
                                    <div style="text-align: center;">City</div>
                                    <div style="text-align: center;">Charge Mode</div>
                                    <div style="text-align: right;">Price</div>
                                </div>
                                
                                @foreach($leaderExperiences as $experience)
                                    <div class="detail-item" style="background: white;">
                                        <div class="detail-name">{{ $experience->experience->name ?? 'N/A' }}</div>
                                        <div class="detail-qty">{{ $experience->experience->city->name ?? 'N/A' }}</div>
                                        <div class="detail-price">{{ $experience->experience->charge_mode?->label() ?? 'N/A' }}</div>
                                        <div class="detail-total">{{ $bCurrency }}{{ number_format($experience->price, 2) }}</div>
                                    </div>
                                @endforeach
                                
                                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px; padding: 10px 12px; background: #fbcfe8; margin-top: 8px; border-radius: 4px; font-weight: 700; color: #ec4899;">
                                    <div style="grid-column: 1 / 4; text-align: right;">👥 Leader Experiences Subtotal:</div>
                                    @php $leaderExperiencesPerLeader = $leaderExperiences->sum("price"); @endphp
                                    <div style="text-align: right; font-family: monospace;">{{ $bCurrency }}{{ number_format($offer->leader_experiences_cost, 2) }} <span style="font-size: 11px; color: #db2777;">({{ number_format($leaderExperiencesPerLeader, 2) }} × {{ $offer->leaders_qty }} = {{ number_format($offer->leader_experiences_cost, 2) }})</span></div>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Leader Expenses -->
                        @php
                            $leaderExpenses = $offer->quotationOfferLeaderExpenses ?? collect();
                        @endphp
                        
                        @if($leaderExpenses->count() > 0)
                            <div style="margin-bottom: 20px; padding: 12px; background: #fef9c3; border-radius: 6px; border-left: 3px solid #eab308;">
                                <div style="font-weight: 700; color: #eab308; margin-bottom: 12px; font-size: 14px;">
                                    💰 Leader Expenses
                                </div>
                                
                                <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 12px; padding: 8px 12px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #fef08a; margin-bottom: 8px;">
                                    <div>Description</div>
                                    <div style="text-align: right;">Price</div>
                                </div>
                                
                                @foreach($leaderExpenses as $expense)
                                    <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 12px; padding: 10px 12px; background: white; margin-bottom: 8px; border-radius: 4px; font-size: 13px;">
                                        <div class="detail-name">{{ $expense->description ?? 'N/A' }}</div>
                                        <div style="text-align: right; font-weight: 700; color: #1f2937; font-family: monospace;">{{ $bCurrency }}{{ number_format($expense->price, 2) }}</div>
                                    </div>
                                @endforeach
                                
                                <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 12px; padding: 10px 12px; background: #fef08a; margin-top: 8px; border-radius: 4px; font-weight: 700; color: #eab308;">
                                    <div style="text-align: right;">👥 Leader Expenses Subtotal:</div>
                                    @php $leaderExpensesPerLeader = $leaderExpenses->sum("price"); @endphp
                                    <div style="text-align: right; font-family: monospace;">{{ $bCurrency }}{{ number_format($offer->leader_expenses_cost, 2) }} <span style="font-size: 11px; color: #ca8a04;">({{ number_format($leaderExpensesPerLeader, 2) }} × {{ $offer->leaders_qty }} = {{ number_format($offer->leader_expenses_cost, 2) }})</span></div>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Leader Accommodations -->
                        @php
                            $leaderAccommodations = $offer->quotationOfferLeaderAccommodations ?? collect();
                        @endphp
                        
                        @if($leaderAccommodations->count() > 0)
                            <div style="margin-bottom: 20px; padding: 12px; background: #f0fdf4; border-radius: 6px; border-left: 3px solid #22c55e;">
                                <div style="font-weight: 700; color: #22c55e; margin-bottom: 12px; font-size: 14px;">
                                    🏨 Leader Accommodations
                                </div>
                                
                                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px; padding: 8px 12px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #bbf7d0; margin-bottom: 8px;">
                                    <div>Hotel</div>
                                    <div style="text-align: center;">Nights</div>
                                    <div style="text-align: right;">Night Price</div>
                                    <div style="text-align: right;">Total</div>
                                </div>
                                
                                @foreach($leaderAccommodations as $accommodation)
                                    <div class="detail-item" style="background: white;">
                                        <div class="detail-name">{{ $accommodation->accommodation->name ?? 'N/A' }}</div>
                                        <div class="detail-qty">{{ $accommodation->nights }}</div>
                                        <div class="detail-price">{{ $bCurrency }}{{ number_format($accommodation->night_price, 2) }}</div>
                                        <div class="detail-total">{{ $bCurrency }}{{ number_format($accommodation->nights * $accommodation->night_price * $offer->leaders_qty, 2) }} <span style="font-size: 11px; color: #9ca3af;">({{ $accommodation->nights }}n × {{ $offer->leaders_qty }} leaders)</span></div>
                                    </div>
                                @endforeach
                                
                                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px; padding: 10px 12px; background: #bbf7d0; margin-top: 8px; border-radius: 4px; font-weight: 700; color: #22c55e;">
                                    <div style="grid-column: 1 / 4; text-align: right;">👥 Leader Accommodations Subtotal:</div>
                                    @php $leaderAccomPerLeader = $leaderAccommodations->sum(fn($a) => $a->nights * $a->night_price); @endphp
                                    <div style="text-align: right; font-family: monospace;">{{ $bCurrency }}{{ number_format($offer->leader_accommodations_cost, 2) }} <span style="font-size: 11px; color: #16a34a;">({{ number_format($leaderAccomPerLeader, 2) }} × {{ $offer->leaders_qty }} = {{ number_format($offer->leader_accommodations_cost, 2) }})</span></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div style="height: 12px;"></div>
            
            <!-- Companions Costs Accordion -->
            @php
                $companions = $offerGroup->quotationOfferGroupCompanions ?? collect();
                $companionsTotalCost = $offer->companions_salaries_cost + $offer->companions_cost;
            @endphp
            
            @if($companions->count() > 0)
                <div class="accordion" x-data="{ open: false }">
                    <div class="accordion-header" @click="open = !open" style="cursor: pointer;">
                        <div class="accordion-header-left">
                            <span>👨‍💼</span>
                            <span class="accordion-title">Companions (Tour Staff) Costs ({{ $companions->count() }} companion{{ $companions->count() > 1 ? 's' : '' }})</span>
                            <svg :class="{ 'open': open }" class="accordion-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="accordion-total">{{ $bCurrency }}{{ number_format($companionsTotalCost, 2) }}</span>
                    </div>
                    <div :class="{ 'open': open }" class="accordion-content">
                        <div class="accordion-body">
                            @foreach($companions as $companion)
                                @php
                                    $companionSalary = ($companion->full_days_qty * $companion->day_price) + 
                                                      ($companion->half_days_qty * $companion->half_day_price);
                                    $companionMealsCost = $companion->meals->sum(fn($m) => $m->qty * $m->price);
                                    $companionAccommodationsCost = $companion->accommodations->sum(fn($a) => $a->nights * $a->night_price);
                                    $companionAttractionsCost = $companion->attractions->sum('price');
                                    $companionSubAttractionsCost = $companion->attractions->sum(fn($a) => $a->subAttractions->sum('price'));
                                    $companionExperiencesCost = $companion->experiences->sum('price');
                                    $companionExpensesCost = $companion->expenses->sum('price');
                                    $companionTicketsCost = $companion->tickets->sum('price');
                                    $companionTotal = $companionSalary + $companionMealsCost + $companionAccommodationsCost + 
                                                     $companionAttractionsCost + $companionSubAttractionsCost + 
                                                     $companionExperiencesCost + $companionExpensesCost + $companionTicketsCost;
                                @endphp
                                
                                <!-- Companion Separator for multiple companions -->
                                @if(!$loop->first)
                                    <div style="height: 1px; background: #e5e7eb; margin: 20px 0;"></div>
                                @endif
                                
                                <!-- Companion Header -->
                                <div style="display: flex; justify-content: space-between; align-items: center; background: #fff7ed; padding: 12px; border-radius: 6px; margin-bottom: 16px; border-left: 3px solid #f59e0b;">
                                    <div style="font-weight: 700; color: #ea580c; font-size: 15px;">
                                        👤 Companion {{ $loop->iteration }}: {{ $companion->companionType->name ?? 'Unknown' }}
                                        @if($companion->livingCity)
                                            <span style="font-weight: 400; color: #a16207; font-size: 13px; margin-left: 8px;">
                                                (📍 {{ $companion->livingCity->name }})
                                            </span>
                                        @endif
                                    </div>
                                    <div style="font-weight: 900; color: #ea580c; font-size: 16px; font-family: monospace;">
                                        {{ $bCurrency }}{{ number_format($companionTotal, 2) }}
                                    </div>
                                </div>
                                    
                                    <!-- Salary -->
                                    @if($companionSalary > 0)
                                        <div style="background: white; padding: 12px; border-radius: 6px; margin-bottom: 20px; border-left: 3px solid #84cc16;">
                                            <div style="font-weight: 600; color: #4d7c0f; margin-bottom: 12px; font-size: 14px;">💼 Salary</div>
                                            
                                            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px; padding: 8px 12px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #ecfccb; margin-bottom: 8px;">
                                                <div>Type</div>
                                                <div style="text-align: center;">Qty</div>
                                                <div style="text-align: right;">Unit Price</div>
                                                <div style="text-align: right;">Total</div>
                                            </div>
                                            
                                            @if($companion->full_days_qty > 0)
                                                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px; padding: 8px 12px; font-size: 12px; border-bottom: 2px solid #f7fee7;">
                                                    <span style="color: #6b7280;">Full Days</span>
                                                    <span style="text-align: center; color: #6b7280;">{{ $companion->full_days_qty }}</span>
                                                    <span style="text-align: right; color: #6b7280; font-family: monospace;">{{ $bCurrency }}{{ number_format($companion->day_price, 2) }}</span>
                                                    <span style="text-align: right; font-weight: 700; font-family: monospace;">{{ $bCurrency }}{{ number_format($companion->full_days_qty * $companion->day_price, 2) }}</span>
                                                </div>
                                            @endif
                                            @if($companion->half_days_qty > 0)
                                                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px; padding: 8px 12px; font-size: 12px;">
                                                    <span style="color: #6b7280;">Half Days</span>
                                                    <span style="text-align: center; color: #6b7280;">{{ $companion->half_days_qty }}</span>
                                                    <span style="text-align: right; color: #6b7280; font-family: monospace;">{{ $bCurrency }}{{ number_format($companion->half_day_price, 2) }}</span>
                                                    <span style="text-align: right; font-weight: 700; font-family: monospace;">{{ $bCurrency }}{{ number_format($companion->half_days_qty * $companion->half_day_price, 2) }}</span>
                                                </div>
                                            @endif
                                            
                                            <div style="text-align: right; margin-top: 8px; padding-top: 8px; border-top: 2px solid #ecfccb; font-weight: 700; color: #4d7c0f; font-family: monospace;">
                                                👥 Salary Total: {{ $bCurrency }}{{ number_format($companionSalary, 2) }}
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Meals Details -->
                                    @if($companion->meals && $companion->meals->count() > 0)
                                        <div style="background: white; padding: 12px; border-radius: 6px; margin-bottom: 20px; border-left: 3px solid #fbbf24;">
                                            <div style="font-weight: 600; color: #d97706; margin-bottom: 12px; font-size: 14px;">🍽️ Meals</div>
                                            
                                            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px; padding: 8px 12px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #fef3c7; margin-bottom: 8px;">
                                                <div>Meal Type</div>
                                                <div style="text-align: center;">Qty</div>
                                                <div style="text-align: right;">Unit Price</div>
                                                <div style="text-align: right;">Total</div>
                                            </div>
                                            
                                            @foreach($companion->meals as $meal)
                                                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px; padding: 8px 12px; font-size: 12px; border-bottom: 2px solid #fffbeb;">
                                                    <span style="color: #6b7280;">{{ $meal->mealType?->name ?? 'Base Budget' }}</span>
                                                    <span style="text-align: center; color: #6b7280;">{{ $meal->qty }}</span>
                                                    <span style="text-align: right; color: #6b7280; font-family: monospace;">{{ $bCurrency }}{{ number_format($meal->price, 2) }}</span>
                                                    <span style="text-align: right; font-weight: 700; font-family: monospace;">{{ $bCurrency }}{{ number_format($meal->qty * $meal->price, 2) }}</span>
                                                </div>
                                            @endforeach
                                            
                                            <div style="text-align: right; margin-top: 8px; padding-top: 8px; border-top: 2px solid #fef3c7; font-weight: 700; color: #d97706; font-family: monospace;">
                                                👥 Meals Total: {{ $bCurrency }}{{ number_format($companionMealsCost, 2) }}
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Accommodations Details -->
                                    @if($companion->accommodations && $companion->accommodations->count() > 0)
                                        <div style="background: white; padding: 12px; border-radius: 6px; margin-bottom: 20px; border-left: 3px solid #f472b6;">
                                            <div style="font-weight: 600; color: #db2777; margin-bottom: 12px; font-size: 14px;">🏨 Accommodations</div>
                                            
                                            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px; padding: 8px 12px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #fce7f3; margin-bottom: 8px;">
                                                <div>Hotel/City</div>
                                                <div style="text-align: center;">Nights</div>
                                                <div style="text-align: right;">Night Price</div>
                                                <div style="text-align: right;">Total</div>
                                            </div>
                                            
                                            @foreach($companion->accommodations as $accommodation)
                                                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px; padding: 8px 12px; font-size: 12px; border-bottom: 2px solid #fdf2f8;">
                                                    <span style="color: #6b7280;">
                                                        @if($accommodation->accommodation_id)
                                                            {{ $accommodation->accommodation->name ?? 'N/A' }}
                                                        @else
                                                            {{ $accommodation->city->name ?? 'Base Budget' }}
                                                        @endif
                                                    </span>
                                                    <span style="text-align: center; color: #6b7280;">{{ $accommodation->nights }}</span>
                                                    <span style="text-align: right; color: #6b7280; font-family: monospace;">{{ $bCurrency }}{{ number_format($accommodation->night_price, 2) }}</span>
                                                    <span style="text-align: right; color: #1f2937; font-weight: 700; font-family: monospace;">{{ $bCurrency }}{{ number_format($accommodation->nights * $accommodation->night_price, 2) }}</span>
                                                </div>
                                            @endforeach
                                            
                                            <div style="text-align: right; margin-top: 8px; padding-top: 8px; border-top: 2px solid #fce7f3; font-weight: 700; color: #db2777; font-family: monospace;">
                                                👥 Accommodations Total: {{ $bCurrency }}{{ number_format($companionAccommodationsCost, 2) }}
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Attractions Details -->
                                    @if($companion->attractions && $companion->attractions->count() > 0)
                                        <div style="background: white; padding: 12px; border-radius: 6px; margin-bottom: 20px; border-left: 3px solid #a78bfa;">
                                            <div style="font-weight: 600; color: #7c3aed; margin-bottom: 12px; font-size: 14px;">🎭 Attractions & Sub-Attractions</div>
                                            
                                            @foreach($companion->attractions as $attraction)
                                                <!-- Main Attraction -->
                                                <div style="margin-bottom: 12px; padding: 10px; background: #faf5ff; border-radius: 4px;">
                                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                                        <div style="font-weight: 600; color: #1f2937; font-size: 14px;">
                                                            🎭 {{ $attraction->attraction->name ?? 'N/A' }}
                                                        </div>
                                                        <div style="text-align: right; font-weight: 700; color: #7c3aed; font-family: monospace;">
                                                            {{ $bCurrency }}{{ number_format($attraction->price, 2) }}
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Sub-Attractions for this attraction -->
                                                    @if($attraction->subAttractions && $attraction->subAttractions->count() > 0)
                                                        <div style="margin-top: 8px; padding-left: 16px; border-left: 2px solid #e9d5ff;">
                                                            @foreach($attraction->subAttractions as $subAttraction)
                                                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 6px 0; font-size: 13px;">
                                                                    <div style="color: #6b7280;">
                                                                        🎪 {{ $subAttraction->subAttraction->name ?? 'N/A' }}
                                                                    </div>
                                                                    <div style="text-align: right; font-weight: 600; color: #1f2937; font-family: monospace;">
                                                                        {{ $bCurrency }}{{ number_format($subAttraction->price, 2) }}
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                            
                                            <div style="text-align: right; margin-top: 8px; padding-top: 8px; border-top: 2px solid #f3e8ff; font-weight: 700; color: #7c3aed; font-family: monospace;">
                                                👥 Attractions Total: {{ $bCurrency }}{{ number_format($companionAttractionsCost + $companionSubAttractionsCost, 2) }}
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Tickets Details -->
                                    @if($companion->tickets && $companion->tickets->count() > 0)
                                        <div style="background: white; padding: 12px; border-radius: 6px; margin-bottom: 20px; border-left: 3px solid #60a5fa;">
                                            <div style="font-weight: 600; color: #2563eb; margin-bottom: 12px; font-size: 14px;">🎫 Tickets</div>
                                            
                                            <div style="display: grid; grid-template-columns: 3fr 1fr 1fr; gap: 12px; padding: 8px 12px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #dbeafe; margin-bottom: 8px;">
                                                <div>Route</div>
                                                <div style="text-align: center;">Class</div>
                                                <div style="text-align: right;">Price</div>
                                            </div>
                                            
                                            @foreach($companion->tickets as $ticket)
                                                <div style="display: grid; grid-template-columns: 3fr 1fr 1fr; gap: 12px; padding: 8px 12px; font-size: 12px; border-bottom: 2px solid #eff6ff;">
                                                    <span style="color: #6b7280;">{{ $ticket->fromCity->name ?? 'N/A' }} → {{ $ticket->toCity->name ?? 'N/A' }}</span>
                                                    <span style="text-align: right; color: #6b7280; font-size: 11px;">{{ $ticket->class?->label() ?? 'N/A' }}</span>
                                                    <span style="text-align: right; color: #1f2937; font-weight: 700; font-family: monospace;">{{ $bCurrency }}{{ number_format($ticket->price, 2) }}</span>
                                                </div>
                                            @endforeach
                                            
                                            <div style="text-align: right; margin-top: 8px; padding-top: 8px; border-top: 2px solid #dbeafe; font-weight: 700; color: #2563eb; font-family: monospace;">
                                                👥 Tickets Total: {{ $bCurrency }}{{ number_format($companionTicketsCost, 2) }}
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Experiences Details -->
                                    @if($companion->experiences && $companion->experiences->count() > 0)
                                        <div style="background: white; padding: 12px; border-radius: 6px; margin-bottom: 20px; border-left: 3px solid #fb923c;">
                                            <div style="font-weight: 600; color: #ea580c; margin-bottom: 12px; font-size: 14px;">✨ Experiences</div>
                                            
                                            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px; padding: 8px 12px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #ffedd5; margin-bottom: 8px;">
                                                <div>Experience Name</div>
                                                <div style="text-align: center;">City</div>
                                                <div style="text-align: center;">Charge Mode</div>
                                                <div style="text-align: right;">Price</div>
                                            </div>
                                            
                                            @foreach($companion->experiences as $experience)
                                                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px; padding: 8px 12px; font-size: 12px; border-bottom: 2px solid #fff7ed;">
                                                    <span style="color: #6b7280;">{{ $experience->experience->name ?? 'N/A' }}</span>
                                                    <span style="text-align: center; color: #6b7280; font-size: 11px;">{{ $experience->experience->city->name ?? 'N/A' }}</span>
                                                    <span style="text-align: center; color: #6b7280; font-size: 11px;">{{ $experience->experience->charge_mode?->label() ?? 'N/A' }}</span>
                                                    <span style="text-align: right; color: #1f2937; font-weight: 700; font-family: monospace;">{{ $bCurrency }}{{ number_format($experience->price, 2) }}</span>
                                                </div>
                                            @endforeach
                                            
                                            <div style="text-align: right; margin-top: 8px; padding-top: 8px; border-top: 2px solid #ffedd5; font-weight: 700; color: #ea580c; font-family: monospace;">
                                                👥 Experiences Total: {{ $bCurrency }}{{ number_format($companionExperiencesCost, 2) }}
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Expenses Details -->
                                    @if($companion->expenses && $companion->expenses->count() > 0)
                                        <div style="background: white; padding: 12px; border-radius: 6px; margin-bottom: 20px; border-left: 3px solid #34d399;">
                                            <div style="font-weight: 600; color: #059669; margin-bottom: 12px; font-size: 14px;">💰 Expenses</div>
                                            
                                            <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 12px; padding: 8px 12px; font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #d1fae5; margin-bottom: 8px;">
                                                <div>Description</div>
                                                <div style="text-align: right;">Price</div>
                                            </div>
                                            
                                            @foreach($companion->expenses as $expense)
                                                <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 12px; padding: 8px 12px; font-size: 12px; border-bottom: 2px solid #f0fdf4;">
                                                    <span style="color: #6b7280;">{{ $expense->description ?? 'N/A' }}</span>
                                                    <span style="text-align: right; color: #1f2937; font-weight: 700; font-family: monospace;">{{ $bCurrency }}{{ number_format($expense->price, 2) }}</span>
                                                </div>
                                            @endforeach
                                            
                                            <div style="text-align: right; margin-top: 8px; padding-top: 8px; border-top: 2px solid #d1fae5; font-weight: 700; color: #059669; font-family: monospace;">
                                                👥 Expenses Total: {{ $bCurrency }}{{ number_format($companionExpensesCost, 2) }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
            @endif
        </div>
    </div>
            
            <div style="height: 12px;"></div>
                       <!-- Group Experiences & Expenses Accordion -->
                    @php
                    // Get all experiences and expenses (already loaded)
                    $allExperiences = $offerGroup->quotationOfferGroupExperiences ?? collect();
                    $allExpenses = $offerGroup->quotationOfferGroupExpenses ?? collect();
                    
                    // Get per-group experiences (filter from loaded collection)
                    $groupExperiences = $allExperiences->filter(function($exp) {
                        return $exp->experience && 
                               $exp->experience->charge_mode === \App\Enums\ChargeModeEnum::PER_GROUP;
                    });
                        
                    // Get per-group expenses (filter from loaded collection)
                    $groupExpenses = $allExpenses->filter(function($exp) {
                        return $exp->charge_mode === \App\Enums\ChargeModeEnum::PER_GROUP;
                    });
                @endphp
                
                @if($groupExperiences->count() > 0 || $groupExpenses->count() > 0)
                    <div class="accordion" x-data="{ open: false }">
                        <div class="accordion-header" @click="open = !open" style="cursor: pointer;">
                            <div class="accordion-header-left">
                                <span>📦</span>
                                <span class="accordion-title">Group Experiences & Expenses</span>
                                <svg :class="{ 'open': open }" class="accordion-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="accordion-total">{{ $bCurrency }}{{ number_format($offer->offer_group_per_group_cost, 2) }}</span>
                        </div>
                        <div :class="{ 'open': open }" class="accordion-content">
                            <div class="accordion-body">
                                <!-- Group Experiences -->
                                @if($groupExperiences->count() > 0)
                                    <div style="background: white; padding: 12px; border-radius: 6px; margin-bottom: 20px; border-left: 3px solid #8b5cf6;">
                                        <div style="font-weight: 600; color: #7c3aed; margin-bottom: 12px; font-size: 14px;">✨ Group Experiences</div>
                                        
                                        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px; padding: 8px 12px; font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #f3e8ff; margin-bottom: 8px;">
                                            <div>Experience Name</div>
                                            <div style="text-align: center;">City</div>
                                            <div style="text-align: center;">Charge Mode</div>
                                            <div style="text-align: right;">Price</div>
                                        </div>
                                        
                                        @foreach($groupExperiences as $experience)
                                            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px; padding: 8px 12px; font-size: 12px; background: #faf5ff; margin-bottom: 8px; border-radius: 4px;">
                                                <span style="color: #374151; font-weight: 500;">{{ $experience->experience->name ?? 'N/A' }}</span>
                                                <span style="text-align: center; color: #6b7280; font-size: 11px;">{{ $experience->experience->city->name ?? 'N/A' }}</span>
                                                <span style="text-align: center; color: #6b7280; font-size: 11px;">Per Group</span>
                                                <span style="text-align: right; font-weight: 700; color: #1f2937; font-family: monospace; font-size: 13px;">{{ $bCurrency }}{{ number_format($experience->price ?? 0, 2) }}</span>
                                            </div>
                                        @endforeach
                                        
                                        <div style="text-align: right; margin-top: 8px; padding-top: 8px; border-top: 2px solid #f3e8ff; font-weight: 700; color: #7c3aed; font-family: monospace;">
                                            👥 Experiences Total: {{ $bCurrency }}{{ number_format($groupExperiences->sum('price'), 2) }}
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Group Expenses -->
                                @if($groupExpenses->count() > 0)
                                    <div style="background: white; padding: 12px; border-radius: 6px; margin-bottom: 20px; border-left: 3px solid #06b6d4;">
                                        <div style="font-weight: 600; color: #0891b2; margin-bottom: 12px; font-size: 14px;">💼 Group Expenses</div>
                                        
                                        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 12px; padding: 8px 12px; font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; border-bottom: 2px solid #cffafe; margin-bottom: 8px;">
                                            <div>Description</div>
                                            <div style="text-align: center;">Charge Mode</div>
                                            <div style="text-align: right;">Price</div>
                                        </div>
                                        
                                        @foreach($groupExpenses as $expense)
                                            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 12px; padding: 8px 12px; font-size: 12px; background: #ecfeff; margin-bottom: 8px; border-radius: 4px;">
                                                <span style="color: #374151; font-weight: 500;">{{ $expense->description ?? 'N/A' }}</span>
                                                <span style="text-align: center; color: #6b7280; font-size: 11px;">Per Group</span>
                                                <span style="text-align: right; font-weight: 700; color: #1f2937; font-family: monospace; font-size: 13px;">{{ $bCurrency }}{{ number_format($expense->price ?? 0, 2) }}</span>
                                            </div>
                                        @endforeach
                                        
                                        <div style="text-align: right; margin-top: 8px; padding-top: 8px; border-top: 2px solid #cffafe; font-weight: 700; color: #0891b2; font-family: monospace;">
                                            👥 Expenses Total: {{ $bCurrency }}{{ number_format($groupExpenses->sum('price'), 2) }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="cost-grid">
                        <div class="cost-item">
                            <span class="cost-label">📦 Group Exp. & Expenses</span>
                            <span class="cost-value">{{ $bCurrency }}{{ number_format($offer->offer_group_per_group_cost, 2) }}</span>
                        </div>
                    </div>
                @endif
         
            <div class="cost-grid">
                <div class="subtotal-row">
                    <span>👥 Subtotal (Per Group)</span>
                    <span>{{ $bCurrency }}{{ number_format($offer->total_per_group_cost, 2) }}</span>
                </div>
            </div>
        </div>
            

        <!-- Final Prices by Room Category -->
        @php
            $quotation = $offer->quotationOfferGroup->quotationItinerary->quotation ?? null;
            $exchangeRate = $quotation ? $quotation->exchange_rate : 1;
            $perGroupPerPerson = $offer->pax_qty > 0 ? $offer->total_per_group_cost / $offer->pax_qty : 0;
            $quotationCurrency = $quotation ? $quotation->currency : null;
            $qCurrency = $quotationCurrency ? ($quotationCurrency->symbol ?? $quotationCurrency->code) : '$';
        @endphp
        
        @foreach($offer->quotationOfferPrices as $offerPrice)
            <div class="room-category-card">
                <div class="room-category-title">
                    🛏️ {{ $offerPrice->roomCategory->name ?? 'Unknown' }}
                </div>
                
                @php
                    $accommodationCost = $offer->getAccommodationCostForRoomCategory($offerPrice->room_category_id);
                    $basePrice = $perPersonTotal + $perGroupPerPerson + $accommodationCost;
                    $priceAfterExchange = $exchangeRate > 0 ? $basePrice / $exchangeRate : $basePrice;
                    $markupAmount = $priceAfterExchange * ($offer->markup / 100);
                    $finalPrice = $priceAfterExchange + $markupAmount;
                @endphp
                
                <div class="calculation-box">
                    <div class="calc-step">
                        <span class="calc-label">👤 Per Person Direct</span>
                        <span class="calc-value">{{ $bCurrency }}{{ number_format($perPersonTotal, 2) }}</span>
                    </div>
                    <div class="calc-step">
                        <span class="calc-label">👤 Per Group Share (÷ {{ $offer->pax_qty }})</span>
                        <span class="calc-value">{{ $bCurrency }}{{ number_format($perGroupPerPerson, 2) }}</span>
                    </div>
                    <div class="calc-step">
                        <span class="calc-label">+ Accommodation</span>
                        <span class="calc-value">{{ $bCurrency }}{{ number_format($accommodationCost, 2) }}</span>
                    </div>
                    <div class="calc-step" style="background: #e0e7ff;">
                        <span class="calc-label" style="font-weight: 700;">= Base Price</span>
                        <span class="calc-value">{{ $bCurrency }}{{ number_format($basePrice, 2) }}</span>
                    </div>
                    <div class="calc-step">
                        <span class="calc-label">÷ Exchange Rate: 1 {{ $qCurrency }} = {{ number_format($exchangeRate, 4) }} {{ $bCurrency }}</span>
                        <span class="calc-value">{{ number_format($exchangeRate, 4) }}</span>
                    </div>
                    <div class="calc-step" style="background: #fef3c7;">
                        <span class="calc-label" style="font-weight: 700;">= Base Fare</span>
                        <span class="calc-value">{{ $qCurrency }}{{ number_format($priceAfterExchange, 2) }}</span>
                    </div>
                    <div class="calc-step">
                        <span class="calc-label">+ Markup ({{ $offer->markup }}%)</span>
                        <span class="calc-value">{{ $qCurrency }}{{ number_format($markupAmount, 2) }}</span>
                    </div>
                </div>
                
                <div class="final-price-box">
                    <div class="final-price-label">💎 Final Price Per Person</div>
                    <div class="final-price-value">{{ $qCurrency }}{{ number_format($finalPrice, 2) }}</div>
                </div>
                
                <!-- Hotel Details -->
                @if($offerPrice->quotationOfferPriceAccommodations->count() > 0)
                    <div class="hotel-details">
                        <div style="font-weight: 700; margin-bottom: 10px; color: #4f46e5; font-size: 14px;">
                            🏨 Hotel Breakdown:
                        </div>
                        @foreach($offerPrice->quotationOfferPriceAccommodations as $accommodation)
                            <div class="hotel-item">
                                <span class="hotel-name">
                                    {{ $accommodation->accommodation->name ?? 'Hotel' }}
                                    @if($accommodation->is_include_breakfast)
                                        <span style="color: #10b981; font-size: 11px; margin-left: 6px;">✓ Breakfast</span>
                                    @endif
                                </span>
                                <span class="hotel-cost">
                                    {{ $accommodation->nights }}n × {{ $bCurrency }}{{ number_format($accommodation->price, 2) }} 
                                    = {{ $bCurrency }}{{ number_format($accommodation->nights * $accommodation->price, 2) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    @else
        <div style="text-align: center; padding: 40px; color: #9ca3af;">
            Offer not found.
        </div>
    @endif
</div>
