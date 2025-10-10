<?php

namespace App\Filament\Resources\Tenants\Pages;

use App\Filament\Resources\Tenants\TenantResource;
use App\Models\TenantSetting;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTenant extends EditRecord
{
    protected static string $resource = TenantResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // تبدیل name به tenant_name برای نمایش
        $data['tenant_name'] = $data['name'];
        
        // بارگذاری داده‌های tenant settings
        $settings = TenantSetting::where('tenant_id', $this->record->id)->first();
        
        if ($settings) {
            // بارگذاری contact_name به name برای نمایش در فرم
            $data['name'] = $settings->contact_name;
            
            $data['settings'] = [
                'language_id' => $settings->language_id,
                'country_id' => $settings->country_id,
                'city_id' => $settings->city_id,
                'mobile_number' => $settings->mobile_number,
                'address' => $settings->address,
                'phone_number' => $settings->phone_number,
                'company_name' => $settings->company_name,
                'company_local_name' => $settings->company_local_name,
            ];
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // جدا کردن داده‌های settings از داده‌های tenant
        $this->settingsData = $data['settings'] ?? [];
        
        // ذخیره contact_name برای استفاده در settings
        $this->contactPersonName = $data['name'] ?? null;
        
        // تبدیل tenant_name به name برای ذخیره در جدول tenants
        if (isset($data['tenant_name'])) {
            $data['name'] = $data['tenant_name'];
            unset($data['tenant_name']);
        }
        
        unset($data['settings']);
        
        return $data;
    }

    protected function afterSave(): void
    {
        // ذخیره یا آپدیت tenant settings
        if (!empty($this->settingsData)) {
            TenantSetting::updateOrCreate(
                ['tenant_id' => $this->record->id],
                [
                    'contact_name' => $this->contactPersonName,
                    ...$this->settingsData,
                ]
            );
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    private array $settingsData = [];
    private ?string $contactPersonName = null;
}
