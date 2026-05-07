@extends('layouts.dashboard')

@section('title', 'Analisis Prediksi Hafalan')
@section('header', 'Analisis Prediksi Hafalan (Random Forest)')

@push('css')
    <style>
        .dt-buttons { margin-bottom: 0 !important; }
        .dataTables_wrapper .dataTables_filter { margin-bottom: 0 !important; }
        .dt-custom-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 1.5rem; flex-wrap: wrap; gap: 10px;
        }
        .btn-group .btn { border-radius: 8px !important; margin-right: 5px; }
        .chart-container { position: relative; height: 300px; width: 100%; }
    </style>
@endpush

@section('content')
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="alert alert-info border-0 shadow-sm h-100 mb-0">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-cpu fs-2"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Kecerdasan Buatan (AI) Sedang Menganalisis...</h6>
                        <p class="mb-0 small">Sistem menggunakan <strong>Random Forest Classifier</strong> untuk mengevaluasi performa siswa berdasarkan normalisasi data konsistensi.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="chart-container">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-end g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Filter Kelas</label>
                    <select class="form-select select2" id="filterKelas">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k }}">{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Rentang Tanggal Setoran</label>
                    <div id="reportrange" class="form-control" style="cursor: pointer; padding: 6px 12px; border: 1px solid #ccc; width: 100%">
                        <i class="bi bi-calendar-event me-1"></i>&nbsp;
                        <span></span> <i class="bi bi-chevron-down float-end mt-1"></i>
                    </div>
                    <input type="hidden" id="startDate">
                    <input type="hidden" id="endDate">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-primary w-100" id="btnFilter">
                        <i class="bi bi-search me-1"></i> Terapkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom-0 pt-4 px-4">
            <h6 class="mb-0 fw-bold">Hasil Analisis Performa Siswa</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle w-100" id="predictionTable">
                    <thead class="bg-light">
                        <tr>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Avg Ayat</th>
                            <th>Kelancaran</th>
                            <th>Konsistensi</th>
                            <th class="text-center">Prediksi Performa</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Informasi Variabel AI</h6>
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-2"><i class="bi bi-check2-circle text-primary me-2"></i> <strong>Rata-rata Ayat:</strong> Beban hafalan per setoran.</li>
                        <li class="mb-2"><i class="bi bi-check2-circle text-primary me-2"></i> <strong>% Kelancaran:</strong> Kualitas hafalan (Status Lancar).</li>
                        <li class="mb-0"><i class="bi bi-check2-circle text-primary me-2"></i> <strong>% Konsistensi:</strong> Persentase kehadiran setor dibanding jumlah hari dalam rentang waktu terpilih.</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Saran Tindakan AI</h6>
                    <div id="aiSuggestions" class="small text-muted italic">
                        Pilih filter dan klik terapkan untuk melihat saran AI.
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            let table;
            let myChart;

            $(function() {
                $('.select2').select2({ theme: 'bootstrap-5', width: '100%' });

                var start = moment().subtract(29, 'days');
                var end = moment();
                function cb(start, end) {
                    $('#reportrange span').html(start.format('DD MMM YYYY') + ' - ' + end.format('DD MMM YYYY'));
                    $('#startDate').val(start.format('YYYY-MM-DD'));
                    $('#endDate').val(end.format('YYYY-MM-DD'));
                }
                $('#reportrange').daterangepicker({
                    startDate: start, endDate: end,
                    ranges: {
                        'Hari Ini': [moment(), moment()],
                        '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                        '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
                        'Bulan Ini': [moment().startOf('month'), moment().endOf('month')]
                    },
                    locale: { format: 'DD/MM/YYYY', applyLabel: "Pilih", cancelLabel: "Batal" }
                }, cb);
                cb(start, end);

                table = $('#predictionTable').DataTable({
                    processing: true, serverSide: false,
                    dom: '<"dt-custom-header"Bf>rtip',
                    buttons: [
                        { extend: 'excelHtml5', text: 'Excel', className: 'btn btn-success btn-sm', title: 'Analisis AI', messageTop: function() { return 'Kelas: ' + ($('#filterKelas').val() || 'Semua') + ' | ' + $('#reportrange span').html(); } },
                        { extend: 'pdfHtml5', text: 'PDF', className: 'btn btn-danger btn-sm', title: 'Analisis AI', messageTop: function() { return 'Kelas: ' + ($('#filterKelas').val() || 'Semua') + ' | ' + $('#reportrange span').html(); }, customize: function(doc) { doc.content[2].table.widths = Array(doc.content[2].table.body[0].length + 1).join('*').split(''); } }
                    ],
                    ajax: {
                        url: "{{ route('prediction.index') }}",
                        data: function(d) {
                            d.kelas = $('#filterKelas').val();
                            d.start_date = $('#startDate').val();
                            d.end_date = $('#endDate').val();
                        }
                    },
                    columns: [
                        { data: 'nama', render: function(data) { return `<strong>${data}</strong>`; } },
                        { data: 'kelas' },
                        { data: 'avg_ayat', render: function(data) { return data + ' ayat'; } },
                        { data: 'pct_lancar' },
                        { data: 'consistency' },
                        { 
                            data: 'prediksi', className: 'text-center',
                            render: function(data) {
                                let badgeClass = data === 'Sangat Baik' ? 'bg-success' : (data === 'Cukup' ? 'bg-warning text-dark' : 'bg-danger');
                                return `<span class="badge ${badgeClass} px-3 py-2 uppercase small">${data}</span>`;
                            }
                        }
                    ],
                    language: { url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json" },
                    initComplete: function(settings, json) { updateChart(json.data); }
                });

                $('#btnFilter').on('click', function() {
                    table.ajax.reload(function(json) { updateChart(json.data); });
                });
            });

            function updateChart(data) {
                const stats = { 'Sangat Baik': 0, 'Cukup': 0, 'Perlu Perhatian': 0 };
                data.forEach(item => { stats[item.prediksi]++; });

                if (myChart) myChart.destroy();
                const ctx = document.getElementById('performanceChart').getContext('2d');
                myChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Sangat Baik', 'Cukup', 'Perlu Perhatian'],
                        datasets: [{
                            data: [stats['Sangat Baik'], stats['Cukup'], stats['Perlu Perhatian']],
                            backgroundColor: ['#2d6a4f', '#ffc107', '#dc3545'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } } }
                    }
                });

                // Update Suggestions
                let suggestion = "";
                if (stats['Perlu Perhatian'] > 0) {
                    suggestion = `<i class="bi bi-lightbulb text-warning me-2"></i> Terdapat ${stats['Perlu Perhatian']} siswa yang performanya menurun. Disarankan untuk menambah jadwal murojaah dan evaluasi metode hafalan mereka.`;
                } else if (stats['Sangat Baik'] > data.length / 2) {
                    suggestion = `<i class="bi bi-check2-all text-success me-2"></i> Performa kelas sangat baik! Pertahankan konsistensi dan pertimbangkan untuk menaikkan target setoran harian secara bertahap.`;
                } else {
                    suggestion = `<i class="bi bi-info-circle text-primary me-2"></i> Performa siswa cukup stabil. Pastikan setiap siswa menjaga konsistensi setoran minimal 3-4 kali seminggu.`;
                }
                $('#aiSuggestions').html(suggestion);
            }
        </script>
    @endpush
@endsection
