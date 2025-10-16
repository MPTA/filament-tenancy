# راهنمای TomatoPHP Translation Manager

## ✅ سیستم آماده است

سیستم ترجمه با Smart Sync نصب و پیکربندی شده است.

---

## 📊 وضعیت فعلی:

### Database:
- ✅ جدول `language_lines` ساخته شده
- ✅ **خالی است** (0 رکورد)

### فایل‌های ترجمه:
- ✅ پوشه `lang/` **خالی است**
- ✅ هیچ فایل ترجمه‌ای وجود ندارد

### تنظیمات:
- ✅ فقط دو زبان: English (en) و 中文 (zh_CN)
- ✅ Plugin در AdminPanel ثبت شده
- ✅ Navigation Group: System Management

---

## 🧪 راهنمای تست مرحله به مرحله:

### تست 1: دسترسی به پنل

```
1. به پنل Admin لاگین کنید
2. منو → System Management → Translations
3. باید صفحه خالی ببینید (Empty State)
```

**انتظار:** لیست خالی با پیام "No translations found"

---

### تست 2: ایجاد اولین ترجمه دستی

از پنل Translations:

```
1. دکمه "New Translation" یا "+" را بزنید
2. فیلدها:
   - Group: messages
   - Key: welcome
   - English: Welcome to our app
   - 中文: 欢迎来到我们的应用
3. Save کنید
```

**انتظار:** یک رکورد در database ایجاد شود

**بررسی:**
```bash
php artisan tinker
>>> DB::table('language_lines')->count()
# باید 1 برگرداند

>>> DB::table('language_lines')->first()
# باید رکورد را نمایش دهد
```

---

### تست 3: Sync فایل‌های ترجمه (Smart Import)

**دستور اصلی:**
```bash
php artisan translations:sync
```

**دستورات پیشرفته:**
```bash
# Sync فقط یک زبان خاص
php artisan translations:sync --locale=zh_CN

# Sync فقط یک گروه خاص
php artisan translations:sync --group=messages

# Force update (حتی ترجمه‌های موجود را هم آپدیت کن)
php artisan translations:sync --force
```

**انتظار:** 
- کلیدهای جدید اضافه می‌شوند
- ترجمه‌های ویرایش شده در پنل **حفظ می‌مانند**
- فقط زبان‌های جدید به کلیدهای موجود اضافه می‌شوند

**خروجی نمونه:**
```
🔄 Smart Translation Sync Started...

📂 Scanning en...
  ✓ messages.php: +3 new, ~0 updated, -0 skipped

✅ Sync Complete!
+----------------------------------+-------+
| Status                           | Count |
+----------------------------------+-------+
| New keys added                   | 3     |
| Languages added to existing keys | 0     |
| Existing keys preserved          | 3     |
+----------------------------------+-------+
```

---

### تست 4: ویرایش ترجمه از پنل

```
1. روی یکی از ترجمه‌ها کلیک کنید (مثلاً hello)
2. ترجمه چینی را تغییر دهید
3. Save کنید
4. بررسی کنید در database تغییر کرده است
```

---

### تست 5: Export و Import

#### Export:
```
1. دکمه "Export" را بزنید
2. یک فایل Excel دانلود می‌شود
3. باز کنید و ببینید ترجمه‌ها داخل آن است
```

#### Import:
```
1. فایل Excel را ویرایش کنید
2. دکمه "Import" را بزنید
3. فایل را آپلود کنید
4. بررسی کنید تغییرات در database اعمال شده‌اند
```

---

### تست 6: فیلتر و جستجو

```
1. چند ترجمه بیشتر اضافه کنید
2. از فیلتر "Filter by Group" استفاده کنید
3. در کادر Search جستجو کنید
4. فیلتر "Filter by Empty Text" را امتحان کنید
```

---

## 📝 Config فایل‌های مهم:

### config/filament-translations.php

```php
'paths' => [
    app_path(),                 // کد اپلیکیشن
    resource_path('views'),     // viewها
    base_path('vendor'),        // همه vendorها (می‌توانید محدود کنید)
],

'locals' => [
    'en' => ['label' => 'English', 'flag' => 'us'],
    'zh_CN' => ['label' => '中文 (简体)', 'flag' => 'cn'],
],

'scan_enabled' => true,
'export_enabled' => true,
'import_enabled' => true,
'use_queue_on_scan' => true,  // Scan در background
```

### config/filament-translation-component.php

```php
'languages' => [
    'en' => ['label' => 'English', 'flag' => 'us'],
    'zh_CN' => ['label' => '中文 (简体)', 'flag' => 'cn'],
],
```

---

## 🎯 نکات تست:

### Queue Worker
اگر Scan کار نمی‌کند، Queue worker را اجرا کنید:
```bash
php artisan queue:work
```

### Clear Cache
بعد از هر تغییر config:
```bash
php artisan config:clear
php artisan cache:clear
```

### بررسی Database
```bash
php artisan tinker
>>> DB::table('language_lines')->get()
```

---

## 🚀 آماده برای تست!

همه چیز آماده است. می‌توانید:
1. به پنل بروید و تست کنید
2. رکورد دستی اضافه کنید
3. فایل بسازید و Scan کنید
4. Export/Import را امتحان کنید

موفق باشید! 🎉

