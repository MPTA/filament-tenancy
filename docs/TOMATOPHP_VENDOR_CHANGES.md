# TomatoPHP Filament Tenancy - Vendor Package Changes

این مستند تمام تغییرات انجام شده در پکیج `vendor/tomatophp/filament-tenancy` را شرح می‌دهد.

---

## ⚠️ هشدار مهم

تغییرات در پوشه `vendor` موقت هستند و با `composer update` یا `composer install` از بین می‌روند.

**راه حل‌های نگهداری:**

### گزینه 1: Fork کردن پکیج (توصیه می‌شود)
```bash
# 1. Fork کنید: tomatophp/filament-tenancy
# 2. تغییرات را در fork خود commit کنید
# 3. در composer.json:
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/YOUR-USERNAME/filament-tenancy"
    }
],
"require": {
    "tomatophp/filament-tenancy": "dev-your-branch"
}
```

### گزینه 2: Patch File
```bash
# ایجاد patch:
cd vendor/tomatophp/filament-tenancy
git diff > ../../../patches/tomatophp-path-tenancy.patch

# اعمال patch بعد از composer install:
patch -p1 < patches/tomatophp-path-tenancy.patch
```

### گزینه 3: Composer Scripts
در `composer.json`:
```json
"scripts": {
    "post-install-cmd": [
        "@php artisan vendor:publish --tag=tomatophp-customizations"
    ]
}
```

---

## 📁 فایل‌های ایجاد شده در Vendor

### 1. `src/Resolvers/PathTenantResolver.php`

**مسیر کامل:**
```
vendor/tomatophp/filament-tenancy/src/Resolvers/PathTenantResolver.php
```

**محتوای کامل:**
```php
<?php

namespace TomatoPHP\FilamentTenancy\Resolvers;

use Illuminate\Routing\Route;
use Stancl\Tenancy\Contracts\Tenant;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedByPathException;
use Stancl\Tenancy\Resolvers\PathTenantResolver as BasePathTenantResolver;

class PathTenantResolver extends BasePathTenantResolver
{
    /**
     * Resolve tenant by name field instead of id.
     */
    public function resolveWithoutCache(...$args): Tenant
    {
        /** @var Route $route */
        $route = $args[0];

        if ($name = $route->parameter(static::$tenantParameterName)) {
            $route->forgetParameter(static::$tenantParameterName);

            // Find tenant by name field instead of id
            $tenant = app(config('tenancy.tenant_model'))->where('name', $name)->first();
            
            if ($tenant) {
                return $tenant;
            }
        }

        throw new TenantCouldNotBeIdentifiedByPathException($name ?? 'unknown');
    }
}
```

**توضیح:**
- Extends: `Stancl\Tenancy\Resolvers\PathTenantResolver`
- Override: `resolveWithoutCache()` method
- تغییر اصلی: از `where('name', ...)` به جای `where('id', ...)` استفاده می‌کند
- Exception: `TenantCouldNotBeIdentifiedByPathException` در صورت عدم یافتن tenant

---

### 2. `src/Middleware/InitializeTenancyByPath.php`

**مسیر کامل:**
```
vendor/tomatophp/filament-tenancy/src/Middleware/InitializeTenancyByPath.php
```

**محتوای کامل:**
```php
<?php

namespace TomatoPHP\FilamentTenancy\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath as BaseInitializeTenancyByPath;
use Stancl\Tenancy\Tenancy;
use TomatoPHP\FilamentTenancy\Resolvers\PathTenantResolver;

class InitializeTenancyByPath extends BaseInitializeTenancyByPath
{
    /**
     * Create a new middleware instance.
     */
    public function __construct(Tenancy $tenancy, PathTenantResolver $resolver)
    {
        $this->tenancy = $tenancy;
        $this->resolver = $resolver;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var \Illuminate\Routing\Route $route */
        $route = $request->route();

        // Only initialize tenancy if the route has the tenant parameter
        // This allows domain/subdomain identification to work when path parameter is absent
        $parameterNames = $route->parameterNames();
        
        if (empty($parameterNames) || $parameterNames[0] !== PathTenantResolver::$tenantParameterName) {
            // No tenant parameter in route, skip this middleware
            return $next($request);
        }

        // Route has tenant parameter, proceed with path-based identification
        return parent::handle($request, $next);
    }
}
```

**توضیح:**
- Extends: `Stancl\Tenancy\Middleware\InitializeTenancyByPath`
- Constructor: inject می‌کند `PathTenantResolver` سفارشی ما را
- `handle()`: چک می‌کند route پارامتر `{tenant}` دارد یا نه
- اگر ندارد: skip می‌کند (برای domain/subdomain routes)
- اگر دارد: tenancy را با resolver سفارشی initialize می‌کند

---

### 3. `src/Middleware/AddTenantToUrlGeneration.php`

**مسیر کامل:**
```
vendor/tomatophp/filament-tenancy/src/Middleware/AddTenantToUrlGeneration.php
```

**محتوای کامل:**
```php
<?php

namespace TomatoPHP\FilamentTenancy\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class AddTenantToUrlGeneration
{
    /**
     * Add tenant parameter to URL generation for path-based routing
     */
    public function handle(Request $request, Closure $next)
    {
        // Only for path-based
        if (config('filament-tenancy.identification_method') !== 'path') {
            return $next($request);
        }
        
        // Extract tenant from route parameter or from tenancy
        $tenantName = $request->route('tenant');
        
        if (!$tenantName && tenancy()->initialized) {
            $tenantName = tenant('name');
        }
        
        if ($tenantName) {
            // Set default route parameter for tenant
            URL::defaults(['tenant' => $tenantName]);
        }
        
        return $next($request);
    }
}
```

**توضیح:**
- فقط برای `identification_method = 'path'` فعال است
- tenant را از route parameter یا tenancy helper می‌گیرد
- `URL::defaults(['tenant' => ...])` را set می‌کند
- نتیجه: همه `route()` و `url()` helper ها خودکار tenant را شامل می‌شوند

---

## 📝 تغییرات در فایل‌های موجود Vendor

### 4. `src/FilamentTenancyServiceProvider.php`

**تغییر 1: افزودن static method جدید**

**موقعیت:** بعد از line 25 (بعد از `const TENANCY_IDENTIFICATION`)

```php
/**
 * Get the appropriate tenant identification middleware based on config
 */
public static function getTenancyIdentificationMiddleware(): string
{
    $method = config('filament-tenancy.identification_method', 'subdomain');
    
    return match($method) {
        'domain' => Middleware\InitializeTenancyByDomain::class,
        'subdomain' => Middleware\InitializeTenancyBySubdomain::class,
        'path' => \TomatoPHP\FilamentTenancy\Middleware\InitializeTenancyByPath::class,
        default => Middleware\InitializeTenancyBySubdomain::class,
    };
}
```

**چرا:**
- const `TENANCY_IDENTIFICATION` static است و نمی‌توان به صورت داینامیک تغییرش داد
- این method بر اساس config، middleware مناسب را برمی‌گرداند
- استفاده در `routes/tenant.php`

---

**تغییر 2: افزودن method برای URL defaults**

**موقعیت:** بعد از `boot()` method

```php
/**
 * Setup URL defaults for path-based tenant identification
 */
protected function setupPathBasedUrlDefaults(): void
{
    if (config('filament-tenancy.identification_method') === 'path') {
        // Listen to tenancy initialized event to set URL defaults
        Event::listen(\Stancl\Tenancy\Events\TenancyInitialized::class, function ($event) {
            \Illuminate\Support\Facades\URL::defaults(['tenant' => tenant('name')]);
        });
    }
}
```

**چرا:**
- زمانی که tenant initialize می‌شود، tenant name را به URL defaults اضافه می‌کند
- این باعث می‌شود همه route generation ها خودکار tenant parameter را داشته باشند

---

**تغییر 3: فراخوانی در boot()**

**موقعیت:** در `boot()` method، خط آخر قبل از `FrameworkColumns::registerMacros()`

```php
public function boot(): void
{
    $this->bootEvents();
    $this->mapRoutes();
    $this->makeTenancyMiddlewareHighestPriority();
    $this->modifyStaticConfigs();
    $this->prepareLivewireForTenancy();
    $this->configureDatabaseNaming();
    $this->setupPathBasedUrlDefaults(); // <- این خط اضافه شد

    FrameworkColumns::registerMacros();
    // ...
}
```

---

### 5. `src/FilamentTenancyAppPlugin.php`

**تغییر 1: Conditional Middlewares**

**موقعیت:** در `register()` method

**قبل:**
```php
$panel
    ->login(TenantLogin::class)
    ->middleware([
        PreventAccessFromCentralDomains::class,
        RedirectIfInertiaMiddleware::class,
    ])
    ->middleware([
        'universal',
        FilamentTenancyServiceProvider::TENANCY_IDENTIFICATION,
        PreventAccessFromCentralDomains::class,
    ], isPersistent: true);
```

**بعد:**
```php
$identificationMethod = config('filament-tenancy.identification_method', 'subdomain');

// For path-based, don't use PreventAccessFromCentralDomains
$middlewares = $identificationMethod === 'path' 
    ? [
        \TomatoPHP\FilamentTenancy\Middleware\AddTenantToUrlGeneration::class,
        RedirectIfInertiaMiddleware::class,
    ]
    : [
        PreventAccessFromCentralDomains::class,
        RedirectIfInertiaMiddleware::class,
    ];

$persistentMiddlewares = $identificationMethod === 'path'
    ? [
        'universal',
        FilamentTenancyServiceProvider::getTenancyIdentificationMiddleware(),
        \TomatoPHP\FilamentTenancy\Middleware\AddTenantToUrlGeneration::class,
    ]
    : [
        'universal',
        FilamentTenancyServiceProvider::getTenancyIdentificationMiddleware(),
        PreventAccessFromCentralDomains::class,
    ];

$panel
    ->login(TenantLogin::class)
    ->middleware($middlewares)
    ->middleware($persistentMiddlewares, isPersistent: true);
```

**چرا:**
- `PreventAccessFromCentralDomains`: برای path-based حذف شد چون باید از central domain قابل دسترسی باشد
- `AddTenantToUrlGeneration`: برای path-based اضافه شد تا URL generation درست کار کند
- `getTenancyIdentificationMiddleware()`: به جای const static استفاده شد

---

**تغییر 2: Domain Configuration**

**قبل:**
```php
$domains = tenant()?->domains()->pluck('domain') ?? [];
$panel->domains($domains);
```

**بعد:**
```php
// For path-based, allow any domain
// For domain/subdomain, set specific domains
if ($identificationMethod === 'path') {
    $panel->domain(null);
} else {
    $domains = tenant()?->domains()->pluck('domain') ?? [];
    $panel->domains($domains);
}
```

**چرا:**
- path-based: می‌خواهیم از هر domain قابل دسترسی باشد
- domain/subdomain: فقط domain های ثبت شده

---

**تغییر 3: Simplify boot() method**

```php
public function boot(Panel $panel): void
{
    // Panel path override is handled in AppServiceProvider via Filament::serving()
}
```

**چرا:**
- logic اصلی در `AppServiceProvider` منتقل شد
- clean separation of concerns

---

## 🔍 دلیل هر تغییر

### چرا PathTenantResolver جدید؟
پکیج Stancl به صورت پیش‌فرض tenant را با `id` جستجو می‌کند:
```php
// Stancl default:
tenancy()->find($id)  // WHERE id = 'balopar' ❌

// ما می‌خواهیم:
Tenant::where('name', 'balopar')->first() // WHERE name = 'balopar' ✅
```

### چرا InitializeTenancyByPath سفارشی؟
- باید از resolver سفارشی ما استفاده کند
- باید چک کند route پارامتر `{tenant}` دارد یا نه
- جلوگیری از تداخل با domain/subdomain middlewares

### چرا AddTenantToUrlGeneration؟
بدون این middleware:
```php
route('filament.app.auth.login')
// Error: Missing required parameter: tenant ❌
```

با این middleware:
```php
URL::defaults(['tenant' => 'balopar'])
route('filament.app.auth.login')
// Result: /tenants/balopar/app/login ✅
```

### چرا PreventAccessFromCentralDomains غیرفعال؟
این middleware جلوی دسترسی از central domain را می‌گیرد:
```php
// بدون غیرفعال کردن:
https://newtripmaker.test/tenants/balopar/app
// Result: 404 Forbidden ❌

// بعد از غیرفعال کردن:
https://newtripmaker.test/tenants/balopar/app
// Result: Works! ✅
```

---

## 🔄 Revert Guide - راهنمای برگشت به حالت قبل

### مرحله 1: Discard Vendor Changes

```bash
# در terminal:
cd /Users/farhad/Herd/newtripmaker
git checkout vendor/tomatophp/

# یا در VS Code:
# Source Control → vendor/tomatophp/ → Discard Changes
```

---

### مرحله 2: Revert Project Files

**`config/filament-tenancy.php`:**
```php
<?php

return [
    "central_domain" => env('CENTRAL_DOMAIN', 'localhost'),
    "single_database" => env('SINGLE_DATABASE', true),
    "tenant_user_model" => \App\Models\Tenants\TenantUser::class,

    "features" => [
        "homepage" => true,
        "auth" => true,
        "impersonation" => true,
    ]
];
// حذف identification_method
```

---

**`routes/tenant.php`:**
```php
Route::middleware([
    'web',
    \TomatoPHP\FilamentTenancy\FilamentTenancyServiceProvider::TENANCY_IDENTIFICATION,
])->group(function () {
    if(config('filament-tenancy.features.impersonation')) {
        Route::get('/login/url', [\TomatoPHP\FilamentTenancy\Http\Controllers\LoginUrl::class, 'index']);
    }

    // Your Tenant routes here
});
```

---

**`bootstrap/app.php`:**
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->group('universal', [
        InitializeTenancyByDomain::class,
        InitializeTenancyBySubdomain::class,
    ]);
})
// حذف InitializeTenancyByPath
```

---

**`app/Providers/Filament/AppPanelProvider.php`:**
```php
->path('app')
// حذف conditional
```

---

**`app/Providers/Filament/TenantAdminPanelProvider.php`:**
```php
->path('admin')
// حذف conditional
```

---

**`app/Providers/AppServiceProvider.php`:**
حذف کد `Filament::serving()`:
```php
// این قسمت را حذف کنید:
\Filament\Facades\Filament::serving(function () {
    // ... کل callback
});
```

---

### مرحله 3: Clear Caches

```bash
php artisan optimize:clear
```

---

## 📊 جدول مقایسه

| Feature | Subdomain | Path-Based |
|---------|-----------|------------|
| **URL** | `tenant.domain.com/app` | `domain.com/tenants/tenant/app` |
| **SSL Certificate** | Wildcard needed | Single domain |
| **DNS Setup** | Wildcard required | Single A record |
| **Tenant در URL** | پنهان (subdomain) | قابل مشاهده (path) |
| **Setup پیچیدگی** | متوسط | ساده |
| **Server Config** | پیچیده‌تر | ساده‌تر |

---

## 🧪 تست‌های لازم بعد از تغییرات

### تست Path-Based:
```bash
# Login page:
curl -I https://newtripmaker.test/tenants/balopar/app/login
# Expected: 200 OK

# Dashboard (unauthenticated):
curl -I https://newtripmaker.test/tenants/balopar/app
# Expected: 302 redirect to /tenants/balopar/app/login

# Admin panel:
curl -I https://newtripmaker.test/tenants/balopar/admin
# Expected: 302 redirect to /tenants/balopar/admin/login
```

### تست Subdomain (بعد از revert):
```bash
curl -I https://balopar.newtripmaker.test/app
# Expected: 302 redirect to login
```

### تست Central Domain:
```bash
curl -I https://newtripmaker.test/admin
# Expected: 302 redirect to central admin login
```

---

## 💡 Best Practices

### 1. در Production

**گزینه A: Fork Package**
- پکیج را fork کنید
- تغییرات را commit کنید
- در composer از fork استفاده کنید

**گزینه B: Patch در CI/CD**
```bash
# در deployment script:
if [ -f patches/tomatophp-path-tenancy.patch ]; then
    cd vendor/tomatophp/filament-tenancy
    git apply ../../../patches/tomatophp-path-tenancy.patch
fi
```

### 2. Validation برای Tenant Names

```php
// در Filament Resource یا migration:
Schema::table('tenants', function (Blueprint $table) {
    $table->string('name')->unique();
    // Add constraint for URL-safe names
});

// Validation rule:
'name' => [
    'required',
    'string',
    'max:255',
    'unique:tenants,name',
    'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', // Only lowercase, numbers, and hyphens
],
```

### 3. Caching برای Performance

```php
// در PathTenantResolver:
public static $shouldCache = true;
public static $cacheTTL = 3600; // 1 hour
public static $cacheStore = 'redis'; // optional
```

---

## 🚨 خطاهای احتمالی و راه حل

### Error: "Tenant could not be identified"
```
علت: نام tenant در URL با database مطابقت ندارد
راه حل: 
  SELECT name FROM tenants;
  URL را با نام دقیق امتحان کنید
```

### Error: "Missing required parameter: tenant"
```
علت: URL::defaults() set نشده
راه حل:
  - چک کنید AddTenantToUrlGeneration در middleware list باشد
  - php artisan optimize:clear
```

### Error: "PreventAccessFromCentralDomains - 403/404"
```
علت: این middleware برای path-based نباید فعال باشد
راه حل:
  - چک کنید FilamentTenancyAppPlugin conditional middleware دارد
  - vendor changes را چک کنید
```

### Error: Panel Switcher → `%7Btenant%7D`
```
علت: Filament::serving() callback اجرا نشده
راه حل:
  - چک کنید AppServiceProvider کد را دارد
  - php artisan optimize:clear
  - browser cache را clear کنید
```

---

## 📞 نگهداری و به‌روزرسانی

### وقتی composer update می‌زنید:

**مشکل:** تغییرات vendor از بین می‌روند

**راه حل:**

1. **قبل از update:** patch بسازید
```bash
cd vendor/tomatophp/filament-tenancy
git diff > ../../../storage/tomatophp-changes.patch
```

2. **بعد از update:** patch را اعمال کنید
```bash
cd vendor/tomatophp/filament-tenancy
git apply ../../../storage/tomatophp-changes.patch
```

یا بهتر: از fork استفاده کنید

---

## 🎯 خلاصه برای مرجع سریع

### فعال کردن Path-Based:
```env
TENANT_IDENTIFICATION=path
```

### فعال کردن Subdomain:
```env
TENANT_IDENTIFICATION=subdomain
```

### فعال کردن Domain:
```env
TENANT_IDENTIFICATION=domain
```

سپس:
```bash
php artisan optimize:clear
```

---

**مستند شده توسط:** AI Assistant  
**تاریخ:** 23 اکتبر 2025  
**نسخه:** 1.0

