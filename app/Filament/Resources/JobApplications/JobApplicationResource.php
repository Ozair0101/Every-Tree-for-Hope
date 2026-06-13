<?php

namespace App\Filament\Resources\JobApplications;

use App\Filament\Resources\JobApplications\Pages\EditJobApplication;
use App\Filament\Resources\JobApplications\Pages\ListJobApplications;
use App\Filament\Resources\JobApplications\Pages\ViewJobApplication;
use App\Models\JobApplication;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class JobApplicationResource extends Resource
{
    protected static ?string $model = JobApplication::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Applications';

    protected static string|\UnitEnum|null $navigationGroup = 'Careers';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Application';

    protected static ?string $pluralModelLabel = 'Applications';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Review')
                    ->columns(2)
                    ->schema([
                        Components\Select::make('status')
                            ->options(JobApplication::STATUSES)
                            ->required()
                            ->default('pending'),
                        Components\DateTimePicker::make('reviewed_at')
                            ->native(false),
                        Components\Textarea::make('admin_notes')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Applicant (read-only)')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        Components\TextInput::make('full_name')->disabled(),
                        Components\TextInput::make('email')->disabled(),
                        Components\TextInput::make('phone')->disabled(),
                        Components\TextInput::make('location')->disabled(),
                        Components\Textarea::make('cover_letter')->disabled()->rows(4)->columnSpanFull(),
                    ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('jobPosting.title')
                    ->label('Applied for')
                    ->weight('bold')
                    ->size('text-lg')
                    ->columnSpanFull(),
                TextEntry::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => JobApplication::STATUSES[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'hired' => 'success',
                        'shortlisted', 'interviewing' => 'info',
                        'rejected' => 'danger',
                        default => 'warning',
                    }),
                TextEntry::make('created_at')->label('Applied on')->dateTime('M d, Y H:i'),
                TextEntry::make('full_name')->label('Full name'),
                TextEntry::make('email')->copyable(),
                TextEntry::make('phone')->copyable(),
                TextEntry::make('location')->placeholder('—'),
                TextEntry::make('years_experience')->label('Years of experience')->placeholder('—'),
                TextEntry::make('linkedin_url')->label('LinkedIn')->url(fn ($record) => $record->linkedin_url)->openUrlInNewTab()->placeholder('—'),
                TextEntry::make('portfolio_url')->label('Portfolio')->url(fn ($record) => $record->portfolio_url)->openUrlInNewTab()->placeholder('—'),
                TextEntry::make('resume')
                    ->label('Résumé / CV')
                    ->state(fn ($record) => $record->resume_path ? 'Download CV' : 'Not provided')
                    ->url(fn ($record) => $record->resume_url)
                    ->openUrlInNewTab()
                    ->badge()
                    ->color('success'),
                TextEntry::make('cover_letter')->placeholder('—')->columnSpanFull(),
                TextEntry::make('admin_notes')->label('Internal notes')->placeholder('—')->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Applicant')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->email),
                Tables\Columns\TextColumn::make('jobPosting.title')
                    ->label('Applied for')
                    ->limit(35)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->toggleable(),
                Tables\Columns\SelectColumn::make('status')
                    ->options(JobApplication::STATUSES)
                    ->selectablePlaceholder(false),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Applied')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(JobApplication::STATUSES),
                Tables\Filters\SelectFilter::make('job_posting_id')
                    ->label('Job')
                    ->relationship('jobPosting', 'slug')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                Actions\Action::make('download_cv')
                    ->label('CV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn ($record) => $record->resume_url)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => filled($record->resume_path)),
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJobApplications::route('/'),
            'view' => ViewJobApplication::route('/{record}'),
            'edit' => EditJobApplication::route('/{record}/edit'),
        ];
    }
}
