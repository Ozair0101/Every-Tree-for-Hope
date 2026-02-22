<?php

namespace App\Filament\Widgets;

use App\Models\Donator;
use App\Models\Event;
use App\Models\ContactMessage;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RecentActivityWidget extends TableWidget
{
    protected static ?int $sort = 4;
    
    protected static ?string $heading = 'Recent Activity';
    
    protected int | string | array $columnSpan = 'full';

    protected function getPollingInterval(): ?string
    {
        return null; // Disable polling to prevent infinite loop
    }

    protected function getTableQuery(): Builder
    {
        // Return empty query - we'll override getRecords instead
        return Donator::query()->whereRaw('1 = 0');
    }

    protected function getRecords(): \Illuminate\Support\Collection
    {
        // Get recent activities from different models
        $recentDonations = Donator::latest('created_at')
            ->select([
                'id',
                'full_name as title',
                'financial_support as description',
                'created_at',
            ])
            ->limit(5)
            ->get()
            ->map(function($item) {
                $item->type = 'donation';
                $item->type_label = 'Donation';
                return $item;
            });

        $recentEvents = Event::latest('created_at')
            ->select([
                'id',
                'title',
                'location as description',
                'created_at',
            ])
            ->limit(5)
            ->get()
            ->map(function($item) {
                $item->type = 'event';
                $item->type_label = 'Event';
                return $item;
            });

        $recentMessages = ContactMessage::latest('created_at')
            ->select([
                'id',
                'name as title',
                'subject as description',
                'created_at',
            ])
            ->limit(5)
            ->get()
            ->map(function($item) {
                $item->type = 'message';
                $item->type_label = 'Contact Message';
                return $item;
            });

        // Combine and sort all activities
        return $recentDonations
            ->merge($recentEvents)
            ->merge($recentMessages)
            ->sortByDesc('created_at')
            ->take(10)
            ->values();
    }

    protected function getTableColumns(): array
    {
        return [
            \Filament\Tables\Columns\TextColumn::make('type_label')
                ->label('Type')
                ->badge()
                ->color(fn ($record) => match($record->type) {
                    'donation' => 'success',
                    'event' => 'primary',
                    'message' => 'warning',
                }),
                
            \Filament\Tables\Columns\TextColumn::make('title')
                ->label('Title/Name')
                ->searchable()
                ->limit(30),
                
            \Filament\Tables\Columns\TextColumn::make('description')
                ->label('Description')
                ->formatStateUsing(fn ($state, $record) => 
                    $record->type === 'donation' ? '$' . number_format($state, 2) : $state
                )
                ->limit(40),
                
            \Filament\Tables\Columns\TextColumn::make('created_at')
                ->label('Time')
                ->dateTime('M j, Y g:i A')
                ->sortable(),
        ];
    }

    public function getTableRecordsPerPage(): int
    {
        return 10;
    }

    public function isTablePaginationEnabled(): bool
    {
        return false;
    }
}
