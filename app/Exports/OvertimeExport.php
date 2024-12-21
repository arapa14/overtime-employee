<?php

namespace App\Exports;

use App\Models\Overtime;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OvertimeExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Mendapatkan data dari model overtime
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Overtime::with('employee')->get(); // Memuat relasi employee
    }

    /**
     * Menentukan headings (kolom) yang akan tampil di file Excel
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Employee Name',
            'NIK',
            'Position',
            'Overtime Hours',
            'Date',
            'Description',
            'Created At',
            'Updated At'
        ];
    }

     /**
     * Menentukan data yang akan dipetakan untuk setiap baris
     *
     * @param  \App\Models\Overtime  $item
     * @return array
     */
    public function map($item): array
    {
        return [
            $item->id,
            $item->employee->name,     // Nama karyawan
            $item->employee->nik,      // NIK karyawan
            $item->employee->position, // Jabatan karyawan
            $item->overtime_hours,     // Jam lembur (pastikan field ini ada di model Overtime)
            $item->tanggal,
            $item->deskripsi,
            $item->created_at,
            $item->updated_at
        ];
    }
}
