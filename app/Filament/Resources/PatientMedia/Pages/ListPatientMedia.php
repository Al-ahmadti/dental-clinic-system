<?php

namespace App\Filament\Resources\PatientMedia\Pages;

use App\Filament\Resources\PatientMedia\PatientMediaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPatientMedia extends ListRecords
{
    protected static string $resource = PatientMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
