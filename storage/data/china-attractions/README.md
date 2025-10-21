# Beijing Attractions Database

## 📋 اطلاعات فایل

- **فایل:** `beijing.json`
- **حجم:** 453 KB
- **رکوردها:** 90 جاذبه گردشگری بیجینگ
- **منبع:** Qunar.com (去哪儿网)
- **تاریخ:** October 2025

## 🌐 ساختار Multi-Language

فایل شامل ساختار دوزبانه (چینی-انگلیسی) است:

```json
{
  "name": {
    "zh": "故宫博物院",
    "en": "The Palace Museum"
  },
  "description": {
    "zh": "متن کامل چینی...",
    "en": "Complete English translation..."
  },
  "ticket_info": {
    "故宫博物院门票成人票": {
      "zh": "故宫博物院门票成人票",
      "en": "Palace Museum Ticket Adult",
      "prices": ["¥125起"]
    }
  },
  "duration_hours": {
    "min": 2,
    "max": 4
  }
}
```

## 📊 فیلدها

| فیلد | نوع | توضیحات | Coverage |
|------|-----|---------|----------|
| `name` | Object | نام (چینی + انگلیسی) | 100% |
| `link` | String | لینک صفحه Qunar | 100% |
| `address` | String | آدرس کامل | 87% |
| `description` | Object | توضیحات (چینی + انگلیسی) | 87% |
| `opening_hours` | String | ساعات بازدید | 87% |
| `image_url` | String | URL تصویر | 88% |
| `rating` | String | امتیاز (0-5) | 88% |
| `suggested_duration` | String | مدت زمان پیشنهادی (متن اصلی) | 88% |
| `duration_hours` | Object | مدت زمان به ساعت (min/max) | 88% |
| `suggested_season` | String | فصل پیشنهادی | 51% |
| `ticket_info` | Object | اطلاعات بلیط (ترجمه شده) | 84% |
| `tips` | String | نکات مهم | 46% |
| `page` | String | شماره صفحه | 100% |

## 🗄️ استفاده در PostgreSQL

### ایجاد جدول:

```sql
CREATE TABLE china_attractions (
    id SERIAL PRIMARY KEY,
    name JSONB NOT NULL,
    link VARCHAR(500),
    address TEXT,
    description JSONB,
    opening_hours TEXT,
    image_url TEXT,
    rating DECIMAL(2,1),
    suggested_duration VARCHAR(200),
    duration_hours JSONB,
    suggested_season TEXT,
    ticket_info JSONB,
    tips TEXT,
    page_number INTEGER,
    city VARCHAR(100) DEFAULT 'Beijing',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index برای بهبود performance
CREATE INDEX idx_attractions_city ON china_attractions(city);
CREATE INDEX idx_attractions_rating ON china_attractions(rating);
CREATE INDEX idx_attractions_name_zh ON china_attractions ((name->>'zh'));
CREATE INDEX idx_attractions_duration ON china_attractions ((duration_hours->>'min'));
```

### نمونه Query ها:

```sql
-- جستجوی جاذبه‌های کمتر از 4 ساعت
SELECT 
    name->>'zh' as name_zh,
    name->>'en' as name_en,
    (duration_hours->>'min')::int || '-' || (duration_hours->>'max')::int as hours
FROM china_attractions 
WHERE (duration_hours->>'max')::int <= 4;

-- جستجوی جاذبه‌های با امتیاز بالا
SELECT name->>'en', rating
FROM china_attractions 
WHERE rating::decimal >= 4.7
ORDER BY rating::decimal DESC;

-- جستجو در توضیحات انگلیسی
SELECT name->>'en', description->>'en'
FROM china_attractions 
WHERE description->>'en' ILIKE '%great wall%';

-- استخراج همه انواع بلیط
SELECT 
    name->>'zh',
    ticket->>'zh' as ticket_zh,
    ticket->>'en' as ticket_en,
    ticket->'prices'->>0 as price
FROM china_attractions,
LATERAL jsonb_each(ticket_info) as t(key, ticket)
WHERE ticket_info IS NOT NULL;
```

## 🚀 استفاده در Laravel

### Model با Spatie Translatable:

```php
use Spatie\Translatable\HasTranslations;

class Attraction extends Model
{
    use HasTranslations;
    
    protected $table = 'china_attractions';
    
    public $translatable = ['name', 'description'];
    
    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'ticket_info' => 'array',
        'duration_hours' => 'array',
        'rating' => 'decimal:1',
    ];
    
    // دریافت نام به زبان فعلی
    public function getTranslatedNameAttribute()
    {
        return $this->name[app()->getLocale()] ?? $this->name['zh'];
    }
    
    // فیلتر بر اساس مدت زمان
    public function scopeByDuration($query, $maxHours)
    {
        return $query->whereRaw("(duration_hours->>'max')::int <= ?", [$maxHours]);
    }
}
```

## 📊 آمار ترجمه

- **Name:** 99% ترجمه شده (89/90)
- **Description:** 87% ترجمه شده (78/90)
- **Ticket Info:** 100% پردازش شده (76/76)

## 📝 نکات مهم

1. **روزها به ساعت تبدیل شده:** 1 روز = 24 ساعت
2. **فیلدهای null:** برای مواردی که اطلاعات وجود ندارد
3. **UTF-8 Encoding:** پشتیبانی کامل از کاراکترهای چینی
4. **JSONB در PostgreSQL:** برای query های پیشرفته روی فیلدهای JSON

## 🔄 فرآیند تبدیل

1. ✅ CSV → JSON
2. ✅ استانداردسازی نام فیلدها (چینی → انگلیسی)
3. ✅ Multi-Language برای Name
4. ✅ ترجمه Description (Google Translate)
5. ✅ پردازش و ترجمه Ticket Info
6. ✅ استخراج duration_hours برای query

---

**تاریخ ایجاد:** October 20, 2025  
**نسخه:** 1.0  
**وضعیت:** آماده برای Production ✅

