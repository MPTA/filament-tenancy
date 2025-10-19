<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * این seeder دیتاهای production و essential را اجرا می‌کند.
     * برای دیتای تستی، از DevelopmentSeeder استفاده کنید.
     * 
     * ترتیب اجرا:
     * 1. Production Data (Currencies, Countries, Provinces, Cities)
     * 2. Essential Data (Languages, Categories, Admin User)
     */
    public function run(): void
    {
        // 1. Production Data (جغرافیایی)
        $this->call([
            ProductionSeeder::class,
        ]);

        // 2. Essential Data (دیتاهای ضروری)
        $this->command->info('🌱 Seeding essential data...');
        
        $this->call([
            LanguageSeeder::class,
            ActivityCategorySeeder::class,
            MealCategorySeeder::class,
            CompanionCategorySeeder::class,
            RoomCategorySeeder::class,
            VehicleCategorySeeder::class,
            AdminUserSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('✅ All production and essential data seeded successfully!');
        
        // نمایش راهنما برای seedهای development
        if (app()->environment(['local', 'development'])) {
            $this->command->newLine();
            $this->command->line('💡 <fg=yellow>Tip:</> Run development seeders with:');
            $this->command->line('   <fg=green>php artisan db:seed --class=DevelopmentSeeder</>');
        }
    }
}
