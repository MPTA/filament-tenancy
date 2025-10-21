# توصیه برای duration_hours

## سوال: JSON یا String؟

### گزینه فعلی (JSON):
```json
"duration_hours": {"min": 2, "max": 4}
```

### گزینه جایگزین (String):
```json
"duration_hours": "2-4"
```

---

## 🎯 توصیه نهایی: **JSON را نگه دارید**

### چرا JSON بهتر است؟

#### ✅ مزایا برای استفاده شما:

1. **Query های پیشرفته:**
```sql
-- جستجوی جاذبه‌های کمتر از 4 ساعت
SELECT name->>'zh', duration_hours
FROM attractions 
WHERE (duration_hours->>'max')::int <= 4;

-- فیلتر برای برنامه‌ریزی هوشمند
SELECT * FROM attractions 
WHERE (duration_hours->>'min')::int >= 2 
  AND (duration_hours->>'max')::int <= 6;
```

2. **Validation قوی‌تر در Laravel:**
```php
'duration_hours.min' => 'required|integer|min:1',
'duration_hours.max' => 'required|integer|gte:duration_hours.min'
```

3. **استفاده در AI/ML:**
```python
# محاسبه زمان کل برنامه سفر
total_hours = sum(a['duration_hours']['max'] for a in attractions)
```

4. **Flexibility:**
```json
// آینده می‌توانید فیلد اضافه کنید:
"duration_hours": {
  "min": 2,
  "max": 4,
  "recommended": 3,
  "unit": "hours"
}
```

#### ⚠️ تنها معایب:

- Casting در query: `(duration_hours->>'max')::int`
- اندکی پیچیده‌تر

---

## 💡 اگر می‌خواهید ساده‌تر باشد:

### روش ترکیبی (بهترین):

در **Migration** دو ستون جداگانه بسازید:

```php
Schema::create('attractions', function (Blueprint $table) {
    $table->id();
    $table->json('name');
    $table->json('description')->nullable();
    
    // روش پیشنهادی:
    $table->integer('duration_min')->nullable();
    $table->integer('duration_max')->nullable();
    
    // یا اگر JSON می‌خواهید:
    $table->json('duration_hours')->nullable();
    
    ...
});
```

### Query ساده با INTEGER columns:
```php
Attraction::where('duration_max', '<=', 4)->get();
Attraction::whereBetween('duration_min', [2, 6])->get();
```

---

## ✅ نتیجه‌گیری:

**برای پروژه شما بهترین است:**

1. در **JSON File**: همین JSON بماند `{"min": 2, "max": 4}`
2. در **Database Migration**: دو ستون `duration_min` و `duration_max` (INTEGER)
3. هنگام Import: JSON را parse کنید و در دو ستون ذخیره کنید

```php
// Import script
foreach ($jsonData as $item) {
    Attraction::create([
        'name' => $item['name'],
        'duration_min' => $item['duration_hours']['min'] ?? null,
        'duration_max' => $item['duration_hours']['max'] ?? null,
        ...
    ]);
}
```

این روش **بهترین Performance** و **سادگی Query** را دارد! ✨
