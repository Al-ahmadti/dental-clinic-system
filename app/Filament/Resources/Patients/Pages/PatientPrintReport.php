<?php

namespace App\Filament\Resources\Patients\Pages;

use App\Filament\Resources\Patients\PatientResource;
use App\Services\Patients\PatientReportBuilder;
use App\Services\Patients\PatientReportData;
use Filament\Actions\Action;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class PatientPrintReport extends Page
{
    use InteractsWithRecord;

    protected static string $resource = PatientResource::class;

    protected static ?string $title = 'تقرير المريض';

    protected string $view = 'filament.resources.patients.pages.patient-print-report';

    protected static string $layout = 'filament-panels::components.layout.simple';

    public bool $autoPrint = false;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        abort_unless(static::getResource()::canView($this->getRecord()), 403);

        $this->autoPrint = request()->boolean('print');
    }

    public function getHeading(): string|Htmlable
    {
        return 'تقرير المريض — '.$this->getRecord()->name;
    }

    public function getReportProperty(): PatientReportData
    {
        return app(PatientReportBuilder::class)->build($this->getRecord());
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('طباعة')
                ->icon('heroicon-o-printer')
                ->alpineClickHandler('window.print()'),
            Action::make('back')
                ->label('العودة للملف')
                ->url(fn (): string => PatientResource::getUrl('view', ['record' => $this->getRecord()]))
                ->color('gray'),
        ];
    }
}
