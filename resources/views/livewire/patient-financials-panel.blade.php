<div class="space-y-3 text-start" dir="rtl">
    @forelse ($rows as $row)
        @php($v = $row['visit'])
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="flex flex-wrap items-start justify-between gap-2">
                <div>
                    <div class="font-semibold text-gray-900 dark:text-white">{{ $v->visit_at?->format('Y-m-d H:i') }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $v->diagnosis ?? '—' }}</div>
                </div>
                <a
                    href="{{ \App\Filament\Resources\Visits\VisitResource::getUrl('edit', ['record' => $v]) }}"
                    class="rounded-lg bg-primary-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-primary-700"
                    wire:navigate
                >
                    دفع
                </a>
            </div>
            <div class="mt-3 grid gap-2 text-sm sm:grid-cols-3">
                <div><span class="text-gray-500">المطلوب:</span> <span class="font-medium tabular-nums">{{ number_format($row['due'], 2) }}</span></div>
                <div><span class="text-gray-500">المدفوع:</span> <span class="font-medium tabular-nums">{{ number_format($row['paid'], 2) }}</span></div>
                <div><span class="text-gray-500">المتبقي:</span> <span class="font-medium tabular-nums text-red-600 dark:text-red-400">{{ number_format($row['balance'], 2) }}</span></div>
            </div>
        </div>
    @empty
        <p class="rounded-lg border border-dashed border-gray-300 p-8 text-center text-gray-500 dark:border-gray-600 dark:text-gray-400">لا توجد زيارات مسجلة.</p>
    @endforelse
</div>
