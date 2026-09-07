<?php

namespace App\Filament\Resources\PatientMedia\Pages;

use App\Filament\Resources\PatientMedia\PatientMediaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPatientMedia extends EditRecord
{
    protected static string $resource = PatientMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
