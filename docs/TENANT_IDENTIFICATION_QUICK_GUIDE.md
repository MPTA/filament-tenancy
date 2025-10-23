# Tenant Identification - Quick Reference Guide

راهنمای سریع برای تغییر روش شناسایی tenant

---

## 🚀 تغییر سریع روش شناسایی

### به Path-Based:

```bash
# در .env یا config/filament-tenancy.php:
TENANT_IDENTIFICATION=path

php artisan optimize:clear
```

**URL ها:**
- App: `https://newtripmaker.test/tenants/balopar/app`
- Admin: `https://newtripmaker.test/tenants/balopar/admin`

---

### به Subdomain:

```bash
TENANT_IDENTIFICATION=subdomain

php artisan optimize:clear
```

**URL ها:**
- App: `https://balopar.newtripmaker.test/app`
- Admin: `https://balopar.newtripmaker.test/admin`

**نیازها:**
- Wildcard SSL certificate (`*.newtripmaker.test`)
- Wildcard DNS record
- در جدول `domains`: subdomain ها (بدون `.newtripmaker.test`)

---

### به Domain:

```bash
TENANT_IDENTIFICATION=domain

php artisan optimize:clear
```

**URL ها:**
- App: `https://custom-domain.com/app`

**نیازها:**
- در جدول `domains`: hostname کامل (مثلاً `balopar.com`)
- DNS برای هر domain
- SSL certificate برای هر domain

---

## 📋 چک‌لیست تغییرات

وقتی روش شناسایی را تغییر می‌دهید:

- [ ] تغییر `TENANT_IDENTIFICATION` در `.env` یا config
- [ ] `php artisan optimize:clear`
- [ ] تست login page
- [ ] تست dashboard
- [ ] تست panel switching
- [ ] چک کردن URL generation در navigation
- [ ] تست logout و login مجدد

---

## ⚡ Commands مفید

```bash
# Clear all caches:
php artisan optimize:clear

# Check current identification method:
php artisan tinker
>>> config('filament-tenancy.identification_method')

# List all routes:
php artisan route:list | grep filament

# Check tenant exists:
php artisan tinker
>>> App\Models\Tenant::where('name', 'balopar')->first()

# Check tenant domains:
php artisan tinker
>>> App\Models\Tenant::where('name', 'balopar')->first()->domains
```

---

## 🐛 مشکلات رایج

### 404 Not Found
```bash
php artisan route:clear
php artisan optimize:clear
# Check route exists:
php artisan route:list | grep "your-route"
```

### Tenant Not Found
```bash
# Check database:
php artisan tinker
>>> App\Models\Tenant::all()->pluck('name')
```

### Panel Switcher خراب
```bash
# Clear browser cache
# Hard refresh: Cmd+Shift+R (Mac) or Ctrl+Shift+R (Windows)
php artisan optimize:clear
```

### Redirect Loop
```bash
# Check middleware order in route:
php artisan route:list --name="your-route-name" --json
# بررسی کنید PreventAccessFromCentralDomains برای path-based نباشد
```

---

## 📞 برای تغییرات آینده

### اگر می‌خواهید همزمان Path و Subdomain:
- **نتیجه:** خیلی پیچیده می‌شود
- **توصیه:** یکی را انتخاب کنید

### اگر می‌خواهید Custom Domain برای tenant:
```php
// در جدول domains:
INSERT INTO domains (domain, tenant_id) VALUES ('custom.com', 'tenant-id');

// config:
TENANT_IDENTIFICATION=domain
```

### اگر می‌خواهید Path prefix را تغییر دهید:
```php
// در routes/tenant.php:
$prefix = config('filament-tenancy.identification_method') === 'path' 
    ? '/clients/{tenant}'  // یا /orgs/{tenant} یا هر چیز دیگر
    : '';
```

سپس panel paths را هم update کنید.

---

## 📚 مستندات مرتبط

- [PATH_BASED_TENANCY.md](./PATH_BASED_TENANCY.md) - مستندات کامل پیاده‌سازی
- [TOMATOPHP_VENDOR_CHANGES.md](./TOMATOPHP_VENDOR_CHANGES.md) - جزئیات تغییرات vendor
- [tenancyforlaravel.com](https://tenancyforlaravel.com/docs/v3/tenant-identification/) - مستندات Stancl Tenancy

---

**آخرین به‌روزرسانی:** 23 اکتبر 2025

