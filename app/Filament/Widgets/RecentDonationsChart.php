<?php

namespace App\Filament\Widgets;

use App\Models\Donator;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class RecentDonationsChart extends ChartWidget
{
    protected static ?int $sort = 2;
    
    protected ?string $heading = 'Donation Trends (Last 6 Months)';
    
    protected static ?int $columns = 2;

    protected function getData(): array
    {
        $data = Donator::select(
            DB::raw('DATE_FORMAT(donation_date, "%Y-%m") as month'),
            DB::raw('SUM(financial_support) as total'),
            DB::raw('COUNT(*) as count')
        )
        ->where('donation_date', '>=', now()->subMonths(6))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $labels = [];
        $donationAmounts = [];
        $donationCounts = [];

        // Fill missing months with zeros
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $monthData = $data->where('month', $month)->first();
            
            $labels[] = now()->subMonths($i)->format('M Y');
            $donationAmounts[] = $monthData ? (float) $monthData->total : 0;
            $donationCounts[] = $monthData ? $monthData->count : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Donation Amount ($)',
                    'data' => $donationAmounts,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.2)',
                    'borderColor' => 'rgba(34, 197, 94, 1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Number of Donations',
                    'data' => $donationCounts,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.3,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'Amount ($)',
                    ],
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Count',
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
        ];
    }
}
