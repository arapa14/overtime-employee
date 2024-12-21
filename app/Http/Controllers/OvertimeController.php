<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Overtime;
use Illuminate\Http\Request;

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

        return redirect()-> route('overtime.index')->with('success', 'Overtime added successfully');
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
}
