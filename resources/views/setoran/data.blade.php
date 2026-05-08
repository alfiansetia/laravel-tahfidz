@extends('layouts.dashboard')

@section('title', 'Data Setoran Tahfidz')
@section('header', 'Data Setoran Tahfidz')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">Riwayat Seluruh Setoran Siswa</h6>
            <div class="d-flex gap-2">
                <a href="{{ route('setoran.index') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Setoran Baru
                </a>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="toggleFilter()">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Filter Section -->
            <div id="filterSection" class="row mb-4 d-none g-2">
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Kelas</label>
                    <select class="form-select select2-filter" id="filterKelas">
                        <option value="">Semua</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k }}">{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Jenis</label>
                    <select class="form-select select2-filter" id="filterJenis">
                        <option value="">Semua</option>
                        <option value="ziyadah">Ziyadah</option>
                        <option value="murojaah">Murojaah</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Status</label>
                    <select class="form-select select2-filter" id="filterStatus">
                        <option value="">Semua</option>
                        <option value="lancar">Lancar</option>
                        <option value="cukup">Cukup</option>
                        <option value="kurang">Kurang</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Rentang Tanggal</label>
                    <div id="reportrange" class="form-control"
                        style="cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                        <i class="bi bi-calendar-event me-1"></i>&nbsp;
                        <span></span> <i class="bi bi-chevron-down float-end mt-1"></i>
                    </div>
                    <input type="hidden" id="startDate">
                    <input type="hidden" id="endDate">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-primary w-100 shadow-sm" id="btnTerapkan">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover w-100" id="setoranTable">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Surah</th>
                            <th>Ayat</th>
                            <th>Jenis</th>
                            <th>Status</th>
                            <th width="5%">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Edit Setoran -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow-sm">
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
                            <input type="text" class="form-control bg-light" id="edit_siswa_nama" readonly>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Tanggal</label>
                                <input type="text" name="tanggal" id="edit_tanggal" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Jenis Setoran</label>
                                <select name="jenis_setoran" id="edit_jenis_setoran" class="form-select" required>
                                    <option value="ziyadah">Ziyadah</option>
                                    <option value="murojaah">Murojaah</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Pilih Surah</label>
                            <select name="surah_id" id="edit_surah_id" class="form-select select2-modal" required>
                                @foreach ($surahs as $s)
                                    <option value="{{ $s->id }}">{{ $s->nomor }}. {{ $s->nama_latin }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-bold">Ayat Dari</label>
                                <input type="number" name="ayat_dari" id="edit_ayat_dari" class="form-control"
                                    required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label small fw-bold">Ayat Sampai</label>
                                <input type="number" name="ayat_sampai" id="edit_ayat_sampai" class="form-control"
                                    required>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-bold">Status Kelancaran</label>
                            <select name="status" id="edit_status" class="form-select" required>
                                <option value="lancar">Lancar</option>
                                <option value="cukup">Cukup</option>
                                <option value="kurang">Kurang</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pb-4 px-4">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
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
                    serverSide: false, // Kita gunakan client-side processing tapi data dipanggil via AJAX dengan filter
                    ajax: {
                        url: "{{ route('setoran.data') }}",
                        data: function(d) {
                            d.kelas = $('#filterKelas').val();
                            d.jenis = $('#filterJenis').val();
                            d.status = $('#filterStatus').val();
                            d.start_date = $('#startDate').val();
                            d.end_date = $('#endDate').val();
                        }
                    },
                    columns: [{
                            data: 'tanggal',
                            render: function(data) {
                                return moment(data).format('DD/MM/YYYY');
                            }
                        },
                        {
                            data: 'siswa.nama'
                        },
                        {
                            data: 'siswa.kelas'
                        },
                        {
                            data: 'surah.nama_latin'
                        },
                        {
                            data: null,
                            render: function(data) {
                                return `Ayat ${data.ayat_dari} - ${data.ayat_sampai}`;
                            }
                        },
                        {
                            data: 'jenis_setoran',
                            render: function(data) {
                                let badgeClass = data === 'ziyadah' ? 'bg-info text-dark' :
                                    'bg-secondary';
                                return `<span class="badge ${badgeClass} text-uppercase small">${data}</span>`;
                            }
                        },
                        {
                            data: 'status',
                            render: function(data) {
                                let badgeClass = '';
                                if (data === 'lancar') badgeClass = 'bg-success';
                                else if (data === 'cukup') badgeClass = 'bg-warning text-dark';
                                else badgeClass = 'bg-danger';
                                return `<span class="badge ${badgeClass} text-uppercase small">${data}</span>`;
                            }
                        },
                        {
                            data: 'id',
                            orderable: false,
                            searchable: false,
                            render: function(data) {
                                return `
                    <div class="btn-group">
                        <button class="btn btn-outline-primary btn-sm" onclick="editSetoran(${data})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-danger btn-sm" onclick="deleteSetoran(${data})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                `;
                            }
                        }
                    ],
                    order: [
                        [0, 'desc']
                    ],
                    language: {
                        url: DATATABLE_LOCALE
                    }
                });

                $('#formEdit').on('submit', function(e) {
                    e.preventDefault();
                    let id = $('#edit_id').val();
                    let url = `{{ url('setoran') }}/${id}`;

                    let formData = $(this).serializeArray();
                    // Konversi format DD/MM/YYYY ke YYYY-MM-DD sebelum dikirim ke backend
                    let tanggalIdx = formData.findIndex(x => x.name === 'tanggal');
                    if (tanggalIdx !== -1) {
                        formData[tanggalIdx].value = moment(formData[tanggalIdx].value, 'DD/MM/YYYY').format(
                            'YYYY-MM-DD');
                    }

                    $.ajax({
                        url: url,
                        method: 'PUT',
                        data: $.param(formData),
                        success: function(res) {
                            $('#modalEdit').modal('hide');
                            table.ajax.reload();
                            showMessage('success', 'Berhasil', res.message);
                        },
                        error: function(err) {
                            showMessage('error', 'Gagal', err.responseJSON.message);
                        }
                    });
                });

                // DateRangePicker untuk Filter
                var start = moment().subtract(29, 'days');
                var end = moment();

                function cb(start, end) {
                    $('#reportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
                    $('#startDate').val(start.format('YYYY-MM-DD'));
                    $('#endDate').val(end.format('YYYY-MM-DD'));
                    if (table) table.draw();
                }

                $('#reportrange').daterangepicker({
                    startDate: start,
                    endDate: end,
                    ranges: {
                        'Hari Ini': [moment(), moment()],
                        'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                        '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
                        'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                        'Bulan Lalu': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                            'month').endOf('month')]
                    },
                    locale: {
                        format: 'DD/MM/YYYY',
                        applyLabel: "Terapkan",
                        cancelLabel: "Batal",
                        customRangeLabel: "Pilih Sendiri",
                    }
                }, cb);

                $('#reportrange span').html('Semua Tanggal');
                $('#startDate, #endDate').val('');

                $('#btnTerapkan').click(function() {
                    table.ajax.reload();
                });
            });

            function toggleFilter() {
                $('#filterSection').toggleClass('d-none');
            }

            function editSetoran(id) {
                $.get(`{{ url('setoran') }}/${id}`, function(res) {
                    $('#edit_id').val(res.id);
                    $('#edit_siswa_nama').val(res.siswa.nama);
                    $('#edit_tanggal').data('daterangepicker').setStartDate(moment(res.tanggal).format('DD/MM/YYYY'));
                    $('#edit_tanggal').data('daterangepicker').setEndDate(moment(res.tanggal).format('DD/MM/YYYY'));
                    $('#edit_jenis_setoran').val(res.jenis_setoran);
                    $('#edit_surah_id').val(res.surah_id).trigger('change');
                    $('#edit_ayat_dari').val(res.ayat_dari);
                    $('#edit_ayat_sampai').val(res.ayat_sampai);
                    $('#edit_status').val(res.status);
                    $('#modalEdit').modal('show');
                });
            }

            function deleteSetoran(id) {
                confirmation('Hapus Setoran?', 'Data setoran ini akan dihapus secara permanen!', function() {
                    $.ajax({
                        url: `{{ url('setoran') }}/${id}`,
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
