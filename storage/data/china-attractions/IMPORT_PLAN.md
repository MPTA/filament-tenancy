# China Attractions Import Plan

## 📋 Overview

This document describes the complete plan for importing China attractions data from JSON files into the database.

**Date:** October 22, 2025  
**Status:** ✅ Completed  
**Total Imported:** 1,280 attractions + 1,855 sub-attractions from 13 cities

---

## 🗺️ City Mapping

| JSON File | City Name | IATA Code | Match Method | Status |
|-----------|-----------|-----------|--------------|--------|
| `beijing.json` | Beijing | BJS | `City::where('code', 'BJS')` | ✅ |
| `chengdu.json` | Chengdu | CTU | `City::where('code', 'CTU')` | ✅ |
| `guangzhou.json` | Guangzhou | CAN | `City::where('code', 'CAN')` | ✅ |
| `guilin.json` | Guilin | KWL | `City::where('code', 'KWL')` | ✅ |
| `haikou.json` | Haikou | HAK | `City::where('code', 'HAK')` | ✅ |
| `hangzhou.json` | Hangzhou | HGH | `City::where('code', 'HGH')` | ✅ |
| `kunming.json` | Kunming | KMG | `City::where('code', 'KMG')` | ✅ |
| `nanjing.json` | Nanjing | NKG | `City::where('code', 'NKG')` | ✅ |
| `sanya.json` | Sanya | SYX | `City::where('code', 'SYX')` | ✅ |
| `shanghai.json` | Shanghai | SHA | `City::where('code', 'SHA')` | ✅ |
| `shenzhen.json` | Shenzhen | SZX | `City::where('code', 'SZX')` | ✅ |
| `suzhou.json` | Suzhou | SZV | `City::where('code', 'SZV')` | ✅ |
| `xian.json` | Xian | N/A | `City::where('name->en', 'Xian')` | ✅ |

**Note:** Xian doesn't have an IATA code, so we match by English name instead.

---

## 🔄 Field Mapping

### Main Attraction Fields

| JSON Field | Database Field | Type | Translatable | Notes |
|------------|----------------|------|--------------|-------|
| `name.en` | `name->en` | jsonb | ✅ | English name |
| `name.zh_CN` | `name->zh_CN` | jsonb | ✅ | Chinese name |
| `description.en` | `description->en` | jsonb | ✅ | English description |
| `description.zh_CN` | `description->zh_CN` | jsonb | ✅ | Chinese description |
| `type` | `type` | enum | ❌ | AttractionTypeEnum value |
| `link` | `link` | string | ❌ | Qunar.com link |
| `address` | `address` | text | ❌ | Full address |
| `opening_hours.en` | `opening_hours->en` | jsonb | ✅ | Opening hours English |
| `opening_hours.zh_CN` | `opening_hours->zh_CN` | jsonb | ✅ | Opening hours Chinese |
| `image_url` | `image_url` | string | ❌ | Image URL |
| `rating` | `rating` | decimal(3,2) | ❌ | Rating 0-5 |
| `suggested_duration.en` | `suggested_duration->en` | jsonb | ✅ | Suggested duration English |
| `suggested_duration.zh_CN` | `suggested_duration->zh_CN` | jsonb | ✅ | Suggested duration Chinese |
| `suggested_season.en` | `suggested_season->en` | jsonb | ✅ | Best season English |
| `suggested_season.zh_CN` | `suggested_season->zh_CN` | jsonb | ✅ | Best season Chinese |
| `ticket_info.en` | `ticket_info->en` | jsonb | ✅ | Ticket info English |
| `ticket_info.zh_CN` | `ticket_info->zh_CN` | jsonb | ✅ | Ticket info Chinese |
| `tips.en` | `tips->en` | jsonb | ✅ | Tips English |
| `tips.zh_CN` | `tips->zh_CN` | jsonb | ✅ | Tips Chinese |
| `duration_hours.min` | `duration_hours->min` | jsonb | ❌ | Minimum hours |
| `duration_hours.max` | `duration_hours->max` | jsonb | ❌ | Maximum hours |
| `local_price` | `local_price` | decimal(10,2) | ❌ | Price for locals |
| `season_spring` | `season_spring` | boolean | ❌ | Available in spring |
| `season_summer` | `season_summer` | boolean | ❌ | Available in summer |
| `season_autumn` | `season_autumn` | boolean | ❌ | Available in autumn |
| `season_winter` | `season_winter` | boolean | ❌ | Available in winter |
| - | `foreigner_price` | decimal(10,2) | ❌ | Not in JSON, set to null |
| - | `country_id` | uuid | ❌ | China's ID (code: CN) |
| City from filename | `city_id` | uuid | ❌ | Matched by IATA code |
| - | `district_id` | uuid | ❌ | Not in JSON, set to null |
| - | `is_active` | boolean | ❌ | Default: true |
| - | `external_id` | string | ❌ | Not in JSON, set to null |

### Sub-Attraction Fields

| JSON Field | Database Field | Type | Notes |
|------------|----------------|------|-------|
| `name.en` | `name->en` | jsonb | English name |
| `name.zh_CN` | `name->zh_CN` | jsonb | Chinese name |
| - | `description` | jsonb | Not in JSON, set to null |
| Parent attraction | `attraction_id` | uuid | Link to parent |
| `local_price` | `local_price` | decimal | If exists in JSON |
| - | `foreigner_price` | decimal | Set to null |
| - | `latitude` | decimal | Not in JSON, set to null |
| - | `longitude` | decimal | Not in JSON, set to null |

---

## 🔧 Seeder Implementation

**File:** `database/seeders/Production/ChinaAttractionSeeder.php`

### Key Features:

1. **City Mapping Array:**
   ```php
   private array $cityMapping = [
       'beijing' => 'BJS',
       'chengdu' => 'CTU',
       // ... other cities
       'xian' => null, // Special case
   ];
   ```

2. **Duplicate Detection:**
   - Tracks processed names per city: `city_id|attraction_name`
   - Allows same name in different cities
   - Throws error if duplicate in same city

3. **Translatable Fields Handling:**
   - Only includes languages that exist in JSON
   - No null values for missing translations
   - Spatie Translatable handles the rest

4. **Type Field:**
   - Reads from JSON `type` field
   - Uses `AttractionTypeEnum::tryFrom()`
   - Defaults to `UNCATEGORIZED` if invalid

5. **Error Handling:**
   - City not found → Stop with error
   - Duplicate name in same city → Stop with error
   - Missing required fields → Stop with error

### Processing Flow:

```
1. Load China country (code: CN)
2. For each JSON file:
   a. Get city by code (or name for Xian)
   b. Validate city exists
   c. For each attraction:
      - Check for duplicates (city_id + name)
      - Prepare translatable fields
      - Create Attraction record
      - For each sub_attraction:
        * Create SubAttraction record
3. Log progress per file
4. Display total summary
```

---

## 📊 Import Statistics

### By City:

| City | Attractions | Sub-Attractions |
|------|-------------|-----------------|
| Beijing | 90 | 97 |
| Chengdu | 100 | 209 |
| Guangzhou | 100 | 24 |
| Guilin | 90 | 144 |
| Haikou | 100 | 44 |
| Hangzhou | 100 | 261 |
| Kunming | 100 | 177 |
| Nanjing | 100 | 271 |
| Sanya | 100 | 66 |
| Shanghai | 100 | 34 |
| Shenzhen | 100 | 114 |
| Suzhou | 100 | 390 |
| Xian | 100 | 24 |
| **TOTAL** | **1,280** | **1,855** |

---

## 🐛 Issues & Fixes

### 1. Duplicate Name - Different Locations
**Issue:** "West Street" exists in both Chengdu and Guilin  
**Solution:** Changed duplicate check from name-only to city_id + name  
**Status:** ✅ Fixed

### 2. Duplicate Name - Same Location
**Issue:** "Huangpu River" appeared twice in Shanghai JSON  
**Solution:** Renamed second occurrence to "Huangpu River Sightseeing Area" (黄浦江观光区)  
**File:** `shanghai.json` line 3441  
**Status:** ✅ Fixed

### 3. Missing Price Field in SubAttraction
**Issue:** Seeder tried to set `price` field which doesn't exist  
**Solution:** Removed `price` field from seeder, only use `local_price` and `foreigner_price`  
**Status:** ✅ Fixed

---

## 🎯 Integration with System

### Production Seeder Order:

```php
ProductionSeeder::class
├── CurrencySeeder
├── CountrySeeder
├── ProvinceSeeder
├── CitySeeder
├── ChinaAccommodationSeeder
└── ChinaAttractionSeeder  // ← New
```

### Development Seeder Updates:

- ❌ Removed: `Development\AttractionSeeder`
- ✅ Updated: `QuotationSeeder` now uses production attractions via random selection

### QuotationSeeder Changes:

**Before:**
```php
$forbiddenCity = Attraction::where('name->en', 'Forbidden City')->first();
```

**After:**
```php
$forbiddenCity = Attraction::where('city_id', $beijing->id)->inRandomOrder()->first();
```

---

## 🎨 Filament Resource Updates

### Removed Province References:

1. **AttractionForm.php:**
   - Removed `province_id` Select field
   - Updated country → city cascade

2. **AttractionsTable.php:**
   - Removed `province?->name` from location column
   - Removed `province.name` from searchable
   - Removed `province_id` filter
   - Removed hardcoded USD, now uses country currency

3. **AttractionInfolist.php:**
   - Removed `province.name` TextEntry
   - Added all new fields with proper formatting

### New Fields Added:

**Form Sections:**
1. Coordinates & Rating
2. Visit Information (opening_hours, duration, season)
3. Seasonal Availability (spring/summer/autumn/winter checkboxes)
4. Ticket & Tips Information
5. External Links (link, image_url, external_id)

**Infolist Sections:**
- Same structure with proper display formatting
- Image preview for `image_url`
- Duration formatted as "X - Y hours"
- Seasonal icons (✓/✗)

---

## ✅ Validation Rules

### Seeder Validation:

1. **Required Fields:**
   - `name` must have at least one language (en or zh_CN)
   - `city` must be found in database
   - `country` (China) must exist

2. **Uniqueness:**
   - Combination of `city_id` + `name->en` must be unique
   - Error thrown if duplicate detected

3. **Data Integrity:**
   - Empty language values are not stored (no null in JSON)
   - Invalid enum type defaults to `UNCATEGORIZED`
   - Sub-attractions only created if parent is successful

---

## 🔧 Maintenance

### Adding New Cities:

1. Add city to `china-cities.json`
2. Add JSON file to `storage/data/china-attractions/{city}.json`
3. Add mapping to `ChinaAttractionSeeder::$cityMapping`
4. Run: `php artisan db:seed --class=Database\Seeders\Production\ChinaAttractionSeeder`

### Updating Attractions:

To re-import (will create duplicates if names match):
```bash
# Delete old data first
php artisan tinker --execute="DB::table('attractions')->delete();"

# Re-run seeder
php artisan db:seed --class=Database\\Seeders\\Production\\ChinaAttractionSeeder
```

Or use fresh migration:
```bash
php artisan migrate:fresh --seed
```

---

## 📁 Related Files

### Seeders:
- `database/seeders/Production/ChinaAttractionSeeder.php` - Main import seeder
- `database/seeders/ProductionSeeder.php` - Calls ChinaAttractionSeeder
- `database/seeders/Development/QuotationSeeder.php` - Uses production attractions

### Models:
- `app/Models/Base/Attraction.php` - Main attraction model
- `app/Models/Base/SubAttraction.php` - Sub-attraction model

### Migrations:
- `2025_09_09_013740_create_attractions_table.php` - Creates attractions table
- `2025_09_09_013747_create_sub_attractions_table.php` - Creates sub_attractions table
- `2025_09_09_021023_change_attractions_and_sub_attractions_name_description_to_jsonb.php` - Changes to jsonb

### Filament Resources:
- `app/Filament/Base/Resources/Attractions/Schemas/AttractionForm.php`
- `app/Filament/Base/Resources/Attractions/Schemas/AttractionInfolist.php`
- `app/Filament/Base/Resources/Attractions/Tables/AttractionsTable.php`

### Translations:
- `lang/en/enums.php` - English enum translations
- `lang/zh_CN/enums.php` - Chinese enum translations

### Data Files:
- `storage/data/china-attractions/*.json` - Source JSON files (13 cities)

---

## 🎓 Lessons Learned

### 1. Spatie Translatable + Casts Conflict

**Problem:** Translatable fields were being cast to `array` in models  
**Solution:** Remove array casts for translatable fields - Spatie handles this automatically

**Before:**
```php
protected $casts = [
    'name' => 'array',           // ❌ Wrong
    'description' => 'array',    // ❌ Wrong
    'opening_hours' => 'array',  // ❌ Wrong
];
```

**After:**
```php
protected $casts = [
    'duration_hours' => 'array', // ✅ Correct (not translatable, just JSON)
    'rating' => 'decimal:2',
];
```

### 2. Unique Constraints

**Problem:** Name uniqueness should be per city, not global  
**Solution:** Use `city_id|name` as unique key in seeder validation

### 3. Currency Display

**Problem:** Prices were hardcoded to USD  
**Solution:** Use `$record->country?->currency?->code` from eager-loaded relation

**Implementation:**
```php
// Model
protected $with = ['country.currency'];

public function getCurrencyIdAttribute()
{
    return $this->country?->currency_id;
}

// Filament
->money(fn ($record) => $record->country?->currency?->code ?? 'USD')
```

### 4. Data Quality

**Fix Applied:** "Huangpu River" duplicate in Shanghai → renamed to "Huangpu River Sightseeing Area"

---

## 🚀 Running the Import

### Full Fresh Import:
```bash
php artisan migrate:fresh --seed
```

### Production Data Only:
```bash
php artisan db:seed --class=ProductionSeeder
```

### Attractions Only:
```bash
php artisan db:seed --class=Database\\Seeders\\Production\\ChinaAttractionSeeder
```

---

## 📝 JSON Structure Example

```json
{
  "name": {
    "en": "The Palace Museum",
    "zh_CN": "故宫博物院"
  },
  "type": "cultural",
  "link": "http://travel.qunar.com/p-oi710603-gugongbowuyuan",
  "address": "北京市东城区景山前街4号",
  "description": {
    "en": "The Forbidden City...",
    "zh_CN": "故宫又称紫禁城..."
  },
  "opening_hours": {
    "zh_CN": "全年 周一 全天不开放..."
  },
  "image_url": "https://...",
  "rating": "4.8",
  "suggested_duration": {
    "zh_CN": "建议游览时间：12小时 - 3天"
  },
  "suggested_season": {
    "zh_CN": "四季皆宜..."
  },
  "ticket_info": {
    "zh_CN": "...",
    "en": "..."
  },
  "tips": {
    "zh_CN": "..."
  },
  "duration_hours": {
    "min": 12,
    "max": 72
  },
  "local_price": null,
  "season_spring": true,
  "season_summer": true,
  "season_autumn": true,
  "season_winter": true,
  "sub_attractions": [
    {
      "name": {
        "zh_CN": "太和殿",
        "en": "TaiHe Hall"
      }
    }
  ]
}
```

---

## ⚠️ Important Notes

1. **Province Field Removed:**
   - Removed from `attractions` table and model
   - All Filament references removed
   - Use `city` and `country` instead

2. **Translatable Fields:**
   - Never cast translatable fields to `array` in models
   - Only include languages that exist (don't add `null` for missing translations)
   - Spatie Translatable handles JSON encoding/decoding

3. **Type Classification:**
   - All attractions must have a `type` in JSON
   - Valid types: `uncategorized`, `natural`, `man_made`, `cultural`, `sport`, `events`, `leisure`
   - Translations available in `lang/*/enums.php`

4. **Duration Hours:**
   - Stored as JSON object: `{min: X, max: Y}`
   - NOT translatable (same for all languages)
   - Display format: "X - Y hours"

---

**Last Updated:** October 22, 2025  
**Version:** 1.0  
**Status:** Production Ready ✅

