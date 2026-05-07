@extends('layouts.dashboard')

@section('title', 'Master Data User')
@section('header', 'Master Data User')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">Daftar Pengguna Sistem</h6>
            <button type="button" class="btn btn-primary btn-sm" onclick="addUser()">
                <i class="bi bi-plus-lg me-1"></i> Tambah User
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover w-100" id="userTable">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal User -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="modalTitle">Tambah User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="userForm">
                    <div class="modal-body p-4">
                        <input type="hidden" name="id" id="userId">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nama Lengkap</label>
                            <input type="text" class="form-control" name="name" id="name" required
                                placeholder="Contoh: Ahmad Abdullah">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Alamat Email</label>
                            <input type="email" class="form-control" name="email" id="email" required
                                placeholder="nama@email.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Kata Sandi</label>
                            <input type="password" class="form-control" name="password" id="password"
                                placeholder="Minimal 8 karakter">
                            <small class="text-muted" id="passwordHelp">Biarkan kosong jika tidak ingin mengubah
                                password.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pb-4 px-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4" id="btnSave">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            let table;
            $(function() {
                table = $('#userTable').DataTable({
                    processing: true,
                    ajax: "{{ route('users.index') }}",
                    columns: [{
                            data: 'name'
                        },
                        {
                            data: 'email'
                        },
                        {
                            data: 'id',
                            orderable: false,
                            searchable: false,
                            render: function(data) {
                                return `
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm" onclick="editUser(${data})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm" onclick="deleteUser(${data})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        `;
                            }
                        }
                    ],
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
                    }
                });

                $('#userForm').on('submit', function(e) {
                    e.preventDefault();
                    const id = $('#userId').val();
                    const url = id ? `{{ url('users') }}/${id}` : "{{ route('users.store') }}";
                    const method = id ? 'PUT' : 'POST';

                    $('#btnSave').prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

                    $.ajax({
                        url: url,
                        method: method,
                        data: $(this).serialize(),
                        success: function(res) {
                            $('#userModal').modal('hide');
                            table.ajax.reload();
                            showMessage('success', 'Berhasil!', res.message);
                        },
                        error: function(err) {
                            const errors = err.responseJSON.errors;
                            let errorList = '';
                            for (let key in errors) {
                                errorList += `${errors[key][0]}<br>`;
                            }
                            showMessage('error', 'Terjadi Kesalahan', errorList);
                        },
                        complete: function() {
                            $('#btnSave').prop('disabled', false).html('Simpan');
                        }
                    });
                });
            });

            function addUser() {
                $('#userForm')[0].reset();
                $('#userId').val('');
                $('#modalTitle').text('Tambah User');
                $('#passwordHelp').addClass('d-none');
                $('#password').prop('required', true);
                $('#userModal').modal('show');
            }

            function editUser(id) {
                $.get(`{{ url('users') }}/${id}`, function(user) {
                    $('#userId').val(user.id);
                    $('#name').val(user.name);
                    $('#email').val(user.email);
                    $('#password').val('').prop('required', false);
                    $('#modalTitle').text('Edit User');
                    $('#passwordHelp').removeClass('d-none');
                    $('#userModal').modal('show');
                }).fail(function(err) {
                    showMessage('error', 'Terjadi Kesalahan', err.responseJSON.message);
                });
            }

            function deleteUser(id) {
                confirmation('Apakah Anda yakin?', 'Data user ini akan dihapus secara permanen!', function() {
                    $.ajax({
                        url: `{{ url('users') }}/${id}`,
                        method: 'DELETE',
                        success: function(res) {
                            table.ajax.reload();
                            showMessage('success', 'Dihapus!', res.message);
                        }
                    });
                });
            }
        </script>
    @endpush
@endsection
