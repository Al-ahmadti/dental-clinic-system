<div class="odontogram w-full space-y-6 text-start" wire:key="odontogram-root">
    <div class="flex flex-wrap items-center gap-4 rounded-xl border border-primary-200/80 bg-primary-50/50 px-4 py-3 text-sm text-primary-950 dark:border-primary-800 dark:bg-primary-950/30 dark:text-primary-50">
        <span class="font-semibold">مفتاح الحالة:</span>
        <span class="inline-flex items-center gap-2"><span class="inline-block h-3.5 w-3.5 rounded-sm ring-1 ring-emerald-800" style="background:#34d399"></span> سليم</span>
        <span class="inline-flex items-center gap-2"><span class="inline-block h-3.5 w-3.5 rounded-sm ring-1 ring-red-900" style="background:#f87171"></span> تسوس</span>
        <span class="inline-flex items-center gap-2"><span class="inline-block h-3.5 w-3.5 rounded-sm ring-1 ring-amber-900" style="background:#fcd34d"></span> معالج</span>
        <span class="inline-flex items-center gap-2"><span class="inline-block h-3.5 w-3.5 rounded-sm ring-1 ring-slate-600" style="background:#cbd5e1"></span> مفقود</span>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-gradient-to-b from-white to-slate-50 p-6 shadow-sm dark:border-gray-700 dark:from-gray-900 dark:to-gray-950">
        <x-odontogram-svg-chart
            :toothStatuses="$toothStatuses"
            :selectedFdi="$selectedFdi"
            wireClickMethod="selectTooth"
            chartId="editor"
            :compact="true"
        />
        <p class="mt-2 text-center text-xs leading-relaxed text-gray-500 dark:text-gray-400">
            أشكال الأسنان مأخوذة من
            <a href="https://github.com/biomathcode/react-odontogram" target="_blank" rel="noopener noreferrer" class="text-primary-700 underline decoration-dotted hover:text-primary-900 dark:text-primary-400">react-odontogram</a>
            (MIT) — ألوان الحالة مخصّصة للعيادة.
        </p>
        <p class="mt-1 text-center text-sm text-gray-600 dark:text-gray-400">انقر على أي سن لعرض خيارات العلاج وتسجيلها.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div
            class="rounded-2xl border-2 p-5 shadow-sm transition-colors dark:bg-gray-900 {{ $selectedFdi ? 'border-primary-500 bg-primary-50/40 dark:border-primary-500 dark:bg-primary-950/20' : 'border-dashed border-gray-300 bg-gray-50/80 dark:border-gray-600 dark:bg-gray-950/50' }}"
            wire:key="treat-panel-{{ $selectedFdi ?? 'none' }}"
        >
            <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">
                @if ($selectedFdi)
                    علاج السن <span class="text-primary-700 dark:text-primary-300">{{ $selectedFdi }}</span>
                    <span class="text-sm font-normal text-gray-600 dark:text-gray-400">(FDI)</span>
                @else
                    خيارات العلاج
                @endif
            </h3>

            @if (! $selectedFdi)
                <div class="rounded-xl bg-white/80 py-10 text-center dark:bg-gray-900/80">
                    <p class="text-base text-gray-600 dark:text-gray-300">اختر سنًا من المخطط أعلاه.</p>
                    <p class="mt-2 text-sm text-gray-500">بعد الاختيار تظهر هنا قائمة الخدمات لتطبيقها على ذلك السن.</p>
                </div>
            @else
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">اختر خدمة ثم اضغط «تطبيق على السن».</p>

                <x-service-catalog-tree
                    :categories="$this->rootCategories"
                    :uncategorized="$this->uncategorizedServices"
                    variant="radios"
                    :selectedServiceId="$selectedServiceId"
                />

                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <button
                        type="button"
                        wire:click="applyTreatment"
                        wire:loading.attr="disabled"
                        @disabled(! $selectedFdi || ! $selectedServiceId)
                        class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="applyTreatment">تطبيق على السن</span>
                        <span wire:loading wire:target="applyTreatment">جاري الحفظ…</span>
                    </button>
                    <button
                        type="button"
                        wire:click="$set('selectedFdi', null)"
                        class="text-sm text-gray-600 underline decoration-dotted hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                    >
                        إلغاء اختيار السن
                    </button>
                </div>
            @endif
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <h3 class="mb-3 text-lg font-semibold text-gray-900 dark:text-white">سجل السن</h3>
            @if ($selectedFdi === null)
                <p class="text-sm text-gray-500 dark:text-gray-400">اختر سنًا لعرض الخط الزمني للعلاجات.</p>
            @elseif ($this->histories->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400">لا توجد علاجات مسجلة لهذا السن بعد.</p>
            @else
                <ul class="max-h-96 space-y-2 overflow-y-auto text-sm">
                    @foreach ($this->histories as $h)
                        <li class="rounded-xl border border-gray-100 bg-slate-50/80 px-3 py-2.5 dark:border-gray-700 dark:bg-gray-800/80">
                            <div class="font-medium text-primary-800 dark:text-primary-200">{{ $h->performed_at?->format('Y-m-d H:i') }}</div>
                            <div>{{ $h->service?->name ?? '—' }} — <span class="text-gray-600 dark:text-gray-300">{{ $h->status }}</span></div>
                            @if ($h->doctor_notes)
                                <div class="mt-1 text-gray-600 dark:text-gray-400">{{ $h->doctor_notes }}</div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
