# Database Seeders

این پروژه شامل seedهای مختلفی برای populate کردن دیتابیس است.

## 🚀 راه‌اندازی سریع

### بعد از `migrate:fresh` فقط **دو دستور**:

```bash
# 1️⃣ Production + Essential Data
php artisan db:seed

# 2️⃣ Development Data (تنانت و دیتای تستی)
php artisan db:seed --class=DevelopmentSeeder
```

یا یک خط:
```bash
php artisan migrate:fresh --seed && php artisan db:seed --class=DevelopmentSeeder
```

---

## 📁 ساختار Seeders

### 1. **ProductionSeeder** (اجرا می‌شود توسط DatabaseSeeder)
دیتاهای جغرافیایی و ارزها (برای production و development):
- ✅ `CurrencySeeder` - 155 ارز (4 فعال: USD, CNY, EUR, GBP)
- ✅ `CountrySeeder` - 250 کشور با تمام فیلدها و ترجمه‌ها
- ✅ `ProvinceSeeder` - 34 استان چین
- ✅ `CitySeeder` - 1272 شهر چین (178 با کد IATA، 1094 بدون کد)

### 2. **DatabaseSeeder** (پیش‌فرض `php artisan db:seed`)
شامل ProductionSeeder + Essential Data:
- ✅ `LanguageSeeder` - زبان‌ها
- ✅ `ActivityCategorySeeder` - دسته‌بندی‌های فعالیت
- ✅ `MealCategorySeeder` - دسته‌بندی‌های غذا
- ✅ `CompanionCategorySeeder` - دسته‌بندی‌های همراه
- ✅ `RoomCategorySeeder` - دسته‌بندی‌های اتاق
- ✅ `VehicleCategorySeeder` - دسته‌بندی‌های خودرو
- ✅ `AdminUserSeeder` - کاربر ادمین (masihparvaz@gmail.com)

### 3. **DevelopmentSeeder** (فقط برای محیط توسعه)
دیتای تستی برای تنانت:
- ✅ `TenantSeeder` - تنانت balopar
- ✅ `ContactSeeder` - 4 مخاطب (2 Lead + 2 Customer)
- ✅ `CompanionTypeSeeder` - 2 نوع همراه (Guide + Translator)
- ✅ `MealTypeSeeder` - 4 نوع غذا
- ✅ `VehicleTypeSeeder` - 3 نوع خودرو
- ✅ `ExperienceSeeder` - 6 تجربه در 3 شهر
- ✅ `AccommodationSeeder` - اقامتگاه‌ها
- ✅ `AttractionSeeder` - جاذبه‌ها
- ✅ `QuotationSeeder` - یک quotation نمونه

---

## 🔄 اجرای جداگانه Seeders

### Production Data (به ترتیب):
```bash
php artisan db:seed --class=CurrencySeeder
php artisan db:seed --class=CountrySeeder
php artisan db:seed --class=ProvinceSeeder
php artisan db:seed --class=CitySeeder
```

یا همه با یک دستور:
```bash
php artisan db:seed --class=ProductionSeeder
```

### Essential Data:
این seedها توسط `DatabaseSeeder` اجرا می‌شوند، اما می‌توانید جداگانه هم اجرا کنید.

### Development Data:
```bash
php artisan db:seed --class=DevelopmentSeeder
```

---

## 📊 آمار دیتاها

### Production Data:
- **155 ارز** (4 فعال)
- **250 کشور** با ترجمه‌های چندزبانه (شامل zh_CN)
- **34 استان** چین
- **1272 شهر** چین:
  - 178 شهر با کد IATA (مثل BJS، SHA، SZX)
  - 1094 شهر بدون کد IATA (استفاده از نام شهر)

### Development Tenant (balopar):
- **Domain**: balopar.newtripmaker.test
- **Email**: balopar@gmail.com
- **Password**: password
- **Currency**: CNY (Yuan)
- **Location**: Beijing, China

---

## 💡 نکات مهم

### شهرهای کلیدی برای Development:
```php
// این شهرها در development seeders استفاده می‌شوند:
City::where('code', 'BJS')->first(); // Beijing
City::where('code', 'SHA')->first(); // Shanghai
City::where('code', 'SZX')->first(); // Shenzhen
```

### شهرهایی که کد IATA ندارند:
```php
// لیست شهرهای بدون کد IATA (برای آپدیت بعدی):
City::where('has_code', false)->get();
```

### ترجمه‌های چینی:
همه دیتاهای جغرافیایی شامل ترجمه چینی هستند با فرمت `zh_CN`:
```php
$city->getTranslation('name', 'zh_CN'); // 北京
```

---

## 🎯 Workflow توصیه شده

### Development:
```bash
# 1. Reset database
php artisan migrate:fresh

# 2. Seed production + essential data
php artisan db:seed

# 3. Seed development data
php artisan db:seed --class=DevelopmentSeeder
```

### Production:
```bash
# فقط دیتاهای production و essential
php artisan migrate
php artisan db:seed
```

---

## 📝 اطلاعات تکمیلی

برای اطلاعات بیشتر درباره Development Seeders، فایل `Development/README.md` را مطالعه کنید.

