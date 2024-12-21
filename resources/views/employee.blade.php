<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management</title>
    <!-- Link to Bootstrap CSS -->
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

        /* Table styles */
        .table th, .table td {
            vertical-align: middle;
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
        <h2 class="my-4">Employee Management</h2>

        <!-- Button to trigger the modal -->
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">Tambah Employee</button>

        <!-- Employee Table -->
        <table class="table table-bordered" id="employee-table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>NIK</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $employee)
                    <tr id="employee-{{ $employee->id }}">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->position }}</td>
                        <td>{{ $employee->nik }}</td>
                        <td>
                            <button class="btn btn-warning btn-sm edit-btn" data-id="{{ $employee->id }}" data-name="{{ $employee->name }}" data-position="{{ $employee->position }}" data-nik="{{ $employee->nik }}" data-bs-toggle="modal" data-bs-target="#editEmployeeModal">Edit</button>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $employee->id }}">Hapus</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal for adding employee -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEmployeeModalLabel">Tambah Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Employee form -->
                    <form id="employee-form" action="{{ route('employee.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="nik">NIK</label>
                            <input type="text" name="nik" id="nik" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="position">Position</label>
                            <input type="text" name="position" id="position" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for editing employee -->
    <div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-labelledby="editEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEmployeeModalLabel">Edit Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Employee edit form -->
                    <form id="edit-employee-form" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="edit-nik">NIK</label>
                            <input type="text" name="nik" id="edit-nik" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="edit-name">Name</label>
                            <input type="text" name="name" id="edit-name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="edit-position">Position</label>
                            <input type="text" name="position" id="edit-position" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional: Script untuk Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- jQuery AJAX Script -->
    <script>
        $(document).ready(function() {
            // Open edit modal with data
            $('.edit-btn').on('click', function() {
                var employeeId = $(this).data('id');
                var employeeName = $(this).data('name');
                var employeePosition = $(this).data('position');
                var employeeNIK = $(this).data('nik');
                
                $('#edit-nik').val(employeeNIK);
                $('#edit-name').val(employeeName);
                $('#edit-position').val(employeePosition);
                
                $('#edit-employee-form').attr('action', '/employee/' + employeeId);
            });

            // Handle edit form submission via AJAX
            $('#edit-employee-form').on('submit', function(e) {
                e.preventDefault();  // Prevent default form submission
                var formData = $(this).serialize();  // Serialize the form data
                var actionUrl = $(this).attr('action');

                $.ajax({
                    url: actionUrl,  // Get form action URL
                    method: 'POST',   // Send POST request
                    data: formData,   // Send form data
                    success: function(response) {
                        // Close the modal and reload the table on success
                        $('#editEmployeeModal').modal('hide');
                        $('#employee-' + response.id).replaceWith(response.html);
                    },
                    error: function(xhr, status, error) {
                        // Handle errors (e.g., validation issues)
                        alert('Terjadi kesalahan, coba lagi.');
                    }
                });
            });

            // Handle delete button click
            $('.delete-btn').on('click', function() {
                var employeeId = $(this).data('id');
                var row = $('#employee-' + employeeId);

                if (confirm("Are you sure you want to delete this employee?")) {
                    $.ajax({
                        url: '/employee/' + employeeId,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            // Remove row from table on success
                            row.remove();
                        },
                        error: function(xhr, status, error) {
                            // Handle errors
                            alert('Terjadi kesalahan, coba lagi.');
                        }
                    });
                }
            });
        });
    </script>

</body>
</html>
