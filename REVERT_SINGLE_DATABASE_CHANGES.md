# راهنمای برگرداندن تغییرات Single Database

برای برگرداندن پروژه به حالت Multi-Database، مراحل زیر را انجام دهید:

## 1. تغییر فایل‌های کانفیگ

### config/filament-tenancy.php
```php
"single_database" => env('SINGLE_DATABASE', false),
```

### config/tenancy.php
```php
'bootstrappers' => [
    Stancl\Tenancy\Bootstrappers\DatabaseTenancyBootstrapper::class,
//        Stancl\Tenancy\Bootstrappers\CacheTenancyBootstrapper::class,
    Stancl\Tenancy\Bootstrappers\FilesystemTenancyBootstrapper::class,
    Stancl\Tenancy\Bootstrappers\QueueTenancyBootstrapper::class,
    // Stancl\Tenancy\Bootstrappers\RedisTenancyBootstrapper::class, // Note: phpredis is needed
],
```

## 2. برگرداندن مدل‌ها

### app/Models/Tenants/ExchangeRate.php
```php
<?php

namespace App\Models\Tenants;

use App\Models\Base\Currency;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ExchangeRate extends Model
{
    use HasTranslations;
    
    protected $translatable = ['name'];
    protected $fillable = ['from_currency_id', 'to_currency_id', 'rate'];

    public function fromCurrency(){
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }

    public function toCurrency(){
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }
}
```

### app/Models/User.php
```php
<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
```

## 3. حذف Migration ها

حذف فایل‌های زیر:
- database/migrations/2025_09_08_014554_add_tenant_id_to_users_table.php
- database/migrations/2025_09_08_015328_modify_users_email_unique_constraint_for_tenancy.php
- database/migrations/2025_09_08_015724_add_foreign_key_to_tenant_id_in_users_table.php
- database/migrations/tenant/2025_09_08_014637_add_tenant_id_to_exchange_rates_table.php
- database/migrations/tenant/2025_09_08_015350_modify_users_email_unique_constraint_for_tenancy.php
- database/migrations/tenant/2025_09_08_015745_add_foreign_key_to_tenant_id_in_users_table.php
- database/migrations/tenant/2025_09_08_015805_add_foreign_key_to_tenant_id_in_exchange_rates_table.php

## 4. اجرای Migration ها

```bash
php artisan migrate:rollback --step=3
php artisan tenants:migrate:rollback --step=3
```

## 5. پاک کردن Cache

```bash
php artisan config:clear
php artisan cache:clear
```

## تغییرات اعمال شده:

1. ✅ تغییر `config/filament-tenancy.php` - single_database = true
2. ✅ تغییر `config/tenancy.php` - غیرفعال کردن DatabaseTenancyBootstrapper
3. ✅ اضافه کردن BelongsToTenant به مدل‌های tenant
4. ✅ حذف مدل TenantUser و استفاده از User اصلی
5. ✅ ایجاد migration برای اضافه کردن tenant_id
6. ✅ تغییر unique constraint برای email + tenant_id
7. ✅ اضافه کردن foreign key constraint برای tenant_id
8. ✅ اجرای migration ها

## فایل‌های تغییر یافته:
- config/filament-tenancy.php
- config/tenancy.php  
- app/Models/User.php (اضافه شدن BelongsToTenant)
- app/Models/Tenants/ExchangeRate.php
- app/Filament/Tenant/Resources/TenantUsers/TenantUserResource.php
- database/migrations/2025_09_08_014554_add_tenant_id_to_users_table.php
- database/migrations/2025_09_08_015328_modify_users_email_unique_constraint_for_tenancy.php
- database/migrations/2025_09_08_015724_add_foreign_key_to_tenant_id_in_users_table.php
- database/migrations/tenant/2025_09_08_014637_add_tenant_id_to_exchange_rates_table.php
- database/migrations/tenant/2025_09_08_015350_modify_users_email_unique_constraint_for_tenancy.php
- database/migrations/tenant/2025_09_08_015745_add_foreign_key_to_tenant_id_in_users_table.php
- database/migrations/tenant/2025_09_08_015805_add_foreign_key_to_tenant_id_in_exchange_rates_table.php
