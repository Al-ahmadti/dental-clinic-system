@php
    /** @var \App\Filament\Resources\Patients\Pages\CreatePatient $livewire */
    $matches = $livewire->duplicatePatients ?? collect();
@endphp

@if ($matches->isNotEmpty())
    <div class="rounded-lg border border-amber-300 bg-amber-50 p-3 text-sm text-amber-950 dark:border-amber-700 dark:bg-amber-950/30 dark:text-amber-100">
        <p class="mb-2 font-semibold">يوجد مريض قد يكون نفس الشخص:</p>
        <ul class="space-y-2">
            @foreach ($matches as $patient)
                <li class="flex flex-wrap items-center justify-between gap-2 rounded-md bg-white/80 px-2 py-1.5 dark:bg-gray-900/80">
                    <span>
                        {{ $patient->name }}
                        @if ($patient->file_number)
                            <span class="text-gray-500">({{ $patient->file_number }})</span>
                        @endif
                        @if ($patient->phone)
                            — {{ $patient->phone }}
                        @endif
                    </span>
                    <a
                        href="{{ \App\Filament\Resources\Patients\PatientResource::getUrl('view', ['record' => $patient]) }}"
                        class="text-primary-700 underline hover:text-primary-900 dark:text-primary-300"
                        target="_blank"
                    >
                        فتح الملف
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif
