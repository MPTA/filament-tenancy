# راهنمای رفع اشکال فیلتر Missing Translation

## ✅ تغییرات انجام شده:

### مشکل 1: هر زبان دو بار نمایش داده می‌شد
**علت:** Config یک nested array است با structure:
```php
[
    'en' => ['label' => 'English', 'flag' => 'us'],
    'zh_CN' => ['label' => '中文 (简体)', 'flag' => 'cn']
]
```

**راه حل:** Extract کردن فقط label ها:
```php
$options = [];
foreach ($locales as $code => $locale) {
    $options[$code] = $locale['label'] ?? $code;
}
// Result: ['en' => 'English', 'zh_CN' => '中文 (简体)']
```

---

### مشکل 2: فیلتر کار نمی‌کرد
**علت‌های احتمالی:**
1. نبود `extends Filter` base class
2. نبود type hints برای query callback
3. نبود return statement در query

**راه حل:**
```php
class MissingTranslation extends Filter  // ← extends Filter
{
    public static function make(): Filters\SelectFilter
    {
        return Filters\SelectFilter::make('missing_translation')
            ->query(function (Builder $query, array $data): Builder {  // ← type hints
                // ...
                return $query->where(...);  // ← return query
            });
    }
}
```

---

## 🧪 تست:

### تست query در Tinker:
```bash
php artisan tinker
```

```php
$locale = 'zh_CN';

$query = \TomatoPHP\FilamentTranslations\Models\Translation::query()
    ->where(function ($q) use ($locale) {
        $q->whereRaw("(text->>?) IS NULL", [$locale])
          ->orWhereRaw("(text->>?) = ''", [$locale]);
    });

// نمایش SQL
echo $query->toSql();

// نمایش نتایج
$query->get()->each(function($t) {
    echo $t->key . ' | EN: ' . ($t->text['en'] ?? 'NULL') . ' | ZH: ' . ($t->text['zh_CN'] ?? 'NULL') . PHP_EOL;
});
```

---

## 🎯 نحوه استفاده در پنل:

1. بروید به: `Admin → System Management → Translations`
2. روی آیکن **Filter** (بالای جدول) کلیک کنید
3. **"Missing Translation"** را انتخاب کنید
4. زبان را انتخاب کنید (مثلاً: **中文 (简体)**)
5. فقط رکوردهایی که به چینی ترجمه نشده‌اند نمایش داده می‌شوند

---

## 🔍 اگر هنوز کار نمی‌کند:

### 1. بررسی کنید فایل‌ها درست هستند:
```bash
# فایل فیلتر
cat app/Filament/Resources/Translations/Tables/Filters/MissingTranslation.php

# فایل TranslationFilters
cat app/Filament/Resources/Translations/Tables/TranslationFilters.php
```

### 2. Clear cache:
```bash
php artisan filament:optimize-clear
php artisan config:clear
php artisan view:clear
```

### 3. بررسی لاگ‌ها:
```bash
tail -f storage/logs/laravel.log
```

### 4. تست query مستقیم:
```bash
php artisan tinker --execute="
\$locale = 'zh_CN';
\$count = \TomatoPHP\FilamentTranslations\Models\Translation::query()
    ->where(function (\$q) use (\$locale) {
        \$q->whereRaw('(text->>?) IS NULL', [\$locale])
          ->orWhereRaw('(text->>?) = \'\'', [\$locale]);
    })
    ->count();
echo 'Found ' . \$count . ' missing ' . \$locale . ' translations' . PHP_EOL;
"
```

### 5. بررسی Browser Console:
- باز کنید Developer Tools (F12)
- برو به Console tab
- ببینید آیا error JavaScript وجود دارد

### 6. بررسی Network Tab:
- باز کنید Developer Tools (F12)
- برو به Network tab
- فیلتر را اعمال کنید
- ببینید آیا request به سرور ارسال می‌شود
- Response را بررسی کنید

---

## 📊 داده‌های تست:

برای تست فیلتر، این داده‌ها را اضافه کنید:

```bash
php artisan tinker
```

```php
// کامل: هر دو زبان دارد
\TomatoPHP\FilamentTranslations\Models\Translation::create([
    'namespace' => '*',
    'group' => 'test',
    'key' => 'complete',
    'text' => ['en' => 'Complete', 'zh_CN' => '完成'],
]);

// ناقص: فقط انگلیسی دارد
\TomatoPHP\FilamentTranslations\Models\Translation::create([
    'namespace' => '*',
    'group' => 'test',
    'key' => 'missing_chinese',
    'text' => ['en' => 'Missing Chinese'],
]);

// ناقص: چینی خالی است
\TomatoPHP\FilamentTranslations\Models\Translation::create([
    'namespace' => '*',
    'group' => 'test',
    'key' => 'empty_chinese',
    'text' => ['en' => 'Empty Chinese', 'zh_CN' => ''],
]);
```

**انتظار:**
- فیلتر "Chinese" → نمایش: `missing_chinese` و `empty_chinese`
- بدون فیلتر → نمایش: همه (3 رکورد)

**پاک کردن داده‌های تست:**
```php
\TomatoPHP\FilamentTranslations\Models\Translation::where('group', 'test')->delete();
```

---

## 🐛 اشکالات رایج:

### Error: "This cache store does not support tagging"
**علت:** CacheTenancyBootstrapper فعال است
**راه حل:** در `config/tenancy.php` آن را comment کنید:
```php
// \Stancl\Tenancy\Bootstrappers\CacheTenancyBootstrapper::class,
```

### Error: "syntax error at or near"
**علت:** Raw query با placeholder درست کار نمی‌کند
**راه حل:** از binding استفاده کنید:
```php
// ✅ درست
whereRaw("(text->>?) IS NULL", [$locale])

// ❌ غلط
whereRaw("(text->>'$locale') IS NULL")
```

### فیلتر نمایش داده نمی‌شود
**علت:** TranslationFilters را فراموش کرده‌اید
**راه حل:** اضافه کنید در `TranslationFilters.php`:
```php
Filters\MissingTranslation::make(),
```

### همه رکوردها نمایش داده می‌شوند
**علت:** Query return نمی‌شود یا type hints ندارد
**راه حل:** اضافه کنید:
```php
->query(function (Builder $query, array $data): Builder {
    // ...
    return $query->where(...);
})
```

---

## ✅ چک‌لیست نهایی:

- [ ] فایل `MissingTranslation.php` درست است و `extends Filter` دارد
- [ ] Query callback type hints دارد: `Builder $query, array $data): Builder`
- [ ] Query return می‌شود: `return $query->where(...)`
- [ ] Options فقط label ها را دارد نه nested array
- [ ] فیلتر در `TranslationFilters.php` اضافه شده
- [ ] Cache clear شده
- [ ] داده‌های تست ایجاد شده
- [ ] Browser console error ندارد

---

## 🎊 اگر همه چیز درست کار کرد:

پنل را رفرش کنید و تست کنید:
1. بروید به Translations
2. Filters → Missing Translation → 中文 (简体)
3. باید فقط ترجمه‌های ناقص نمایش داده شوند! 🎉

**اگر همچنان کار نکرد، screenshot یا error log را ارسال کنید.**

