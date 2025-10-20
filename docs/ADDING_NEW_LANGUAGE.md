# راهنمای اضافه کردن زبان جدید

سیستم ترجمه به صورت خودکار با تمام زبان‌ها کار می‌کند. برای اضافه کردن زبان جدید، فقط سه فایل را ویرایش کنید:

## 1. تنظیم Language Switcher
**فایل:** `app/Providers/AppServiceProvider.php`

```php
LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
    $switch
        ->locales(['en', 'zh_CN', 'fa', 'ar'])  // اضافه کردن locale جدید
        ->excludes([
            'admin',
            'base'
        ])
        ->labels([
            'en' => 'English',
            'zh_CN' => '中文',
            'fa' => 'فارسی',        // اضافه کردن label
            'ar' => 'العربية',     // اضافه کردن label
        ]);
});
```

## 2. تنظیم Filament Panels
**فایل‌ها:** 
- `app/Providers/Filament/BasePanelProvider.php`
- `app/Providers/Filament/TenantAdminPanelProvider.php`

```php
->plugin(SpatieTranslatablePlugin::make()
    ->defaultLocales(['en', 'zh_CN', 'fa', 'ar'])  // همان locales
)
```

## 3. تنظیم Filament Translations
**فایل:** `config/filament-translations.php`

```php
'locals' => [
    'en' => [
        'label' => 'English',
        'flag' => 'us',
    ],
    'zh_CN' => [
        'label' => '中文 (简体)',
        'flag' => 'cn',
    ],
    'fa' => [
        'label' => 'فارسی',
        'flag' => 'ir',
    ],
    'ar' => [
        'label' => 'العربية',
        'flag' => 'sa',
    ],
],
```

## 4. پاکسازی Cache

```bash
php artisan optimize:clear
```

---

## نکات مهم

### 1. کدهای Locale استاندارد
استفاده از کدهای استاندارد ISO 639-1 و ISO 3166-1:
- `en` - English
- `zh_CN` - Chinese (Simplified)
- `fa` - Persian/Farsi
- `ar` - Arabic
- `es` - Spanish
- `fr` - French
- `de` - German
- `ja` - Japanese
- `ko` - Korean

### 2. Trait‌ها به صورت خودکار کار می‌کنند

**`TranslatableUiLocale`:**
- به صورت خودکار locale UI را از LanguageSwitch دریافت می‌کند
- Locale variants را handle می‌کند (مثلاً `zh` → `zh_CN`)
- هنگام ذخیره، locale صحیح را تشخیص می‌دهد

**`HasTranslatableFallback`:**
- اگر ترجمه برای locale فعلی موجود نباشد، اولین ترجمه موجود را نمایش می‌دهد
- برای تمام فیلدهای `$translatable` در model به صورت خودکار اعمال می‌شود

### 3. استفاده در Model‌های جدید

```php
use App\Traits\HasTranslatableFallback;
use Spatie\Translatable\HasTranslations;

class YourModel extends Model
{
    use HasTranslations, HasTranslatableFallback;
    
    protected $translatable = ['name', 'description', 'any_other_field'];
}
```

### 4. استفاده در Edit Pages

```php
use App\Filament\Shared\Concerns\TranslatableUiLocale;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditYourResource extends EditRecord
{
    use Translatable, TranslatableUiLocale {
        TranslatableUiLocale::getDefaultTranslatableLocale insteadof Translatable;
        TranslatableUiLocale::afterSave insteadof Translatable;
        TranslatableUiLocale::mutateFormDataBeforeSave insteadof Translatable;
    }
}
```

---

## مثال: اضافه کردن زبان فارسی

### 1. AppServiceProvider
```php
->locales(['en', 'zh_CN', 'fa'])
->labels([
    'en' => 'English',
    'zh_CN' => '中文',
    'fa' => 'فارسی',
])
```

### 2. Panel Providers
```php
->plugin(SpatieTranslatablePlugin::make()
    ->defaultLocales(['en', 'zh_CN', 'fa'])
)
```

### 3. filament-translations.php
```php
'fa' => [
    'label' => 'فارسی',
    'flag' => 'ir',
],
```

### 4. پاکسازی و تست
```bash
php artisan optimize:clear
```

حالا می‌توانید:
- به زبان فارسی سوییچ کنید
- رکوردهای جدید به فارسی ایجاد کنید
- رکوردهای موجود را به فارسی ترجمه کنید
- اگر ترجمه فارسی نباشد، اولین ترجمه موجود نمایش داده می‌شود

---

## رفع مشکلات

### مشکل: فیلدها خالی هستند
- مطمئن شوید locale در هر سه فایل یکسان است
- Cache را پاک کنید: `php artisan optimize:clear`
- Trait `HasTranslatableFallback` را به model اضافه کنید

### مشکل: ذخیره در locale اشتباه
- مطمئن شوید `TranslatableUiLocale` در Edit page استفاده شده
- مطمئن شوید `insteadof` برای هر سه متد تنظیم شده است

### مشکل: Locale variants (zh vs zh_CN)
- `normalizeLocale()` به صورت خودکار آن‌ها را handle می‌کند
- فقط مطمئن شوید locale اصلی شما در تنظیمات وجود دارد

