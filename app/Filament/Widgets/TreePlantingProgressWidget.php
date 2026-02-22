<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\Donator;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TreePlantingProgressWidget extends ChartWidget
{
    protected static ?int $sort = 3;
    
    protected static ?int $columns = 1;

    protected function getPollingInterval(): ?string
    {
        return null; // Disable polling to prevent infinite loop
    }

    public function getHeading(): string
    {
        return 'Tree Planting Progress';
    }

    protected function getData(): array
    {
        $treesFromDonations = Donator::sum('trees_sponsored');
        $treesFromEvents = Event::sum('trees_planted');
        $totalTrees = $treesFromDonations + $treesFromEvents;
        
        // Set a goal (you can make this configurable)
        $goal = 10000;
        $progress = min(($totalTrees / $goal) * 100, 100);

        // Get monthly tree planting data
        $monthlyData = Event::select(
            DB::raw('DATE_FORMAT(date, "%Y-%m") as month'),
            DB::raw('SUM(trees_planted) as trees_planted')
        )
        ->where('date', '>=', now()->subMonths(12))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $labels = [];
        $treesPlanted = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $monthData = $monthlyData->where('month', $month)->first();
            
            $labels[] = now()->subMonths($i)->format('M');
            $treesPlanted[] = $monthData ? (int) $monthData->trees_planted : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Trees Planted Monthly',
                    'data' => $treesPlanted,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.5)',
                    'borderColor' => 'rgba(34, 197, 94, 1)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        $treesFromDonations = Donator::sum('trees_sponsored');
        $treesFromEvents = Event::sum('trees_planted');
        $totalTrees = $treesFromDonations + $treesFromEvents;
        $goal = 10000;
        $progress = min(($totalTrees / $goal) * 100, 100);
        
        return [
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
                'title' => [
                    'display' => true,
                    'text' => "Progress: {$progress}% ({$totalTrees} / {$goal} trees)",
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Number of Trees',
                    ],
                ],
            ],
        ];
    }
}
