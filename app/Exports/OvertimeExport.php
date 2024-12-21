<?php

namespace App\Exports;

use App\Models\Overtime;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class OvertimeExport implements FromCollection, WithMapping, WithStyles, WithEvents
{
    public function collection()
    {
        return Overtime::with('employee')->get();
    }

    public function map($item): array
    {
        return [
            $item->id,
            $item->tanggal,
            $item->hari,
            $item->status_hari,
            $item->deskripsi,         // Overtime description
            $item->mulai,             // Overtime start time
            $item->selesai,           // Overtime end time
            $item->employee->nik,      // Employee's NIK
            $item->employee->name,     // Employee's name
            $item->employee->position, // Employee's position
        ];
    }

    public function styles($sheet)
    {
        return [
            'A1:F1' => [
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                for ($col = 'A'; $col <= 'Z'; $col++) {
                    $sheet->setCellValue($col . '1', ''); // Clear each cell in the range
                }

                // Set title
                $sheet->mergeCells('A1:F1');
                $sheet->setCellValue('A1', 'SURAT PERINTAH LEMBUR');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => [
                        'top' => ['borderStyle' => Border::BORDER_THIN],
                        'bottom' => ['borderStyle' => Border::BORDER_THIN],
                        'left' => ['borderStyle' => Border::BORDER_THIN],
                        'right' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

                // Add date and workday
                $data = $this->collection();
                $tgl = $data->first() ? $data->first()->tanggal : 'Error - Tanggal tidak diketahui.';
                $hari = $data->first() ? $data->first()->hari : 'Error  - Hari tidak diketahui.';
                $status = $data->first()
                    ? ($data->first()->status_hari == 'libur' ? '✓' : '')
                    : 'Error - Status hari tidak diketahui.';
                $sheet->setCellValue('A2', 'Tgl: ' . $tgl);
                $sheet->setCellValue('A3', 'Lembur pada hari: ' . $hari);
                $sheet->setCellValue('A4', 'Hari Libur:' . $status);
                $sheet->mergeCells('E3:F3');

                // Header row
                $headers = ['No', 'NIK', 'Nama Karyawan', 'Jabatan', 'Mulai Lembur', 'Berakhir Lembur'];
                $startRow = 8;
                foreach ($headers as $key => $header) {
                    $sheet->setCellValueByColumnAndRow($key + 1, $startRow, $header);
                }
                $sheet->getStyle('A8:F8')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => [
                        'top' => ['borderStyle' => Border::BORDER_THIN],
                        'bottom' => ['borderStyle' => Border::BORDER_THIN],
                        'left' => ['borderStyle' => Border::BORDER_THIN],
                        'right' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

                // Add description under header
                $description = $data->first() ? $data->first()->deskripsi : 'Tidak ada deskripsi.';
                $sheet->mergeCells('A5:F7');
                $sheet->setCellValue('A5', 'Deskripsi: ' . $description);
                $sheet->getStyle('A5')->applyFromArray([
                    'font' => ['italic' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_TOP,
                    ],
                    'borders' => [
                        'top' => ['borderStyle' => Border::BORDER_THIN],
                        'bottom' => ['borderStyle' => Border::BORDER_THIN],
                        'left' => ['borderStyle' => Border::BORDER_THIN],
                        'right' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

                // Table data
                $row = 9;
                foreach ($data as $index => $item) {
                    $sheet->setCellValue('A' . $row, $index + 1);
                    $sheet->setCellValueExplicit('B' . $row, $item->employee->nik, DataType::TYPE_STRING);
                    $sheet->setCellValue('C' . $row, $item->employee->name);
                    $sheet->setCellValue('D' . $row, $item->employee->position);
                    $sheet->setCellValue('E' . $row, $item->mulai);
                    $sheet->setCellValue('F' . $row, $item->selesai);
                    $row++;
                }

                // Apply borders to all cells in the sheet (from A1 to the last data row)
                $lastRow = $row - 1;
                $sheet->getStyle('A1:F' . $lastRow)->applyFromArray([
                    'borders' => [
                        'top' => ['borderStyle' => Border::BORDER_THIN],
                        'bottom' => ['borderStyle' => Border::BORDER_THIN],
                        'left' => ['borderStyle' => Border::BORDER_THIN],
                        'right' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

                // Auto resize columns
                foreach (range('A', 'F') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
