<?php

namespace App\Providers;

use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use BezhanSalleh\PanelSwitch\PanelSwitch;
use Filament\Notifications\Livewire\Notifications;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Controllers\TenantAssetsController;
use TomatoPHP\FilamentTenancy\FilamentTenancyServiceProvider;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configure Filament notifications position (bottom-right corner)
        Notifications::alignment(Alignment::End);
        Notifications::verticalAlignment(VerticalAlignment::End);
        
        // Configure TenantAssetsController middleware for tenant file access
        TenantAssetsController::$tenancyMiddleware = FilamentTenancyServiceProvider::TENANCY_IDENTIFICATION;
        
        // Update public disk URL for current tenant subdomain (fix CORS issues)
        \Illuminate\Support\Facades\Event::listen(
            \Stancl\Tenancy\Events\TenancyInitialized::class,
            function () {
                $appUrl = request()->getSchemeAndHttpHost();
                
                // Forget public disk to force rebuild with new URL
                \Illuminate\Support\Facades\Storage::forgetDisk('public');
                
                // Update public disk URL for current tenant subdomain
                config([
                    'filesystems.disks.public.url' => "{$appUrl}/storage",
                ]);
            }
        );
        
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['en','zh_CN']) // also accepts a closure
                ->excludes([
                    'admin',
                    'base'
                ])
                ->labels([
                    'en' => 'English',
                    'zh_CN' => '中文',
                ]);
        });

        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
            // اگر در tenant context نیستیم (central domain)
            if (!tenant()) {
                // فقط پنل‌های مرکزی را نمایش بده
                $panels = ['base', 'admin'];
            } else {
                // در tenant فقط پنل‌های tenant را نمایش بده
                $panels = ['app', 'tenant-admin'];
            }
            
            $panelSwitch->panels($panels);
        });
    }
}
