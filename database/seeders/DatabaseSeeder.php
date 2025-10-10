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
     * این seeders برای همه محیط‌ها (production و development) اجرا می‌شوند.
     * برای دیتای تستی، از DevelopmentSeeder استفاده کنید.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding essential data...');
        
        // Seedهای ضروری برای همه محیط‌ها
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
        $this->command->info('✅ Essential data seeded successfully!');
        
        // نمایش راهنما برای seedهای development
        if (app()->environment(['local', 'development'])) {
            $this->command->newLine();
            $this->command->line('💡 <fg=yellow>Tip:</> Run development seeders with:');
            $this->command->line('   <fg=green>php artisan db:seed --class=DevelopmentSeeder</>');
        }
    }
}
