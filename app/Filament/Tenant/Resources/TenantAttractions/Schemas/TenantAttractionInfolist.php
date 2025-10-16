<?php

namespace App\Filament\Tenant\Resources\TenantAttractions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid as InfolistGrid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\Select as InfolistSelect;
use Filament\Schemas\Schema;
use App\Models\Base\Currency;
use Filament\Actions\Action;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;

class TenantAttractionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('tenant-attractions.sections.basic_information.title'))
                    ->description(__('tenant-attractions.sections.basic_information.description'))
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        InfolistGrid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('common-fields.name'))
                                    ->badge()
                                    ->color('primary'),
                                TextEntry::make('type')
                                    ->label(__('common-fields.type'))
                                    ->badge()
                                    ->color('info'),
                            ]),
                        InfolistGrid::make(2)
                            ->schema([
                                TextEntry::make('city.name')
                                    ->label(__('common-fields.city'))
                                    ->badge()
                                    ->color('success'),
                                TextEntry::make('district.name')
                                    ->label(__('common-fields.district'))
                                    ->badge()
                                    ->color('secondary'),
                            ]),
                        TextEntry::make('description')
                            ->label(__('common-fields.description'))
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make(__('tenant-attractions.sections.tenant_pricing.title'))
                    ->description(__('tenant-attractions.sections.tenant_pricing.description'))
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        TextEntry::make('tenant_local_price')
                            ->label(__('common-fields.local_price'))
                            ->badge()
                            ->color('success')
                            ->getStateUsing(function ($record) {
                                $price = \App\Models\Tenants\TenantAttraction::where('tenant_id', tenant('id'))
                                    ->where('attraction_id', $record->id)
                                    ->first();
                                return $price?->local_price ? number_format((float)$price->local_price, 2) : __('tenant-attractions.messages.not_set');
                            }),
                        TextEntry::make('tenant_foreigner_price')
                            ->label(__('common-fields.foreigner_price'))
                            ->badge()
                            ->color('info')
                            ->getStateUsing(function ($record) {
                                $price = \App\Models\Tenants\TenantAttraction::where('tenant_id', tenant('id'))
                                    ->where('attraction_id', $record->id)
                                    ->first();
                                return $price?->foreigner_price ? number_format((float)$price->foreigner_price, 2) : __('tenant-attractions.messages.not_set');
                            }),
                        TextEntry::make('tenant_additional_content')
                            ->label(__('common-fields.additional_content'))
                            ->getStateUsing(function ($record) {
                                $price = \App\Models\Tenants\TenantAttraction::where('tenant_id', tenant('id'))
                                    ->where('attraction_id', $record->id)
                                    ->first();
                                return $price?->additional_content ? json_encode($price->additional_content) : __('tenant-attractions.messages.no_additional_content');
                            })
                            ->columnSpanFull(),
                        Actions::make([
                            Action::make('edit_pricing')
                                ->label(__('tenant-attractions.actions.edit_pricing'))
                                ->color('primary')
                                ->icon('heroicon-o-pencil')
                                ->modal()
                                ->modalHeading(__('tenant-attractions.modals.edit_pricing_heading'))
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextInput::make('tenant_local_price')
                                                ->label(__('common-fields.local_price'))
                                                ->prefix(function () {
                                                    return tenant()->settings?->country?->currency?->symbol ?? '$';
                                                })
                                                ->rules(['nullable', 'numeric', 'min:0'])
                                                ->inputMode('decimal')
                                                ->default(function ($record) {
                                                    $price = \App\Models\Tenants\TenantAttraction::where('tenant_id', tenant('id'))
                                                        ->where('attraction_id', $record->id)
                                                        ->first();
                                                    return $price?->local_price;
                                                }),
                                            TextInput::make('tenant_foreigner_price')
                                                ->label(__('common-fields.foreigner_price'))
                                                ->prefix(function () {
                                                    return tenant()->settings?->country?->currency?->symbol ?? '$';
                                                })
                                                ->rules(['nullable', 'numeric', 'min:0'])
                                                ->inputMode('decimal')
                                                ->default(function ($record) {
                                                    $price = \App\Models\Tenants\TenantAttraction::where('tenant_id', tenant('id'))
                                                        ->where('attraction_id', $record->id)
                                                        ->first();
                                                    return $price?->foreigner_price;
                                                }),
                                        ]),
                                    Textarea::make('tenant_additional_content')
                                        ->label(__('common-fields.additional_content'))
                                        ->rows(3)
                                        ->default(function ($record) {
                                            $price = \App\Models\Tenants\TenantAttraction::where('tenant_id', tenant('id'))
                                                ->where('attraction_id', $record->id)
                                                ->first();
                                            return $price?->additional_content ? json_encode($price->additional_content) : '';
                                        })
                                        ->columnSpanFull(),
                                    
                                    // Sub Attractions Section
                                    Section::make(__('tenant-attractions.sections.sub_attractions_pricing.title'))
                                        ->description(__('tenant-attractions.sections.sub_attractions_pricing.description'))
                                        ->schema([
                                            Repeater::make('sub_attractions')
                                                ->label(__('tenant-attractions.repeater.sub_attractions'))
                                                ->addable(false)
                                                ->deletable(false)
                                                ->reorderable(false)
                                                
                                                ->hiddenLabel()
                                                ->default(function ($record) {
                                                    if (!$record) return [];
                                                    return $record->subAttractions->map(function ($subAttraction) use ($record) {
                                                        $tenantAttractionId = \App\Models\Tenants\TenantAttraction::where('attraction_id', $record->id)->first()?->id;
                                                        $price = \App\Models\Tenants\TenantSubAttraction::where('sub_attraction_id', $subAttraction->id)
                                                            ->where('tenant_attraction_id', $tenantAttractionId)
                                                            ->first();
                                                        
                                                        return [
                                                            'id' => $subAttraction->id,
                                                            'name' => $subAttraction->name,
                                                            'tenant_attraction_id' => $tenantAttractionId,
                                                            'tenant_local_price' => $price?->local_price,
                                                            'tenant_foreigner_price' => $price?->foreigner_price,
                                                            'tenant_additional_content' => $price?->additional_content ? json_encode($price->additional_content) : '',
                                                        ];
                                                    })->toArray();
                                                })
                                                ->schema([
                                                    Grid::make(3)
                                                        ->schema([
                                                            TextInput::make('name')
                                                                ->label(__('common-fields.name'))
                                                                ->disabled()
                                                                ->dehydrated(false),
                                                            TextInput::make('tenant_local_price')
                                                                ->label(__('common-fields.local_price'))
                                                                ->prefix(function () {
                                                                    return tenant()->settings?->country?->currency?->symbol ?? '$';
                                                                })
                                                                ->rules(['nullable', 'numeric', 'min:0'])
                                                                ->inputMode('decimal'),
                                                            TextInput::make('tenant_foreigner_price')
                                                                ->label(__('common-fields.foreigner_price'))
                                                                ->prefix(function () {
                                                                    return tenant()->settings?->country?->currency?->symbol ?? '$';
                                                                })
                                                                ->rules(['nullable', 'numeric', 'min:0'])
                                                                ->inputMode('decimal'),
                                                        ]),
                                                    Textarea::make('tenant_additional_content')
                                                        ->label(__('common-fields.additional_content'))
                                                        ->rows(2)
                                                        ->columnSpanFull(),
                                                ])
                                                ->columns(1)
                                                
                                                ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                                        ])
                                        ->collapsible(),
                                ])
                                ->action(function (array $data, $record) {
                                    $attractionId = $record->id;
                                    
                                    // Save main attraction pricing
                                    $priceRecord = \App\Models\Tenants\TenantAttraction::firstOrNew([
                                        'attraction_id' => $attractionId,
                                    ]);

                                    $priceRecord->fill([
                                        'local_price' => $data['tenant_local_price'] ?? null,
                                        'foreigner_price' => $data['tenant_foreigner_price'] ?? null,
                                        'additional_content' => isset($data['tenant_additional_content']) && $data['tenant_additional_content'] ? json_decode($data['tenant_additional_content'], true) : null,
                                        'creator_user_id' => \Illuminate\Support\Facades\Auth::id(),
                                    ]);
                                    $priceRecord->save();

                                    // Save sub attractions pricing
                                    if (isset($data['sub_attractions']) && is_array($data['sub_attractions'])) {
                                        foreach ($data['sub_attractions'] as $subAttractionData) {
                                            if (isset($subAttractionData['id']) && isset($subAttractionData['tenant_attraction_id'])) {
                                                $subPriceRecord = \App\Models\Tenants\TenantSubAttraction::firstOrNew([
                                                    'sub_attraction_id' => $subAttractionData['id'],
                                                    'tenant_attraction_id' => $subAttractionData['tenant_attraction_id'],
                                                ]);

                                                $subPriceRecord->fill([
                                                    'local_price' => $subAttractionData['tenant_local_price'] ?? null,
                                                    'foreigner_price' => $subAttractionData['tenant_foreigner_price'] ?? null,
                                                    'additional_content' => isset($subAttractionData['tenant_additional_content']) && $subAttractionData['tenant_additional_content'] ? json_decode($subAttractionData['tenant_additional_content'], true) : null,
                                                    'creator_user_id' => \Illuminate\Support\Facades\Auth::id(),
                                                ]);
                                                $subPriceRecord->save();
                                            }
                                        }
                                    }

                                    \Filament\Notifications\Notification::make()
                                        ->title(__('tenant-attractions.notifications.pricing_updated'))
                                        ->success()
                                        ->send();
                                }),
                        ])
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

            ]);
    }
}
