@php
    $badgeClasses = [
        'primary' => 'bg-blue-100 text-blue-800 dark:bg-blue-950/50 dark:text-blue-200',
        'secondary' => 'bg-teal-100 text-teal-800 dark:bg-teal-950/50 dark:text-teal-200',
        'slate' => 'bg-slate-200 text-slate-800 dark:bg-slate-700 dark:text-slate-100',
    ];
@endphp

<div
    class="space-y-6 text-start"
    dir="rtl"
    x-data="{
        open: false,
        compare: false,
        zoom: false,
        index: 0,
        items: @js($lightboxItems),
        openAt(i) {
            this.index = i;
            this.compare = false;
            this.zoom = false;
            this.open = true;
        },
        openById(id) {
            const i = this.items.findIndex(x => x.id === id);
            if (i >= 0) this.openAt(i);
        },
        next() {
            if (! this.items.length) return;
            this.index = (this.index + 1) % this.items.length;
            this.zoom = false;
        },
        prev() {
            if (! this.items.length) return;
            this.index = (this.index - 1 + this.items.length) % this.items.length;
            this.zoom = false;
        },
        current() {
            return this.items[this.index] || null;
        },
        comparePair() {
            const cur = this.current();
            if (! cur) return { before: null, after: null };
            const sameVisit = (a, b) => a.visitId && b.visitId && a.visitId === b.visitId;
            let before = this.items.find(x => x.kind === 'before' && (sameVisit(x, cur) || ! cur.visitId));
            let after = this.items.find(x => x.kind === 'after' && (sameVisit(x, cur) || ! cur.visitId));
            if (! before) before = this.items.find(x => x.kind === 'before') || null;
            if (! after) after = this.items.find(x => x.kind === 'after') || null;
            return { before, after };
        }
    }"
    @keydown.escape.window="open = false"
>
    {{-- Upload --}}
    <div class="rounded-2xl border border-primary-100 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <h3 class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100">إضافة صور</h3>

        <div class="mb-3 grid gap-3 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">النوع</label>
                <select wire:model="uploadKind" class="w-full rounded-xl border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-800">
                    @foreach ($kindOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">الزيارة (اختياري)</label>
                <select wire:model="uploadVisitId" class="w-full rounded-xl border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-800">
                    <option value="">بدون زيارة</option>
                    @foreach ($visits as $visit)
                        <option value="{{ $visit->id }}">{{ $visit->visit_at?->format('Y-m-d H:i') }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <label
            class="flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-primary-300 bg-primary-50/40 px-4 py-8 text-center transition hover:bg-primary-50 dark:border-primary-700 dark:bg-primary-950/20 dark:hover:bg-primary-950/40"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mb-2 h-10 w-10 text-primary-600 dark:text-primary-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
            </svg>
            <span class="text-sm font-medium text-gray-800 dark:text-gray-100">اسحب الصور هنا أو اضغط للاختيار</span>
            <span class="mt-1 text-xs text-gray-500">JPG, PNG, WEBP — حتى 10 ميغابايت لكل صورة</span>
            <input type="file" wire:model="uploads" multiple accept="image/jpeg,image/png,image/webp" class="hidden" />
        </label>

        <div wire:loading wire:target="uploads" class="mt-2 text-xs text-primary-700 dark:text-primary-300">جاري تجهيز الملفات…</div>

        @error('uploads') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
        @error('uploads.*') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror

        @if (count($uploads))
            <p class="mt-2 text-xs text-gray-600 dark:text-gray-300">تم اختيار {{ count($uploads) }} ملف/ملفات</p>
        @endif

        <div class="mt-4 flex justify-start">
            <button
                type="button"
                wire:click="save"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-primary-700 disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="save">إضافة صور</span>
                <span wire:loading wire:target="save">جاري الرفع…</span>
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap gap-2">
        @foreach ([
            'all' => 'الكل',
            'before' => 'قبل العلاج',
            'after' => 'بعد العلاج',
            'radiology' => 'أشعة',
        ] as $key => $label)
            <button
                type="button"
                wire:click="setFilter('{{ $key }}')"
                @class([
                    'rounded-full px-3 py-1.5 text-xs font-semibold transition',
                    'bg-primary-600 text-white' => $filter === $key,
                    'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700' => $filter !== $key,
                ])
            >
                {{ $label }} ({{ $counts[$key] ?? 0 }})
            </button>
        @endforeach
    </div>

    {{-- Grid --}}
    @if ($groups->isEmpty())
        <p class="rounded-2xl border border-dashed border-gray-300 p-10 text-center text-sm text-gray-500 dark:border-gray-600 dark:text-gray-400">
            لا توجد صور بعد — ابدأ برفع صورة قبل/بعد أو أشعة
        </p>
    @else
        <div class="space-y-8">
            @foreach ($groups as $groupKey => $group)
                <section wire:key="group-{{ $groupKey }}">
                    <h4 class="mb-3 text-sm font-bold text-gray-800 dark:text-gray-100">{{ $group['label'] }}</h4>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @foreach ($group['items'] as $m)
                            @php
                                $flatIndex = collect($lightboxItems)->search(fn ($item) => (int) $item['id'] === (int) $m->id);
                                $badge = $badgeClasses[$m->kindColor()] ?? $badgeClasses['slate'];
                            @endphp
                            <div
                                class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900"
                                wire:key="media-{{ $m->id }}"
                            >
                                <button
                                    type="button"
                                    class="aspect-square w-full bg-gray-100 dark:bg-gray-800"
                                    @if ($m->isImage() && $flatIndex !== false)
                                        @click="openAt({{ $flatIndex }})"
                                    @endif
                                >
                                    @if ($m->isImage())
                                        <img src="{{ $m->url() }}" alt="{{ $m->kindLabel() }}" class="h-full w-full object-cover" loading="lazy" />
                                    @else
                                        <span class="flex h-full items-center justify-center text-sm text-primary-700 underline dark:text-primary-300">فتح الملف</span>
                                    @endif
                                </button>
                                <div class="space-y-2 p-3">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $badge }}">{{ $m->kindLabel() }}</span>
                                        <span class="text-[11px] text-gray-500 dark:text-gray-400">{{ $m->created_at?->format('Y-m-d') }}</span>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        @if ($m->isImage())
                                            <button type="button" class="rounded-lg border border-gray-200 px-2 py-1 text-[11px] font-medium hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-gray-800" @click="openById({{ $m->id }})">تكبير</button>
                                        @endif
                                        <a href="{{ $m->url() }}" download="{{ $m->original_name ?? basename($m->path) }}" class="rounded-lg border border-gray-200 px-2 py-1 text-[11px] font-medium hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-gray-800">تحميل</a>
                                        <button
                                            type="button"
                                            wire:click="deleteMedia({{ $m->id }})"
                                            wire:confirm="هل تريد حذف هذه الصورة؟ لا يمكن التراجع"
                                            class="rounded-lg border border-red-200 px-2 py-1 text-[11px] font-medium text-red-700 hover:bg-red-50 dark:border-red-900 dark:text-red-300 dark:hover:bg-red-950/40"
                                        >حذف</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    @endif

    {{-- Lightbox --}}
    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex flex-col bg-black/90"
        style="display: none;"
        @click.self="open = false"
    >
        <div class="flex items-center justify-between gap-2 px-4 py-3 text-white">
            <div class="text-sm" x-text="current() ? (current().kindLabel + ' — ' + current().date) : ''"></div>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" class="rounded-lg bg-white/10 px-3 py-1.5 text-xs font-semibold hover:bg-white/20" @click="compare = !compare; zoom = false" x-text="compare ? 'عرض عادي' : 'مقارنة قبل/بعد'"></button>
                <button type="button" class="rounded-lg bg-white/10 px-3 py-1.5 text-xs font-semibold hover:bg-white/20" @click="zoom = !zoom" x-show="!compare">تكبير</button>
                <button type="button" class="rounded-lg bg-white/10 px-3 py-1.5 text-xs font-semibold hover:bg-white/20" @click="prev()" x-show="!compare">السابق</button>
                <button type="button" class="rounded-lg bg-white/10 px-3 py-1.5 text-xs font-semibold hover:bg-white/20" @click="next()" x-show="!compare">التالي</button>
                <button type="button" class="rounded-lg bg-white/10 px-3 py-1.5 text-xs font-semibold hover:bg-white/20" @click="open = false">إغلاق</button>
            </div>
        </div>

        <div class="flex flex-1 items-center justify-center overflow-auto p-4" x-show="!compare">
            <template x-if="current() && current().isImage">
                <img
                    :src="current().url"
                    :alt="current().kindLabel"
                    class="max-h-[85vh] max-w-full transition duration-200"
                    :class="zoom ? 'scale-150 object-contain cursor-zoom-out' : 'object-contain cursor-zoom-in'"
                    @click="zoom = !zoom"
                />
            </template>
        </div>

        <div class="grid flex-1 grid-cols-1 gap-4 overflow-auto p-4 md:grid-cols-2" x-show="compare" x-cloak>
            <div class="flex flex-col items-center rounded-xl bg-white/5 p-3">
                <p class="mb-2 text-xs font-semibold text-blue-200">قبل العلاج</p>
                <template x-if="comparePair().before">
                    <img :src="comparePair().before.url" class="max-h-[70vh] max-w-full object-contain" alt="قبل" />
                </template>
                <p class="mt-2 text-xs text-white/60" x-show="!comparePair().before">لا توجد صورة قبل</p>
            </div>
            <div class="flex flex-col items-center rounded-xl bg-white/5 p-3">
                <p class="mb-2 text-xs font-semibold text-teal-200">بعد العلاج</p>
                <template x-if="comparePair().after">
                    <img :src="comparePair().after.url" class="max-h-[70vh] max-w-full object-contain" alt="بعد" />
                </template>
                <p class="mt-2 text-xs text-white/60" x-show="!comparePair().after">لا توجد صورة بعد</p>
            </div>
        </div>
    </div>
</div>
