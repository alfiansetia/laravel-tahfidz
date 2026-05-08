@extends('layouts.dashboard')

@section('title', 'Master Data Siswa')
@section('header', 'Master Data Siswa')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">Daftar Siswa Tahfidz</h6>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="toggleFilter()">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <button type="button" class="btn btn-primary btn-sm" onclick="addSiswa()">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Siswa
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Filter Section -->
            <div id="filterSection" class="row mb-4 d-none">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Filter Kelas</label>
                    <select class="form-select select2-filter" id="filterKelas">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k }}">{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover w-100" id="siswaTable">
                    <thead>
                        <tr>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Siswa -->
    <div class="modal fade" id="siswaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="modalTitle">Tambah Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="siswaForm">
                    <div class="modal-body p-4">
                        <input type="hidden" name="id" id="siswaId">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama" id="nama" required
                                placeholder="Contoh: Muhammad Al-Fatih">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Kelas</label>
                            <select class="form-select select2" name="kelas" id="kelas" required>
                                <option value="">Pilih Kelas</option>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k }}">{{ $k }}</option>
                                @endforeach
                            </select>
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
                // Initialize Select2
                $('.select2').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#siswaModal')
                });

                // Initialize Select2 for Filter
                $('.select2-filter').select2({
                    theme: 'bootstrap-5',
                });

                table = $('#siswaTable').DataTable({
                    processing: true,
                    ajax: "{{ route('siswa.index') }}",
                    language: {
                        url: DATATABLE_LOCALE
                    },
                    columns: [{
                            data: 'nama'
                        },
                        {
                            data: 'kelas'
                        },
                        {
                            data: 'id',
                            orderable: false,
                            searchable: false,
                            render: function(data) {
                                return `
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm" onclick="editSiswa(${data})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm" onclick="deleteSiswa(${data})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        `;
                            }
                        }
                    ],
                });

                // Trigger filter change (Client Side)
                $('#filterKelas').on('change', function() {
                    const val = $(this).val();
                    // Gunakan regex ^val$ untuk mencari yang persis sama
                    table.column(1).search(val ? '^' + val + '$' : '', true, false).draw();
                });

                $('#siswaForm').on('submit', function(e) {
                    e.preventDefault();
                    const id = $('#siswaId').val();
                    const url = id ? `{{ url('siswa') }}/${id}` : "{{ route('siswa.store') }}";
                    const method = id ? 'PUT' : 'POST';

                    $('#btnSave').prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

                    $.ajax({
                        url: url,
                        method: method,
                        data: $(this).serialize(),
                        success: function(res) {
                            $('#siswaModal').modal('hide');
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

            function toggleFilter() {
                $('#filterSection').toggleClass('d-none');
            }

            function addSiswa() {
                $('#siswaForm')[0].reset();
                $('#siswaId').val('');
                $('#kelas').val(null).trigger('change');
                $('#modalTitle').text('Tambah Siswa');
                $('#siswaModal').modal('show');
            }

            function editSiswa(id) {
                $.get(`{{ url('siswa') }}/${id}`, function(siswa) {
                    $('#siswaId').val(siswa.id);
                    $('#nama').val(siswa.nama);
                    $('#kelas').val(siswa.kelas).trigger('change');
                    $('#modalTitle').text('Edit Siswa');
                    $('#siswaModal').modal('show');
                }).fail(function(err) {
                    showMessage('error', 'Terjadi Kesalahan', err.responseJSON.message);
                });
            }

            function deleteSiswa(id) {
                confirmation('Apakah Anda yakin?', 'Data siswa ini akan dihapus secara permanen!', function() {
                    $.ajax({
                        url: `{{ url('siswa') }}/${id}`,
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
