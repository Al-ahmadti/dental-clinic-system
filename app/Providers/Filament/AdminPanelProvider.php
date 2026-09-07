<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\ClinicStatsOverview;
use App\Models\ClinicSetting;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Schema;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $primary = Color::hex('#2563EB');
        $secondary = Color::hex('#14B8A6');
        $brandName = 'عيادة الأسنان';

        try {
            if (Schema::hasTable('clinic_settings')) {
                $settings = ClinicSetting::query()->first();
                if ($settings?->primary_color) {
                    $primary = Color::hex($settings->primary_color);
                }
                if ($settings?->secondary_color) {
                    $secondary = Color::hex($settings->secondary_color);
                }
                if ($settings?->clinic_name) {
                    $brandName = $settings->clinic_name;
                }
            }
        } catch (\Throwable) {
            // NativePHP bundled PHP has SQLite only; ignore DB probe failures during boot.
        }

        $panel = $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->font('Cairo')
            ->brandName($brandName)
            ->login()
            ->collapsibleNavigationGroups()
            ->navigationGroups([
                NavigationGroup::make('العيادة'),
                NavigationGroup::make('الكتالوج'),
                NavigationGroup::make('المخزون والتوريد')
                    ->collapsed(),
                NavigationGroup::make('الإعدادات'),
            ])
            ->colors([
                'primary' => $primary,
                'secondary' => $secondary,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                ClinicStatsOverview::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);

        try {
            if (Schema::hasTable('clinic_settings')) {
                $settings = ClinicSetting::query()->first();
                if ($settings?->logo_path && file_exists(storage_path('app/public/'.$settings->logo_path))) {
                    $panel = $panel->brandLogo(asset('storage/'.$settings->logo_path));
                }
            }
        } catch (\Throwable) {
            // Ignore logo DB probe failures (e.g. missing PDO driver during early native boot).
        }

        return $panel;
    }
}
