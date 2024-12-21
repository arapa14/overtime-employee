<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Overtime</title>
    <!-- Link ke Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Navbar Styles */
        nav {
            background-color: #007BFF;
            padding: 15px;
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        nav a {
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        nav a:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav>
        <a href="{{ route('employee.create') }}">Employee</a>
        <a href="{{ route('overtime.index') }}">Overtime</a>
    </nav>

    <div class="container">
        <h2 class="my-4">Daftar Overtime</h2>
        <a href="{{ url('/export-overtime') }}" class="btn btn-success mb-3">
            Export to Excel
        </a>
        

        <!-- Button to trigger the modal -->
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addOvertimeModal">Tambah Overtime</button>

        <table class="table table-bordered" id="overtime-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Hari</th>
                    <th>Status Hari</th>
                    <th>Deskripsi</th>
                    <th>Employee</th>
                    <th>Mulai</th>
                    <th>Selesai</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($overtimes as $overtime)
                    <tr>
                        <td>{{ $overtime->tanggal }}</td>
                        <td>{{ $overtime->hari }}</td>
                        <td>{{ $overtime->status_hari }}</td>
                        <td>{{ $overtime->deskripsi }}</td>
                        <td>{{ $overtime->employee->name }}</td>
                        <td>{{ $overtime->mulai }}</td>
                        <td>{{ $overtime->selesai }}</td>
                        <td>
                            <!-- Contoh aksi Edit dan Hapus -->
                            <a href="{{ route('overtime.edit', $overtime->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('overtime.destroy', $overtime->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal for adding overtime -->
    <div class="modal fade" id="addOvertimeModal" tabindex="-1" aria-labelledby="addOvertimeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addOvertimeModalLabel">Tambah Overtime</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Overtime form -->
                    <form id="overtime-form" action="{{ route('overtime.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="hari">Hari</label>
                            <input type="text" name="hari" id="hari" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="status_hari">Status Hari</label>
                            <select name="status_hari" id="status_hari" class="form-control" required>
                                <option value="kerja">Kerja</option>
                                <option value="libur">Libur</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" class="form-control"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="employee_id">Employee</label>
                            <select name="employee_id" id="employee_id" class="form-control" required>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="mulai">Mulai</label>
                            <input type="time" name="mulai" id="mulai" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="selesai">Selesai</label>
                            <input type="time" name="selesai" id="selesai" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional: Script untuk Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <!-- jQuery AJAX Update Script -->
    <script>
        $(document).ready(function() {
            $('#overtime-form').on('submit', function(e) {
                e.preventDefault();  // Prevent the form from submitting normally
                var formData = $(this).serialize();  // Serialize form data

                $.ajax({
                    url: $(this).attr('action'),  // Form action URL
                    method: 'POST',               // HTTP method
                    data: formData,               // Data to be sent
                    success: function(response) {
                        // On success, close the modal and reload the table
                        $('#addOvertimeModal').modal('hide');
                        location.reload();  // Reload the page to show updated data
                    },
                    error: function(xhr, status, error) {
                        // Handle errors
                        alert('Terjadi kesalahan, coba lagi.');
                    }
                });
            });
        });
    </script>
</body>
</html>
