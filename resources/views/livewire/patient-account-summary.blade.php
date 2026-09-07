<div class="space-y-6 text-start" dir="rtl">
    <div class="rounded-xl border border-primary-100 bg-primary-50/50 p-4 dark:border-primary-900 dark:bg-primary-950/20">
        <h3 class="text-sm font-semibold text-primary-900 dark:text-primary-100">إجمالي كل الزيارات</h3>
        <div class="mt-3 grid gap-3 sm:grid-cols-3 text-sm">
            <div><span class="text-gray-600 dark:text-gray-300">المطلوب:</span> <span class="font-bold tabular-nums">{{ number_format($this->totalsLifetime['due'], 2) }}</span></div>
            <div><span class="text-gray-600 dark:text-gray-300">المدفوع:</span> <span class="font-bold tabular-nums">{{ number_format($this->totalsLifetime['paid'], 2) }}</span></div>
            <div><span class="text-gray-600 dark:text-gray-300">المتبقي:</span> <span class="font-bold tabular-nums text-red-600 dark:text-red-400">{{ number_format($this->totalsLifetime['balance'], 2) }}</span></div>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
        <h3 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">ملخص لفترة محددة</h3>
        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-xs text-gray-500">من</label>
                <input type="date" wire:model.live="range_from" class="fi-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-950 dark:text-white" />
            </div>
            <div>
                <label class="mb-1 block text-xs text-gray-500">إلى</label>
                <input type="date" wire:model.live="range_to" class="fi-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-950 dark:text-white" />
            </div>
        </div>
        <div class="mt-4 grid gap-3 sm:grid-cols-3 text-sm">
            <div><span class="text-gray-600 dark:text-gray-300">المطلوب:</span> <span class="font-bold tabular-nums">{{ number_format($this->totalsRange['due'], 2) }}</span></div>
            <div><span class="text-gray-600 dark:text-gray-300">المدفوع:</span> <span class="font-bold tabular-nums">{{ number_format($this->totalsRange['paid'], 2) }}</span></div>
            <div><span class="text-gray-600 dark:text-gray-300">المتبقي:</span> <span class="font-bold tabular-nums text-red-600 dark:text-red-400">{{ number_format($this->totalsRange['balance'], 2) }}</span></div>
        </div>
        <div class="mt-4">
            <a
                href="{{ $this->statementUrl }}"
                class="inline-flex items-center justify-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700"
                wire:navigate
            >
                طباعة كشف حساب لهذه الفترة
            </a>
        </div>
    </div>
</div>
