<?php

namespace App\Filament\Resources\TreeRequests\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class TreeRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('location')
                    ->label('Location'),

                TextEntry::make('number_of_trees')
                    ->label('Number of Trees'),

                TextEntry::make('water_source')
                    ->label('Water Source')
                    ->placeholder('Not specified'),

                TextEntry::make('responsible_person')
                    ->label('Responsible Person'),

                TextEntry::make('phone_whatsapp')
                    ->label('Phone / WhatsApp'),

                TextEntry::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'reviewing' => 'Reviewing',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'completed' => 'Completed',
                        default => $state ?? 'Pending',
                    }),

                TextEntry::make('admin_notes')
                    ->label('Admin Notes')
                    ->placeholder('-'),

                TextEntry::make('media_paths')
                    ->label('Media')
                    ->state(function ($record) {
                        if (!$record || empty($record->media_paths) || !is_array($record->media_paths)) {
                            return null;
                        }

                        $items = [];
                        foreach ($record->media_paths as $path) {
                            if (!$path) {
                                continue;
                            }

                            $url = asset('storage/' . ltrim($path, '/'));
                            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

                            if (in_array($ext, ['mp4', 'webm', 'mov', 'qt'])) {
                                $items[] = '<video controls class="w-full rounded-lg border border-gray-200" src="' . e($url) . '"></video>';
                            } else {
                                $items[] = '<a href="' . e($url) . '" target="_blank" rel="noopener" class="block">'
                                    . '<img class="w-full h-auto rounded-lg border border-gray-200" src="' . e($url) . '" alt="Media" />'
                                    . '</a>';
                            }
                        }

                        if (empty($items)) {
                            return null;
                        }

                        return new HtmlString('<div class="grid grid-cols-1 md:grid-cols-2 gap-4">' . implode('', $items) . '</div>');
                    })
                    ->html()
                    ->placeholder('No media uploaded.'),
            ]);
    }
}
