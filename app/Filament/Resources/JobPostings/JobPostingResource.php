<?php

namespace App\Filament\Resources\JobPostings;

use App\Filament\Resources\JobPostings\Pages\CreateJobPosting;
use App\Filament\Resources\JobPostings\Pages\EditJobPosting;
use App\Filament\Resources\JobPostings\Pages\ListJobPostings;
use App\Models\JobPosting;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class JobPostingResource extends Resource
{
    protected static ?string $model = JobPosting::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationLabel = 'Job Openings';

    protected static string|\UnitEnum|null $navigationGroup = 'Careers';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Job Opening';

    protected static ?string $pluralModelLabel = 'Job Openings';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Employer & category')
                    ->description('Which company is hiring, and where this job is listed on the board.')
                    ->icon('heroicon-o-building-office-2')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Components\Select::make('company_id')
                            ->label('Hiring company')
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Components\TextInput::make('name')->required()->maxLength(255),
                                Components\TextInput::make('industry'),
                                Components\TextInput::make('website')->url(),
                            ])
                            ->helperText('Leave empty to post under your own organisation.'),
                        Components\Select::make('job_category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Components\TextInput::make('name.en')->label('Name (English)')->required(),
                            ]),
                    ]),

                Tabs::make('Translations')
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->tabs([
                        Tabs\Tab::make('English')
                            ->badge('default')
                            ->schema(static::translatableFields('en', false)),
                        Tabs\Tab::make('Dari (دری)')
                            ->schema(static::translatableFields('fa', true)),
                        Tabs\Tab::make('Pashto (پښتو)')
                            ->schema(static::translatableFields('ps', true)),
                    ]),

                Section::make('Job details')
                    ->description('Used for filtering and the job overview card.')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Components\Select::make('department')
                            ->options(JobPosting::DEPARTMENTS)
                            ->searchable()
                            ->placeholder('Select a department'),
                        Components\Select::make('employment_type')
                            ->options(JobPosting::EMPLOYMENT_TYPES)
                            ->required()
                            ->default('full_time'),
                        Components\Select::make('experience_level')
                            ->options(JobPosting::EXPERIENCE_LEVELS)
                            ->placeholder('Any / not specified'),
                        Components\TextInput::make('salary_range')
                            ->label('Salary / compensation')
                            ->placeholder('e.g. AFN 35,000–45,000 / month')
                            ->maxLength(255),
                        Components\TextInput::make('location')
                            ->placeholder('e.g. Kabul — Head Office')
                            ->maxLength(255),
                        Components\Select::make('province')
                            ->searchable()
                            ->options(array_combine(static::provinces(), static::provinces()))
                            ->placeholder('Select province'),
                        Components\Toggle::make('is_remote')
                            ->label('Remote / work from home')
                            ->inline(false),
                        Components\TextInput::make('openings')
                            ->label('Number of openings')
                            ->numeric()
                            ->minValue(1)
                            ->default(1),
                        Components\DatePicker::make('application_deadline')
                            ->native(false)
                            ->minDate(now())
                            ->helperText('Leave empty for "open until filled". Past the deadline the post is shown as closed.'),
                    ]),

                Section::make('Publishing')
                    ->icon('heroicon-o-megaphone')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Components\TextInput::make('slug')
                            ->helperText('Used in the URL. Leave blank to generate from the English title.')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull(),
                        Components\Toggle::make('is_active')
                            ->label('Published')
                            ->default(true)
                            ->helperText('Unpublished jobs are hidden from the website.'),
                        Components\Toggle::make('is_featured')
                            ->label('Featured')
                            ->helperText('Featured jobs are highlighted and listed first.'),
                        Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first.'),
                    ]),
            ]);
    }

    /**
     * The translatable text fields for one locale.
     */
    protected static function translatableFields(string $locale, bool $rtl): array
    {
        $required = $locale === 'en';
        $extra = $rtl ? ['dir' => 'rtl'] : [];

        return [
            Components\TextInput::make("title.$locale")
                ->label('Job title')
                ->required($required)
                ->maxLength(255)
                ->extraAttributes($extra)
                ->formatStateUsing(fn ($record) => $record?->getTranslation('title', $locale, false))
                ->columnSpanFull(),
            Components\TextInput::make("summary.$locale")
                ->label('Short summary')
                ->placeholder('One line shown on the job card')
                ->maxLength(255)
                ->extraAttributes($extra)
                ->formatStateUsing(fn ($record) => $record?->getTranslation('summary', $locale, false))
                ->columnSpanFull(),
            Components\Textarea::make("description.$locale")
                ->label('Description')
                ->required($required)
                ->rows(6)
                ->extraAttributes($extra)
                ->helperText($locale === 'en' ? 'Shown by default when a translation is missing.' : null)
                ->formatStateUsing(fn ($record) => $record?->getTranslation('description', $locale, false))
                ->columnSpanFull(),
            Components\Textarea::make("responsibilities.$locale")
                ->label('Key responsibilities')
                ->rows(5)
                ->extraAttributes($extra)
                ->helperText('One responsibility per line — shown as a bullet list.')
                ->formatStateUsing(fn ($record) => $record?->getTranslation('responsibilities', $locale, false))
                ->columnSpanFull(),
            Components\Textarea::make("requirements.$locale")
                ->label('Requirements / qualifications')
                ->rows(5)
                ->extraAttributes($extra)
                ->helperText('One requirement per line — shown as a bullet list.')
                ->formatStateUsing(fn ($record) => $record?->getTranslation('requirements', $locale, false))
                ->columnSpanFull(),
            Components\Textarea::make("benefits.$locale")
                ->label('What we offer (optional)')
                ->rows(4)
                ->extraAttributes($extra)
                ->helperText('One benefit per line — shown as a bullet list.')
                ->formatStateUsing(fn ($record) => $record?->getTranslation('benefits', $locale, false))
                ->columnSpanFull(),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('company.logo_path')
                    ->label('')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => $record->company?->logo_url)
                    ->visible(fn ($record) => (bool) $record?->company),
                Tables\Columns\TextColumn::make('title')
                    ->limit(40)
                    ->searchable(query: function ($query, string $search) {
                        return $query->where('title', 'like', "%{$search}%");
                    })
                    ->sortable()
                    ->description(fn ($record) => $record->company?->name),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('employment_type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => JobPosting::EMPLOYMENT_TYPES[$state] ?? $state)
                    ->badge(),
                Tables\Columns\TextColumn::make('location')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('applications_count')
                    ->counts('applications')
                    ->label('Applicants')
                    ->badge()
                    ->color('success'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Published')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('application_deadline')
                    ->date()
                    ->placeholder('Open')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('employment_type')
                    ->options(JobPosting::EMPLOYMENT_TYPES),
                Tables\Filters\SelectFilter::make('company_id')
                    ->label('Company')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('job_category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Published'),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
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
            'index' => ListJobPostings::route('/'),
            'create' => CreateJobPosting::route('/create'),
            'edit' => EditJobPosting::route('/{record}/edit'),
        ];
    }

    protected static function provinces(): array
    {
        return [
            'Badakhshan', 'Badghis', 'Baghlan', 'Balkh', 'Bamyan', 'Daykundi', 'Farah',
            'Faryab', 'Ghazni', 'Ghor', 'Helmand', 'Herat', 'Jowzjan', 'Kabul', 'Kandahar',
            'Kapisa', 'Khost', 'Kunar', 'Kunduz', 'Laghman', 'Logar', 'Maimana', 'Nangarhar',
            'Nimruz', 'Nuristan', 'Paktia', 'Paktika', 'Panjshir', 'Parwan', 'Samangan',
            'Sar-e Pol', 'Takhar', 'Urozgan', 'Wardak', 'Zabul',
        ];
    }
}
