<?php

namespace App\Filament\Widgets;

use App\Models\Patient;
use App\Services\Clinic\ClinicFinancialStats;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClinicStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $patientCounts = Patient::query()
            ->selectRaw('gender, count(*) as c')
            ->groupBy('gender')
            ->pluck('c', 'gender');

        $clinic = app(ClinicFinancialStats::class);
        $fin = $clinic->snapshot();
        $visits = $clinic->visitCounts();
        $topService = $clinic->topServiceNameThisMonth();

        return [
            Stat::make('المرضى', (string) Patient::query()->count())
                ->description('ذكر: '.(int) ($patientCounts['male'] ?? 0).' | أنثى: '.(int) ($patientCounts['female'] ?? 0)),
            Stat::make('زيارات اليوم', (string) $visits['today'])
                ->description('هذا الأسبوع: '.$visits['week'].' | هذا الشهر: '.$visits['month']),
            Stat::make('تحصيل الشهر', number_format($fin->revenue_month, 2))
                ->description('مصاريف: '.number_format($fin->expenses_month, 2).' | صافي: '.number_format($fin->net_month, 2)),
            Stat::make('المتبقي على المرضى', number_format($fin->total_outstanding, 2))
                ->description('زيارات بمبلغ متبقٍ: '.$fin->unpaid_visits_count)
                ->color($fin->total_outstanding > 0.01 ? 'danger' : 'success'),
            Stat::make('زيارات غير مكتملة الدفع', (string) $fin->unpaid_visits_count)
                ->description('أكثر خدمة هذا الشهر: '.$topService),
        ];
    }
}
