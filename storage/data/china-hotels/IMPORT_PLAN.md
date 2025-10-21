# China Hotels Import Plan

## نمای کلی (Overview)
این سند شامل پلن کامل import کردن هتل‌های چین از فایل‌های JSON به جدول `accommodations` در دیتابیس است.

## ساختار فایل‌های JSON

### فایل‌های موجود (15 فایل):
- china_beijing_hotels.json (245KB, 2381 lines)
- china_shanghai_hotels.json (616KB, 4475 lines)
- china_guangzhou_hotels.json (1.2MB)
- china_hongkong_hotels.json (1.7MB)
- china_shenzhen_hotels.json (333KB, 2963 lines)
- china_nanjing_hotels.json (916KB, 8171 lines)
- china_sanya_hotels.json (475KB, 3488 lines)
- china_macau_hotels.json (308KB, 1724 lines)
- china_chengdu_hotels.json (155KB, 1346 lines)
- china_kunming_hotels.json (141KB, 1304 lines)
- china_guilin_hotels.json (99KB, 926 lines)
- china_suzhou_hotels.json (77KB, 758 lines)
- china_hangzhou_hotels.json (64KB, 611 lines)
- china_xian_hotels.json (27KB, 233 lines)
- china_haikou_hotels.json (25KB, 146 lines)

### نمونه ساختار JSON:
```json
{
  "countyCode": "CN",
  "countyName": "China",
  "cityCode": "119536",
  "cityName": "Haikou",
  "HotelCode": "1011095",
  "HotelName": {
    "zh_CN": "海口铂尔曼酒店",
    "en": "Pullman HAIKOU"
  },
  "HotelRating": "ThreeStar",
  "Address": "21 Xi Yuan Road...",
  "Attractions": "Distances are displayed...",
  "Description": "<p>HeadLine : In Qiongshan...</p>",
  "FaxNumber": "86-898-66108866",
  "HotelFacilities": "elevator Children's club...",
  "Map": "20.074793|110.361404",
  "PhoneNumber": "86-898-66106666",
  "PinCode": "570209",
  "HotelWebsiteUrl": "http://www.hojochina.com",
  "star_rating": 3,
  "latitude": 20.074793,
  "longitude": 110.361404
}
```

## مپینگ فیلدها (Field Mapping)

### JSON → Database (accommodations table)

| JSON Field | Database Field | Type | Notes |
|------------|---------------|------|-------|
| `star_rating` | `star_rating` | integer | مستقیم استفاده می‌شود |
| `latitude` | `latitude` | decimal(10,8) | مستقیم |
| `longitude` | `longitude` | decimal(11,8) | مستقیم |
| `HotelCode` | `external_id` | string | **کلید یکتا** - باید unique باشد |
| `PhoneNumber` | `phone_number` | string | مستقیم |
| `HotelWebsiteUrl` | `website` | string | مستقیم |
| `Address` | `address` | text | مستقیم |
| `HotelName.en` | `name['en']` | jsonb | قابل ترجمه - فقط انگلیسی |
| `HotelName.zh_CN` | `name['zh_CN']` | jsonb | قابل ترجمه - فقط چینی |
| `Description` | `description['en']` | jsonb | قابل ترجمه - فقط انگلیسی |
| `Attractions` | `attractions_data['en']` | jsonb | قابل ترجمه - فقط انگلیسی |
| `HotelFacilities` | `facilities['en']` | jsonb | آرایه - پارس با فاصله |
| - | `content` | jsonb | null - در JSON نیست |
| - | `district_id` | uuid | null - در JSON نیست |
| - | `is_active` | boolean | true - پیش‌فرض |

### فیلدهای محاسباتی (Derived Fields):

| Database Field | Value | How to Calculate |
|---------------|-------|------------------|
| `country_id` | China UUID | `Country::where('code', 'CN')->first()->id` |
| `city_id` | City UUID | `City::where('name->en', $cityName)->first()->id` |
| `external_dataset` | Filename | basename(file, '.json') - e.g., "china_beijing_hotels" |

## قوانین و محدودیت‌ها (Rules & Constraints)

### 1. Unique Constraints
- **HotelCode باید unique باشد** در تمام فایل‌ها
- در صورت تکرار: خطا و توقف seeder

### 2. Required Fields
- `cityName`: باید موجود باشد
- `HotelCode`: باید موجود باشد
- شهر باید در دیتابیس موجود باشد

### 3. Error Handling
- اگر شهر پیدا نشد → Exception + توقف
- اگر HotelCode تکراری بود → Exception + توقف
- اگر فیلد required خالی بود → Exception + توقف

### 4. Data Processing
- **HotelFacilities**: Split by space → فیلتر empty values → ذخیره به عنوان array
- **Translatable fields**: فقط انگلیسی ذخیره می‌شود (`['en' => value]`)
- **Chinese names**: هم انگلیسی و هم چینی ذخیره می‌شود

## Implementation

### Seeder Location
```
database/seeders/Production/ChinaAccommodationSeeder.php
```

### خروجی Seeder:
```
🏨 Seeding China accommodations from JSON files...

📄 Processing: china_beijing_hotels
   ✓ Imported 2381 hotels
📄 Processing: china_shanghai_hotels
   ✓ Imported 4475 hotels
...

✅ Successfully imported 28,526 hotels from 15 files
```

### Integration with ProductionSeeder
```php
$this->call([
    CurrencySeeder::class,
    CountrySeeder::class,
    ProvinceSeeder::class,
    CitySeeder::class,
    Production\ChinaAccommodationSeeder::class, // ← After cities
]);
```

## ترتیب اجرای Seeders

**مهم**: Seeders باید به ترتیب زیر اجرا شوند:

1. ✅ CurrencySeeder
2. ✅ CountrySeeder (نیاز به Currencies)
3. ✅ ProvinceSeeder (نیاز به Countries)
4. ✅ CitySeeder (نیاز به Provinces)
5. ✅ **ChinaAccommodationSeeder** (نیاز به Cities & Country)

```bash
php artisan migrate:fresh --seed
```

## Known Issues

### PinCode Field
⚠️ **مشکل**: فیلد `PinCode` در JSON کد پستی است و برای چند هتل تکراری است.

**مثال**: PinCode "100004" در Beijing برای 5 هتل استفاده شده:
- Hotel Kunlun (HotelCode: 1015491)
- Beijing Hotel (HotelCode: 1016398)
- The Imperial Mansion Beijing Marriott... (HotelCode: 1031701)
- Metropark Lido Hotel Beijing (HotelCode: 1058820)
- China World Hotel, Beijing (HotelCode: 1058826)

**راه‌حل**: از `HotelCode` به عنوان `external_id` استفاده می‌شود زیرا unique است.

## City Matching Strategy

شهرها با استفاده از **نام انگلیسی** match می‌شوند:

```php
$city = City::where('name->en', $hotel['cityName'])->first();
```

با استفاده از **Spatie Translatable** package، جستجو در فیلدهای چند زبانه انجام می‌شود.

### شهرهای کلیدی:
- Beijing (BJS)
- Shanghai (SHA)
- Shenzhen (SZX)
- Guangzhou (CAN)
- Hong Kong (HKG)
- Macau (MFM)

## Testing

### بررسی تعداد هتل‌ها:
```sql
SELECT 
    external_dataset, 
    COUNT(*) as hotel_count,
    COUNT(DISTINCT external_id) as unique_hotels
FROM accommodations 
WHERE country_id = (SELECT id FROM countries WHERE code = 'CN')
GROUP BY external_dataset
ORDER BY hotel_count DESC;
```

### بررسی هتل‌های بدون شهر:
```sql
SELECT * FROM accommodations WHERE city_id IS NULL;
```

### بررسی تکراری‌ها:
```sql
SELECT external_id, COUNT(*) 
FROM accommodations 
GROUP BY external_id 
HAVING COUNT(*) > 1;
```

## Development Seeder Changes

### فایل‌های حذف شده:
- ❌ `database/seeders/Development/AccommodationSeeder.php` (دیگر نیاز نیست)

### فایل‌های به‌روز شده:
1. **DevelopmentSeeder.php**: حذف AccommodationSeeder از call list
2. **QuotationSeeder.php**: استفاده از هتل‌های production به جای development

```php
// قبل:
$beijingLuxury = Accommodation::where('name->en', 'Beijing Luxury Palace')->first();

// بعد:
$beijingLuxury = Accommodation::where('city_id', $beijing->id)
                              ->where('star_rating', '>=', 4)
                              ->first();
```

## Maintenance

### اضافه کردن فایل جدید:
1. فایل JSON را در `storage/data/china-hotels/` قرار دهید
2. Seeder به صورت خودکار تمام فایل‌های `*.json` را پردازش می‌کند
3. مجدداً `php artisan migrate:fresh --seed` را اجرا کنید

### به‌روزرسانی داده‌ها:
فعلاً seeder از `create()` استفاده می‌کند (نه `updateOrCreate`)، بنابراین:
- برای به‌روزرسانی باید database را fresh کنید
- یا seeder را به `updateOrCreate` تغییر دهید

---

**تاریخ ایجاد**: 2025-10-21  
**نسخه**: 1.0  
**وضعیت**: پیاده‌سازی شده و تست شده

