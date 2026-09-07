<?php

namespace App\Filament\Resources\ClinicSettings\Pages;

use App\Filament\Resources\ClinicSettings\ClinicSettingResource;
use Filament\Resources\Pages\EditRecord;

class EditClinicSetting extends EditRecord
{
    protected static string $resource = ClinicSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'تم حفظ إعدادات العيادة';
    }
}
