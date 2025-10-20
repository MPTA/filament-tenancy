<?php

namespace Database\Seeders\Development;

use App\Models\Base\VehicleCategory;
use App\Models\Tenant;
use App\Models\Tenants\VehicleType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get required data
        $tenant = Tenant::find('balopar');

        if (!$tenant) {
            $this->command->warn('Tenant "balopar" not found. Please run TenantSeeder first.');
            return;
        }

        // Get vehicle categories
        $suv = VehicleCategory::where('slug', 'suv')->first();
        $minibus = VehicleCategory::where('slug', 'minibus')->first();
        $bus = VehicleCategory::where('slug', 'bus')->first();

        if (!$suv || !$minibus || !$bus) {
            $this->command->warn('Required vehicle categories not found. Please run VehicleCategorySeeder first.');
            return;
        }

        // Initialize tenant context
        tenancy()->initialize($tenant);

        $vehicleTypes = [
            [
                'name' => [
                    'en' => 'SUV',
                    'fa' => 'شاسی بلند',
                    'zh_CN' => 'SUV越野车',
                ],
                'slug' => 'balopar-suv',
                'vehicle_category_id' => $suv->id,
                'capacity_from' => 4,
                'capacity_to' => 6,
                'per_day_price' => 500.00,
                'half_day_price' => 300.00,
                'max_hour_per_day' => 10,
                'max_hour_half_day' => 5,
                'extra_hour_price' => 60.00,
                'airport_transfer_price' => 200.00,
                'is_vip' => false,
            ],
            [
                'name' => [
                    'en' => 'Mini Bus',
                    'fa' => 'مینی بوس',
                    'zh_CN' => '小巴士',
                ],
                'slug' => 'balopar-minibus',
                'vehicle_category_id' => $minibus->id,
                'capacity_from' => 10,
                'capacity_to' => 18,
                'per_day_price' => 800.00,
                'half_day_price' => 500.00,
                'max_hour_per_day' => 10,
                'max_hour_half_day' => 5,
                'extra_hour_price' => 80.00,
                'airport_transfer_price' => 300.00,
                'is_vip' => false,
            ],
            [
                'name' => [
                    'en' => 'Bus',
                    'fa' => 'اتوبوس',
                    'zh_CN' => '大巴士',
                ],
                'slug' => 'balopar-bus',
                'vehicle_category_id' => $bus->id,
                'capacity_from' => 30,
                'capacity_to' => 50,
                'per_day_price' => 1200.00,
                'half_day_price' => 700.00,
                'max_hour_per_day' => 10,
                'max_hour_half_day' => 5,
                'extra_hour_price' => 100.00,
                'airport_transfer_price' => 400.00,
                'is_vip' => false,
            ],
        ];

        foreach ($vehicleTypes as $vehicleTypeData) {
            if (!VehicleType::query()->where('slug', $vehicleTypeData['slug'])->exists()) {
                VehicleType::create([
                    'tenant_id' => $tenant->id,
                    ...$vehicleTypeData,
                ]);
            }
        }

        // End tenant context
        tenancy()->end();

        $this->command->info('✅ Vehicle types for tenant "balopar" created successfully!');
        $this->command->line('   1. SUV (4-6 pax) - Per Day: ¥500 | Half Day: ¥300');
        $this->command->line('   2. Mini Bus (10-18 pax) - Per Day: ¥800 | Half Day: ¥500');
        $this->command->line('   3. Bus (30-50 pax) - Per Day: ¥1200 | Half Day: ¥700');
    }
}
