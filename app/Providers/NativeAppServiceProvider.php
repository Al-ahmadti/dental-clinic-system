<?php

namespace App\Providers;

use App\Models\ClinicSetting;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Native\Desktop\Contracts\ProvidesPhpIni;
use Native\Desktop\Facades\Window;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        $this->seedDesktopDefaultsIfNeeded();

        // Electron must load the PHP server host+port from the boot request,
        // not APP_URL without a port (causes blank/black window).
        if (request()->getHttpHost()) {
            URL::forceRootUrl(request()->getSchemeAndHttpHost());
        }

        $title = 'عيادة الأسنان';
        if (Schema::hasTable('clinic_settings')) {
            $title = ClinicSetting::query()->value('clinic_name') ?: $title;
        }

        Window::open()
            ->title($title)
            ->url(url('/admin/login'))
            ->width(1400)
            ->height(900)
            ->minWidth(1024)
            ->minHeight(700)
            ->hideMenu();
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
            'memory_limit' => '512M',
            'max_execution_time' => '0',
            'max_input_time' => '0',
        ];
    }

    protected function seedDesktopDefaultsIfNeeded(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        if (User::query()->exists()) {
            return;
        }

        Artisan::call('db:seed', [
            '--class' => 'NativeDesktopSeeder',
            '--force' => true,
        ]);
    }
}
