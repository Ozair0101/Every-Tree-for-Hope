<?php

namespace App\Filament\Widgets;

use App\Models\Donator;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class TopDonatorsWidget extends TableWidget
{
    protected static ?int $sort = 5;
    
    protected static ?string $heading = 'Top Donators';
    
    protected static ?int $columns = 1;

    protected function getTableQuery(): Builder
    {
        return Donator::verified()
            ->orderBy('financial_support', 'desc')
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            \Filament\Tables\Columns\TextColumn::make('full_name')
                ->label('Name')
                ->searchable()
                ->sortable()
                ->weight('bold'),
                
            \Filament\Tables\Columns\TextColumn::make('financial_support')
                ->label('Total Donated')
                ->money('USD')
                ->sortable()
                ->weight('semibold')
                ->color('success'),
                
            \Filament\Tables\Columns\TextColumn::make('trees_sponsored')
                ->label('Trees Sponsored')
                ->numeric()
                ->sortable()
                ->formatStateUsing(fn ($state) => number_format($state) . ' trees')
                ->color('primary'),
                
            \Filament\Tables\Columns\TextColumn::make('location')
                ->label('Location')
                ->searchable()
                ->limit(20),
                
            \Filament\Tables\Columns\IconColumn::make('is_featured')
                ->label('Featured')
                ->boolean()
                ->trueIcon('heroicon-o-star')
                ->falseIcon('heroicon-o-star'),
                
            \Filament\Tables\Columns\TextColumn::make('donation_date')
                ->label('Last Donation')
                ->date('M j, Y')
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

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No donators found';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Start by adding some donators to see them here.';
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            \Filament\Actions\Action::make('create')
                ->label('Create Donator')
                ->url(route('filament.admin.resources.donators.create'))
                ->icon('heroicon-o-plus')
                ->button(),
        ];
    }
}
