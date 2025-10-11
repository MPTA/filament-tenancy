<?php

namespace Database\Seeders\Development;

use App\Enums\ContactTypeEnum;
use App\Enums\GenderEnum;
use App\Models\Base\Country;
use App\Models\Contact;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get tenant and country
        $tenant = Tenant::find('balopar');
        $china = Country::where('code', 'CN')->first();

        if (!$tenant || !$china) {
            $this->command->warn('Required data not found. Please run other seeders first.');
            return;
        }

        // 2 Leads
        $leads = [
            [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'john.smith@example.com',
                'phone' => '+86-10-12345678',
                'mobile' => '+86-138-0000-1111',
                'company' => 'ABC Travel Agency',
                'gender' => GenderEnum::MALE,
                'postal_address' => 'Beijing, Chaoyang District',
                'type' => ContactTypeEnum::LEAD,
                'country_id' => $china->id,
                'tenant_id' => $tenant->id,
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.johnson@example.com',
                'phone' => '+86-21-87654321',
                'mobile' => '+86-139-0000-2222',
                'company' => 'Global Tours Ltd',
                'gender' => GenderEnum::FEMALE,
                'postal_address' => 'Shanghai, Pudong District',
                'type' => ContactTypeEnum::LEAD,
                'country_id' => $china->id,
                'tenant_id' => $tenant->id,
            ],
        ];

        // 2 Customers
        $customers = [
            [
                'first_name' => 'Michael',
                'last_name' => 'Chen',
                'email' => 'michael.chen@example.com',
                'phone' => '+86-755-11223344',
                'mobile' => '+86-137-0000-3333',
                'company' => 'Tech Solutions Inc',
                'gender' => GenderEnum::MALE,
                'postal_address' => 'Shenzhen, Nanshan District',
                'type' => ContactTypeEnum::CUSTOMER,
                'country_id' => $china->id,
                'tenant_id' => $tenant->id,
            ],
            [
                'first_name' => 'Emma',
                'last_name' => 'Wang',
                'email' => 'emma.wang@example.com',
                'phone' => '+86-10-99887766',
                'mobile' => '+86-136-0000-4444',
                'company' => 'Business Ventures Co',
                'gender' => GenderEnum::FEMALE,
                'postal_address' => 'Beijing, Haidian District',
                'type' => ContactTypeEnum::CUSTOMER,
                'country_id' => $china->id,
                'tenant_id' => $tenant->id,
            ],
        ];

        // Create contacts
        foreach (array_merge($leads, $customers) as $contactData) {
            Contact::updateOrCreate(
                ['email' => $contactData['email'], 'tenant_id' => $tenant->id],
                $contactData
            );
        }

        $this->command->info('✅ Contacts for tenant "balopar" created successfully!');
        $this->command->line('   2 Leads: John Smith, Sarah Johnson');
        $this->command->line('   2 Customers: Michael Chen, Emma Wang');
    }
}

