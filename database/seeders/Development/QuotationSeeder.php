<?php

namespace Database\Seeders\Development;

use App\Enums\ActivityCategoryTypeEnum;
use App\Enums\InquiryTypeEnum;
use App\Enums\MealPartEnum;
use App\Enums\QuotationTypeEnum;
use App\Enums\TicketClassEnum;
use App\Enums\TransportModeEnum;
use App\Models\Base\ActivityCategory;
use App\Models\Base\Attraction;
use App\Models\Base\City;
use App\Models\Base\SubAttraction;
use App\Models\Contact;
use App\Models\Tenant;
use App\Models\Tenants\Experience;
use App\Models\Tenants\Inquiry;
use App\Models\Tenants\MealType;
use App\Models\Tenants\Quotation;
use App\Models\Tenants\QuotationItinerary;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuotationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::find('balopar');
        
        if (!$tenant) {
            $this->command->warn('Tenant "balopar" not found. Please run TenantSeeder first.');
            return;
        }

        // Get first contact (John Smith)
        $contact = Contact::where('tenant_id', $tenant->id)
            ->where('email', 'john.smith@example.com')
            ->first();

        if (!$contact) {
            $this->command->warn('Contact not found. Please run ContactSeeder first.');
            return;
        }

        // Get USD currency
        $usd = \App\Models\Base\Currency::where('code', 'USD')->first();
        
        if (!$usd) {
            $this->command->warn('USD currency not found. Please run CurrencySeeder first.');
            return;
        }

        // Get cities
        $beijing = City::where('code', 'BJ')->first();
        $shanghai = City::where('code', 'SH')->first();
        $shenzhen = City::where('code', 'SZ')->first();

        // Get meal types
        $buffetBreakfast = MealType::where('tenant_id', $tenant->id)->where('name->en', 'Buffet Breakfast')->first();
        $chineseStandard = MealType::where('tenant_id', $tenant->id)->where('name->en', 'Chinese Standard')->first();
        $turkishStandard = MealType::where('tenant_id', $tenant->id)->where('name->en', 'Turkish Standard')->first();

        // Get accommodations
        $beijingLuxury = \App\Models\Base\Accommodation::where('name->en', 'Beijing Luxury Palace')->first();
        $shanghaiRoyal = \App\Models\Base\Accommodation::where('name->en', 'Shanghai Royal Suites')->first();
        $shenzhenPearl = \App\Models\Base\Accommodation::where('name->en', 'Shenzhen Pearl Tower')->first();

        // Get attractions
        $forbiddenCity = Attraction::where('name->en', 'Forbidden City')->first();
        $bund = Attraction::where('name->en', 'The Bund')->first();
        $westLake = Attraction::where('name->en', 'West Lake')->first();
        $windowOfWorld = Attraction::where('name->en', 'Window of the World')->first();
        $splcMuseum = Attraction::where('name->en', 'SPLC Museum')->first();

        // Get sub-attractions
        $hallOfSupremeHarmony = $forbiddenCity ? SubAttraction::where('attraction_id', $forbiddenCity->id)->where('name->en', 'Hall of Supreme Harmony')->first() : null;
        $imperialGarden = $forbiddenCity ? SubAttraction::where('attraction_id', $forbiddenCity->id)->where('name->en', 'Imperial Garden')->first() : null;
        $leisureLake = $windowOfWorld ? SubAttraction::where('attraction_id', $windowOfWorld->id)->where('name->en', 'Leisure Lake')->first() : null;

        // Get experiences
        $hutongTour = Experience::where('tenant_id', $tenant->id)->where('city_id', $beijing->id)->where('name->en', 'like', '%Hutong%')->first();
        $shanghaiNightCruise = Experience::where('tenant_id', $tenant->id)->where('city_id', $shanghai->id)->where('name->en', 'like', '%Night Cruise%')->first();
        $shenzhenTechTour = Experience::where('tenant_id', $tenant->id)->where('city_id', $shenzhen->id)->where('name->en', 'like', '%Tech%')->first();

        // Get activity categories
        $mealCategory = ActivityCategory::where('type', ActivityCategoryTypeEnum::MEAL->value)->first();
        $attractionCategory = ActivityCategory::where('type', ActivityCategoryTypeEnum::ATTRACTION->value)->first();
        $ticketCategory = ActivityCategory::where('type', ActivityCategoryTypeEnum::TICKET->value)->first();
        $experienceCategory = ActivityCategory::where('type', ActivityCategoryTypeEnum::EXPERIENCE->value)->first();

        // Get any user for this tenant
        $adminUser = \App\Models\User::where('tenant_id', $tenant->id)->first();

        if (!$adminUser) {
            $this->command->warn('No user found for tenant "balopar".');
            return;
        }

        // Initialize tenancy context
        tenancy()->initialize($tenant);

        DB::transaction(function () use (
            $tenant,
            $contact,
            $usd,
            $beijing,
            $shanghai,
            $shenzhen,
            $buffetBreakfast,
            $chineseStandard,
            $turkishStandard,
            $beijingLuxury,
            $shanghaiRoyal,
            $shenzhenPearl,
            $forbiddenCity,
            $bund,
            $westLake,
            $windowOfWorld,
            $splcMuseum,
            $hallOfSupremeHarmony,
            $imperialGarden,
            $leisureLake,
            $hutongTour,
            $shanghaiNightCruise,
            $shenzhenTechTour,
            $mealCategory,
            $attractionCategory,
            $ticketCategory,
            $experienceCategory,
            $adminUser
        ) {
            // 1. Create Inquiry
            $inquiry = Inquiry::create([
                'number' => '2500100',
                'title' => 'China Discovery Tour - 5 Days',
                'type' => InquiryTypeEnum::ITINERARY,
                'contact_id' => $contact->id,
                'requested_currency_id' => $usd->id,
                'tenant_id' => $tenant->id,
                'creator_user_id' => $adminUser->id,
            ]);
            
            // Create InquiryItinerary with dates
            $inquiryItinerary = $inquiry->inquiryItinerary()->create([
                'date_type' => \App\Enums\InquiryDateTypeEnum::FIXED_DATE,
                'from_date' => now()->addDay(), // Tomorrow
                'to_date' => now()->addWeek(), // One week later
                'tenant_id' => $tenant->id,
            ]);

            // 2. Create Quotation
            $quotation = Quotation::create([
                'number' => '1000100',
                'type' => QuotationTypeEnum::ITINERARY,
                'inquiry_id' => $inquiry->id,
                'currency_id' => $usd->id,
                'exchange_rate' => 7.0000,
                'expire_date' => now()->addWeek(), // One week later
                'tenant_id' => $tenant->id,
                'creator_user_id' => $adminUser->id,
            ]);

            // 3. Create QuotationItinerary
            $quotationItinerary = QuotationItinerary::create([
                'quotation_id' => $quotation->id,
                'is_foreigner_passengers' => true,
                'tenant_id' => $tenant->id,
            ]);

            // 4. Create Itinerary (morphed to QuotationItinerary)
            $itinerary = $quotationItinerary->itinerary()->create([
                'tenant_id' => $tenant->id,
                'travel_mode' => \App\Enums\TravelModeEnum::AIR,
                'is_complete' => false,
                'creator_user_id' => $adminUser->id,
            ]);

            // 5. Create Itinerary Days with Activities
            
            // Day 1: Beijing - Forbidden City + Hutong Tour
            $day1 = $itinerary->days()->create([
                'day_number' => 1,
                'current_city_id' => $beijing->id,
                'accommodation_city_id' => $beijing->id,
                'accommodation_id' => $beijingLuxury->id,
                'accommodation_star_rating' => 5,
                'vehicle_usage_mode' => 'full_day',
                'companion_hire_mode' => 'daily',
                'tenant_id' => $tenant->id,
                'creator_user_id' => $adminUser->id,
            ]);

            // Day 1 Meals
            $this->createMeal($day1, $buffetBreakfast, MealPartEnum::BREAKFAST, $mealCategory, $adminUser);
            $this->createMeal($day1, $chineseStandard, MealPartEnum::LUNCH, $mealCategory, $adminUser);
            $this->createMeal($day1, $chineseStandard, MealPartEnum::DINNER, $mealCategory, $adminUser);

            // Day 1 Attraction (Forbidden City with subs)
            if ($forbiddenCity) {
                $this->createAttraction($day1, $beijing, $forbiddenCity, false, [$hallOfSupremeHarmony, $imperialGarden], $attractionCategory, $adminUser);
            }

            // Day 1 Experience (Hutong Tour)
            if ($hutongTour) {
                $this->createExperience($day1, $beijing, $hutongTour, $experienceCategory, $adminUser);
            }

            // Day 2: Beijing → Shanghai - The Bund + Night Cruise
            $day2 = $itinerary->days()->create([
                'day_number' => 2,
                'current_city_id' => $beijing->id,
                'accommodation_city_id' => $shanghai->id,
                'accommodation_id' => $shanghaiRoyal->id,
                'accommodation_star_rating' => 5,
                'vehicle_usage_mode' => 'full_day',
                'companion_hire_mode' => 'daily',
                'tenant_id' => $tenant->id,
                'creator_user_id' => $adminUser->id,
            ]);

            // Day 2 Meals
            $this->createMeal($day2, $buffetBreakfast, MealPartEnum::BREAKFAST, $mealCategory, $adminUser);
            $this->createMeal($day2, $chineseStandard, MealPartEnum::LUNCH, $mealCategory, $adminUser);
            $this->createMeal($day2, $chineseStandard, MealPartEnum::DINNER, $mealCategory, $adminUser);

            // Day 2 Flight Ticket
            $this->createTicket($day2, $beijing, $shanghai, TransportModeEnum::AIR, TicketClassEnum::ECONOMY, $ticketCategory, $adminUser);

            // Day 2 Attraction (The Bund - outview)
            if ($bund) {
                $this->createAttraction($day2, $shanghai, $bund, true, [], $attractionCategory, $adminUser);
            }

            // Day 2 Experience (Shanghai Night Cruise)
            if ($shanghaiNightCruise) {
                $this->createExperience($day2, $shanghai, $shanghaiNightCruise, $experienceCategory, $adminUser);
            }

            // Day 3: Shanghai - West Lake
            $day3 = $itinerary->days()->create([
                'day_number' => 3,
                'current_city_id' => $shanghai->id,
                'accommodation_city_id' => $shanghai->id,
                'accommodation_id' => $shanghaiRoyal->id,
                'accommodation_star_rating' => 5,
                'vehicle_usage_mode' => 'full_day',
                'companion_hire_mode' => 'daily',
                'tenant_id' => $tenant->id,
                'creator_user_id' => $adminUser->id,
            ]);

            // Day 3 Meals
            $this->createMeal($day3, $buffetBreakfast, MealPartEnum::BREAKFAST, $mealCategory, $adminUser);
            $this->createMeal($day3, $turkishStandard, MealPartEnum::LUNCH, $mealCategory, $adminUser);
            $this->createMeal($day3, $chineseStandard, MealPartEnum::DINNER, $mealCategory, $adminUser);

            // Day 3 Flight Ticket
            $this->createTicket($day3, $shanghai, $shenzhen, TransportModeEnum::AIR, TicketClassEnum::ECONOMY, $ticketCategory, $adminUser);

            // Day 3 Attraction (West Lake)
            if ($westLake) {
                $this->createAttraction($day3, $shanghai, $westLake, false, [], $attractionCategory, $adminUser);
            }

            // Day 3 Experience (West Lake Tour)
            if ($shenzhenTechTour) {
                $this->createExperience($day3, $shanghai, $shenzhenTechTour, $experienceCategory, $adminUser);
            }

            // Day 4: Shanghai → Shenzhen - Window of the World
            $day4 = $itinerary->days()->create([
                'day_number' => 4,
                'current_city_id' => $shanghai->id,
                'accommodation_city_id' => $shenzhen->id,
                'accommodation_id' => $shenzhenPearl->id,
                'accommodation_star_rating' => 5,
                'vehicle_usage_mode' => 'full_day',
                'companion_hire_mode' => 'daily',
                'tenant_id' => $tenant->id,
                'creator_user_id' => $adminUser->id,
            ]);

            // Day 4 Meals
            $this->createMeal($day4, $buffetBreakfast, MealPartEnum::BREAKFAST, $mealCategory, $adminUser);
            $this->createMeal($day4, $turkishStandard, MealPartEnum::LUNCH, $mealCategory, $adminUser);
            $this->createMeal($day4, $turkishStandard, MealPartEnum::DINNER, $mealCategory, $adminUser);

            // Day 4 Attraction (Window of the World with sub)
            if ($windowOfWorld) {
                $this->createAttraction($day4, $shenzhen, $windowOfWorld, false, [$leisureLake], $attractionCategory, $adminUser);
            }

            // Day 5: Shenzhen - SPLC Museum + Departure
            $day5 = $itinerary->days()->create([
                'day_number' => 5,
                'current_city_id' => $shenzhen->id,
                'accommodation_city_id' => null,
                'accommodation_id' => null,
                'accommodation_star_rating' => null,
                'vehicle_usage_mode' => 'full_day',
                'companion_hire_mode' => 'daily',
                'tenant_id' => $tenant->id,
                'creator_user_id' => $adminUser->id,
            ]);

            // Day 5 Meals
            $this->createMeal($day5, $buffetBreakfast, MealPartEnum::BREAKFAST, $mealCategory, $adminUser);
            $this->createMeal($day5, $turkishStandard, MealPartEnum::LUNCH, $mealCategory, $adminUser);
            $this->createMeal($day5, $turkishStandard, MealPartEnum::DINNER, $mealCategory, $adminUser);

            // Day 5 Attraction (SPLC Museum)
            if ($splcMuseum) {
                $this->createAttraction($day5, $shenzhen, $splcMuseum, false, [], $attractionCategory, $adminUser);
            }

            // Day 5 Experience (Shenzhen Tech Tour)
            if ($shenzhenTechTour) {
                $this->createExperience($day5, $shenzhen, $shenzhenTechTour, $experienceCategory, $adminUser);
            }

            // 6. Generate Breakdown automatically
            $quotationItinerary->generateBreakdownFromItinerary();
        });

        // End tenancy context
        tenancy()->end();

        $this->command->info('✅ Sample Quotation with Inquiry and Itinerary created successfully!');
        $this->command->line('   Inquiry: 2500100');
        $this->command->line('   Quotation: 1000100');
        $this->command->line('   Itinerary: 5 days (Beijing → Shanghai → Shenzhen)');
    }

    private function createMeal($day, $mealType, $mealPart, $mealCategory, $user)
    {
        if (!$mealType) return;

        $activity = $day->activities()->create([
            'city_id' => $day->current_city_id,
            'description' => ucfirst($mealPart->value) . ' meal',
            'activity_category_id' => $mealCategory->id,
            'tenant_id' => $day->tenant_id,
        ]);

        $activity->meal()->create([
            'meal_type_id' => $mealType->id,
            'meal_part' => $mealPart,
            'is_included_with_hotel' => false,
            'tenant_id' => $day->tenant_id,
        ]);
    }

    private function createAttraction($day, $city, $attraction, $isOutview, $subAttractions, $attractionCategory, $user)
    {
        $activity = $day->activities()->create([
            'city_id' => $city->id,
            'description' => 'Visit ' . $attraction->name,
            'activity_category_id' => $attractionCategory->id,
            'tenant_id' => $day->tenant_id,
        ]);

        $attractionActivity = $activity->attraction()->create([
            'attraction_id' => $attraction->id,
            'is_outview' => $isOutview,
            'tenant_id' => $day->tenant_id,
        ]);

        // Add sub-attractions
        foreach ($subAttractions as $subAttraction) {
            if ($subAttraction) {
                $attractionActivity->subAttractions()->create([
                    'sub_attraction_id' => $subAttraction->id,
                    'tenant_id' => $day->tenant_id,
                ]);
            }
        }
    }

    private function createTicket($day, $fromCity, $toCity, $transportMode, $class, $ticketCategory, $user)
    {
        $activity = $day->activities()->create([
            'city_id' => $fromCity->id,
            'description' => 'Flight from ' . $fromCity->name . ' to ' . $toCity->name,
            'activity_category_id' => $ticketCategory->id,
            'tenant_id' => $day->tenant_id,
        ]);

        $activity->ticket()->create([
            'to_city_id' => $toCity->id,
            'transport_mode' => $transportMode,
            'class' => $class,
            'tenant_id' => $day->tenant_id,
        ]);
    }

    private function createExperience($day, $city, $experience, $experienceCategory, $user)
    {
        if (!$experience) return;

        $activity = $day->activities()->create([
            'city_id' => $city->id,
            'description' => 'Experience: ' . $experience->name,
            'activity_category_id' => $experienceCategory->id,
            'tenant_id' => $day->tenant_id,
        ]);

        $activity->experience()->create([
            'experience_id' => $experience->id,
            'tenant_id' => $day->tenant_id,
        ]);
    }
}

