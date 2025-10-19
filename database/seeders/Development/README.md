# Development Seeders 🔧

این پوشه شامل **seedهای اختصاصی محیط Development** است که دیتای تستی و نمونه برای توسعه را فراهم می‌کند.

## ⚠️ هشدار مهم

**این seedها نباید در production اجرا شوند!**

## 📦 Seedهای موجود

| Seeder | توضیحات | دیتا |
|--------|---------|------|
| `TenantSeeder` | Tenant تستی | balopar (CNY, English, Beijing) |
| `CompanionTypeSeeder` | انواع همراهان برای balopar | English Guide, English Translator |
| `MealTypeSeeder` | انواع غذا برای balopar | Chinese Standard, Hotpot, Buffet Breakfast, Turkish Standard |
| `VehicleTypeSeeder` | انواع خودرو برای balopar | SUV, Mini Bus, Bus |
| `ExperienceSeeder` | تجربه‌ها برای balopar | 6 تجربه (2 در هر شهر): Peking Duck, Kung Fu, River Cruise, Acrobatic, Tea, Calligraphy |
| `AccommodationSeeder` | هتل‌ها و قیمت‌های تستی | 6 هتل (2 در هر شهر: 4★ و 5★) با قیمت Single/Twin |
| `AttractionSeeder` | جاذبه‌های گردشگری | 9 جاذبه (3 در هر شهر) با قیمت Local/Foreigner |

## 🚀 نحوه استفاده

### روش پیشنهادی (فقط 2 دستور):
```bash
# 1️⃣ Production + Essential Data
php artisan db:seed

# 2️⃣ Development Data
php artisan db:seed --class=DevelopmentSeeder
```

### همراه با migrate:fresh:
```bash
php artisan migrate:fresh --seed && php artisan db:seed --class=DevelopmentSeeder
```

### اجرای یک seeder خاص:
```bash
php artisan db:seed --class=Database\\Seeders\\Development\\TenantSeeder
```

### پیش‌نیاز: seedهای Production
`DatabaseSeeder` به صورت خودکار seedهای production را اجرا می‌کند:
- ProductionSeeder (Currencies, Countries, Provinces, Cities)
- Essential Data (Languages, Categories, Admin User)

اگر نیاز به اجرای جداگانه داشتید:
```bash
php artisan db:seed --class=ProductionSeeder
```

## 🔒 امنیت

`DevelopmentSeeder` به صورت خودکار محیط را چک می‌کند و فقط در محیط‌های `local` یا `development` اجرا می‌شود.

## ➕ اضافه کردن Seeder جدید

1. Seeder جدید را در این پوشه بسازید
2. Namespace را به `Database\Seeders\Development` تغییر دهید
3. آن را به `DevelopmentSeeder.php` اضافه کنید

## 📝 نکات

- این seedها با `updateOrCreate` کار می‌کنند و می‌توان چندین بار اجرا کرد
- وابستگی‌ها به صورت خودکار چک می‌شوند
- پیام‌های خطا و هشدار برای debug فراهم شده است

