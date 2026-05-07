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
                    <select class="form-select" id="filterJenis">
                        <option value="">Semua Jenis</option>
                        <option value="ziyadah">Ziyadah</option>
                        <option value="murojaah">Murojaah</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Filter Status</label>
                    <select class="form-select" id="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="lancar">Lancar</option>
                        <option value="cukup">Cukup</option>
                        <option value="kurang">Kurang</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Rentang Tanggal</label>
                    <div id="reportrange" class="form-control"
                        style="cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                        <i class="bi bi-calendar-event me-1"></i>&nbsp;
                        <span></span> <i class="bi bi-chevron-down float-end mt-1"></i>
                    </div>
                    <input type="hidden" id="startDate">
                    <input type="hidden" id="endDate">
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

    @push('js')
        <script>
            let table;
            $(function() {
                $('.select2-filter').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Filter Kelas'
                });

                table = $('#setoranTable').DataTable({
                    processing: true,
                    ajax: "{{ route('setoran.data') }}",
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
                            <button class="btn btn-outline-danger btn-sm" onclick="deleteSetoran(${data})">
                                <i class="bi bi-trash"></i>
                            </button>
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

                $('#filterKelas').on('change', function() {
                    const val = $(this).val();
                    table.column(2).search(val).draw();
                });

                $('#filterJenis').on('change', function() {
                    const val = $(this).val();
                    table.column(5).search(val).draw();
                });

                $('#filterStatus').on('change', function() {
                    const val = $(this).val();
                    table.column(6).search(val).draw();
                });

                // Date Range Picker Logic
                var start = moment().subtract(29, 'days');
                var end = moment();

                function cb(start, end) {
                    $('#reportrange span').html(start.format('DD MMM YYYY') + ' - ' + end.format('DD MMM YYYY'));
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

                // Initial clear or set range
                $('#reportrange span').html('Semua Tanggal');
                $('#startDate, #endDate').val('');

                // Custom Filter Logic for Date Range
                $.fn.dataTable.ext.search.push(
                    function(settings, data, dataIndex) {
                        var min = $('#startDate').val();
                        var max = $('#endDate').val();
                        var date = data[0]; // Kolom Tanggal ada di index 0

                        if (min === '' && max === '') return true;
                        if (min === '' && date <= max) return true;
                        if (max === '' && date >= min) return true;
                        if (date >= min && date <= max) return true;
                        return false;
                    }
                );
            });

            function toggleFilter() {
                $('#filterSection').toggleClass('d-none');
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
