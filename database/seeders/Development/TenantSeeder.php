<?php

namespace Database\Seeders\Development;

use App\Models\Base\City;
use App\Models\Base\Country;
use App\Models\Base\Currency;
use App\Models\Base\Language;
use App\Models\Tenant;
use App\Models\Tenants\TenantUser;
use App\Models\TenantSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get required data
        $china = Country::where('code', 'CN')->first();
        $beijing = City::where('code', 'BJ')->first();
        $cny = Currency::where('code', 'CNY')->first();
        $english = Language::where('code', 'en')->first();

        if (!$china || !$beijing || !$cny || !$english) {
            $this->command->warn('Required data not found. Please run other seeders first.');
            return;
        }

        // بررسی اگر tenant از قبل وجود دارد
        if (Tenant::find('balopar')) {
            $this->command->warn('Tenant "balopar" already exists. Skipping...');
            return;
        }

        // ایجاد Tenant
        $tenant = Tenant::create([
            'id' => 'balopar',
            'name' => 'balopar',
            'email' => 'balopar@gmail.com',
            'phone' => null,
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        // ایجاد Domain
        $tenant->domains()->create([
            'domain' => 'balopar',
        ]);

        // ایجاد Tenant Settings
        TenantSetting::create([
            'tenant_id' => $tenant->id,
            'currency_id' => $cny->id,
            'language_id' => $english->id,
            'country_id' => $china->id,
            'city_id' => $beijing->id,
            'company_name' => 'Balopar Travel Agency',
            'company_local_name' => 'بالوپار',
        ]);

        // ایجاد User برای Tenant (در single database mode)
        if (config('filament-tenancy.single_database')) {
            // Initialize tenant context
            tenancy()->initialize($tenant);

            TenantUser::create([
                'name' => 'balopar',
                'email' => 'balopar@gmail.com',
                'password' => Hash::make('password'),
                'tenant_id' => $tenant->id,
                'email_verified_at' => now(),
            ]);

            // End tenant context
            tenancy()->end();
        }

        $this->command->info('✅ Tenant "balopar" created successfully!');
        $this->command->line('   Domain: balopar.' . config('filament-tenancy.central_domain'));
        $this->command->line('   Email: balopar@gmail.com');
        $this->command->line('   Password: password');
        $this->command->line('   Currency: CNY (Yuan)');
        $this->command->line('   Language: English');
        $this->command->line('   Location: Beijing, China');
    }
}
