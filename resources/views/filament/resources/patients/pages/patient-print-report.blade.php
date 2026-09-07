@php($r = $this->report)

<div
    class="patient-report report-root mx-auto max-w-5xl space-y-6 p-4 text-start print:max-w-none print:p-0"
    dir="rtl"
    style="--clinic-primary: {{ $r->primaryColor }};"
>
    <style>
        .patient-report {
            --clinic-primary-light: color-mix(in srgb, var(--clinic-primary) 12%, white);
            --clinic-primary-muted: color-mix(in srgb, var(--clinic-primary) 25%, white);
            color: #111827;
        }
        .report-header {
            border-bottom: 3px solid var(--clinic-primary);
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }
        .report-logo { max-height: 64px; max-width: 180px; margin-bottom: 0.5rem; }
        .report-title { color: var(--clinic-primary); font-size: 1.5rem; font-weight: 700; margin: 0; }
        .report-muted { color: #4b5563; }
        .report-card {
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 1rem 1.25rem;
            background: #fff;
        }
        .report-stat {
            border-radius: 0.75rem;
            padding: 1rem;
            text-align: center;
            border: 1px solid #e5e7eb;
        }
        .report-stat-due { background: #f9fafb; }
        .report-stat-paid { background: var(--clinic-primary-light); border-color: var(--clinic-primary-muted); }
        .report-stat-balance { background: #fef2f2; border-color: #fecaca; }
        .report-stat-value-paid { color: var(--clinic-primary); }
        .report-stat-value-balance { color: #b91c1c; }
        .report-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        .report-table th {
            background: var(--clinic-primary-light);
            color: var(--clinic-primary);
            font-weight: 600;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            text-align: right;
        }
        .report-table td {
            padding: 0.5rem 0.75rem;
            border: 1px solid #e5e7eb;
            vertical-align: top;
            color: #111827;
        }
        .report-table tbody tr:nth-child(even) { background: #f9fafb; }
        .report-section-title {
            color: var(--clinic-primary);
            font-size: 1.125rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            padding-bottom: 0.25rem;
            border-bottom: 2px solid var(--clinic-primary-muted);
        }
        .report-footer {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px dashed #d1d5db;
            font-size: 0.75rem;
            color: #6b7280;
        }
        .report-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        .report-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
        }
        .report-btn-primary {
            background: var(--clinic-primary);
            color: #fff;
        }
        .report-btn-secondary {
            background: #fff;
            color: #374151;
            border-color: #d1d5db;
        }
        @media print {
            html, body {
                background: #fff !important;
                color: #111 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .fi-simple-layout-header,
            .fi-topbar,
            .fi-sidebar,
            .fi-layout,
            .fi-layout-sidebar,
            .fi-header,
            .fi-footer,
            .fi-no-print,
            [data-filament-action],
            button.fi-btn,
            [x-cloak] {
                display: none !important;
            }
            .fi-simple-main-ctn,
            .fi-simple-main,
            .fi-main,
            .fi-main-ctn,
            .fi-page-content,
            .patient-report,
            .report-root {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .patient-report,
            .patient-report *:not(.report-title):not(.report-section-title):not(.report-stat-value-paid) {
                color: #111827 !important;
            }
            .report-title,
            .report-section-title {
                color: var(--clinic-primary) !important;
            }
            .report-stat-value-paid { color: var(--clinic-primary) !important; }
            .report-stat-value-balance { color: #b91c1c !important; }
            .report-card,
            .report-stat,
            .report-table th,
            .report-table td {
                background: #fff !important;
                border-color: #d1d5db !important;
            }
            .report-table th { background: var(--clinic-primary-light) !important; }
            .report-table tbody tr:nth-child(even) { background: #f9fafb !important; }
            .report-stat-paid { background: var(--clinic-primary-light) !important; }
            .report-stat-balance { background: #fef2f2 !important; }
            .report-card, .report-stat, section { break-inside: avoid; }
        }
    </style>

    <div class="fi-no-print report-toolbar">
        <button type="button" class="report-btn report-btn-primary" onclick="window.print()">
            طباعة
        </button>
        <a
            href="{{ \App\Filament\Resources\Patients\PatientResource::getUrl('view', ['record' => $r->patient]) }}"
            class="report-btn report-btn-secondary"
            wire:navigate
        >
            العودة للملف
        </a>
    </div>

    <div class="fi-no-print rounded-lg border border-amber-200 bg-amber-50 px-4 py-2 text-sm text-amber-900">
        معاينة التقرير — استخدم زر «طباعة» أعلاه أو Ctrl+P.
    </div>

    <header class="report-header">
        @if ($r->logoUrl)
            <img src="{{ $r->logoUrl }}" alt="شعار العيادة" class="report-logo">
        @endif
        <h1 class="report-title">تقرير المريض</h1>
        <div class="mt-2 space-y-1 text-sm report-muted">
            @if ($r->clinic?->clinic_name)
                <div><span class="font-medium">العيادة:</span> {{ $r->clinic->clinic_name }}</div>
            @endif
            @if ($r->clinic?->phone)
                <div><span class="font-medium">هاتف العيادة:</span> {{ $r->clinic->phone }}</div>
            @endif
            @if ($r->clinic?->address)
                <div><span class="font-medium">العنوان:</span> {{ $r->clinic->address }}</div>
            @endif
            <div><span class="font-medium">تاريخ الطباعة:</span> {{ now()->format('Y-m-d H:i') }}</div>
        </div>
    </header>

    <section>
        <h2 class="report-section-title">بيانات المريض</h2>
        <div class="report-card grid gap-3 sm:grid-cols-2">
            <div><span class="report-muted">الاسم:</span> <span class="font-semibold">{{ $r->patient->name }}</span></div>
            <div><span class="report-muted">رقم الملف:</span> <span class="font-medium">{{ $r->patient->file_number ?? '—' }}</span></div>
            <div><span class="report-muted">الهاتف:</span> <span class="font-medium">{{ $r->patient->phone ?? '—' }}</span></div>
            <div><span class="report-muted">الجنس:</span> <span class="font-medium">{{ $r->genderLabel() }}</span></div>
            <div><span class="report-muted">تاريخ الميلاد:</span> <span class="font-medium">{{ $r->patient->birth_date?->format('Y-m-d') ?? '—' }}</span></div>
            @if ($r->notes_excerpt)
                <div class="sm:col-span-2"><span class="report-muted">ملاحظات:</span> {{ $r->notes_excerpt }}</div>
            @endif
        </div>
    </section>

    <section>
        <h2 class="report-section-title">الملخص المالي</h2>
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="report-stat report-stat-due">
                <div class="text-xs report-muted">إجمالي المطلوب</div>
                <div class="mt-1 text-xl font-bold tabular-nums">{{ number_format($r->total_due, 2) }}</div>
            </div>
            <div class="report-stat report-stat-paid">
                <div class="text-xs report-muted">إجمالي المدفوع</div>
                <div class="mt-1 text-xl font-bold tabular-nums report-stat-value-paid">{{ number_format($r->total_paid, 2) }}</div>
            </div>
            <div class="report-stat report-stat-balance">
                <div class="text-xs report-muted">المتبقي</div>
                <div class="mt-1 text-xl font-bold tabular-nums report-stat-value-balance">{{ number_format($r->total_balance, 2) }}</div>
            </div>
        </div>
    </section>

    <section>
        <h2 class="report-section-title">سجل الزيارات</h2>
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>الطبيب</th>
                        <th>الخدمات</th>
                        <th>التشخيص</th>
                        <th>المطلوب</th>
                        <th>المدفوع</th>
                        <th>المتبقي</th>
                        <th>الدفع</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($r->visits as $row)
                        <tr>
                            <td class="tabular-nums whitespace-nowrap">{{ $row->visit_at }}</td>
                            <td>{{ $row->doctor_name }}</td>
                            <td>{{ $row->services_summary }}</td>
                            <td>{{ $row->diagnosis ?? '—' }}</td>
                            <td class="tabular-nums">{{ number_format($row->due, 2) }}</td>
                            <td class="tabular-nums">{{ number_format($row->paid, 2) }}</td>
                            <td class="tabular-nums font-medium report-stat-value-balance">{{ number_format($row->balance, 2) }}</td>
                            <td>{{ $row->payment_label }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-6 text-center report-muted">لا توجد زيارات مسجّلة.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section>
        <h2 class="report-section-title">حالة الأسنان (ملخص)</h2>
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>رقم السن (FDI)</th>
                        <th>الحالة الحالية</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($r->teeth as $tooth)
                        <tr>
                            <td class="font-medium">{{ $tooth['fdi_number'] }}</td>
                            <td>{{ $tooth['current_status'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="py-6 text-center report-muted">لا توجد بيانات أسنان مسجّلة في المخطط.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <footer class="report-footer">
        تقرير داخلي للعيادة — يُستخدم لأغراض المتابعة الطبية والإدارية فقط.
    </footer>
</div>

@if ($this->autoPrint)
    @script
        <script>
            const triggerPrint = () => {
                requestAnimationFrame(() => {
                    setTimeout(() => window.print(), 400);
                });
            };

            if (document.readyState === 'complete') {
                triggerPrint();
            } else {
                window.addEventListener('load', triggerPrint, { once: true });
            }
        </script>
    @endscript
@endif
