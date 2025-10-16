# راهنمای Customize کردن Translation Resource

## ✅ انجام شد!

Resource ترجمه‌ها با موفقیت از vendor به پروژه کپی شده و آماده customize است.

---

## 📁 فایل‌های کپی شده:

```
app/Filament/Resources/Translations/
├── TranslationResource.php          # ← Resource اصلی
├── Pages/
│   ├── CreateTranslation.php        # صفحه Create
│   ├── EditTranslation.php          # صفحه Edit
│   ├── ListTranslations.php         # صفحه List
│   └── ManageTranslations.php       # صفحه Manage (Modal mode)
├── Schemas/
│   ├── TranslationForm.php          # ← Form Schema
│   └── Components/
│       ├── Component.php
│       ├── Group.php
│       ├── Key.php
│       └── Text.php
├── Tables/
│   ├── TranslationsTable.php        # ← Table Schema
│   ├── Actions/
│   │   ├── DeleteAction.php
│   │   ├── EditAction.php
│   │   └── ViewAction.php
│   ├── BulkActions/
│   │   └── DeleteAction.php
│   ├── Columns/
│   │   ├── Key.php
│   │   ├── Text.php
│   │   ├── CreatedAt.php
│   │   └── UpdatedAt.php
│   ├── Filters/
│   │   ├── Group.php
│   │   └── Text.php
│   └── HeaderActions/
│       ├── ExportAction.php
│       └── ImportAction.php
└── Actions/
    ├── ClearAction.php
    ├── CreateAction.php
    ├── DeleteAction.php
    ├── EditAction.php
    ├── ScanAction.php
    └── ViewAction.php
```

---

## ⚙️ تغییرات انجام شده:

### 1. کپی فایل‌ها
```bash
✅ vendor/tomatophp/.../Translations → app/Filament/Resources/Translations
```

### 2. تغییر Namespace
```php
# قبل:
namespace TomatoPHP\FilamentTranslations\Filament\Resources\Translations;

# بعد:
namespace App\Filament\Resources\Translations;
```

### 3. آپدیت Config
```php
// config/filament-translations.php
'translation_resource' => \App\Filament\Resources\Translations\TranslationResource::class,
```

---

## 🎨 حالا می‌توانید Customize کنید:

### 1️⃣ تغییر Navigation

**فایل:** `app/Filament/Resources/Translations/TranslationResource.php`

```php
// تغییر Group
public static function getNavigationGroup(): ?string
{
    return 'My Custom Group'; // بجای 'System Management'
}

// تغییر Icon
public static function getNavigationIcon(): string
{
    return 'heroicon-o-globe-alt'; // بجای 'heroicon-m-language'
}

// تغییر Label
public static function getNavigationLabel(): string
{
    return 'My Translations'; // بجای 'Translations'
}

// تغییر Sort
public static function getNavigationSort(): ?int
{
    return 10;
}
```

---

### 2️⃣ تغییر Form

**فایل:** `app/Filament/Resources/Translations/Schemas/TranslationForm.php`

```php
public static function getDefaultComponents(): array
{
    return [
        Components\Group::make(),
        Components\Key::make(),
        Components\Text::make(),
        
        // فیلد جدید اضافه کنید:
        \Filament\Forms\Components\Textarea::make('notes')
            ->label('Notes')
            ->rows(3),
    ];
}
```

**یا کل فرم را override کنید:**

```php
public static function configure(Schema $schema): Schema
{
    return $schema->schema([
        \Filament\Forms\Components\Section::make('Translation Details')
            ->schema([
                Components\Group::make(),
                Components\Key::make()
                    ->required()
                    ->maxLength(255),
                Components\Text::make(),
            ]),
    ]);
}
```

---

### 3️⃣ تغییر Table

**فایل:** `app/Filament/Resources/Translations/Tables/TranslationsTable.php`

**اضافه کردن ستون جدید:**

```php
// در فایل: app/Filament/Resources/Translations/Tables/Columns/
// یک فایل جدید بسازید مثل UpdatedBy.php

namespace App\Filament\Resources\Translations\Tables\Columns;

use Filament\Tables\Columns\TextColumn;

class UpdatedBy
{
    public static function make(): TextColumn
    {
        return TextColumn::make('updated_by')
            ->label('Updated By')
            ->searchable();
    }
}
```

**سپس در TranslationsTable.php:**

```php
use App\Filament\Resources\Translations\Tables\Columns\UpdatedBy;

public static function getDefaultColumns(): array
{
    return [
        Column\Key::make(),
        Column\Text::make(),
        Column\CreatedAt::make(),
        Column\UpdatedAt::make(),
        UpdatedBy::make(), // ← جدید
    ];
}
```

---

### 4️⃣ اضافه کردن Filter

**فایل:** `app/Filament/Resources/Translations/Tables/TranslationFilters.php`

```php
public static function getDefaultFilters(): array
{
    return [
        Filter\Group::make(),
        Filter\Text::make(),
        
        // Filter جدید:
        \Filament\Tables\Filters\SelectFilter::make('status')
            ->label('Status')
            ->options([
                'translated' => 'Translated',
                'pending' => 'Pending',
            ]),
    ];
}
```

---

### 5️⃣ اضافه کردن Action

**فایل:** `app/Filament/Resources/Translations/Tables/TranslationActions.php`

```php
public static function getDefaultActions(): array
{
    return [
        Action\ViewAction::make(),
        Action\EditAction::make(),
        Action\DeleteAction::make(),
        
        // Action جدید:
        \Filament\Tables\Actions\Action::make('duplicate')
            ->label('Duplicate')
            ->icon('heroicon-o-document-duplicate')
            ->action(function ($record) {
                // Logic برای کپی کردن
            }),
    ];
}
```

---

### 6️⃣ تغییر Validation

**فایل:** `app/Filament/Resources/Translations/Schemas/Components/Key.php`

```php
public static function make(): Field
{
    return \Filament\Forms\Components\TextInput::make('key')
        ->label(trans('filament-translations::translation.columns.key'))
        ->required()
        ->maxLength(255) // ← تغییر دهید
        ->unique(ignoreRecord: true)
        ->rules(['alpha_dash']) // ← قانون جدید اضافه کنید
        ->helperText('Only letters, numbers, dashes and underscores allowed');
}
```

---

### 7️⃣ اضافه کردن Header Action

**فایل:** `app/Filament/Resources/Translations/Pages/ListTranslations.php`

```php
use Filament\Actions;

public function getHeaderActions(): array
{
    return array_merge(
        parent::getHeaderActions(),
        [
            Actions\Action::make('backup')
                ->label('Backup Translations')
                ->icon('heroicon-o-archive-box-arrow-down')
                ->action(function () {
                    // Logic برای backup
                }),
        ]
    );
}
```

---

## 🎯 مثال‌های کاربردی:

### مثال 1: اضافه کردن Badge برای Status

```php
// app/Filament/Resources/Translations/Tables/Columns/Status.php

namespace App\Filament\Resources\Translations\Tables\Columns;

use Filament\Tables\Columns\BadgeColumn;

class Status
{
    public static function make(): BadgeColumn
    {
        return BadgeColumn::make('status')
            ->label('Status')
            ->colors([
                'success' => 'translated',
                'warning' => 'pending',
                'danger' => 'missing',
            ]);
    }
}
```

---

### مثال 2: اضافه کردن Bulk Action برای Mark as Reviewed

```php
// app/Filament/Resources/Translations/Tables/BulkActions/MarkReviewedAction.php

namespace App\Filament\Resources\Translations\Tables\BulkActions;

use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class MarkReviewedAction
{
    public static function make(): BulkAction
    {
        return BulkAction::make('mark_reviewed')
            ->label('Mark as Reviewed')
            ->icon('heroicon-o-check-circle')
            ->action(function (Collection $records) {
                $records->each->update(['reviewed' => true]);
            })
            ->deselectRecordsAfterCompletion();
    }
}
```

---

### مثال 3: تغییر Modal به Pages

```php
// config/filament-translations.php
'modal' => false, // تغییر از true به false

// حالا صفحات جداگانه برای Create/Edit دارید
```

---

## 🚨 نکات مهم:

### ✅ چیزهایی که باید از vendor استفاده کنند:

```php
// Model
use TomatoPHP\FilamentTranslations\Models\Translation;

// Services
use TomatoPHP\FilamentTranslations\Facade\FilamentTranslations;
use TomatoPHP\FilamentTranslations\Services\TranslationScanner;

// Jobs
use TomatoPHP\FilamentTranslations\Jobs\TranslationJob;
```

### ❌ چیزهایی که نباید تغییر دهید:

- Model `Translation` را modify نکنید
- Facade `FilamentTranslations` را override نکنید
- Migration ها را دست نزنید

---

## 🔄 آپدیت کردن از Vendor:

اگر پکیج آپدیت شد و می‌خواهید تغییرات جدید را دریافت کنید:

```bash
# 1. فایل‌های فعلی را Backup کنید
cp -r app/Filament/Resources/Translations app/Filament/Resources/Translations.backup

# 2. فایل‌های vendor جدید را کپی کنید
cp -r vendor/tomatophp/filament-translations/src/Filament/Resources/Translations app/Filament/Resources/

# 3. Namespace ها را دوباره تغییر دهید
find app/Filament/Resources/Translations -type f -name "*.php" -exec sed -i '' 's|TomatoPHP\\FilamentTranslations\\Filament\\Resources\\Translations|App\\Filament\\Resources\\Translations|g' {} +

# 4. تغییرات custom خود را از backup بردارید
```

---

## 📝 مثال کامل: Custom Translation Resource

```php
// app/Filament/Resources/Translations/TranslationResource.php

namespace App\Filament\Resources\Translations;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TranslationResource extends Resource
{
    protected static ?string $model = \TomatoPHP\FilamentTranslations\Models\Translation::class;

    protected static ?string $slug = 'my-translations';
    
    protected static ?string $recordTitleAttribute = 'key';
    
    protected static bool $isScopedToTenant = false;
    
    // Custom Navigation
    public static function getNavigationGroup(): ?string
    {
        return 'Content Management';
    }
    
    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-language';
    }
    
    public static function getNavigationSort(): ?int
    {
        return 100;
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    
    // Form & Table
    public static function form(Schema $schema): Schema
    {
        return Schemas\TranslationForm::configure($schema);
    }
    
    public static function table(Table $table): Table
    {
        return Tables\TranslationsTable::configure($table);
    }
    
    // Pages
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTranslations::route('/'),
            'create' => Pages\CreateTranslation::route('/create'),
            'edit' => Pages\EditTranslation::route('/{record}/edit'),
        ];
    }
}
```

---

## 🎊 همه چیز آماده است!

حالا می‌توانید:
- ✅ Navigation را تغییر دهید
- ✅ Form را customize کنید
- ✅ Table را تغییر دهید
- ✅ Action/Filter جدید اضافه کنید
- ✅ Validation را تنظیم کنید
- ✅ هر چیزی که می‌خواهید!

**شروع کنید با ویرایش فایل‌ها در `app/Filament/Resources/Translations/`** 🚀

