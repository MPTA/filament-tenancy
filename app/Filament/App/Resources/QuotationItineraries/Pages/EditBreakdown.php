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
            Actions\Action::make('view_itinerary')
                ->label('View Itinerary')
                ->url(fn() => route('filament.app.resources.quotation-itineraries.view', $this->record))
                ->icon('heroicon-o-eye'),
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
                $data['rooms'] = $accommodation->rooms->toArray();
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
            $this->record->breakdown->update($data);
            Notification::make()
                ->title('Breakdown updated successfully!')
                ->success()
                ->send();
        }
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
