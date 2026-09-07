<?php

namespace App\Filament\Resources\PatientMedia\Pages;

use App\Filament\Resources\PatientMedia\PatientMediaResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePatientMedia extends CreateRecord
{
    protected static string $resource = PatientMediaResource::class;

    public function mount(): void
    {
        parent::mount();

        if (request()->filled('patient_id')) {
            $this->form->fill([
                'patient_id' => (int) request()->query('patient_id'),
            ]);
        }
    }
}
