<div class="patient-visit-composer mx-auto max-w-6xl space-y-6 px-2 py-4 text-start" dir="rtl">
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="order-2 lg:order-1">
            <x-odontogram-svg-chart
                :toothStatuses="$toothStatuses"
                :selectedFdi="$selectedChartFdi"
                wireClickMethod="addLineForTooth"
                chartId="patient-visit"
                :compact="false"
            />
        </div>

        <div class="order-1 space-y-4 lg:order-2">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">تاريخ ووقت الزيارة</label>
                <input
                    type="datetime-local"
                    wire:model="visit_at"
                    class="fi-input block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                />
                @error('visit_at')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">الوصف / التشخيص</label>
                <input
                    type="text"
                    wire:model="diagnosis"
                    class="fi-input block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                    placeholder="مثال: خلع، حشو، تنظيف..."
                />
            </div>

            <div class="flex flex-wrap gap-2">
                <button
                    type="button"
                    wire:click="addLineWithoutTooth"
                    class="rounded-lg border border-primary-600 bg-white px-3 py-1.5 text-sm font-medium text-primary-800 hover:bg-primary-50 dark:border-primary-500 dark:text-primary-200 dark:hover:bg-primary-950/40"
                >
                    سطر بدون سن (فم كامل)
                </button>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800/80">
                        <tr>
                            <th class="px-3 py-2 text-end font-semibold text-gray-700 dark:text-gray-200">FDI</th>
                            <th class="px-3 py-2 text-end font-semibold text-gray-700 dark:text-gray-200">الخدمة</th>
                            <th class="px-3 py-2 text-end font-semibold text-gray-700 dark:text-gray-200">السعر</th>
                            <th class="px-3 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white dark:divide-gray-800 dark:bg-gray-900">
                        @forelse ($lines as $i => $line)
                            <tr wire:key="line-{{ $i }}">
                                <td class="px-3 py-2 tabular-nums text-gray-800 dark:text-gray-200">
                                    {{ $line['fdi_number'] ?? '—' }}
                                </td>
                                <td class="max-w-sm px-3 py-2 align-top">
                                    <div class="rounded-lg border border-gray-200 bg-gray-50/50 px-2 py-2 dark:border-gray-600 dark:bg-gray-950/40">
                                        @if (! empty($line['service_label']))
                                            <p class="line-clamp-2 text-sm font-medium text-gray-900 dark:text-white">{{ $line['service_label'] }}</p>
                                        @else
                                            <p class="text-sm text-gray-500 dark:text-gray-400">لم يُختر علاج</p>
                                        @endif
                                        <button
                                            type="button"
                                            wire:click="openServicePicker({{ $i }})"
                                            class="mt-2 w-full rounded-lg border border-primary-600 bg-white px-2 py-1.5 text-xs font-medium text-primary-800 hover:bg-primary-50 dark:border-primary-500 dark:bg-gray-900 dark:text-primary-200 dark:hover:bg-primary-950/40"
                                        >
                                            {{ ! empty($line['service_id']) ? 'تغيير العلاج' : 'اختر العلاج' }}
                                        </button>
                                    </div>
                                    @error('lines.'.$i.'.service_id')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                                <td class="px-3 py-2">
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        wire:model.live="lines.{{ $i }}.line_price"
                                        class="fi-input w-28 rounded-lg border border-gray-300 bg-white px-2 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                                    />
                                    @error('lines.'.$i.'.line_price')
                                        <p class="text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                                <td class="px-3 py-2 text-end">
                                    <button
                                        type="button"
                                        wire:click="removeLine({{ $i }})"
                                        class="text-sm text-red-600 underline decoration-dotted hover:text-red-800"
                                    >
                                        حذف
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    انقر على أسنان في المخطط أو أضف سطراً بدون سن.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (count($lines) > 0)
                <div class="flex items-center justify-between rounded-lg bg-primary-50 px-4 py-3 text-primary-950 dark:bg-primary-950/30 dark:text-primary-50">
                    <span class="font-semibold">الإجمالي</span>
                    <span class="text-lg font-bold tabular-nums">{{ number_format($this->linesTotal, 2) }}</span>
                </div>
            @endif

            <div class="flex flex-wrap items-center gap-3 pt-2">
                <button
                    type="button"
                    wire:click="saveAndClose"
                    wire:loading.attr="disabled"
                    wire:target="saveAndClose"
                    @disabled(count($lines) === 0)
                    class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-6 py-2.5 text-sm font-semibold text-white shadow hover:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="saveAndClose">حفظ</span>
                    <span wire:loading wire:target="saveAndClose">جاري الحفظ…</span>
                </button>
                <button
                    type="button"
                    wire:click="saveAndPay"
                    wire:loading.attr="disabled"
                    wire:target="saveAndPay"
                    @disabled(count($lines) === 0)
                    class="inline-flex items-center justify-center rounded-xl border-2 border-primary-600 bg-white px-6 py-2.5 text-sm font-semibold text-primary-800 hover:bg-primary-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-primary-500 dark:bg-gray-900 dark:text-primary-200 dark:hover:bg-primary-950/40"
                >
                    <span wire:loading.remove wire:target="saveAndPay">حفظ ودفع</span>
                    <span wire:loading wire:target="saveAndPay">جاري الحفظ…</span>
                </button>
            </div>
        </div>
    </div>

    @if ($servicePickerLineIndex !== null)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            wire:key="service-picker-modal"
            wire:click.self="closeServicePicker"
        >
            <div
                class="max-h-[88vh] w-full max-w-lg overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-2xl dark:border-gray-600 dark:bg-gray-900"
                wire:click.stop
            >
                <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">اختيار العلاج</h3>
                    <button
                        type="button"
                        wire:click="closeServicePicker"
                        class="rounded-lg p-1 text-gray-500 hover:bg-gray-100 hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-white"
                    >
                        <span class="sr-only">إغلاق</span>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="max-h-[calc(88vh-4rem)] space-y-3 overflow-y-auto p-4">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">بحث سريع</label>
                        <input
                            type="search"
                            wire:model.live.debounce.300ms="serviceSearch"
                            placeholder="اكتب جزءاً من اسم العلاج أو التصنيف…"
                            class="fi-input block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-950 dark:text-white"
                        />
                    </div>
                    @if (count($this->filteredServicePickerOptions) > 0)
                        <div class="rounded-xl border border-primary-100 bg-primary-50/40 p-2 dark:border-primary-900 dark:bg-primary-950/20">
                            <p class="mb-2 text-xs font-medium text-primary-900 dark:text-primary-100">نتائج البحث</p>
                            <ul class="max-h-40 space-y-1 overflow-y-auto text-sm">
                                @foreach ($this->filteredServicePickerOptions as $sid => $label)
                                    <li wire:key="search-hit-{{ $sid }}">
                                        <button
                                            type="button"
                                            wire:click="pickServiceFromModal({{ $sid }})"
                                            class="w-full rounded-md px-2 py-1.5 text-start hover:bg-white dark:hover:bg-gray-800"
                                        >
                                            {{ $label }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <p class="text-center text-xs text-gray-500 dark:text-gray-400">أو تصفّح التصنيفات أدناه</p>
                    @endif
                    <x-service-catalog-tree
                        :categories="$this->rootCategories"
                        :uncategorized="$this->uncategorizedServices"
                        variant="buttons"
                        :selectedServiceId="$lines[$servicePickerLineIndex]['service_id'] ?? null"
                    />
                </div>
            </div>
        </div>
    @endif
</div>
