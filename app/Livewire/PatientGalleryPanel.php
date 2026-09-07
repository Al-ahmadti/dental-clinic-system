<?php

namespace App\Livewire;

use App\Livewire\Concerns\AuthorizesPatientAccess;
use App\Models\Patient;
use App\Models\PatientMedia;
use App\Models\Visit;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithFileUploads;

class PatientGalleryPanel extends Component
{
    use AuthorizesPatientAccess;
    use WithFileUploads;

    public int $patientId;

    /** @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $uploads = [];

    public string $uploadKind = PatientMedia::KIND_BEFORE;

    public mixed $uploadVisitId = null;

    public string $filter = 'all';

    public function mount(int $patientId): void
    {
        $this->authorizePatientAccess($patientId);
        $this->patientId = $patientId;
    }

    public function updatedUploads(): void
    {
        $this->validate([
            'uploads.*' => 'nullable|image|max:10240|mimes:jpg,jpeg,png,webp',
        ], [
            'uploads.*.image' => 'يجب أن يكون الملف صورة.',
            'uploads.*.max' => 'حجم الصورة يجب ألا يتجاوز 10 ميغابايت.',
            'uploads.*.mimes' => 'الصيغ المسموحة: JPG, PNG, WEBP.',
        ]);
    }

    public function save(): void
    {
        $this->authorizePatientAccess($this->patientId);

        $this->uploadVisitId = filled($this->uploadVisitId) ? (int) $this->uploadVisitId : null;

        $this->validate([
            'uploads' => 'required|array|min:1',
            'uploads.*' => 'required|image|max:10240|mimes:jpg,jpeg,png,webp',
            'uploadKind' => 'required|in:before,after,radiology',
            'uploadVisitId' => 'nullable|integer',
        ], [
            'uploads.required' => 'اختر صورة واحدة على الأقل.',
            'uploads.*.image' => 'يجب أن يكون الملف صورة.',
            'uploads.*.max' => 'حجم الصورة يجب ألا يتجاوز 10 ميغابايت.',
        ]);

        if ($this->uploadVisitId) {
            $visit = Visit::query()->findOrFail($this->uploadVisitId);
            abort_unless((int) $visit->patient_id === $this->patientId, 403);
        }

        foreach ($this->uploads as $file) {
            $path = $file->store('patient-media', 'local');

            PatientMedia::query()->create([
                'patient_id' => $this->patientId,
                'visit_id' => $this->uploadVisitId,
                'kind' => $this->uploadKind,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
            ]);
        }

        $this->uploads = [];
        $this->uploadVisitId = null;
        $this->uploadKind = PatientMedia::KIND_BEFORE;

        Notification::make()
            ->title('تم رفع الصور')
            ->success()
            ->send();
    }

    public function deleteMedia(int $id): void
    {
        $this->authorizePatientAccess($this->patientId);

        $media = PatientMedia::query()
            ->where('patient_id', $this->patientId)
            ->whereKey($id)
            ->firstOrFail();

        $media->delete();

        Notification::make()
            ->title('تم حذف الصورة')
            ->success()
            ->send();
    }

    public function setFilter(string $filter): void
    {
        if (! in_array($filter, ['all', 'before', 'after', 'radiology'], true)) {
            return;
        }

        $this->filter = $filter;
    }

    public function render(): View
    {
        $allMedia = Patient::query()
            ->findOrFail($this->patientId)
            ->patientMedia()
            ->with('visit')
            ->orderByDesc('created_at')
            ->get();

        $counts = [
            'all' => $allMedia->count(),
            'before' => $allMedia->where('kind', PatientMedia::KIND_BEFORE)->count(),
            'after' => $allMedia->where('kind', PatientMedia::KIND_AFTER)->count(),
            'radiology' => $allMedia->where('kind', PatientMedia::KIND_RADIOLOGY)->count(),
        ];

        $filtered = $this->filter === 'all'
            ? $allMedia
            : $allMedia->where('kind', $this->filter)->values();

        $groups = $this->groupByVisit($filtered);

        $visits = Visit::query()
            ->where('patient_id', $this->patientId)
            ->orderByDesc('visit_at')
            ->get(['id', 'visit_at']);

        $lightboxItems = $filtered->map(fn (PatientMedia $m): array => [
            'id' => $m->id,
            'url' => $m->url(),
            'kind' => $m->kind,
            'kindLabel' => $m->kindLabel(),
            'kindColor' => $m->kindColor(),
            'date' => $m->created_at?->format('Y-m-d H:i') ?? '',
            'visitId' => $m->visit_id,
            'visitLabel' => $m->visit?->visit_at?->format('Y-m-d') ?? 'بدون زيارة',
            'isImage' => $m->isImage(),
            'downloadName' => $m->original_name ?? basename($m->path),
        ])->values()->all();

        return view('livewire.patient-gallery-panel', [
            'groups' => $groups,
            'counts' => $counts,
            'visits' => $visits,
            'lightboxItems' => $lightboxItems,
            'kindOptions' => PatientMedia::kindOptions(),
        ]);
    }

    /**
     * @param  Collection<int, PatientMedia>  $media
     * @return Collection<string, Collection<int, PatientMedia>>
     */
    protected function groupByVisit(Collection $media): Collection
    {
        $withVisit = $media
            ->filter(fn (PatientMedia $m) => $m->visit_id !== null)
            ->groupBy(fn (PatientMedia $m) => (string) $m->visit_id)
            ->sortByDesc(function (Collection $items) {
                return $items->first()?->visit?->visit_at?->timestamp ?? 0;
            });

        $without = $media->filter(fn (PatientMedia $m) => $m->visit_id === null)->values();

        $groups = collect();

        foreach ($withVisit as $visitId => $items) {
            $label = $items->first()?->visit?->visit_at?->format('Y-m-d') ?? $visitId;
            $groups->put('visit:'.$visitId, [
                'label' => 'زيارة '.$label,
                'visit_id' => (int) $visitId,
                'items' => $items->values(),
            ]);
        }

        if ($without->isNotEmpty()) {
            $groups->put('none', [
                'label' => 'بدون زيارة',
                'visit_id' => null,
                'items' => $without,
            ]);
        }

        return $groups;
    }
}
