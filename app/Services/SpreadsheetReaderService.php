<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SpreadsheetReaderService
{
    /**
     * @return array<int, array<string, string|null>>
     */
    public function readRows(UploadedFile $file): array
    {
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, true, true, false);

        if (count($data) === 0) {
            return [];
        }

        $headers = array_map(fn ($header) => $this->normalizeHeader((string) $header), (array) array_shift($data));
        $rows = [];

        foreach ($data as $index => $rowValues) {
            $row = [];

            foreach ($headers as $columnIndex => $header) {
                if ($header === '') {
                    continue;
                }

                $rawValue = $rowValues[$columnIndex] ?? null;
                $row[$header] = is_string($rawValue) ? trim($rawValue) : (is_null($rawValue) ? null : (string) $rawValue);
            }

            if ($this->isEmptyRow($row)) {
                continue;
            }

            $row['__row'] = (string) ($index + 2);
            $rows[] = $row;
        }

        return $rows;
    }

    private function normalizeHeader(string $header): string
    {
        $header = strtolower(trim($header));
        $header = preg_replace('/[^a-z0-9]+/', '_', $header) ?? '';

        return trim($header, '_');
    }

    /**
     * @param array<string, string|null> $row
     */
    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $key => $value) {
            if ($key === '__row') {
                continue;
            }

            if ($value !== null && $value !== '') {
                return false;
            }
        }

        return true;
    }
}
