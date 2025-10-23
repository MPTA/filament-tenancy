# Path-Based Tenancy Implementation - Complete Documentation

این مستند تمام تغییرات انجام شده برای پیاده‌سازی path-based tenancy را شرح می‌دهد.

---

## 📋 خلاصه تغییرات

سیستم را طوری تغییر دادیم که tenant ها با URL path قابل دسترسی باشند:

**قبل:**
```
https://balopar.newtripmaker.test/app  (subdomain)
```

**بعد:**
```
https://newtripmaker.test/tenants/balopar/app  (path-based)
```

**مزایا:**
- نیازی به wildcard SSL certificate نیست
- همه tenant ها از یک domain اصلی قابل دسترسی هستند
- مشکل SSL در سرور حل می‌شود

---

## 🗂️ تغییرات در فایل‌های پروژه

### 1. `config/filament-tenancy.php`
**تغییر:** افزودن config برای روش شناسایی tenant

```php
/**
 * Tenant identification method
 * Options: 'domain', 'subdomain', 'path'
 */
"identification_method" => env('TENANT_IDENTIFICATION', 'path'),
```

**توضیح:**
- این config تعیین می‌کند tenant چگونه شناسایی شود
- می‌توان از `.env` با `TENANT_IDENTIFICATION` override کرد
- مقادیر ممکن: `domain`, `subdomain`, `path`

---

### 2. `routes/tenant.php`
**تغییر:** افزودن prefix داینامیک بر اساس identification method

```php
// Check if path-based identification is enabled
$prefix = config('filament-tenancy.identification_method') === 'path' ? '/tenants/{tenant}' : '';

Route::prefix($prefix)->middleware([
    'web',
    \TomatoPHP\FilamentTenancy\FilamentTenancyServiceProvider::getTenancyIdentificationMiddleware(),
])->group(function () {
    // ... routes
});
```

**توضیح:**
- اگر `identification_method = 'path'` → prefix: `/tenants/{tenant}`
- اگر `subdomain` یا `domain` → بدون prefix
- middleware از method جدید `getTenancyIdentificationMiddleware()` استفاده می‌کند

---

### 3. `bootstrap/app.php`
**تغییر:** افزودن `InitializeTenancyByPath` به universal middleware group

```php
$middleware->group('universal', [
    \TomatoPHP\FilamentTenancy\Middleware\InitializeTenancyByPath::class,
    InitializeTenancyByDomain::class,
    InitializeTenancyBySubdomain::class,
]);
```

**توضیح:**
- `InitializeTenancyByPath`: middleware سفارشی در پکیج TomatoPHP
- اولویت path بالاتر از domain و subdomain است
- اگر route پارامتر `{tenant}` داشته باشد، این middleware فعال می‌شود

---

### 4. `app/Providers/Filament/AppPanelProvider.php`
**تغییر:** path panel به صورت داینامیک

```php
->path(config('filament-tenancy.identification_method') === 'path' ? 'tenants/{tenant}/app' : 'app')
```

**توضیح:**
- برای path-based: `tenants/{tenant}/app` → `/tenants/balopar/app`
- برای subdomain: `app` → `/app` (روی subdomain)

---

### 5. `app/Providers/Filament/TenantAdminPanelProvider.php`
**تغییر:** path panel به صورت داینامیک

```php
->path(config('filament-tenancy.identification_method') === 'path' ? 'tenants/{tenant}/admin' : 'admin')
```

**توضیح:**
- برای path-based: `tenants/{tenant}/admin` → `/tenants/balopar/admin`
- برای subdomain: `admin` → `/admin` (روی subdomain)

---

### 6. `app/Providers/AppServiceProvider.php`
**تغییر:** اضافه کردن logic برای PanelSwitch

```php
// Override panel paths for path-based tenancy before PanelSwitch renders
\Filament\Facades\Filament::serving(function () {
    if (config('filament-tenancy.identification_method') === 'path' && tenancy()->initialized) {
        $tenantName = tenant('name');
        
        foreach (\Filament\Facades\Filament::getPanels() as $panel) {
            $originalPath = $panel->getPath();
            if (str_contains($originalPath, '{tenant}')) {
                $newPath = str_replace('{tenant}', $tenantName, $originalPath);
                
                // Use reflection to update path
                $reflection = new \ReflectionClass($panel);
                $property = $reflection->getProperty('path');
                $property->setAccessible(true);
                $property->setValue($panel, $newPath);
            }
        }
    }
});
```

**توضیح:**
- این کد قبل از render شدن هر صفحه Filament اجرا می‌شود
- `{tenant}` را در panel paths با tenant name واقعی جایگزین می‌کند
- برای PanelSwitch ضروری است تا URL های صحیح generate شوند
- از Reflection API استفاده می‌کند چون property `path` در Panel protected است

---

## 📦 تغییرات در پکیج TomatoPHP

### فایل‌های ایجاد شده:

#### 1. `vendor/tomatophp/filament-tenancy/src/Resolvers/PathTenantResolver.php`
**نقش:** Resolver سفارشی که tenant را با field `name` پیدا می‌کند (به جای `id`)

```php
<?php

namespace TomatoPHP\FilamentTenancy\Resolvers;

use Illuminate\Routing\Route;
use Stancl\Tenancy\Contracts\Tenant;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedByPathException;
use Stancl\Tenancy\Resolvers\PathTenantResolver as BasePathTenantResolver;

class PathTenantResolver extends BasePathTenantResolver
{
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

**چرا لازم بود:**
- پکیج Stancl به صورت پیش‌فرض tenant را با `id` جستجو می‌کند
- ما می‌خواهیم با `name` جستجو کنیم (مثلاً `balopar`)

---

#### 2. `vendor/tomatophp/filament-tenancy/src/Middleware/InitializeTenancyByPath.php`
**نقش:** Middleware سفارشی که از resolver سفارشی ما استفاده می‌کند

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
    public function __construct(Tenancy $tenancy, PathTenantResolver $resolver)
    {
        $this->tenancy = $tenancy;
        $this->resolver = $resolver;
    }

    public function handle(Request $request, Closure $next)
    {
        /** @var \Illuminate\Routing\Route $route */
        $route = $request->route();

        // Only initialize tenancy if the route has the tenant parameter
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

**چرا لازم بود:**
- از resolver سفارشی ما استفاده می‌کند
- فقط زمانی فعال می‌شود که route پارامتر `{tenant}` داشته باشد
- جلوی تداخل با domain/subdomain middleware ها را می‌گیرد

---

#### 3. `vendor/tomatophp/filament-tenancy/src/Middleware/AddTenantToUrlGeneration.php`
**نقش:** تنظیم URL defaults برای route generation صحیح

```php
<?php

namespace TomatoPHP\FilamentTenancy\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class AddTenantToUrlGeneration
{
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

**چرا لازم بود:**
- Filament وقتی می‌خواهد URL generate کند (مثلاً redirect به login)، پارامتر `{tenant}` را نمی‌داند
- این middleware tenant را به URL defaults اضافه می‌کند
- همه route() و url() helper ها به صورت خودکار tenant را شامل می‌شوند

---

### فایل‌های تغییر یافته:

#### 4. `vendor/tomatophp/filament-tenancy/src/FilamentTenancyServiceProvider.php`

**تغییر 1:** افزودن method برای دریافت middleware مناسب

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

**تغییر 2:** افزودن method برای تنظیم URL defaults

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

**تغییر 3:** فراخوانی method جدید در boot()

```php
public function boot(): void
{
    // ... existing code ...
    $this->setupPathBasedUrlDefaults(); // <- اضافه شد
    // ... rest of code ...
}
```

---

#### 5. `vendor/tomatophp/filament-tenancy/src/FilamentTenancyAppPlugin.php`

**تغییر 1:** middleware های conditional بر اساس identification method

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
```

**چرا:**
- `PreventAccessFromCentralDomains`: برای path-based غیرفعال می‌شود چون می‌خواهیم از central domain دسترسی داشته باشیم
- `AddTenantToUrlGeneration`: فقط برای path-based اضافه می‌شود

**تغییر 2:** تنظیم domain بر اساس method

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
- path-based: باید از هر domain قابل دسترسی باشد → `domain(null)`
- subdomain: فقط از subdomain های ثبت شده → `domains($domains)`

---

## 🔄 نحوه کار سیستم

### 1. Request Flow برای Path-Based:

```
User → https://newtripmaker.test/tenants/balopar/app
  ↓
Laravel Router: Match route {tenant}/app → tenant = 'balopar'
  ↓
Middleware: InitializeTenancyByPath
  ↓
PathTenantResolver: SELECT * FROM tenants WHERE name = 'balopar'
  ↓
Tenancy Initialized: tenant('name') = 'balopar'
  ↓
AddTenantToUrlGeneration: URL::defaults(['tenant' => 'balopar'])
  ↓
Filament Authenticate: Redirect to login
  ↓
URL Generation: route('filament.app.auth.login', ['tenant' => 'balopar'])
  ↓
Result: /tenants/balopar/app/login ✅
```

### 2. Panel Switching:

```
User clicks Panel Switcher
  ↓
Filament::serving() callback in AppServiceProvider
  ↓
Replace {tenant} in panel paths: 'tenants/{tenant}/admin' → 'tenants/balopar/admin'
  ↓
PanelSwitch generates URL
  ↓
Result: /tenants/balopar/admin ✅
```

---

## 🔧 تغییر به Subdomain یا Domain

### برگشت به Subdomain:

**مرحله 1:** تغییر config

```php
// در config/filament-tenancy.php یا .env
"identification_method" => 'subdomain'
// یا
TENANT_IDENTIFICATION=subdomain
```

**مرحله 2:** Clear cache

```bash
php artisan optimize:clear
```

**نتیجه:**
- Routes بدون prefix ثبت می‌شوند
- Panel paths: `app` و `admin` (بدون tenants/)
- دسترسی: `https://balopar.newtripmaker.test/app`
- middleware `PreventAccessFromCentralDomains` فعال می‌شود

---

### تغییر به Domain:

```php
"identification_method" => 'domain'
```

**تفاوت با subdomain:**
- در جدول `domains` باید hostname کامل ثبت شود (مثلاً `balopar.com`)
- subdomain: فقط نام ثبت می‌شود (مثلاً `balopar`)

---

## ↩️ نحوه Revert کردن تغییرات

### گزینه 1: Discard تغییرات vendor (ساده‌ترین)

```bash
# در VS Code Git panel:
# - vendor/tomatophp/filament-tenancy/ را discard کنید
```

سپس:

```php
// config/filament-tenancy.php
"identification_method" => 'subdomain'
```

```php
// routes/tenant.php - به حالت قبل:
Route::middleware([
    'web',
    \TomatoPHP\FilamentTenancy\FilamentTenancyServiceProvider::TENANCY_IDENTIFICATION,
])->group(function () {
    //...
});
```

```php
// bootstrap/app.php - حذف InitializeTenancyByPath:
$middleware->group('universal', [
    InitializeTenancyByDomain::class,
    InitializeTenancyBySubdomain::class,
]);
```

```php
// AppPanelProvider & TenantAdminPanelProvider:
->path('app')  // یا 'admin'
```

```php
// AppServiceProvider - حذف Filament::serving() callback
// کد PanelSwitch را به حالت قبل برگردانید
```

---

### گزینه 2: نگه داشتن قابلیت Switch

اگر می‌خواهید قابلیت switch بین path و subdomain را نگه دارید:
- فایل‌های vendor را نگه دارید
- فقط config را تغییر دهید:

```php
// برای subdomain:
"identification_method" => 'subdomain'

// برای path:
"identification_method" => 'path'

// برای domain:
"identification_method" => 'domain'
```

---

## 🧪 تست کردن

### Path-Based (`identification_method = 'path'`):

```bash
# App Panel:
✅ https://newtripmaker.test/tenants/balopar/app
✅ https://newtripmaker.test/tenants/balopar/app/login

# Admin Panel:
✅ https://newtripmaker.test/tenants/balopar/admin
✅ https://newtripmaker.test/tenants/balopar/admin/login

# Custom Routes:
✅ https://newtripmaker.test/tenants/balopar/login/url
```

### Subdomain (`identification_method = 'subdomain'`):

```bash
# App Panel:
✅ https://balopar.newtripmaker.test/app
✅ https://balopar.newtripmaker.test/app/login

# Admin Panel:
✅ https://balopar.newtripmaker.test/admin
```

### چک Central Domain:

```bash
# Central Admin (همیشه):
✅ https://newtripmaker.test/admin

# Base Panel (همیشه):
✅ https://newtripmaker.test/base
```

---

## 📊 خلاصه فایل‌های تغییر یافته

### پروژه (6 فایل):
```
M  app/Providers/AppServiceProvider.php
M  app/Providers/Filament/AppPanelProvider.php
M  app/Providers/Filament/TenantAdminPanelProvider.php
M  bootstrap/app.php
M  config/filament-tenancy.php
M  routes/tenant.php
```

### پکیج TomatoPHP:

**تغییر یافته (2 فایل):**
```
M  vendor/tomatophp/filament-tenancy/src/FilamentTenancyAppPlugin.php
M  vendor/tomatophp/filament-tenancy/src/FilamentTenancyServiceProvider.php
```

**ایجاد شده (3 فایل):**
```
A  vendor/tomatophp/filament-tenancy/src/Resolvers/PathTenantResolver.php
A  vendor/tomatophp/filament-tenancy/src/Middleware/InitializeTenancyByPath.php
A  vendor/tomatophp/filament-tenancy/src/Middleware/AddTenantToUrlGeneration.php
```

---

## ⚠️ نکات مهم

### 1. Tenant Names باید URL-Safe باشند

برای path-based، نام tenant ها باید:
- بدون فاصله
- بدون کاراکترهای خاص
- فقط حروف، اعداد و dash (`-`)

**اضافه کردن validation:**

```php
// در Filament Resource یا Model:
'name' => 'required|string|alpha_dash|unique:tenants,name'
```

### 2. URL Generation

همه URL generation ها باید از helper های Laravel استفاده کنند:
- ✅ `route('name')` - خودکار tenant را اضافه می‌کند
- ✅ `url('/path')` - با URL::defaults() کار می‌کند
- ❌ Hard-coded URLs - ممکن است tenant را نداشته باشد

### 3. Performance

- PathTenantResolver از cache استفاده نمی‌کند (می‌توان فعال کرد)
- هر request یک SELECT query برای tenant دارد
- برای production، caching را فعال کنید:

```php
// در PathTenantResolver:
public static $shouldCache = true;
public static $cacheTTL = 3600; // 1 hour
```

### 4. Security

- Tenant name در URL visible است
- اگر می‌خواهید tenant ها private باشند، از subdomain استفاده کنید
- همیشه authentication را چک کنید

---

## 🐛 Troubleshooting

### مشکل: 404 Not Found

**علت:** Routes ثبت نشده یا cache
**حل:**
```bash
php artisan route:clear
php artisan optimize:clear
```

### مشکل: Tenant Not Found

**علت:** نام tenant در URL با database مطابقت ندارد
**حل:** چک کنید:
```sql
SELECT id, name FROM tenants WHERE name = 'your-tenant-name';
```

### مشکل: Panel Switcher به `%7Btenant%7D` می‌رود

**علت:** `Filament::serving()` callback اجرا نشده
**حل:** Cache را clear کنید یا check کنید که کد در AppServiceProvider درست است

### مشکل: PreventAccessFromCentralDomains error

**علت:** middleware برای path-based نباید فعال باشد
**حل:** check کنید که در `FilamentTenancyAppPlugin` به درستی conditional شده باشد

---

## 📝 Checklist برای آینده

اگر بخواهید تغییراتی بدهید:

- [ ] آیا می‌خواهید به subdomain برگردید؟ → تغییر config و revert vendor
- [ ] آیا می‌خواهید همزمان path و subdomain کار کنند؟ → نیاز به تغییرات عمیق‌تر
- [ ] آیا می‌خواهید validation برای tenant names اضافه کنید؟
- [ ] آیا می‌خواهید caching را فعال کنید؟
- [ ] آیا می‌خواهید custom domain برای هر tenant؟ → از `domain` method استفاده کنید

---

## 🎯 نتیجه‌گیری

این پیاده‌سازی:
- ✅ از SSL subdomain مشکل نمی‌سازد
- ✅ همه tenant ها از یک domain قابل دسترسی هستند
- ✅ با Filament کامل کار می‌کند
- ✅ Panel switching صحیح عمل می‌کند
- ✅ URL generation به درستی tenant را شامل می‌شود
- ✅ قابل تغییر به subdomain یا domain
- ✅ تغییرات پکیج TomatoPHP در git قابل مشاهده و revert هستند

---

**تاریخ پیاده‌سازی:** 23 اکتبر 2025  
**نسخه Laravel:** 12.35.0  
**نسخه PHP:** 8.3.26  
**Tenant Package:** tenancyforlaravel v3  
**Filament Package:** tomatophp/filament-tenancy

