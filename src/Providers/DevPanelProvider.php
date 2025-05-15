<?php

namespace TTM\DevPanel\Providers;

use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class DevPanelProvider extends PanelProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/dev-panel.php', 'dev-panel');

        parent::register();
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('devPanel')
            ->path(config('dev-panel.path'))
            ->discoverResources(in: app()->bootstrapPath().'/cache/filament-resources', for: 'App\\Filament\\Resources')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware(config('dev-panel.auth_middleware'));
    }
}
