<?php

namespace App\Filament\Resources\Patients\Pages;

use App\Filament\Resources\Patients\PatientResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;

class ManagePatientOdontogram extends Page
{
    use InteractsWithRecord;

    protected static string $resource = PatientResource::class;

    protected static ?string $title = 'مخطط الأسنان';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        abort_unless(static::getResource()::canView($this->getRecord()), 403);

        $this->redirect(
            PatientResource::getUrl('view', ['record' => $this->getRecord()]).'?patient_tab=odontogram',
            navigate: true,
        );
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([]);
    }
}
