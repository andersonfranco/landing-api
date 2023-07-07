<?php

namespace App\Framework\Models\Traits;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

trait ExportToXLSX
{
    public function exportToXLSX(string $filename = null): void
    {
        $data = $this->getAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([
            array_keys(reset($data) ?: []),
            ...array_map(fn($row) => array_map(fn($col) => is_array($col) ? implode(',', $col) : $col, $row), $data)
        ], NULL, 'A1');

        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        if ($filename !== null) {
            $writer->save($filename. '.xlsx');
            return;
        }

        header('Content-Disposition: attachment;filename=" ' . $this->plural . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}