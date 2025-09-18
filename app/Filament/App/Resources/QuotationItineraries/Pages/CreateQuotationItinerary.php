<?php

namespace App\Filament\App\Resources\QuotationItineraries\Pages;

use App\Enums\InquiryTypeEnum;
use App\Enums\QuotationTypeEnum;
use App\Filament\App\Resources\QuotationItineraries\QuotationItineraryResource;
use App\Filament\App\Resources\QuotationItineraries\Schemas\ComprehensiveQuotationItineraryForm;
use App\Models\Tenants\Inquiry;
use App\Models\Tenants\InquiryItinerary;
use App\Models\Tenants\Quotation;
use App\Models\Tenants\QuotationItinerary;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateQuotationItinerary extends CreateRecord
{
    protected static string $resource = QuotationItineraryResource::class;

    public function form(Schema $schema): Schema
    {
        return ComprehensiveQuotationItineraryForm::configure($schema);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Return the data as-is since we'll handle creation in the create method
        return $data;
    }

    protected function getFormValidationRules(): array
    {
        return [
            'inquiry_title' => 'required|string|max:255',
            'inquiry_description' => 'nullable|string',
            'inquiry_reference' => 'nullable|string|max:255',
            'inquiry_contact_id' => 'required|exists:contacts,id',
            'inquiry_requested_currency_id' => 'required|exists:currencies,id',
            'inquiry_date_type' => 'required|string',
            'accommodation_stars' => 'nullable|integer|min:1|max:5',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'quotation_type' => 'required|string',
            'exchange_rate' => 'required|numeric|min:0',
            'expire_date' => 'required|date|after:today',
            'quotation_description' => 'nullable|string',
            'internal_note' => 'nullable|string',
        ];
    }

    protected function handleRecordCreation(array $data): QuotationItinerary
    {
        // Start a database transaction
        return DB::transaction(function () use ($data) {
            // 1. Create Inquiry
            $inquiry = Inquiry::create([
                'type' => InquiryTypeEnum::ITINERARY,
                'title' => $data['inquiry_title'],
                'description' => $data['inquiry_description'] ?? '',
                'reference' => $data['inquiry_reference'] ?? null,
                'contact_id' => $data['inquiry_contact_id'] ?? null,
                'requested_currency_id' => $data['inquiry_requested_currency_id'],
                'published_at' => now(),
                'creator_user_id' => Auth::id(),
            ]);

            // 2. Create InquiryItinerary
            $inquiryItinerary = InquiryItinerary::create([
                'inquiry_id' => $inquiry->id,
                'date_type' => $data['inquiry_date_type'],
                'from_date' => $data['from_date'] ?? null,
                'to_date' => $data['to_date'] ?? null,
                'accommodation_stars' => $data['accommodation_stars'] ?? null,
            ]);

            // 3. Create Quotation
            $quotation = Quotation::create([
                'inquiry_id' => $inquiry->id,
                'type' => QuotationTypeEnum::ITINERARY,
                'currency_id' => $data['inquiry_requested_currency_id'], // Use inquiry currency
                'exchange_rate' => $data['exchange_rate'],
                'description' => $data['quotation_description'] ?? '',
                'internal_note' => $data['internal_note'] ?? '',
                'expire_date' => $data['expire_date'],
                'creator_user_id' => Auth::id(),
            ]);

            // 4. Create QuotationItinerary
            $quotationItinerary = QuotationItinerary::create([
                'quotation_id' => $quotation->id,
            ]);

            return $quotationItinerary;
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
