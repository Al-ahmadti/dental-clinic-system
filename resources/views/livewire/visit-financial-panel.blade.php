<div class="visit-financial-panel mb-8 space-y-4 text-start" dir="rtl" wire:key="visit-fin-{{ $visitId }}">
    <h2 class="text-base font-semibold text-gray-900 dark:text-white">الدفعات والملخص</h2>

    <div class="grid gap-3 sm:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="text-sm text-gray-500 dark:text-gray-400">تكلفة العلاج</div>
            <div class="mt-1 text-xl font-bold tabular-nums text-gray-900 dark:text-white">{{ number_format($totalDue, 2) }}</div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="text-sm text-gray-500 dark:text-gray-400">المدفوع</div>
            <div class="mt-1 text-xl font-bold tabular-nums text-primary-700 dark:text-primary-400">{{ number_format($totalPaid, 2) }}</div>
        </div>
        <div
            @class([
                'rounded-xl border p-4 shadow-sm',
                'border-red-300 bg-red-50 dark:border-red-800 dark:bg-red-950/40' => $balanceDue > 0,
                'border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-950/30' => $balanceDue <= 0,
            ])
        >
            <div class="text-sm font-medium text-gray-600 dark:text-gray-300">المتبقي</div>
            <div
                @class([
                    'mt-1 text-xl font-bold tabular-nums',
                    'text-red-700 dark:text-red-400' => $balanceDue > 0,
                    'text-emerald-800 dark:text-emerald-300' => $balanceDue <= 0,
                ])
            >
                {{ number_format($balanceDue, 2) }}
            </div>
        </div>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2">
            <button
                type="button"
                wire:click="setPaymentFilter(null)"
                @class([
                    'rounded-lg px-3 py-1.5 text-sm font-medium',
                    $paymentFilter === null
                        ? 'bg-primary-600 text-white'
                        : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200',
                ])
            >
                الكل ({{ $visit->payments->count() }})
            </button>
            <button
                type="button"
                wire:click="setPaymentFilter('cash')"
                @class([
                    'rounded-lg px-3 py-1.5 text-sm font-medium',
                    $paymentFilter === 'cash'
                        ? 'bg-primary-600 text-white'
                        : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200',
                ])
            >
                كاش
            </button>
            <button
                type="button"
                wire:click="setPaymentFilter('check')"
                @class([
                    'rounded-lg px-3 py-1.5 text-sm font-medium',
                    $paymentFilter === 'check'
                        ? 'bg-primary-600 text-white'
                        : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200',
                ])
            >
                شيك
            </button>
        </div>

        <button
            type="button"
            wire:click="openPaymentModal"
            class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-primary-600 text-2xl font-light text-white shadow-lg ring-2 ring-primary-200 hover:bg-primary-700 dark:ring-primary-900"
            title="إضافة دفعة"
        >
            +
        </button>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
        @if ($paymentsList->isEmpty())
            <div class="flex flex-col items-center justify-center gap-2 py-16 text-gray-500 dark:text-gray-400">
                <svg class="h-14 w-14 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>لا توجد دفعات مسجلة</span>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                <thead class="bg-primary-600 text-white">
                    <tr>
                        <th class="px-4 py-2 text-end font-semibold">#</th>
                        <th class="px-4 py-2 text-end font-semibold">المبلغ</th>
                        <th class="px-4 py-2 text-end font-semibold">الطريقة</th>
                        <th class="px-4 py-2 text-end font-semibold">تاريخ الدفع</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($paymentsList as $p)
                        <tr wire:key="pay-{{ $p->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-800/80">
                            <td class="px-4 py-2 tabular-nums text-gray-700 dark:text-gray-300">{{ $p->id }}</td>
                            <td class="px-4 py-2 font-medium tabular-nums">{{ number_format((float) $p->amount, 2) }}</td>
                            <td class="px-4 py-2">
                                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs dark:bg-gray-800">{{ $p->payment_method }}</span>
                                @if ($p->check_number)
                                    <span class="text-xs text-gray-500">({{ $p->check_number }})</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ $p->paid_at?->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @if ($showPaymentModal)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
            wire:click.self="closePaymentModal"
            role="dialog"
            aria-modal="true"
        >
            <div
                class="w-full max-w-lg rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-700 dark:bg-gray-900"
                @click.stop
            >
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">إضافة دفعة</h3>
                    <button type="button" wire:click="closePaymentModal" class="text-2xl leading-none text-red-600 hover:text-red-800">
                        &times;
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">المريض</span>
                        <div class="font-medium text-gray-900 dark:text-white">{{ $visit->patient?->name ?? '—' }}</div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">المبلغ المتبقي</label>
                            <input
                                type="text"
                                readonly
                                value="{{ number_format($balanceDue, 2) }}"
                                class="fi-input w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                            />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">قيمة الدفعة</label>
                            <input
                                type="number"
                                step="0.01"
                                min="0.01"
                                wire:model="payment_amount"
                                class="fi-input w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                            />
                            @error('payment_amount')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">تاريخ الدفع</label>
                        <input
                            type="datetime-local"
                            wire:model="paid_at"
                            class="fi-input w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                        />
                        @error('paid_at')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">طريقة الدفع</label>
                        <select
                            wire:model.live="payment_method"
                            class="fi-input w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                        >
                            <option value="cash">كاش</option>
                            <option value="card">بطاقة</option>
                            <option value="transfer">تحويل</option>
                            <option value="check">شيك</option>
                        </select>
                    </div>

                    @if ($payment_method === 'check')
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">رقم الشيك</label>
                            <input
                                type="text"
                                wire:model="check_number"
                                class="fi-input w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white"
                            />
                            @error('check_number')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <div class="flex justify-end gap-2 pt-2">
                        <button
                            type="button"
                            wire:click="closePaymentModal"
                            class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800"
                        >
                            إلغاء
                        </button>
                        <button
                            type="button"
                            wire:click="savePayment"
                            wire:loading.attr="disabled"
                            class="rounded-lg bg-primary-600 px-5 py-2 text-sm font-semibold text-white hover:bg-primary-700 disabled:opacity-50"
                        >
                            حفظ
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
