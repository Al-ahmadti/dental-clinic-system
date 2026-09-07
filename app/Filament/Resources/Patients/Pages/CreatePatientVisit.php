<?php

namespace App\Filament\Resources\Patients\Pages;

use App\Filament\Resources\Patients\PatientResource;
use App\Livewire\PatientVisitComposer;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;

class CreatePatientVisit extends Page
{
    use InteractsWithRecord;

    protected static string $resource = PatientResource::class;

    protected static ?string $title = 'زيارة جديدة';

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return false;
    }

    protected Width|string|null $maxContentWidth = Width::Full;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        abort_unless(static::getResource()::canView($this->getRecord()), 403);
    }

    public function content(Schema $schema): Schema
    {
        $pid = (int) $this->getRecord()->getKey();

        return $schema
            ->components([
                Livewire::make(PatientVisitComposer::class, fn (): array => [
                    'patientId' => $pid,
                ])->extraAttributes(['class' => 'fi-patient-visit-composer w-full max-w-none']),
            ]);
    }
}
