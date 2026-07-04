<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use UnitEnum;

/**
 * @property-read Schema $form
 */
class ManageSiteSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|UnitEnum|null $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Site Settings';

    protected static ?string $title = 'Site Settings';

    protected static ?int $navigationSort = 99;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'donate_url' => Setting::get('donate_url'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Donate button')
                    ->description('This link is used by every "Donate" button across the website — navbar, pages and footer. Change it here once and it updates everywhere.')
                    ->icon('heroicon-o-heart')
                    ->schema([
                        TextInput::make('donate_url')
                            ->label('Donate button link')
                            ->required()
                            ->maxLength(500)
                            ->placeholder('#?campaign=camp_XXXXXXXXXXXX')
                            ->helperText('Paste the full campaign link exactly as provided by your donation platform.'),
                    ]),
            ])
            ->statePath('data');
    }

    /**
     * Render the form (and its Save button) using Filament's default page view,
     * so this page needs no custom Blade file to deploy.
     */
    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make($this->getFormActions()),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::set('donate_url', trim((string) $data['donate_url']));

        Notification::make()
            ->title('Donate link updated')
            ->body('Every donate button on the site now uses the new link.')
            ->success()
            ->send();
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save changes')
                ->submit('save'),
        ];
    }
}
