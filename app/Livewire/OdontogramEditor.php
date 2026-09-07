<?php

namespace App\Livewire;

use App\Livewire\Concerns\AuthorizesPatientAccess;
use App\Models\Patient;
use App\Models\Service;
use App\Support\ServiceCatalogTree;
use App\Models\ToothTreatmentHistory;
use App\Services\Odontogram\ToothStateService;
use App\Support\FdiTooth;
use Filament\Notifications\Notification;
use Livewire\Component;

class OdontogramEditor extends Component
{
    use AuthorizesPatientAccess;

    /**
     * @internal ممرَّر من غلاف Filament Schema ولا يُستخدم مباشرةً
     */
    public mixed $record = null;

    public int $patientId;

    public ?int $selectedFdi = null;

    public ?int $selectedServiceId = null;

    /** @var array<int, string> */
    public array $toothStatuses = [];

    public function mount(int $patientId): void
    {
        $this->authorizePatientAccess($patientId);
        $this->patientId = $patientId;
        $this->refreshTeeth();
    }

    public function refreshTeeth(): void
    {
        $patient = Patient::query()->findOrFail($this->patientId);
        $this->toothStatuses = [];
        foreach (FdiTooth::permanentAdultTeeth() as $fdi) {
            $this->toothStatuses[$fdi] = (string) ($patient->patientTeeth()
                ->where('fdi_number', $fdi)
                ->value('current_status') ?? 'healthy');
        }
    }

    public function selectTooth(int $fdi): void
    {
        if (! FdiTooth::isValid($fdi)) {
            return;
        }
        $this->selectedFdi = $fdi;
    }

    public function applyTreatment(ToothStateService $toothState): void
    {
        if ($this->selectedFdi === null || $this->selectedServiceId === null) {
            Notification::make()->title('اختر سنًا وخدمة')->warning()->send();

            return;
        }

        $service = Service::query()->findOrFail($this->selectedServiceId);
        $patient = Patient::query()->findOrFail($this->patientId);
        $status = ToothStateService::inferStatusFromService($service);

        $toothState->applyTreatment(
            patient: $patient,
            fdiNumber: $this->selectedFdi,
            newStatus: $status,
            userId: (int) auth()->id(),
            serviceId: (int) $service->getKey(),
        );

        $this->refreshTeeth();
        $this->selectedServiceId = null;

        Notification::make()->title('تم حفظ العلاج')->success()->send();
    }

    public function getHistoriesProperty()
    {
        if ($this->selectedFdi === null) {
            return collect();
        }

        return ToothTreatmentHistory::query()
            ->where('patient_id', $this->patientId)
            ->where('fdi_number', $this->selectedFdi)
            ->with(['service', 'user'])
            ->orderByDesc('performed_at')
            ->get();
    }

    public function getUncategorizedServicesProperty()
    {
        return ServiceCatalogTree::uncategorizedServices();
    }

    public function getRootCategoriesProperty()
    {
        return ServiceCatalogTree::rootCategories();
    }

    public function render()
    {
        return view('livewire.odontogram-editor');
    }
}
