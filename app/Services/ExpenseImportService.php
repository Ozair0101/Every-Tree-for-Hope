<?php

namespace App\Services;

use App\Models\Expense;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExpenseImportService
{
    /**
     * Import expenses from an Excel file.
     *
     * Expected format:
     * Row 1: Title header like "Weekly Report (May25-Jun2nd) paid by the team"
     *         The "who_paid" is extracted from text after "paid by"
     * Row 2: Column headers (No, Date, Description, Quantity, Unit Price, Total Cost, Notes)
     * Row 3+: Data rows
     * Last row with "Total" in description column is skipped
     */
    public static function import(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        if (count($rows) < 3) {
            return ['imported' => 0, 'errors' => ['File has too few rows.']];
        }

        // --- Extract "who_paid" from the header row (row 1) ---
        $headerText = '';
        $firstRow = $rows[1] ?? [];
        foreach ($firstRow as $cell) {
            if (!empty($cell)) {
                $headerText .= ' ' . $cell;
            }
        }
        // Also check merged cells — the title might span across the first row
        $headerText = trim($headerText);
        $whoPaid = self::extractWhoPaid($headerText);

        // --- Find column mapping from row 2 ---
        // We look for the row that contains header keywords
        $headerRowIndex = null;
        $colMap = [];

        foreach ($rows as $rowIndex => $row) {
            $rowText = strtolower(implode(' ', array_filter(array_map('strval', $row))));
            if (str_contains($rowText, 'date') && (str_contains($rowText, 'description') || str_contains($rowText, 'توضیحات'))) {
                $headerRowIndex = $rowIndex;
                foreach ($row as $colLetter => $val) {
                    $lower = strtolower(trim((string) $val));
                    if (str_contains($lower, 'date') || str_contains($lower, 'تاریخ')) {
                        $colMap['date'] = $colLetter;
                    } elseif (str_contains($lower, 'description') || str_contains($lower, 'توضیحات')) {
                        $colMap['description'] = $colLetter;
                    } elseif (str_contains($lower, 'quantity') || str_contains($lower, 'تعداد') || str_contains($lower, 'مقدار')) {
                        $colMap['quantity'] = $colLetter;
                    } elseif (str_contains($lower, 'unit') || str_contains($lower, 'واحد')) {
                        $colMap['unit_price'] = $colLetter;
                    } elseif (str_contains($lower, 'total') || str_contains($lower, 'مجموع')) {
                        $colMap['total_cost'] = $colLetter;
                    } elseif (str_contains($lower, 'note') || str_contains($lower, 'یادداشت') || str_contains($lower, 'یادونه')) {
                        $colMap['notes'] = $colLetter;
                    }
                }
                break;
            }
        }

        if (!$headerRowIndex || empty($colMap['date'])) {
            return ['imported' => 0, 'errors' => ['Could not detect column headers in the file.']];
        }

        // --- Parse data rows ---
        $imported = 0;
        $errors = [];

        foreach ($rows as $rowIndex => $row) {
            if ($rowIndex <= $headerRowIndex) {
                continue; // skip header rows
            }

            $descVal = trim((string) ($row[$colMap['description'] ?? ''] ?? ''));

            // Skip empty rows and "Total" rows
            if (empty($descVal) || strtolower($descVal) === 'total' || str_contains($descVal, 'مجموع')) {
                continue;
            }

            // Parse date
            $dateRaw = $row[$colMap['date'] ?? ''] ?? null;
            $date = self::parseDate($dateRaw);
            if (!$date) {
                $errors[] = "Row {$rowIndex}: Could not parse date '{$dateRaw}'";
                continue;
            }

            // Clean description — remove Dari/Pashto secondary text (often on a new line)
            $description = self::cleanDescription($descVal);

            $quantity = trim((string) ($row[$colMap['quantity'] ?? ''] ?? ''));
            $unitPrice = self::parseNumber($row[$colMap['unit_price'] ?? ''] ?? 0);
            $totalCost = self::parseNumber($row[$colMap['total_cost'] ?? ''] ?? 0);
            $notes = trim((string) ($row[$colMap['notes'] ?? ''] ?? ''));

            Expense::create([
                'date' => $date,
                'description' => $description,
                'expense_type' => self::guessExpenseType($description),
                'quantity' => $quantity ?: null,
                'unit_price' => $unitPrice,
                'total_cost' => $totalCost,
                'who_paid' => $whoPaid,
                'notes' => $notes ?: null,
            ]);

            $imported++;
        }

        return ['imported' => $imported, 'errors' => $errors, 'who_paid' => $whoPaid];
    }

    private static function extractWhoPaid(string $headerText): ?string
    {
        // Match "paid by the team", "paid by Tamim", etc.
        if (preg_match('/paid\s+by\s+(?:the\s+)?(.+?)(?:\s*[\n\r]|$)/i', $headerText, $m)) {
            return ucfirst(trim($m[1]));
        }
        // Dari: "توسط تیم" or "توسط ..."
        if (preg_match('/توسط\s+(.+?)(?:\s*[\n\r]|$)/u', $headerText, $m)) {
            return trim($m[1]);
        }
        return null;
    }

    private static function parseDate($raw): ?string
    {
        if (empty($raw)) {
            return null;
        }

        // If it's a numeric Excel serial date
        if (is_numeric($raw) && $raw > 40000) {
            try {
                return Carbon::createFromTimestamp(
                    \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($raw)
                )->format('Y-m-d');
            } catch (\Exception $e) {
                // fall through
            }
        }

        // Try common formats
        $formats = ['n/j/Y', 'm/d/Y', 'Y-m-d', 'd/m/Y', 'M d, Y', 'M d Y'];
        foreach ($formats as $fmt) {
            try {
                return Carbon::createFromFormat($fmt, trim((string) $raw))->format('Y-m-d');
            } catch (\Exception $e) {
                continue;
            }
        }

        // Last resort: let Carbon guess
        try {
            return Carbon::parse($raw)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private static function cleanDescription(string $desc): string
    {
        // If there's a newline, take the first line (English) and trim
        $lines = preg_split('/[\r\n]+/', $desc);
        return trim($lines[0] ?? $desc);
    }

    private static function guessExpenseType(string $description): string
    {
        $lower = strtolower($description);

        if (str_contains($lower, 'tree') || str_contains($lower, 'درخت') || str_contains($lower, 'sapling') || str_contains($lower, 'نهال')) {
            return \App\Enums\ExpenseType::TREE->value;
        }
        if (str_contains($lower, 'transport') || str_contains($lower, 'ترانسپورت') || str_contains($lower, 'trip')) {
            return \App\Enums\ExpenseType::TRANSPORTATION->value;
        }
        if (str_contains($lower, 'water') || str_contains($lower, 'آبیاری') || str_contains($lower, 'اوبو')) {
            return \App\Enums\ExpenseType::WATER->value;
        }
        if (str_contains($lower, 'pipe') || str_contains($lower, 'پایپ')) {
            return \App\Enums\ExpenseType::PIPE->value;
        }
        if (str_contains($lower, 'pick') || str_contains($lower, 'tool') || str_contains($lower, 'کلند') || str_contains($lower, 'shovel') || str_contains($lower, 'bottle')) {
            return \App\Enums\ExpenseType::TOOLS->value;
        }
        if (str_contains($lower, 'snack') || str_contains($lower, 'refresh') || str_contains($lower, 'خوراک') || str_contains($lower, 'food') || str_contains($lower, 'آب و')) {
            return \App\Enums\ExpenseType::SNACK->value;
        }

        return \App\Enums\ExpenseType::OTHER->value;
    }

    private static function parseNumber($val): float
    {
        if (is_numeric($val)) {
            return (float) $val;
        }
        // Remove commas, spaces, currency symbols
        $cleaned = preg_replace('/[^\d.]/', '', (string) $val);
        return $cleaned !== '' ? (float) $cleaned : 0;
    }
}
