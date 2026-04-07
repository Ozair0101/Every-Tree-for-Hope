<?php

namespace App\Filament\Resources\Expenses\Pages;

use App\Filament\Resources\Expenses\ExpenseResource;
use App\Services\ExpenseImportService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;

class ListExpenses extends ListRecords
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    FileUpload::make('file')
                        ->label('Excel File (.xlsx, .xls)')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                        ])
                        ->disk('local')
                        ->directory('imports')
                        ->required()
                        ->helperText('Upload your weekly expense report Excel file. The "Paid By" name will be extracted from the header row automatically.'),
                ])
                ->action(function (array $data): void {
                    $filePath = Storage::disk('local')->path($data['file']);

                    try {
                        $result = ExpenseImportService::import($filePath);

                        // Clean up uploaded file
                        Storage::disk('local')->delete($data['file']);

                        if ($result['imported'] > 0) {
                            $msg = "Successfully imported {$result['imported']} expense(s)";
                            if ($result['who_paid']) {
                                $msg .= " — Paid by: {$result['who_paid']}";
                            }

                            Notification::make()
                                ->title('Import Complete')
                                ->body($msg)
                                ->success()
                                ->send();
                        }

                        if (!empty($result['errors'])) {
                            Notification::make()
                                ->title('Import Warnings')
                                ->body(implode("\n", array_slice($result['errors'], 0, 5)))
                                ->warning()
                                ->send();
                        }

                        if ($result['imported'] === 0 && empty($result['errors'])) {
                            Notification::make()
                                ->title('No Data Found')
                                ->body('The file was read but no expense rows were found.')
                                ->warning()
                                ->send();
                        }
                    } catch (\Exception $e) {
                        Storage::disk('local')->delete($data['file']);

                        Notification::make()
                            ->title('Import Failed')
                            ->body('Error: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            CreateAction::make(),
        ];
    }
}
