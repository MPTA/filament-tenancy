# China Attractions Import Plan

## 📋 Overview

**Date:** October 22, 2025  
**Status:** ✅ Completed  
**Total Imported:** 1,280 attractions + 1,855 sub-attractions from 13 cities

---

## 🗺️ City Mapping

| JSON File | City Name | IATA Code | Match Method | Status |
|-----------|-----------|-----------|--------------|--------|
| beijing.json | Beijing | BJS | where('code', 'BJS') | ✅ |
| chengdu.json | Chengdu | CTU | where('code', 'CTU') | ✅ |
| guangzhou.json | Guangzhou | CAN | where('code', 'CAN') | ✅ |
| guilin.json | Guilin | KWL | where('code', 'KWL') | ✅ |
| haikou.json | Haikou | HAK | where('code', 'HAK') | ✅ |
| hangzhou.json | Hangzhou | HGH | where('code', 'HGH') | ✅ |
| kunming.json | Kunming | KMG | where('code', 'KMG') | ✅ |
| nanjing.json | Nanjing | NKG | where('code', 'NKG') | ✅ |
| sanya.json | Sanya | SYX | where('code', 'SYX') | ✅ |
| shanghai.json | Shanghai | SHA | where('code', 'SHA') | ✅ |
| shenzhen.json | Shenzhen | SZX | where('code', 'SZX') | ✅ |
| suzhou.json | Suzhou | SZV | where('code', 'SZV') | ✅ |
| xian.json | Xian | N/A | where('name->en', 'Xian') | ✅ |

**Note:** Xian doesn't have IATA code - match by English name.

---

## 🔄 Field Mapping

### Attraction Fields

| JSON Field | DB Field | Type | Translatable | Notes |
|------------|----------|------|--------------|-------|
| name.en | name->en | jsonb | ✅ | Required |
| name.zh_CN | name->zh_CN | jsonb | ✅ | Required |
| description.en | description->en | jsonb | ✅ | Optional |
| description.zh_CN | description->zh_CN | jsonb | ✅ | Optional |
| type | type | enum | ❌ | AttractionTypeEnum |
| link | link | string | ❌ | Qunar.com URL |
| address | address | text | ❌ | Full address |
| opening_hours | opening_hours | jsonb | ✅ | Multi-language |
| image_url | image_url | string | ❌ | Image URL |
| rating | rating | decimal | ❌ | 0-5 scale |
| suggested_duration | suggested_duration | jsonb | ✅ | Multi-language |
| suggested_season | suggested_season | jsonb | ✅ | Multi-language |
| ticket_info | ticket_info | jsonb | ✅ | Multi-language |
| tips | tips | jsonb | ✅ | Multi-language |
| duration_hours | duration_hours | jsonb | ❌ | {min, max} object |
| local_price | local_price | decimal | ❌ | Optional |
| season_* | season_* | boolean | ❌ | 4 seasons |
| - | country_id | uuid | ❌ | China (CN) |
| - | city_id | uuid | ❌ | From mapping |
| - | is_active | boolean | ❌ | Default: true |

### Sub-Attraction Fields

| JSON Field | DB Field | Type | Notes |
|------------|----------|------|-------|
| name.en | name->en | jsonb | English |
| name.zh_CN | name->zh_CN | jsonb | Chinese |
| local_price | local_price | decimal | If exists |
| - | attraction_id | uuid | Parent ID |

---

## 📊 Import Statistics

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

## 🛠️ Implementation

### Seeder: ChinaAttractionSeeder.php

**Location:** `database/seeders/Production/ChinaAttractionSeeder.php`

**Key Logic:**
- Reads all .json files from `storage/data/china-attractions/`
- Maps filename to city using IATA code or name
- Validates duplicate: `city_id|name->en` must be unique
- Only stores non-null language values
- Creates sub-attractions for each parent

**Validation:**
- City not found → Error & stop
- Duplicate name in same city → Error & stop
- Missing required name → Error & stop

---

## 🐛 Issues Fixed

### 1. Duplicate Names - Different Cities
**Problem:** "West Street" in Chengdu AND Guilin  
**Solution:** Check uniqueness per city (city_id + name)  
**Status:** ✅ Fixed

### 2. Duplicate Names - Same City
**Problem:** "Huangpu River" twice in Shanghai  
**Fix:** Renamed to "Huangpu River Sightseeing Area"  
**File:** shanghai.json line 3441  
**Status:** ✅ Fixed

### 3. Spatie Translatable Conflict
**Problem:** Translatable fields cast to array  
**Solution:** Remove array casts - Spatie handles it  
**Status:** ✅ Fixed (14 models updated)

---

## 🎨 Filament Integration

### Province Field Removed

**Files Updated:**
1. AttractionForm.php - Removed province_id Select
2. AttractionsTable.php - Removed province filter & column
3. AttractionInfolist.php - Removed province TextEntry

### New Fields Added

**Form Sections:**
- Coordinates & Rating
- Visit Information (hours, duration, season)
- Seasonal Availability (4 checkboxes)
- Ticket & Tips
- External Links

**Currency Display:**
- Changed from hardcoded USD to `$record->country->currency->code`
- Uses eager-loaded relation: `protected $with = ['country.currency']`

---

## 🔧 Running Import

```bash
# Full fresh import
php artisan migrate:fresh --seed

# Production only
php artisan db:seed --class=ProductionSeeder

# Attractions only
php artisan db:seed --class=Database\\Seeders\\Production\\ChinaAttractionSeeder
```

---

## 📁 Related Files

**Seeders:**
- database/seeders/Production/ChinaAttractionSeeder.php
- database/seeders/ProductionSeeder.php
- database/seeders/Development/QuotationSeeder.php (updated)

**Models:**
- app/Models/Base/Attraction.php
- app/Models/Base/SubAttraction.php

**Migrations:**
- 2025_09_09_013740_create_attractions_table.php
- 2025_09_09_013747_create_sub_attractions_table.php

**Filament:**
- app/Filament/Base/Resources/Attractions/Schemas/AttractionForm.php
- app/Filament/Base/Resources/Attractions/Schemas/AttractionInfolist.php
- app/Filament/Base/Resources/Attractions/Tables/AttractionsTable.php

**Translations:**
- lang/en/enums.php (added uncategorized)
- lang/zh_CN/enums.php (added 未分类)

---

**Version:** 1.0  
**Status:** Production Ready ✅
