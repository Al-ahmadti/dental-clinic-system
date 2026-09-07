<?php

namespace App\Filament\Resources\Patients\Pages;

use App\Filament\Resources\Patients\PatientResource;
use App\Services\Patients\PatientFinancialTotals;
use App\Services\Patients\PatientFinancialTotalsResult;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class PatientAccountStatement extends Page
{
    use InteractsWithRecord;

    protected static string $resource = PatientResource::class;

    protected static ?string $title = 'كشف حساب';

    protected string $view = 'filament.resources.patients.pages.patient-account-statement';

    public ?string $from = null;

    public ?string $to = null;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        abort_unless(static::getResource()::canView($this->getRecord()), 403);

        $this->from = request()->query('from', now()->subMonths(3)->format('Y-m-d'));
        $this->to = request()->query('to', now()->format('Y-m-d'));
    }

    public function getHeading(): string|Htmlable
    {
        return 'كشف حساب — '.$this->getRecord()->name;
    }

    public function getFinancialProperty(): PatientFinancialTotalsResult
    {
        $from = Carbon::parse($this->from ?? now()->subMonths(3))->startOfDay();
        $to = Carbon::parse($this->to ?? now())->endOfDay();

        return app(PatientFinancialTotals::class)->forPatient($this->getRecord(), $from, $to);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('طباعة')
                ->icon('heroicon-o-printer')
                ->alpineClickHandler('window.print()'),
            Action::make('downloadPdf')
                ->label('تحميل PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn (): string => route('patients.account-statement.pdf', [
                    'patient' => $this->getRecord(),
                    'from' => $this->from,
                    'to' => $this->to,
                ]))
                ->openUrlInNewTab(),
            Action::make('back')
                ->label('العودة للملف')
                ->url(fn (): string => PatientResource::getUrl('view', ['record' => $this->getRecord()]))
                ->color('gray'),
        ];
    }
}
