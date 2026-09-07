<div class="space-y-4 text-start" dir="rtl">
    @forelse ($visits as $visit)
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900" wire:key="visit-card-{{ $visit->id }}">
            <div class="flex flex-wrap items-start justify-between gap-2">
                <div>
                    <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $visit->visit_at?->format('Y-m-d H:i') }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $visit->user?->name ?? '—' }}</div>
                </div>
                <a
                    href="{{ \App\Filament\Resources\Visits\VisitResource::getUrl('edit', ['record' => $visit]) }}"
                    class="rounded-lg border border-primary-600 px-3 py-1.5 text-sm font-medium text-primary-800 hover:bg-primary-50 dark:border-primary-500 dark:text-primary-200 dark:hover:bg-primary-950/40"
                    wire:navigate
                >
                    تعديل
                </a>
            </div>
            <p class="mt-2 text-sm text-gray-700 dark:text-gray-200">
                <span class="font-medium">الخدمات:</span> {{ \App\Livewire\PatientVisitsTimeline::servicesSummary($visit) }}
            </p>
            @php
                $due = round($visit->totalLineAmount(), 2);
                $paid = round($visit->totalPayments(), 2);
                $balance = round($visit->balanceDue(), 2);
            @endphp
            <div class="mt-3 grid gap-2 text-sm sm:grid-cols-3">
                <div><span class="text-gray-500">المطلوب:</span> <span class="font-medium tabular-nums">{{ number_format($due, 2) }}</span></div>
                <div><span class="text-gray-500">المدفوع:</span> <span class="font-medium tabular-nums">{{ number_format($paid, 2) }}</span></div>
                <div><span class="text-gray-500">المتبقي:</span> <span class="font-medium tabular-nums text-red-600 dark:text-red-400">{{ number_format($balance, 2) }}</span></div>
            </div>
            <div class="mt-2 flex flex-wrap items-center gap-2 text-sm">
                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs dark:bg-gray-800">{{ \App\Livewire\PatientVisitsTimeline::paymentLabel($visit) }}</span>
            </div>
        </div>
    @empty
        <p class="rounded-lg border border-dashed border-gray-300 p-8 text-center text-gray-500 dark:border-gray-600 dark:text-gray-400">لا توجد زيارات بعد.</p>
    @endforelse
</div>
