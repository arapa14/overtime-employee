<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Overtime;
use Illuminate\Http\Request;
use App\Exports\OvertimeExport;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Tcpdf;

class OvertimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua data overtime untuk ditampilkan di index
        $overtimes = Overtime::with('employee')->get(); // Menyertakan relasi employee
        $employees = Employee::all();
        return view('overtime.index', compact('overtimes', 'employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil semua data employee untuk dropdown
        $employees = Employee::all();
        return view('overtime.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'hari' => 'required|string',
            'status_hari' => 'required|in:kerja,libur',
            'deskripsi' => 'nullable|string',
            'employee_id' => 'required|exists:employee,id',
            'mulai' => 'required|date_format:H:i',
            'selesai' => 'required|date_format:H:i|after:mulai',
        ]);

        Overtime::create($request->all());

        return redirect()->route('overtime.index')->with('success', 'Overtime added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function export()
    {
        // Define the base file path
        $basePath = 'excel/overtime_report';
        $extension = '.xlsx';

        // Check if the file already exists and append a number if it does
        $filePath = $basePath . $extension;
        $counter = 1;

        // Loop to find an available filename
        while (Storage::disk('public')->exists($filePath)) {
            $filePath = $basePath . '_' . $counter . $extension;
            $counter++;
        }

        // Export data to Excel and store it on the 'public' disk
        Excel::store(new OvertimeExport, $filePath, 'public');

        // Return the file for download
        return response()->download(storage_path('app/public/' . $filePath));
    }


    public function toPdf()
    {
        try {
            // Path to the Excel file
            $excelFilePath = storage_path('app/public/excel/overtime_report.xlsx');
        
            // Check if the file exists
            if (!file_exists($excelFilePath)) {
                return response()->json(['error' => 'Excel file not found.'], 404);
            }
        
            // Load the Excel file into a Spreadsheet object
            $spreadsheet = IOFactory::load($excelFilePath);
        
            // Create a Tcpdf writer instance
            $writer = new Tcpdf($spreadsheet);
        
            // Define the output PDF directory and base file name
            $pdfFolder = storage_path('app/public/pdf');
            $baseFileName = 'overtime_report';
            $extension = '.pdf';
        
            // Ensure the PDF directory exists
            if (!is_dir($pdfFolder)) {
                mkdir($pdfFolder, 0777, true);
            }
        
            // Generate unique file name
            $pdfPath = $pdfFolder . '/' . $baseFileName . $extension;
            $counter = 1;
            while (file_exists($pdfPath)) {
                $pdfPath = $pdfFolder . '/' . $baseFileName . '_' . $counter . $extension;
                $counter++;
            }
        
            // Save the spreadsheet as a PDF
            $writer->save($pdfPath);
        
            // Return the PDF for download
            return response()->download($pdfPath);
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            return response()->json(['error' => 'Failed to process the Excel file: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
        
    }
}
