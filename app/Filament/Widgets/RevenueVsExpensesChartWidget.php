<?php

namespace App\Filament\Widgets;

use App\Services\Clinic\ClinicFinancialStats;
use Filament\Widgets\ChartWidget;

class RevenueVsExpensesChartWidget extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $heading = 'تحصيل مقابل مصاريف (آخر 6 أشهر)';

    protected ?string $maxHeight = '280px';

    protected int|string|array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $series = app(ClinicFinancialStats::class)->monthlySeries(6);

        return [
            'datasets' => [
                [
                    'label' => 'تحصيل',
                    'data' => $series['revenue'],
                    'backgroundColor' => 'rgba(37, 99, 235, 0.75)',
                ],
                [
                    'label' => 'مصاريف',
                    'data' => $series['expenses'],
                    'backgroundColor' => 'rgba(239, 68, 68, 0.6)',
                ],
            ],
            'labels' => $series['labels'],
        ];
    }
}
