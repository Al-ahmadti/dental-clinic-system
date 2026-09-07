<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Patients\PatientResource;
use App\Services\Clinic\ClinicFinancialStats;
use Filament\Widgets\Widget;
use Illuminate\Contracts\Support\Htmlable;

class OutstandingPatientsWidget extends Widget
{
    protected static bool $isDiscovered = false;

    protected ?string $heading = 'أعلى المرضى بالمبالغ المتبقية';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.outstanding-patients';

    public function getHeading(): string|Htmlable|null
    {
        return $this->heading;
    }

    /**
     * @return array<int, array{patient: \App\Models\Patient, balance: float}>
     */
    public function getRowsProperty(): array
    {
        return app(ClinicFinancialStats::class)
            ->topOutstandingPatients(10)
            ->all();
    }

    public function patientUrl(int $patientId): string
    {
        return PatientResource::getUrl('view', ['record' => $patientId]);
    }
}
