<?php

namespace App\Filament\Resources\Patients\Pages;

use App\Filament\Resources\Patients\PatientResource;
use App\Livewire\OdontogramEditor;
use App\Livewire\PatientAccountSummary;
use App\Livewire\PatientGalleryPanel;
use App\Livewire\PatientVisitsTimeline;
use Filament\Actions\Action;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class ManagePatientDashboard extends Page
{
    use InteractsWithRecord;

    protected static string $resource = PatientResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        abort_unless(static::getResource()::canView($this->getRecord()), 403);
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->name;
    }

    protected function getHeaderActions(): array
    {
        $patient = $this->getRecord();

        return [
            Action::make('print')
                ->label('طباعة تقرير')
                ->icon('heroicon-o-printer')
                ->url(fn (): string => PatientResource::getUrl('report', [
                    'record' => $this->getRecord(),
                ]).'?print=1')
                ->openUrlInNewTab(),
            Action::make('statement')
                ->label('كشف حساب')
                ->icon('heroicon-o-document-text')
                ->url(fn (): string => PatientResource::getUrl('statement', [
                    'record' => $this->getRecord(),
                ]).'?'.http_build_query([
                    'from' => now()->subMonths(3)->format('Y-m-d'),
                    'to' => now()->format('Y-m-d'),
                ])),
            Action::make('newVisit')
                ->label('زيارة جديدة')
                ->icon('heroicon-o-calendar-days')
                ->url(PatientResource::getUrl('create-visit', ['record' => $patient])),
            Action::make('uploadMedia')
                ->label('رفع صورة')
                ->icon('heroicon-o-photo')
                ->url(fn (): string => PatientResource::getUrl('view', [
                    'record' => $this->getRecord(),
                ]).'?patient_tab=gallery'),
        ];
    }

    public function content(Schema $schema): Schema
    {
        $pid = (int) $this->getRecord()->getKey();

        return $schema
            ->components([
                Tabs::make('patientTabs')
                    ->persistTabInQueryString('patient_tab')
                    ->tabs([
                        Tab::make('المخطط')
                            ->id('odontogram')
                            ->schema([
                                Livewire::make(OdontogramEditor::class, fn (): array => [
                                    'patientId' => $pid,
                                ])->extraAttributes(['class' => 'fi-odontogram-host w-full max-w-none']),
                            ]),
                        Tab::make('الزيارات')
                            ->id('visits')
                            ->schema([
                                Livewire::make(PatientVisitsTimeline::class, fn (): array => [
                                    'patientId' => $pid,
                                ]),
                            ]),
                        Tab::make('الملخص المالي')
                            ->id('account')
                            ->schema([
                                Livewire::make(PatientAccountSummary::class, fn (): array => [
                                    'patientId' => $pid,
                                ]),
                            ]),
                        Tab::make('المعرض')
                            ->id('gallery')
                            ->schema([
                                Livewire::make(PatientGalleryPanel::class, fn (): array => [
                                    'patientId' => $pid,
                                ]),
                            ]),
                    ]),
            ]);
    }
}
