<?php

namespace App\Filament\Resources\Teams\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TeamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('position')
                    ->required()
                    ->options([
                        'Project Coordinator' => 'Project Coordinator',
                        'Community & Partnerships Coordinator' => 'Community & Partnerships Coordinator',
                        'Co_Leader Coordinator' => 'Co_Leader Coordinator',
                        'Website Manager & Developer' => 'Website Manager & Developer',
                        'Media & outreach coordianotr' => 'Media & outreach coordianotr',
                        'Sustainability Advisor' => 'Sustainability Advisor',
                        'Founder Project Leader' => 'Founder Project Leader',
                        'Enviromental & Technical Advisor' => 'Enviromental & Technical Advisor',
                        'StoryTelling & Narrative Lead' => 'StoryTelling & Narrative Lead',
                    ])
                    ->searchable(),
                FileUpload::make('image_url')
                    ->image()
                    ->required(),
                TextInput::make('linkedin_url')
                    ->url(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                Textarea::make('bio')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
