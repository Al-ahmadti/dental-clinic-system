<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>كشف حساب</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; direction: rtl; text-align: right; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #2563EB; padding-bottom: 12px; }
        .logo { max-height: 56px; max-width: 160px; margin-bottom: 8px; }
        h1 { font-size: 18px; margin: 0 0 6px; color: #2563EB; }
        .meta { color: #444; line-height: 1.6; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: right; }
        th { background: #DBEAFE; }
        tfoot td { font-weight: bold; background: #EFF6FF; }
        .totals { margin-top: 12px; }
    </style>
</head>
<body>
    <div class="header">
        @if ($logoPath)
            <img src="{{ $logoPath }}" class="logo" alt="logo">
        @endif
        <h1>كشف حساب</h1>
        <div class="meta">
            @if ($clinic?->clinic_name)
                <div><strong>العيادة:</strong> {{ $clinic->clinic_name }}</div>
            @endif
            @if ($clinic?->phone)
                <div><strong>هاتف العيادة:</strong> {{ $clinic->phone }}</div>
            @endif
            @if ($clinic?->address)
                <div><strong>العنوان:</strong> {{ $clinic->address }}</div>
            @endif
            <div><strong>المريض:</strong> {{ $patient->name }}</div>
            @if ($patient->file_number)
                <div><strong>رقم الملف:</strong> {{ $patient->file_number }}</div>
            @endif
            @if ($patient->phone)
                <div><strong>هاتف المريض:</strong> {{ $patient->phone }}</div>
            @endif
            <div><strong>الفترة:</strong> {{ $from }} — {{ $to }}</div>
            <div><strong>تاريخ الطباعة:</strong> {{ now()->format('Y-m-d H:i') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>تاريخ الزيارة</th>
                <th>المطلوب</th>
                <th>المدفوع</th>
                <th>المتبقي</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($financial->visits as $visit)
                @php($due = round($visit->totalLineAmount(), 2))
                @php($paid = round($visit->totalPayments(), 2))
                @php($bal = round($visit->balanceDue(), 2))
                <tr>
                    <td>{{ $visit->visit_at?->format('Y-m-d H:i') }}</td>
                    <td>{{ number_format($due, 2) }}</td>
                    <td>{{ number_format($paid, 2) }}</td>
                    <td>{{ number_format($bal, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">لا توجد زيارات في هذه الفترة.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td>الإجمالي</td>
                <td>{{ number_format($financial->total_due, 2) }}</td>
                <td>{{ number_format($financial->total_paid, 2) }}</td>
                <td>{{ number_format($financial->total_balance, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
