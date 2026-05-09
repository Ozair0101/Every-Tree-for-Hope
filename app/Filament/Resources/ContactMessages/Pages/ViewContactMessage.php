<?php

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use Filament\Actions;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
            Actions\Action::make('markAsRead')
                ->label('Mark as Read')
                ->icon('heroicon-o-check')
                ->color('success')
                ->visible(fn ($record) => $record->status === 'unread')
                ->action(fn ($record) => $record->update(['status' => 'read'])),
            Actions\Action::make('markAsUnread')
                ->label('Mark as Unread')
                ->icon('heroicon-o-envelope')
                ->color('warning')
                ->visible(fn ($record) => $record->status === 'read')
                ->action(fn ($record) => $record->update(['status' => 'unread'])),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('From')
                    ->columnSpan(1),

                TextEntry::make('email')
                    ->label('Email')
                    ->copyable()
                    ->columnSpan(1),

                TextEntry::make('subject')
                    ->label('Subject')
                    ->columnSpan(2),

                TextEntry::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'unread' => 'Unread',
                        'read' => 'Read',
                        default => $state ?? 'Unknown',
                    })
                    ->columnSpan(1),

                TextEntry::make('created_at')
                    ->label('Received')
                    ->formatStateUsing(fn (?string $state): ?string => $state ? \Carbon\Carbon::parse($state)->format('d/m/Y') : null)
                    ->columnSpan(1),

                TextEntry::make('message')
                    ->label('Message')
                    ->columnSpan(2),
            ])
            ->columns(2);
    }
}
