<div class="patient-statement space-y-6 p-4 text-start print:p-2" dir="rtl">
    <style>
        @media print {
            html, body {
                background: #fff !important;
                color: #111 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .fi-no-print,
            .fi-header,
            .fi-sidebar,
            .fi-topbar,
            .fi-footer,
            .fi-layout-sidebar,
            .fi-simple-layout-header,
            [data-filament-action],
            button.fi-btn,
            [x-cloak] { display: none !important; }
            .fi-simple-main-ctn,
            .fi-simple-main,
            .fi-main,
            .fi-main-ctn,
            .fi-page-content,
            .patient-statement {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                max-width: 100% !important;
            }
            .patient-statement,
            .patient-statement * {
                color: #111827 !important;
            }
            .patient-statement .text-red-600,
            .patient-statement .text-red-700 {
                color: #b91c1c !important;
            }
            .patient-statement thead {
                background: #f0fdfa !important;
            }
            .patient-statement table,
            .patient-statement th,
            .patient-statement td {
                border-color: #d1d5db !important;
            }
        }
    </style>

    <div class="fi-no-print rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">من تاريخ</label>
                <input type="date" wire:model.live="from" class="fi-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-950 dark:text-white" />
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">إلى تاريخ</label>
                <input type="date" wire:model.live="to" class="fi-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-950 dark:text-white" />
            </div>
        </div>
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">يُحدَّث الجدول تلقائياً عند تغيير التواريخ. استخدم «طباعة» في الشريط العلوي.</p>
    </div>

    @php($f = $this->financial)
    @php($patient = $this->getRecord())

    <header class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">كشف حساب</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300"><span class="font-medium">المريض:</span> {{ $patient->name }}</p>
        @if ($patient->file_number)
            <p class="text-sm text-gray-600 dark:text-gray-300"><span class="font-medium">رقم الملف:</span> {{ $patient->file_number }}</p>
        @endif
        @if ($patient->phone)
            <p class="text-sm text-gray-600 dark:text-gray-300"><span class="font-medium">الهاتف:</span> {{ $patient->phone }}</p>
        @endif
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            الفترة: {{ $from }} — {{ $to }}
        </p>
    </header>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-2 text-end font-semibold">تاريخ الزيارة</th>
                    <th class="px-4 py-2 text-end font-semibold">المطلوب</th>
                    <th class="px-4 py-2 text-end font-semibold">المدفوع</th>
                    <th class="px-4 py-2 text-end font-semibold">المتبقي</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($f->visits as $visit)
                    @php($due = round($visit->totalLineAmount(), 2))
                    @php($paid = round($visit->totalPayments(), 2))
                    @php($bal = round($visit->balanceDue(), 2))
                    <tr>
                        <td class="px-4 py-2 tabular-nums">{{ $visit->visit_at?->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-2 tabular-nums">{{ number_format($due, 2) }}</td>
                        <td class="px-4 py-2 tabular-nums">{{ number_format($paid, 2) }}</td>
                        <td class="px-4 py-2 tabular-nums font-medium text-red-600 dark:text-red-400">{{ number_format($bal, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">لا توجد زيارات في هذه الفترة.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-primary-50/80 font-semibold dark:bg-primary-950/30">
                <tr>
                    <td class="px-4 py-3 text-end">الإجمالي</td>
                    <td class="px-4 py-3 tabular-nums">{{ number_format($f->total_due, 2) }}</td>
                    <td class="px-4 py-3 tabular-nums">{{ number_format($f->total_paid, 2) }}</td>
                    <td class="px-4 py-3 tabular-nums text-red-700 dark:text-red-300">{{ number_format($f->total_balance, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
