<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
    use HasFactory;

    protected $table = 'overtime';

    protected $fillable = [
        'tanggal',
        'hari',
        'status_hari',
        'deskripsi',
        'employee_id',
        'mulai',
        'selesai',
    ];

    // Relasi dengan Employee (Seorang Overtime dimiliki oleh satu Employee)
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
