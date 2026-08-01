<?php

namespace App\Filament\Resources\Tasks;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Filament\Resources\Tasks\Pages\CreateTask;
use App\Filament\Resources\Tasks\Pages\EditTask;
use App\Filament\Resources\Tasks\Pages\ListTasks;
use App\Models\Task;
use App\Models\User;
use App\Services\Tasks\Export\TaskExporter;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

/**
 * The task board in the admin panel.
 *
 * Everything the mobile API exposes is manageable here too, through the same
 * models and the same policy — {@see \App\Policies\TaskPolicy} answers for both,
 * so a coordinator's permissions cannot mean one thing on the phone and another
 * at a desk.
 *
 * The table is where a coordinator actually lives, so it carries the filters,
 * the search and both exports rather than hiding them on a separate report page.
 */
class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Tasks';

    protected static string|\UnitEnum|null $navigationGroup = 'Field Operations';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    /** Count of work waiting on a reviewer — the queue that ages if ignored. */
    public static function getNavigationBadge(): ?string
    {
        $awaiting = static::getModel()::query()->awaitingReview()->count();

        return $awaiting > 0 ? (string) $awaiting : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    /** Searchable from the panel's global search bar by title or reference. */
    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'reference', 'location_name'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('The work')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Components\TextInput::make('title')
                            ->required()
                            ->maxLength(180)
                            ->columnSpanFull(),
                        Components\Textarea::make('description')
                            ->rows(3)
                            ->maxLength(5000)
                            ->columnSpanFull(),
                        Components\Textarea::make('instructions')
                            ->rows(6)
                            ->maxLength(20000)
                            ->helperText('Step-by-step guidance shown to the volunteer in the field.')
                            ->columnSpanFull(),
                        Components\Select::make('priority')
                            ->options(TaskPriority::options())
                            ->default(TaskPriority::MEDIUM->value)
                            ->required(),
                        // A plain place name, kept from the old location
                        // section. The coordinates and the on-site radius that
                        // sat beside it are gone — see the note on the geo
                        // defaults in the migration that turned them off.
                        Components\TextInput::make('location_name')
                            ->label('Location')
                            ->maxLength(255)
                            ->placeholder('e.g. Sharak Haji Nabi hillside')
                            ->columnSpanFull(),
                    ]),

                Section::make('Schedule')
                    ->icon('heroicon-o-calendar-days')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        // Dates, not date-times. Field work is planned by the
                        // day — "water the saplings on Thursday" — and a clock
                        // time is precision nobody sets deliberately, so it ends
                        // up being whatever the form defaulted to.
                        Components\DatePicker::make('start_date')
                            ->native(false)
                            ->displayFormat('d M Y'),
                        Components\DatePicker::make('due_date')
                            ->native(false)
                            ->displayFormat('d M Y')
                            // A deadline before the start is nearly always a
                            // typo, and one that makes the task overdue the
                            // moment it is published.
                            ->afterOrEqual('start_date'),
                        Components\TextInput::make('estimated_hours')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.25)
                            ->suffix('h'),
                    ]),

                Section::make('Assignment')
                    ->icon('heroicon-o-user-group')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        /*
                         * A shortcut, not a second source of truth. Picking a
                         * role drops that role's members into the list below,
                         * where they can then be trimmed. Storing "assigned to
                         * a role" instead would mean the team silently changes
                         * whenever someone is granted or loses the role — and
                         * a task whose assignees move under you is not a task
                         * anyone can be held to.
                         */
                        Components\Select::make('assign_role')
                            ->label('Add everyone with a role')
                            ->options(fn () => Role::query()->orderBy('name')->pluck('name', 'name'))
                            ->searchable()
                            ->placeholder('Choose a role…')
                            ->helperText('Adds them to the list below. Nothing is saved until you do.')
                            ->dehydrated(false)
                            ->live()
                            ->afterStateUpdated(function ($state, Components\Select $component, callable $get, callable $set) {
                                if (blank($state)) {
                                    return;
                                }

                                $ids = User::query()->role($state)->pluck('id')->all();

                                // Merged, not replaced: picking a second role
                                // should widen the team, and anyone added by
                                // hand must survive the click.
                                $set('assignee_ids', array_values(array_unique(
                                    array_merge($get('assignee_ids') ?? [], $ids),
                                )));

                                // Clear itself so the same role can be picked
                                // again after manual edits.
                                $component->state(null);
                            }),

                        Components\Select::make('assignee_ids')
                            ->label('Assigned to')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(fn () => User::query()
                                ->orderBy('name')
                                ->get()
                                ->mapWithKeys(fn (User $u) => [
                                    // The email disambiguates the two people
                                    // who share a first name, which a name-only
                                    // list cannot.
                                    $u->id => trim($u->name.' '.($u->lastname ?? '')).' — '.$u->email,
                                ]))
                            ->helperText('Search by name or email. They are notified when the task is saved.')
                            ->dehydrated(false)
                            ->columnSpanFull(),

                        Components\TextInput::make('max_assignees')
                            ->numeric()
                            ->minValue(1)
                            ->helperText('1 for a single-assignee task. Leave empty for no limit.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Ref')
                    ->searchable()
                    ->copyable()
                    ->toggleable()
                    // The reference is one token and must never be broken
                    // across lines — a half-shown TSK-2026-000001 is unusable
                    // for the copy button sitting next to it.
                    ->extraCellAttributes(['style' => 'white-space: nowrap;']),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(70)
                    ->wrap()
                    ->tooltip(fn (Task $r) => strlen($r->title) > 70 ? $r->title : null)
                    /*
                     * A floor on the width, which is what was actually broken.
                     *
                     * A wrapping column has no intrinsic width, so once the
                     * other columns and the action buttons pushed the table
                     * past the viewport the browser shrank this one to its
                     * narrowest possible box — the longest single word — and
                     * rendered the title one word per line down eight rows.
                     * Reserving space here means the table gives ground
                     * elsewhere instead.
                     */
                    ->extraHeaderAttributes(['style' => 'min-width: 16rem;'])
                    ->extraCellAttributes(['style' => 'min-width: 16rem;'])
                    ->description(fn (Task $r) => $r->category?->name),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (TaskStatus $state) => $state->label())
                    ->color(fn (TaskStatus $state) => match ($state) {
                        TaskStatus::APPROVED => 'success',
                        TaskStatus::REJECTED, TaskStatus::CANCELLED => 'danger',
                        TaskStatus::SUBMITTED, TaskStatus::UNDER_REVIEW => 'warning',
                        TaskStatus::IN_PROGRESS => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->formatStateUsing(fn (TaskPriority $state) => $state->label())
                    ->color(fn (TaskPriority $state) => match ($state) {
                        TaskPriority::CRITICAL => 'danger',
                        TaskPriority::HIGH => 'warning',
                        TaskPriority::MEDIUM => 'info',
                        TaskPriority::LOW => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('assignees_count')
                    ->counts('assignees')
                    ->label('Team')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('progress')
                    ->label('Progress')
                    ->formatStateUsing(fn ($state) => "{$state}%")
                    ->badge()
                    ->color(fn ($state) => $state >= 100 ? 'success' : ($state > 0 ? 'info' : 'gray'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->placeholder('—')
                    // The single most useful signal on the board: red means
                    // someone needs to be chased today.
                    ->color(fn (Task $r) => $r->is_overdue ? 'danger' : null)
                    ->description(fn (Task $r) => $r->due_date?->diffForHumans()),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(TaskStatus::options())
                    ->multiple(),
                Tables\Filters\SelectFilter::make('priority')
                    ->options(TaskPriority::options())
                    ->multiple(),
                Tables\Filters\SelectFilter::make('task_category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('assignee')
                    ->label('Assigned to')
                    ->relationship('assignees.user', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('overdue')
                    ->label('Overdue only')
                    ->toggle()
                    ->query(fn (Builder $query) => $query->overdue()),
                Tables\Filters\Filter::make('unassigned')
                    ->label('Unassigned only')
                    ->toggle()
                    ->query(fn (Builder $query) => $query->whereDoesntHave('assignees')),
                Tables\Filters\Filter::make('due_between')
                    ->schema([
                        Components\DatePicker::make('from')->label('Due from'),
                        Components\DatePicker::make('until')->label('Due until'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'] ?? null, fn ($q, $d) => $q->whereDate('due_date', '>=', $d))
                        ->when($data['until'] ?? null, fn ($q, $d) => $q->whereDate('due_date', '<=', $d))),
            ])
            /*
             * One menu, not four buttons.
             *
             * Publish / Cancel / Edit / Delete rendered inline as labelled
             * buttons occupied more horizontal space than any data column, and
             * on a laptop that pushed the last of them off the right edge
             * entirely — the actions were there but unreachable without
             * sideways scrolling.
             */
            ->recordActions([
                Actions\ActionGroup::make([
                    Actions\Action::make('publish')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('info')
                        ->authorize('update_task')
                        ->visible(fn (Task $r) => $r->status === TaskStatus::DRAFT)
                        ->requiresConfirmation()
                        ->action(fn (Task $r) => app(\App\Services\Tasks\TaskService::class)
                            ->publish($r, auth()->user())),
                    Actions\Action::make('cancel')
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->authorize('cancel_task')
                        ->visible(fn (Task $r) => ! $r->status->isTerminal())
                        ->schema([
                            Components\TextInput::make('reason')
                                ->required()
                                ->maxLength(255)
                                ->helperText('Shown to everyone assigned.'),
                        ])
                        ->action(fn (Task $r, array $data) => app(\App\Services\Tasks\TaskService::class)
                            ->cancel($r, auth()->user(), $data['reason'])),
                    Actions\EditAction::make(),
                    Actions\DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                // Both exports honour the filters and search currently applied
                // to the table. An export that quietly ignored them would look
                // right and be wrong, which is the worst combination.
                Actions\Action::make('export_excel')
                    ->label('Export Excel')
                    ->icon('heroicon-o-table-cells')
                    ->color('success')
                    ->authorize('export_task')
                    ->action(fn ($livewire) => app(TaskExporter::class)
                        ->toExcel($livewire->getFilteredSortedTableQuery(), 'tasks')),
                Actions\Action::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->authorize('export_task')
                    // Opens in a new tab, where the print dialogue offers
                    // "Save as PDF". See TaskExporter::toPdfData().
                    ->url(fn ($livewire) => route('admin.tasks.export.pdf', request()->query()), shouldOpenInNewTab: true),
            ])
            ->defaultSort('due_date', 'asc')
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->striped();
    }

    /**
     * Eager-load what the table renders.
     *
     * Without this the category and the assignee count are a query per row —
     * fifty rows, a hundred queries, on the page a coordinator opens most.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('category:id,name');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTasks::route('/'),
            'create' => CreateTask::route('/create'),
            'edit' => EditTask::route('/{record}/edit'),
        ];
    }
}
