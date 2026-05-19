<?php

namespace App\Filament\Resources\Faqs;

use App\Filament\Resources\Faqs\Pages\CreateFaq;
use App\Filament\Resources\Faqs\Pages\EditFaq;
use App\Filament\Resources\Faqs\Pages\ListFaqs;
use App\Models\Faq;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationLabel = 'FAQs';

    protected static string|\UnitEnum|null $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'FAQ';

    protected static ?string $pluralModelLabel = 'FAQs';

    protected static ?string $recordTitleAttribute = 'question';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Translations')
                ->columnSpanFull()
                ->persistTabInQueryString()
                ->tabs([
                    Tabs\Tab::make('English')
                        ->schema([
                            Select::make('category.en')
                                ->label('Category (English)')
                                ->options([
                                    'Introduction & Background' => 'Introduction & Background',
                                    'Goals & Mission' => 'Goals & Mission',
                                    'Working Methods' => 'Working Methods',
                                    'Team & Structure' => 'Team & Structure',
                                    'Impact & Community' => 'Impact & Community',
                                    'Future & Vision' => 'Future & Vision',
                                    'Challenges' => 'Challenges',
                                    'General' => 'General',
                                ])
                                ->searchable()
                                ->required()
                                ->formatStateUsing(fn ($record) => $record?->getTranslation('category', 'en', false))
                                ->columnSpanFull(),
                            Textarea::make('question.en')
                                ->label('Question (English)')
                                ->required()
                                ->rows(3)
                                ->formatStateUsing(fn ($record) => $record?->getTranslation('question', 'en', false))
                                ->columnSpanFull(),
                            Textarea::make('answer.en')
                                ->label('Answer (English)')
                                ->rows(6)
                                ->helperText('Shown by default. Visitors see this when their language has no translation.')
                                ->formatStateUsing(fn ($record) => $record?->getTranslation('answer', 'en', false))
                                ->columnSpanFull(),
                        ]),
                    Tabs\Tab::make('Dari (دری)')
                        ->schema([
                            TextInput::make('category.fa')
                                ->label('دسته‌بندی (دری)')
                                ->maxLength(255)
                                ->extraAttributes(['dir' => 'rtl'])
                                ->helperText('در صورت خالی بودن، نسخهٔ انگلیسی استفاده می‌شود.')
                                ->formatStateUsing(fn ($record) => $record?->getTranslation('category', 'fa', false))
                                ->columnSpanFull(),
                            Textarea::make('question.fa')
                                ->label('پرسش (دری)')
                                ->rows(3)
                                ->extraAttributes(['dir' => 'rtl'])
                                ->formatStateUsing(fn ($record) => $record?->getTranslation('question', 'fa', false))
                                ->columnSpanFull(),
                            Textarea::make('answer.fa')
                                ->label('پاسخ (دری)')
                                ->rows(6)
                                ->extraAttributes(['dir' => 'rtl'])
                                ->formatStateUsing(fn ($record) => $record?->getTranslation('answer', 'fa', false))
                                ->columnSpanFull(),
                        ]),
                    Tabs\Tab::make('Pashto (پښتو)')
                        ->schema([
                            TextInput::make('category.ps')
                                ->label('کټګوري (پښتو)')
                                ->maxLength(255)
                                ->extraAttributes(['dir' => 'rtl'])
                                ->helperText('که خالي وي، انګلیسي نسخه کارول کیږي.')
                                ->formatStateUsing(fn ($record) => $record?->getTranslation('category', 'ps', false))
                                ->columnSpanFull(),
                            Textarea::make('question.ps')
                                ->label('پوښتنه (پښتو)')
                                ->rows(3)
                                ->extraAttributes(['dir' => 'rtl'])
                                ->formatStateUsing(fn ($record) => $record?->getTranslation('question', 'ps', false))
                                ->columnSpanFull(),
                            Textarea::make('answer.ps')
                                ->label('ځواب (پښتو)')
                                ->rows(6)
                                ->extraAttributes(['dir' => 'rtl'])
                                ->formatStateUsing(fn ($record) => $record?->getTranslation('answer', 'ps', false))
                                ->columnSpanFull(),
                        ]),
                ]),

            TextInput::make('asked_by_name')
                ->label('Asked By (Name)')
                ->maxLength(255),

            TextInput::make('asked_by_email')
                ->label('Asked By (Email)')
                ->email()
                ->maxLength(255),

            TextInput::make('sort_order')
                ->label('Sort Order')
                ->numeric()
                ->default(0),

            Toggle::make('is_verified')
                ->label('Verified & Published')
                ->helperText('Only verified FAQs with answers are shown on the public page')
                ->default(false),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('question')
                    ->limit(60)
                    ->searchable()
                    ->wrap()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('answer')
                    ->limit(50)
                    ->toggleable()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('asked_by_name')
                    ->label('Asked By')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Published')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'Introduction & Background' => 'Introduction & Background',
                        'Goals & Mission' => 'Goals & Mission',
                        'Working Methods' => 'Working Methods',
                        'Team & Structure' => 'Team & Structure',
                        'Impact & Community' => 'Impact & Community',
                        'Future & Vision' => 'Future & Vision',
                        'Challenges' => 'Challenges',
                        'General' => 'General',
                    ])
                    // category is now a JSON translatable column — filter on the English value
                    ->query(function (Builder $query, array $data) {
                        if (filled($data['value'] ?? null)) {
                            $query->where('category->en', $data['value']);
                        }
                    }),
                Tables\Filters\SelectFilter::make('is_verified')
                    ->label('Status')
                    ->options(['1' => 'Published', '0' => 'Unpublished']),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()->requiresConfirmation(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('sort_order')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFaqs::route('/'),
            'create' => CreateFaq::route('/create'),
            'edit' => EditFaq::route('/{record}/edit'),
        ];
    }
}
