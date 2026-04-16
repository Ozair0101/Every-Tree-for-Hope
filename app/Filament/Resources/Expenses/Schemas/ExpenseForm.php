<?php

namespace App\Filament\Resources\Expenses\Schemas;

use App\Enums\ExpenseType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')
                    ->label('Date')
                    ->required()
                    ->native(false)
                    ->displayFormat('M d, Y')
                    ->default(now()),

                TextInput::make('description')
                    ->label('Description')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., Tree, Transportation, Pipe...'),

                Select::make('expense_type')
                    ->label('Expense Type')
                    ->options(ExpenseType::options())
                    ->searchable()
                    ->required()
                    ->native(false),

                TextInput::make('quantity')
                    ->label('Quantity')
                    ->maxLength(100)
                    ->placeholder('e.g., 12, 3 trips, 50 meter'),

                TextInput::make('unit_price')
                    ->label('Unit Price (AFN)')
                    ->numeric()
                    ->prefix('AFN')
                    ->step(0.01)
                    ->default(0)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $qty = floatval($get('quantity'));
                        if ($qty > 0 && $state) {
                            $set('total_cost', $qty * floatval($state));
                        }
                    }),

                TextInput::make('total_cost')
                    ->label('Total Cost (AFN)')
                    ->numeric()
                    ->prefix('AFN')
                    ->step(0.01)
                    ->required()
                    ->default(0),

                TextInput::make('who_paid')
                    ->label('Paid By')
                    ->maxLength(255)
                    ->placeholder('e.g., Team, Tamim, Donation Fund'),

                Textarea::make('notes')
                    ->label('Notes')
                    ->placeholder('Additional details...')
                    ->rows(3)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
}
