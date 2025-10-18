<?php

namespace App\Filament\App\Widgets;

use App\Models\Tenants\QuotationItinerary;
use App\Models\Tenants\Inquiry;
use App\Models\Tenants\TenantContact;
use App\Enums\ContactTypeEnum;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CombinedStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // Quotations stats
        $quotationsTotal = QuotationItinerary::count();
        $quotationsThisMonth = QuotationItinerary::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $quotationsLastMonth = QuotationItinerary::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        
        $quotationsPercentageChange = $quotationsLastMonth > 0 
            ? round((($quotationsThisMonth - $quotationsLastMonth) / $quotationsLastMonth) * 100, 1)
            : ($quotationsThisMonth > 0 ? 100 : 0);
        
        $quotationsDescription = $quotationsPercentageChange > 0 
            ? "{$quotationsPercentageChange}% " . __('common-fields.increase_from_last_month')
            : ($quotationsPercentageChange < 0 
                ? abs($quotationsPercentageChange) . "% " . __('common-fields.decrease_from_last_month')
                : __('common-fields.no_change_from_last_month'));

        // Inquiries stats
        $inquiriesTotal = Inquiry::count();
        $inquiriesThisMonth = Inquiry::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $inquiriesLastMonth = Inquiry::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        
        $inquiriesPercentageChange = $inquiriesLastMonth > 0 
            ? round((($inquiriesThisMonth - $inquiriesLastMonth) / $inquiriesLastMonth) * 100, 1)
            : ($inquiriesThisMonth > 0 ? 100 : 0);
        
        $inquiriesDescription = $inquiriesPercentageChange > 0 
            ? "{$inquiriesPercentageChange}% " . __('common-fields.increase_from_last_month')
            : ($inquiriesPercentageChange < 0 
                ? abs($inquiriesPercentageChange) . "% " . __('common-fields.decrease_from_last_month')
                : __('common-fields.no_change_from_last_month'));

        // Contacts stats (only customer and lead types from current tenant)
        $contactsTotal = TenantContact::whereIn('type', [ContactTypeEnum::CUSTOMER, ContactTypeEnum::LEAD])->count();
        $contactsThisMonth = TenantContact::whereIn('type', [ContactTypeEnum::CUSTOMER, ContactTypeEnum::LEAD])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $contactsLastMonth = TenantContact::whereIn('type', [ContactTypeEnum::CUSTOMER, ContactTypeEnum::LEAD])
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        
        $contactsPercentageChange = $contactsLastMonth > 0 
            ? round((($contactsThisMonth - $contactsLastMonth) / $contactsLastMonth) * 100, 1)
            : ($contactsThisMonth > 0 ? 100 : 0);
        
        $contactsDescription = $contactsPercentageChange > 0 
            ? "{$contactsPercentageChange}% " . __('common-fields.increase_from_last_month')
            : ($contactsPercentageChange < 0 
                ? abs($contactsPercentageChange) . "% " . __('common-fields.decrease_from_last_month')
                : __('common-fields.no_change_from_last_month'));

        return [
            Stat::make(__('app-quotation-itineraries.widgets.total_quotations'), $quotationsTotal)
                ->description($quotationsDescription)
                ->descriptionIcon($quotationsPercentageChange > 0 ? 'heroicon-m-arrow-trending-up' : ($quotationsPercentageChange < 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-minus'))
                ->color($quotationsPercentageChange > 0 ? 'success' : ($quotationsPercentageChange < 0 ? 'danger' : 'gray'))
                ->chart(
                    QuotationItinerary::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                        ->where('created_at', '>=', now()->subDays(7))
                        ->groupBy('date')
                        ->orderBy('date')
                        ->pluck('count')
                        ->toArray()
                ),
                
            Stat::make(__('app-inquiry-itineraries.widgets.total_inquiries'), $inquiriesTotal)
                ->description($inquiriesDescription)
                ->descriptionIcon($inquiriesPercentageChange > 0 ? 'heroicon-m-arrow-trending-up' : ($inquiriesPercentageChange < 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-minus'))
                ->color($inquiriesPercentageChange > 0 ? 'success' : ($inquiriesPercentageChange < 0 ? 'danger' : 'gray'))
                ->chart(
                    Inquiry::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                        ->where('created_at', '>=', now()->subDays(7))
                        ->groupBy('date')
                        ->orderBy('date')
                        ->pluck('count')
                        ->toArray()
                ),
                
            Stat::make(__('app-contacts.widgets.total_contacts'), $contactsTotal)
                ->description($contactsDescription)
                ->descriptionIcon($contactsPercentageChange > 0 ? 'heroicon-m-arrow-trending-up' : ($contactsPercentageChange < 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-minus'))
                ->color($contactsPercentageChange > 0 ? 'success' : ($contactsPercentageChange < 0 ? 'danger' : 'gray'))
                ->chart(
                    $this->getContactsChartData()
                ),
        ];
    }

    private function getContactsChartData(): array
    {
        $chartData = [];
        
        // Generate data for the last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = TenantContact::whereIn('type', [ContactTypeEnum::CUSTOMER, ContactTypeEnum::LEAD])
                ->whereDate('created_at', $date)
                ->count();
            $chartData[] = $count;
        }
        
        return $chartData;
    }
}

