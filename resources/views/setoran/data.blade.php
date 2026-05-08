@extends('layouts.dashboard')

@section('title', 'Data Setoran Tahfidz')
@section('header', 'Data Setoran Tahfidz')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center pt-4 px-4">
            <h6 class="mb-0 fw-bold text-primary">Riwayat Seluruh Setoran Siswa</h6>
            <div class="d-flex gap-2">
                <a href="{{ route('setoran.index') }}" class="btn btn-primary btn-sm rounded-pill px-3">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Baru
                </a>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="toggleFilter()">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
            </div>
        </div>
        <div class="card-body p-4">
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
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Filter Jenis</label>
                    <select class="form-select select2-filter" id="filterJenis">
                        <option value="">Semua Jenis</option>
                        <option value="ziyadah">Ziyadah</option>
                        <option value="murojaah">Murojaah</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Rentang Tanggal</label>
                    <div id="reportrange" class="form-control"
                        style="cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                        <i class="bi bi-calendar-event me-1"></i>&nbsp;
                        <span></span> <i class="bi bi-chevron-down float-end mt-1"></i>
                    </div>
                    <input type="hidden" id="startDate">
                    <input type="hidden" id="endDate">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary w-100" id="btnTerapkan">Filter</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle w-100" id="setoranTable">
                    <thead class="bg-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Surah</th>
                            <th>Ayat</th>
                            <th>Jenis</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Edit Setoran -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold">Edit Data Setoran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEdit">
                    @csrf
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Siswa</label>
                            <input type="text" class="form-control bg-light" id="edit_nama_siswa" readonly>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold">Tanggal</label>
                                <input type="text" class="form-control" name="tanggal" id="edit_tanggal" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">Jenis Setoran</label>
                                <select class="form-select" name="jenis_setoran" id="edit_jenis" required>
                                    <option value="ziyadah">Ziyadah</option>
                                    <option value="murojaah">Murojaah</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Pilih Surah</label>
                            <select class="form-select select2-modal" name="surah_id" id="edit_surah_id" required>
                                @foreach ($surahs as $s)
                                    <option value="{{ $s->id }}" data-ayat="{{ $s->jumlah_ayat }}">
                                        {{ $s->nomor }}. {{ $s->nama_latin }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold">Ayat Dari</label>
                                <input type="number" class="form-control" name="ayat_dari" id="edit_dari" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">Ayat Sampai</label>
                                <input type="number" class="form-control" name="ayat_sampai" id="edit_sampai" required>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-bold">Status Kelancaran</label>
                            <select class="form-select" name="status" id="edit_status" required>
                                <option value="lancar">Lancar</option>
                                <option value="cukup">Cukup</option>
                                <option value="kurang">Kurang</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pb-4 px-4">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4" id="btnUpdate">Simpan
                            Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            let table;
            $(function() {
                $('.select2-filter').select2({
                    theme: 'bootstrap-5',
                });
                $('.select2-modal').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#modalEdit'),
                    width: '100%'
                });

                $('#edit_tanggal').daterangepicker({
                    singleDatePicker: true,
                    autoApply: true,
                    locale: {
                        format: 'DD/MM/YYYY'
                    }
                });

                table = $('#setoranTable').DataTable({
                    processing: true,
                    ajax: {
                        url: "{{ route('setoran.data') }}",
                        data: function(d) {
                            d.kelas = $('#filterKelas').val();
                            d.jenis = $('#filterJenis').val();
                            d.start_date = $('#startDate').val();
                            d.end_date = $('#endDate').val();
                        }
                    },
                    columns: [{
                            data: 'tanggal',
                            render: data => moment(data).format('DD/MM/YYYY')
                        },
                        {
                            data: 'siswa.nama',
                            render: data => `<strong>${data}</strong>`
                        },
                        {
                            data: 'siswa.kelas'
                        },
                        {
                            data: 'surah.nama_latin'
                        },
                        {
                            data: null,
                            render: row => `${row.ayat_dari} - ${row.ayat_sampai}`
                        },
                        {
                            data: 'jenis_setoran',
                            render: data =>
                                `<span class="badge ${data === 'ziyadah' ? 'bg-info text-dark' : 'bg-secondary'} text-uppercase small">${data}</span>`
                        },
                        {
                            data: 'status',
                            render: data => {
                                let badge = data === 'lancar' ? 'bg-success' : (data === 'cukup' ?
                                    'bg-warning text-dark' : 'bg-danger');
                                return `<span class="badge ${badge} text-uppercase small">${data}</span>`;
                            }
                        },
                        {
                            data: 'id',
                            className: 'text-center',
                            render: data => `
                                <div class="btn-group">
                                    <button class="btn btn-light btn-sm" onclick="editSetoran(${data})" title="Edit">
                                        <i class="bi bi-pencil-square text-primary"></i>
                                    </button>
                                    <button class="btn btn-light btn-sm" onclick="deleteSetoran(${data})" title="Hapus">
                                        <i class="bi bi-trash text-danger"></i>
                                    </button>
                                </div>
                            `
                        }
                    ],
                    order: [
                        [0, 'desc']
                    ],
                    language: {
                        url: DATATABLE_LOCALE
                    }
                });

                // DateRangePicker untuk Filter
                var start = moment().subtract(29, 'days');
                var end = moment();

                function cb(start, end) {
                    $('#reportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
                    $('#startDate').val(start.format('YYYY-MM-DD'));
                    $('#endDate').val(end.format('YYYY-MM-DD'));
                }
                $('#reportrange').daterangepicker({
                    startDate: start,
                    endDate: end,
                    ranges: {
                        'Hari Ini': [moment(), moment()],
                        '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                        '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
                        'Bulan Ini': [moment().startOf('month'), moment().endOf('month')]
                    },
                    locale: {
                        format: 'DD/MM/YYYY'
                    }
                }, cb);
                cb(start, end);

                $('#btnTerapkan').click(function() {
                    table.ajax.reload();
                });

                // Submit Update
                $('#formEdit').on('submit', function(e) {
                    e.preventDefault();
                    let id = $('#edit_id').val();

                    // Format date for DB
                    let formData = $(this).serializeArray();
                    let dateIdx = formData.findIndex(x => x.name === 'tanggal');
                    formData[dateIdx].value = moment(formData[dateIdx].value, 'DD/MM/YYYY').format(
                        'YYYY-MM-DD');

                    $('#btnUpdate').prop('disabled', true).text('Menyimpan...');

                    $.ajax({
                        url: `{{ url('setoran') }}/${id}`,
                        method: 'PUT',
                        data: formData,
                        success: function(res) {
                            $('#modalEdit').modal('hide');
                            table.ajax.reload();
                            showMessage('success', 'Berhasil', res.message);
                        },
                        error: function(err) {
                            showMessage('error', 'Gagal', err.responseJSON.message);
                        },
                        complete: function() {
                            $('#btnUpdate').prop('disabled', false).text('Simpan Perubahan');
                        }
                    });
                });
            });

            function toggleFilter() {
                $('#filterSection').toggleClass('d-none');
            }

            function editSetoran(id) {
                $.get(`{{ url('setoran') }}/${id}`, function(data) {
                    $('#edit_id').val(data.id);
                    $('#edit_nama_siswa').val(data.siswa.nama);
                    $('#edit_tanggal').val(moment(data.tanggal).format('DD/MM/YYYY')).trigger('change');
                    $('#edit_jenis').val(data.jenis_setoran);
                    $('#edit_surah_id').val(data.surah_id).trigger('change');
                    $('#edit_dari').val(data.ayat_dari);
                    $('#edit_sampai').val(data.ayat_sampai);
                    $('#edit_status').val(data.status);
                    $('#modalEdit').modal('show');
                });
            }

            function deleteSetoran(id) {
                confirmation('Hapus data ini?', 'Data setoran tidak dapat dikembalikan setelah dihapus!', function() {
                    $.ajax({
                        url: `{{ url('setoran') }}/${id}`,
                        method: 'DELETE',
                        success: function(res) {
                            table.ajax.reload();
                            showMessage('success', 'Terhapus', res.message);
                        }
                    });
                });
            }
        </script>
    @endpush
@endsection
