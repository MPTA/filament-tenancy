# Workflow نهایی سیستم ترجمه

## 🎯 سیستم آماده است!

سیستم ترجمه با TomatoPHP Translation Manager + Smart Sync Command نصب شده است.

---

## 📋 دستورات اصلی:

### Sync ترجمه‌ها (اصلی):
```bash
php artisan translations:sync
```

### Sync فقط یک زبان:
```bash
php artisan translations:sync --locale=zh_CN
```

### Sync فقط یک گروه:
```bash
php artisan translations:sync --group=messages
```

### Force Update (اجباری):
```bash
php artisan translations:sync --force
```

---

## 🔄 Workflow کامل توسعه:

### مرحله 1: Developer فایل می‌سازد

```php
// lang/en/notifications.php
<?php

return [
    'user_created' => 'User created successfully',
    'user_deleted' => 'User deleted',
];

// lang/zh_CN/notifications.php
<?php

return [
    'user_created' => '用户创建成功',
    'user_deleted' => '用户已删除',
];
```

**Commit به Git:**
```bash
git add lang/
git commit -m "Add notification translations"
git push
```

---

### مرحله 2: Deploy به Production

```bash
git pull
```

---

### مرحله 3: Sync ترجمه‌ها

```bash
php artisan translations:sync
```

**خروجی:**
```
🔄 Smart Translation Sync Started...

📂 Scanning en...
  ✓ notifications.php: +2 new, ~0 updated, -0 skipped

✅ Sync Complete!
+----------------------------------+-------+
| Status                           | Count |
+----------------------------------+-------+
| New keys added                   | 2     |
| Languages added to existing keys | 0     |
| Existing keys preserved          | X     |
+----------------------------------+-------+
```

---

### مرحله 4: کاربر چینی Review می‌کند

از پنل:
```
Admin → System Management → Translations
```

- ترجمه‌های جدید را می‌بیند
- در صورت نیاز اصلاح می‌کند
- ترجمه‌های AI را بهینه می‌کند

---

## ⚙️ Smart Sync چطور کار می‌کند:

```
برای هر کلید در فایل:

  اگر در database موجود است:
    ✅ ترجمه‌های موجود را نگه دار (دست نزن)
    ✅ فقط زبان‌های جدید را اضافه کن
    
  اگر کلید جدید است:
    ✅ تمام ترجمه‌ها را از فایل بگیر و اضافه کن
```

**نتیجه:**
- ویرایش‌های کاربر از پنل **حفظ می‌شوند** ✅
- کلیدهای جدید **اضافه می‌شوند** ✅
- Deploy مشکلی ایجاد نمی‌کند ✅

---

## 🎨 قابلیت‌های پنل Translation Manager:

### مشاهده و ویرایش:
- 🔍 جستجو در ترجمه‌ها
- 🏷️ فیلتر بر اساس Group
- 🌐 تغییر بین زبان‌ها
- ✏️ ویرایش inline

### Import/Export:
- 📥 Import از Excel
- 📤 Export به Excel
- 📋 مناسب برای ترجمه توسط مترجم

### مدیریت:
- 🗑️ حذف ترجمه‌ها
- ➕ اضافه کردن دستی
- 📊 آمار ترجمه‌ها

---

## 📂 ساختار فایل‌ها:

```
lang/
├── en/
│   ├── messages.php         # ترجمه‌های عمومی
│   ├── auth.php            # احراز هویت
│   ├── validation.php      # اعتبارسنجی
│   └── ...
├── zh_CN/
│   ├── messages.php
│   ├── auth.php
│   ├── validation.php
│   └── ...
└── vendor/
    └── filament-translation-component/
        └── en/
            └── messages.php  # labels فرم پلاگین (ضروری)
```

---

## 🚨 نکات مهم:

### Deploy Safe:
- ✅ ترجمه‌ها در database هستند
- ✅ Deploy کد ترجمه‌ها را خراب نمی‌کند
- ✅ فقط بعد از deploy دستور sync بزنید

### Git Strategy:
- ✅ فایل‌های lang/ در Git هستند
- ✅ Database از backup گرفته می‌شود
- ✅ فایل‌ها = Source of Truth

### Performance:
- ✅ Redis برای cache
- ✅ JSONB برای PostgreSQL
- ✅ مناسب production

---

## 🛠️ Troubleshooting:

### ترجمه‌ها نمایش داده نمی‌شوند:
```bash
php artisan cache:clear
php artisan config:clear
```

### Label چینی نمایش داده نمی‌شود:
بررسی کنید فایل وجود دارد:
```
lang/vendor/filament-translation-component/en/messages.php
```

### Sync کار نمی‌کند:
```bash
php artisan config:clear
php artisan translations:sync
```

---

## 🚀 شروع کار:

```bash
# 1. ایجاد فایل‌های ترجمه
# lang/en/your_file.php

# 2. Sync به database
php artisan translations:sync

# 3. Review از پنل
# Admin → Translations

# 4. همین! 🎉
```

---

همه چیز آماده است! موفق باشید! 🎊

