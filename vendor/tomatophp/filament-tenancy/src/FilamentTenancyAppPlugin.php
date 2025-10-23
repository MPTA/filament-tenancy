<?php

namespace TomatoPHP\FilamentTenancy;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Nwidart\Modules\Module;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use TomatoPHP\FilamentTenancy\Filament\Pages\TenantLogin;
use TomatoPHP\FilamentTenancy\Http\Middleware\ApplyPanelColorsMiddleware;
use TomatoPHP\FilamentTenancy\Http\Middleware\RedirectIfInertiaMiddleware;

class FilamentTenancyAppPlugin implements Plugin
{
    private bool $isActive = false;

    public function getId(): string
    {
        return 'filament-tenancy-app';
    }

    public function register(Panel $panel): void
    {
        if(class_exists(Module::class) && \Nwidart\Modules\Facades\Module::find('FilamentTenancy')?->isEnabled()){
            $this->isActive = true;
        }
        else {
            $this->isActive = true;
        }

        if($this->isActive) {
            $identificationMethod = config('filament-tenancy.identification_method', 'subdomain');
            
            // For path-based, don't use PreventAccessFromCentralDomains
            // because we WANT to access from central domain
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

            // For path-based identification, allow any domain
            // For domain/subdomain, set specific domains
            if ($identificationMethod === 'path') {
                $panel->domain(null);
            } else {
                $domains = tenant()?->domains()->pluck('domain') ?? [];
                $panel->domains($domains);
            }
        }
    }

    public function boot(Panel $panel): void
    {
        // Panel path override is handled in AppServiceProvider via Filament::serving()
    }

    public static function make(): static
    {
        return new static();
    }
}
