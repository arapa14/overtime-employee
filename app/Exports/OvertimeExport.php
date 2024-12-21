<?php

namespace App\Exports;

use App\Models\Overtime;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Tcpdf;

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
            'B2:F2' => [
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

                // Get the highest row and column that is used in the sheet
                $highestColumn = $sheet->getHighestColumn(); // e.g., 'Z'
                $highestRow = $sheet->getHighestRow(); // e.g., 100

                // Loop through all columns (from 'A' to the highest column)
                for ($col = 'A'; $col <= $highestColumn; $col++) {
                    // Loop through all rows (from 1 to the highest row)
                    for ($row = 1; $row <= $highestRow; $row++) {
                        $sheet->setCellValue($col . $row, ''); // Clear each cell
                    }
                }


                // Title starting from B2
                $sheet->mergeCells('B2:G2');
                $sheet->setCellValue('B2', 'SURAT PERINTAH LEMBUR');
                $sheet->getStyle('B2:G2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => [
                        'top' => ['borderStyle' => Border::BORDER_THIN],
                        'bottom' => ['borderStyle' => Border::BORDER_THIN],
                        'left' => ['borderStyle' => Border::BORDER_THIN],
                        'right' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

                // Add date and workday starting from B3
                $data = $this->collection();
                $tgl = $data->first() ? $data->first()->tanggal : 'Error - Tanggal tidak diketahui.';
                $hari = $data->first() ? $data->first()->hari : 'Error  - Hari tidak diketahui.';
                $status = $data->first()
                    ? ($data->first()->status_hari == 'libur' ? '✓' : '')
                    : 'Error - Status hari tidak diketahui.';
                $sheet->mergeCells('B3:G3');
                $sheet->setCellValue('B3', 'Tgl: ' . $tgl);
                $sheet->mergeCells('B4:G4');
                $sheet->setCellValue('B4', 'Lembur pada hari: ' . $hari);
                $sheet->mergeCells('B5:G5');
                $sheet->setCellValue('B5', 'Hari Libur:' . $status);
                $sheet->getStyle('B3:B5')->applyFromArray([
                    'borders' => [
                        'left' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);
                $sheet->getStyle('G3:G5')->applyFromArray([
                    'borders' => [
                        'right' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

                // Add description starting from B7
                $description = $data->first() ? $data->first()->deskripsi : 'Tidak ada deskripsi.';
                $sheet->mergeCells('B6:G8');
                $sheet->setCellValue('B6', 'Deskripsi: ' . $description);
                $sheet->getStyle('B6:G8')->applyFromArray([
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

                $headers = ['No', 'NIK', 'Nama Karyawan', 'Jabatan', 'Mulai Lembur', 'Berakhir Lembur'];
                $startRow = 9;
                foreach ($headers as $key => $header) {
                    $column = chr(66 + $key); // Menghasilkan B, C, D, E, F, G berdasarkan posisi key
                    $sheet->setCellValue($column . $startRow, $header); // Menulis header pada B9, C9, D9, dst.

                    // Mengatur gaya dengan border untuk setiap kolom header
                    $sheet->getStyle($column . $startRow)->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'borders' => [
                            'top' => ['borderStyle' => Border::BORDER_THIN],
                            'bottom' => ['borderStyle' => Border::BORDER_THIN],
                            'left' => ['borderStyle' => Border::BORDER_THIN],
                            'right' => ['borderStyle' => Border::BORDER_THIN],
                        ],
                    ]);
                }

                $row = 10;
                foreach ($data as $index => $item) {
                    // Add data to each cell (columns B to G)
                    $sheet->setCellValue('B' . $row, $index + 1);
                    $sheet->setCellValueExplicit('C' . $row, $item->employee->nik, DataType::TYPE_STRING);
                    $sheet->setCellValue('D' . $row, $item->employee->name);
                    $sheet->setCellValue('E' . $row, $item->employee->position);
                    $sheet->setCellValue('F' . $row, $item->mulai);
                    $sheet->setCellValue('G' . $row, $item->selesai);

                    // Loop through columns B to G and apply borders
                    foreach (range('B', 'G') as $column) {
                        $sheet->getStyle($column . $row)->applyFromArray([
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                            'borders' => [
                                'top' => ['borderStyle' => Border::BORDER_THIN],
                                'bottom' => ['borderStyle' => Border::BORDER_THIN],
                                'left' => ['borderStyle' => Border::BORDER_THIN],
                                'right' => ['borderStyle' => Border::BORDER_THIN],
                            ],
                        ]);
                    }
                    $row++;
                }

                // Apply borders to the last row
                $lastRow = $row - 1; // Last data row
                foreach (range('B', 'G') as $column) {
                    $sheet->getStyle($column . $lastRow)->applyFromArray([
                        'borders' => [
                            'top' => ['borderStyle' => Border::BORDER_THIN],
                            'bottom' => ['borderStyle' => Border::BORDER_THIN],
                            'left' => ['borderStyle' => Border::BORDER_THIN],
                            'right' => ['borderStyle' => Border::BORDER_THIN],
                        ],
                    ]);
                }




                // Auto resize columns
                foreach (range('B', 'G') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }

    public function toPdf()
    {
        // // Load the spreadsheet (generated by Maatwebsite Excel)
        // $spreadsheet = IOFactory::load('path/to/your/excel-file.xlsx');

        // // Use the TCPDF writer to convert to PDF
        // $writer = new Tcpdf($spreadsheet);

        // // Set paper size and margins for the PDF
        // $writer->setPaperSize('A4');
        // $writer->setMargins(10, 10, 10); // Set margins

        // // Save the PDF file to the desired location
        // $pdfPath = 'path/to/save/output-file.pdf';
        // $writer->save($pdfPath);
    }
}
