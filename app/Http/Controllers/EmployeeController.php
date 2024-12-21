<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ambil semua data employee
        $employees = Employee::all();
        // Cek apakah form akan ditampilkan
        $showForm = $request->input('show_form');

        // Jika tidak ada data, tampilkan pesan atau alternatif lain
        if ($employees->isEmpty()) {
            $message = 'No employees found yet.';
        } else {
            $message = null;
        }

        // Kirim data dan pesan ke tampilan
        return view('employee', compact('employees', 'showForm', 'message'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::all();
        return view('employee', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //validasi data
        $request->validate([
            'nik' => 'required|numeric',
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255'
        ]);

        //menyimpan data
        Employee::create($request->all());

        //redirecht dengan pesan sukses
        return redirect()->route('employee.create')->with('success', 'Employee added suscessfully');
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
        // Validasi input
        $request->validate([
            'nik' => 'required|numeric',
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255'
        ]);

        // Menemukan employee berdasarkan ID
        $employee = Employee::findOrFail($id);

        // Mengupdate data employee
        $employee->update($request->all());

        // Mengembalikan response JSON
        return response()->json([
            'success' => true,
            'message' => 'Employee updated successfully',
            'employee' => $employee
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Menemukan employee berdasarkan ID
        $employee = Employee::findOrFail($id);

        // Menghapus employee
        $employee->delete();

        // Mengembalikan response JSON
        return response()->json([
            'success' => true,
            'message' => 'Employee deleted successfully'
        ]);
    }
}
