<?php

namespace App\Plugins;

use Filament\Panel;
use TomatoPHP\FilamentTenancy\FilamentTenancyPlugin;
use TomatoPHP\FilamentTenancy\Http\Middleware\RedirectIfInertiaMiddleware;

/**
 * Custom Filament Tenancy Plugin
 * این پلاگین resource پیش‌فرض Tomato را غیرفعال می‌کند
 */
class CustomFilamentTenancyPlugin extends FilamentTenancyPlugin
{
    public function register(Panel $panel): void
    {
        // ثبت middleware و domain بدون resource
        $panel
            ->middleware([
                RedirectIfInertiaMiddleware::class,
            ])
            ->persistentMiddleware(['universal'])
            ->domains([
                config('filament-tenancy.central_domain')
            ]);
        
        // عمداً resource را ثبت نمی‌کنیم تا resource سفارشی ما استفاده شود
    }
}

