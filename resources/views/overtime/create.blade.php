<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Overtime</title>
    <!-- Link ke Bootstrap CSS atau styling lain -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery for AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <div class="container">
        <h2 class="my-4">Tambah Overtime</h2>
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

    <!-- Optional: Script untuk Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <!-- jQuery AJAX submission script -->
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
                        // On success, replace content without reloading
                        alert('Overtime berhasil ditambahkan!');
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
