<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\OutstandingPatientsWidget;
use App\Filament\Widgets\PaymentsTrendChartWidget;
use App\Filament\Widgets\RevenueVsExpensesChartWidget;
use App\Filament\Widgets\VisitsTrendChartWidget;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ClinicAnalytics extends Page
{
    protected static ?string $navigationLabel = 'إحصائيات العيادة';

    protected static string|\UnitEnum|null $navigationGroup = 'العيادة';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'إحصائيات العيادة';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected string $view = 'filament.pages.clinic-analytics';

    public static function canAccess(): bool
    {
        return auth()->check();
    }

    protected function getHeaderWidgets(): array
    {
        return [
            RevenueVsExpensesChartWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            VisitsTrendChartWidget::class,
            PaymentsTrendChartWidget::class,
            OutstandingPatientsWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return [
            'md' => 2,
            'xl' => 2,
        ];
    }
}
