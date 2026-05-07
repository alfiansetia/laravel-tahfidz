@extends('layouts.dashboard')

@section('title', 'Master Data Surah')
@section('header', 'Master Data Surah')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">Daftar Surah Al-Qur'an</h6>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="toggleFilter()">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <button type="button" class="btn btn-primary btn-sm" onclick="addSurah()">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Surah
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Filter Section -->
            <div id="filterSection" class="row mb-4 d-none">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Filter Tempat Turun</label>
                    <select class="form-select" id="filterTempatTurun">
                        <option value="">Semua</option>
                        <option value="Mekah">Mekah</option>
                        <option value="Madinah">Madinah</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover w-100" id="surahTable">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Surah</th>
                            <th>Latin</th>
                            <th>Arti</th>
                            <th>Jumlah Ayat</th>
                            <th>Tempat Turun</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Surah -->
    <div class="modal fade" id="surahModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="modalTitle">Tambah Surah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="surahForm">
                    <div class="modal-body p-4">
                        <input type="hidden" name="id" id="surahId">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold small">Nomor Surah</label>
                                <input type="number" class="form-control" name="nomor" id="nomor" required
                                    placeholder="Contoh: 1">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold small">Nama (Arab)</label>
                                <input type="text" class="form-control" name="nama" id="nama" required
                                    placeholder="Contoh: الفاتحة">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold small">Nama Latin</label>
                                <input type="text" class="form-control" name="nama_latin" id="nama_latin" required
                                    placeholder="Contoh: Al-Fatihah">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold small">Jumlah Ayat</label>
                                <input type="number" class="form-control" name="jumlah_ayat" id="jumlah_ayat" required
                                    placeholder="Contoh: 7">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold small">Tempat Turun</label>
                                <select class="form-select" name="tempat_turun" id="tempat_turun" required>
                                    <option value="Mekah">Mekah</option>
                                    <option value="Madinah">Madinah</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold small">Arti</label>
                                <input type="text" class="form-control" name="arti" id="arti" required
                                    placeholder="Contoh: Pembukaan">
                            </div>
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
                table = $('#surahTable').DataTable({
                    processing: true,
                    ajax: "{{ route('surah.index') }}",
                    language: {
                        url: DATATABLE_LOCALE
                    },
                    columns: [{
                            data: 'nomor'
                        },
                        {
                            data: 'nama',
                            render: function(data) {
                                return `<span class="fs-5">${data}</span>`;
                            }
                        },
                        {
                            data: 'nama_latin'
                        },
                        {
                            data: 'arti'
                        },
                        {
                            data: 'jumlah_ayat'
                        },
                        {
                            data: 'tempat_turun',
                            render: function(data) {
                                let badgeClass = data === 'Mekah' ? 'bg-success' : 'bg-primary';
                                return `<span class="badge ${badgeClass}">${data}</span>`;
                            }
                        },
                        {
                            data: 'id',
                            orderable: false,
                            searchable: false,
                            render: function(data) {
                                return `
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm" onclick="editSurah(${data})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm" onclick="deleteSurah(${data})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        `;
                            }
                        }
                    ],
                });

                // Trigger filter change (Client Side)
                $('#filterTempatTurun').on('change', function() {
                    const val = $(this).val();
                    table.column(5).search(val).draw();
                });

                $('#surahForm').on('submit', function(e) {
                    e.preventDefault();
                    const id = $('#surahId').val();
                    const url = id ? `{{ url('surah') }}/${id}` : "{{ route('surah.store') }}";
                    const method = id ? 'PUT' : 'POST';

                    $('#btnSave').prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

                    $.ajax({
                        url: url,
                        method: method,
                        data: $(this).serialize(),
                        success: function(res) {
                            $('#surahModal').modal('hide');
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

            function addSurah() {
                $('#surahForm')[0].reset();
                $('#surahId').val('');
                $('#modalTitle').text('Tambah Surah');
                $('#surahModal').modal('show');
            }

            function editSurah(id) {
                $.get(`{{ url('surah') }}/${id}`, function(surah) {
                    $('#surahId').val(surah.id);
                    $('#nomor').val(surah.nomor);
                    $('#nama').val(surah.nama);
                    $('#nama_latin').val(surah.nama_latin);
                    $('#jumlah_ayat').val(surah.jumlah_ayat);
                    $('#tempat_turun').val(surah.tempat_turun);
                    $('#arti').val(surah.arti);
                    $('#modalTitle').text('Edit Surah');
                    $('#surahModal').modal('show');
                }).fail(function(err) {
                    showMessage('error', 'Terjadi Kesalahan', err.responseJSON.message);
                });
            }

            function deleteSurah(id) {
                confirmation('Apakah Anda yakin?', 'Data surah ini akan dihapus secara permanen!', function() {
                    $.ajax({
                        url: `{{ url('surah') }}/${id}`,
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
