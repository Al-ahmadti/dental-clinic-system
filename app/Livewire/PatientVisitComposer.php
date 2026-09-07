<?php

namespace App\Livewire;

use App\Livewire\Concerns\AuthorizesPatientAccess;
use App\Filament\Resources\Patients\PatientResource;
use App\Filament\Resources\Visits\VisitResource;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Visit;
use App\Models\VisitLineItem;
use App\Services\Odontogram\ToothStateService;
use App\Support\ServiceCatalogTree;
use App\Support\FdiTooth;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PatientVisitComposer extends Component
{
    use AuthorizesPatientAccess;

    public int $patientId;

    public string $visit_at = '';

    public string $diagnosis = '';

    /** @var array<int, array{fdi_number: int|null, service_id: int|null, line_price: float|int|string, service_label: ?string}> */
    public array $lines = [];

    public ?int $selectedChartFdi = null;

    /** @var array<int, string> */
    public array $toothStatuses = [];

    public ?int $servicePickerLineIndex = null;

    public string $serviceSearch = '';

    public function mount(int $patientId): void
    {
        $this->authorizePatientAccess($patientId);
        $this->patientId = $patientId;
        $this->visit_at = now()->format('Y-m-d\TH:i');
        $this->refreshToothStatuses();
    }

    public function refreshToothStatuses(): void
    {
        $this->toothStatuses = [];
        $patient = Patient::query()->find($this->patientId);
        if (! $patient) {
            return;
        }

        foreach (FdiTooth::permanentAdultTeeth() as $fdi) {
            $this->toothStatuses[$fdi] = (string) ($patient->patientTeeth()
                ->where('fdi_number', $fdi)
                ->value('current_status') ?? 'healthy');
        }
    }

    public function addLineForTooth(int $fdi): void
    {
        if (! FdiTooth::isValid($fdi)) {
            return;
        }

        $this->selectedChartFdi = $fdi;
        $this->lines[] = [
            'fdi_number' => $fdi,
            'service_id' => null,
            'line_price' => 0,
            'service_label' => null,
        ];
    }

    public function addLineWithoutTooth(): void
    {
        $this->lines[] = [
            'fdi_number' => null,
            'service_id' => null,
            'line_price' => 0,
            'service_label' => null,
        ];
    }

    public function removeLine(int $index): void
    {
        $this->closeServicePicker();
        unset($this->lines[$index]);
        $this->lines = array_values($this->lines);
    }

    public function syncLinePrice(int $index): void
    {
        if (! isset($this->lines[$index])) {
            return;
        }

        $serviceId = $this->lines[$index]['service_id'] ?? null;
        if (! $serviceId) {
            return;
        }

        $price = Service::query()->whereKey($serviceId)->value('price');
        $this->lines[$index]['line_price'] = $price !== null ? (float) $price : 0;
    }

    public function getLinesTotalProperty(): float
    {
        $sum = 0.0;
        foreach ($this->lines as $line) {
            $sum += (float) ($line['line_price'] ?? 0);
        }

        return round($sum, 2);
    }

    public function selectServiceForLine(int $index, int $serviceId): void
    {
        if (! isset($this->lines[$index])) {
            return;
        }

        $this->lines[$index]['service_id'] = $serviceId;
        $this->syncLinePrice($index);
        $labels = ServiceCatalogTree::flattenedServiceLabels();
        $this->lines[$index]['service_label'] = $labels[$serviceId]
            ?? (string) Service::query()->whereKey($serviceId)->value('name');
    }

    public function openServicePicker(int $index): void
    {
        if (! isset($this->lines[$index])) {
            return;
        }
        $this->servicePickerLineIndex = $index;
        $this->serviceSearch = '';
    }

    public function closeServicePicker(): void
    {
        $this->servicePickerLineIndex = null;
        $this->serviceSearch = '';
    }

    public function pickServiceFromModal(int $serviceId): void
    {
        if ($this->servicePickerLineIndex === null) {
            return;
        }
        $idx = $this->servicePickerLineIndex;
        $this->selectServiceForLine($idx, $serviceId);
        $this->closeServicePicker();
    }

    public function getFilteredServicePickerOptionsProperty(): array
    {
        $needle = mb_strtolower(trim($this->serviceSearch));
        $all = ServiceCatalogTree::flattenedServiceLabels();
        if ($needle === '') {
            return [];
        }
        $out = [];
        foreach ($all as $id => $label) {
            if (mb_stripos($label, $needle) !== false) {
                $out[$id] = $label;
            }
        }

        return $out;
    }

    public function getRootCategoriesProperty()
    {
        return ServiceCatalogTree::rootCategories();
    }

    public function getUncategorizedServicesProperty()
    {
        return ServiceCatalogTree::uncategorizedServices();
    }

    private function persistVisit(ToothStateService $toothState): ?Visit
    {
        $this->validate([
            'visit_at' => ['required', 'date'],
            'diagnosis' => ['nullable', 'string', 'max:65535'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.service_id' => ['required', 'integer', 'exists:services,id'],
            'lines.*.line_price' => ['required', 'numeric', 'min:0.01'],
            'lines.*.fdi_number' => ['nullable', 'integer'],
        ], [], [
            'visit_at' => 'تاريخ الزيارة',
            'lines' => 'بنود العلاج',
        ]);

        foreach ($this->lines as $i => $line) {
            $fdi = $line['fdi_number'] ?? null;
            if ($fdi !== null && ! FdiTooth::isValid((int) $fdi)) {
                Notification::make()->title('رقم سن غير صالح في السطر '.($i + 1))->danger()->send();

                return null;
            }
        }

        $patient = Patient::query()->findOrFail($this->patientId);
        $userId = (int) auth()->id();

        return DB::transaction(function () use ($patient, $userId, $toothState): Visit {
            $visit = Visit::query()->create([
                'patient_id' => $patient->getKey(),
                'user_id' => $userId,
                'visit_at' => Carbon::parse($this->visit_at),
                'status' => 'completed',
                'diagnosis' => $this->diagnosis !== '' ? $this->diagnosis : null,
                'notes' => null,
            ]);

            foreach ($this->lines as $line) {
                $serviceId = (int) $line['service_id'];
                $unitPrice = round((float) $line['line_price'], 2);
                $fdi = isset($line['fdi_number']) ? (int) $line['fdi_number'] : null;

                VisitLineItem::query()->create([
                    'visit_id' => $visit->getKey(),
                    'fdi_number' => $fdi,
                    'service_id' => $serviceId,
                    'quantity' => 1,
                    'unit_price' => $unitPrice,
                    'notes' => null,
                ]);

                if ($fdi !== null) {
                    $service = Service::query()->findOrFail($serviceId);
                    $toothState->applyTreatment(
                        patient: $patient,
                        fdiNumber: $fdi,
                        newStatus: ToothStateService::inferStatusFromService($service),
                        userId: $userId,
                        serviceId: $serviceId,
                        visitId: (int) $visit->getKey(),
                    );
                }
            }

            return $visit;
        });
    }

    public function saveAndClose(ToothStateService $toothState): void
    {
        $visit = $this->persistVisit($toothState);
        if (! $visit) {
            return;
        }

        Notification::make()->title('تم حفظ الزيارة')->success()->send();

        $url = PatientResource::getUrl('view', ['record' => $this->patientId]).'?patient_tab=visits';

        $this->redirect($url, navigate: true);
    }

    public function saveAndPay(ToothStateService $toothState): void
    {
        $visit = $this->persistVisit($toothState);
        if (! $visit) {
            return;
        }

        Notification::make()->title('تم حفظ الزيارة')->success()->send();

        $url = VisitResource::getUrl('edit', ['record' => $visit]);

        $this->redirect($url, navigate: true);
    }

    public function render(): View
    {
        return view('livewire.patient-visit-composer');
    }
}
