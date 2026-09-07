<?php

namespace App\Filament\Resources\ClinicSettings\Pages;

use App\Filament\Resources\ClinicSettings\ClinicSettingResource;
use App\Models\ClinicSetting;
use Filament\Resources\Pages\ListRecords;

class ListClinicSettings extends ListRecords
{
    protected static string $resource = ClinicSettingResource::class;

    public function mount(): void
    {
        $setting = ClinicSetting::query()->firstOrCreate(
            [],
            [
                'clinic_name' => 'عيادة الأسنان',
                'primary_color' => '#2563EB',
                'secondary_color' => '#14B8A6',
            ]
        );

        $this->redirect(ClinicSettingResource::getUrl('edit', ['record' => $setting]));
    }
}
