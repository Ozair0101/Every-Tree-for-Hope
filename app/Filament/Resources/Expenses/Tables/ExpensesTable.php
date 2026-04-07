<?php

namespace App\Filament\Resources\Expenses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                TextColumn::make('quantity')
                    ->label('Quantity')
                    ->alignCenter(),

                TextColumn::make('unit_price')
                    ->label('Unit Price')
                    ->numeric(decimalPlaces: 0)
                    ->suffix(' AFN')
                    ->alignEnd()
                    ->sortable(),

                TextColumn::make('total_cost')
                    ->label('Total Cost')
                    ->numeric(decimalPlaces: 0)
                    ->suffix(' AFN')
                    ->alignEnd()
                    ->sortable()
                    ->weight('bold')
                    ->color('danger')
                    ->summarize(Sum::make()->label('Total')->suffix(' AFN')),

                TextColumn::make('who_paid')
                    ->label('Paid By')
                    ->searchable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(40)
                    ->toggleable()
                    ->wrap(),
            ])
            ->filters([
                SelectFilter::make('who_paid')
                    ->label('Paid By')
                    ->options(fn () => \App\Models\Expense::distinct()->pluck('who_paid', 'who_paid')->filter()->toArray()),
            ])
            ->actions([
                EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->color('warning'),

                DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->emptyStateHeading('No expenses recorded')
            ->emptyStateDescription('Create your first expense entry to start tracking costs.')
            ->emptyStateActions([
                CreateAction::make(),
            ])
            ->defaultSort('date', 'desc')
            ->striped();
    }
}
