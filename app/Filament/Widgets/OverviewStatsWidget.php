<?php

namespace App\Filament\Widgets;

use App\Models\Donator;
use App\Models\Event;
use App\Models\ContactMessage;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class OverviewStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalDonations = Donator::sum('financial_support');
        $totalTreesPlanted = Donator::sum('trees_sponsored') + Event::sum('trees_planted');
        $totalEvents = Event::where('is_active', true)->count();
        $pendingMessages = ContactMessage::where('status', 'pending')->count();
        $totalDonators = Donator::verified()->count();
        $totalVolunteers = Event::sum('volunteers');

        // Calculate growth percentages (comparing with last month)
        $lastMonthDonations = Donator::where('donation_date', '>=', now()->subMonth())->sum('financial_support');
        $donationGrowth = $totalDonations > 0 ? (($lastMonthDonations / $totalDonations) * 100) : 0;

        return [
            Stat::make('Total Donations', '$' . number_format($totalDonations, 2))
                ->description($donationGrowth > 0 ? '+$' . number_format($lastMonthDonations, 2) . ' this month' : 'No recent donations')
                ->descriptionIcon($donationGrowth > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($donationGrowth > 0 ? 'success' : 'warning')
                ->chart([7, 12, 10, 14, 15, 18, 20]),

            Stat::make('Trees Planted', number_format($totalTreesPlanted))
                ->description('Total environmental impact')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([3, 5, 8, 12, 15, 20, 25]),

            Stat::make('Active Events', $totalEvents)
                ->description('Upcoming and ongoing')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),

            Stat::make('Total Donators', $totalDonators)
                ->description('Verified supporters')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Total Volunteers', $totalVolunteers)
                ->description('Community engagement')
                ->descriptionIcon('heroicon-m-hand-raised')
                ->color('warning'),

            Stat::make('Pending Messages', $pendingMessages)
                ->description('Need attention')
                ->descriptionIcon('heroicon-m-envelope')
                ->color($pendingMessages > 0 ? 'danger' : 'success'),
        ];
    }
}
