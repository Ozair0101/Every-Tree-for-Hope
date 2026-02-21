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

    protected function getTableQuery(): Builder
    {
        // Get recent activities from different models
        $recentDonations = Donator::latest('created_at')
            ->select([
                'id',
                'full_name as title',
                'financial_support as description',
                'created_at',
                DB::raw("'donation' as type"),
                DB::raw("'Donation' as type_label"),
            ])
            ->limit(5);

        $recentEvents = Event::latest('created_at')
            ->select([
                'id',
                'title',
                'location as description',
                'created_at',
                DB::raw("'event' as type"),
                DB::raw("'Event' as type_label"),
            ])
            ->limit(5);

        $recentMessages = ContactMessage::latest('created_at')
            ->select([
                'id',
                'name as title',
                'subject as description',
                'created_at',
                DB::raw("'message' as type"),
                DB::raw("'Contact Message' as type_label"),
            ])
            ->limit(5);

        // Combine all activities
        return $recentDonations->unionAll($recentEvents)->unionAll($recentMessages)
            ->orderBy('created_at', 'desc')
            ->limit(10);
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
