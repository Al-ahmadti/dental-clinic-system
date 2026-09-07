<?php

namespace App\Filament\Widgets;

use App\Services\Clinic\ClinicFinancialStats;
use Filament\Widgets\ChartWidget;

class VisitsTrendChartWidget extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $heading = 'عدد الزيارات شهرياً';

    protected ?string $maxHeight = '260px';

    protected int|string|array $columnSpan = 1;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $series = app(ClinicFinancialStats::class)->monthlySeries(6);

        return [
            'datasets' => [
                [
                    'label' => 'زيارات',
                    'data' => $series['visits'],
                    'borderColor' => 'rgb(37, 99, 235)',
                    'backgroundColor' => 'rgba(37, 99, 235, 0.15)',
                    'fill' => true,
                ],
            ],
            'labels' => $series['labels'],
        ];
    }
}
