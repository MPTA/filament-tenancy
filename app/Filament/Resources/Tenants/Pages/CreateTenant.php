<?php

namespace App\Filament\Resources\Tenants\Pages;

use App\Filament\Resources\Tenants\TenantResource;
use App\Models\TenantSetting;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // جدا کردن داده‌های settings از داده‌های tenant
        $this->settingsData = $data['settings'] ?? [];
        unset($data['settings']);
        
        // اگر id ست نشده، از name بسازیم
        if (empty($data['id'])) {
            $data['id'] = Str::slug($data['name'], '_');
        }
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // ساخت domain برای tenant
        if ($this->data['domain'] ?? null) {
            $this->record->domains()->create([
                'domain' => $this->data['domain'],
            ]);
        }

        // ساخت tenant settings
        if (!empty($this->settingsData)) {
            TenantSetting::create([
                'tenant_id' => $this->record->id,
                ...$this->settingsData,
            ]);
        }

        // ساخت user برای tenant (فقط در single database mode)
        if (config('filament-tenancy.single_database')) {
            $userData = [
                'name' => $this->record->name,
                'email' => $this->record->email,
                'password' => $this->record->password,
                'tenant_id' => $this->record->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            $userModelClass = config('filament-tenancy.tenant_user_model', \App\Models\User::class);
            
            // Initialize tenant context برای tenant_id مناسب
            tenancy()->initialize($this->record);
            
            $userModelClass::updateOrCreate(
                [
                    'email' => $userData['email'],
                    'tenant_id' => $this->record->id,
                ],
                $userData
            );
            
            // End tenant context
            tenancy()->end();
        }
    }

    private array $settingsData = [];
}
