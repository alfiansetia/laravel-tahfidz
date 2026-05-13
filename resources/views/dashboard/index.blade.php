@extends('layouts.dashboard')

@section('title', 'Dashboard Tahfidz')
@section('header', 'Ringkasan Aktivitas Tahfidz')

@section('content')
    <!-- Statistik Cards -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary-subtle text-primary p-3 rounded-3 me-3">
                            <i class="bi bi-people fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small mb-1">Total Siswa</h6>
                            <h3 class="fw-bold mb-0">{{ $stats['total_siswa'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success-subtle text-success p-3 rounded-3 me-3">
                            <i class="bi bi-calendar-check fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small mb-1">Setoran Hari Ini</h6>
                            <h3 class="fw-bold mb-0">{{ $stats['setoran_today'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning-subtle text-warning p-3 rounded-3 me-3">
                            <i class="bi bi-book fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small mb-1">Total Surah</h6>
                            <h3 class="fw-bold mb-0">{{ $stats['total_surah'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-info-subtle text-info p-3 rounded-3 me-3">
                            <i class="bi bi-graph-up-arrow fs-3"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small mb-1">Tingkat Kelancaran</h6>
                            <h3 class="fw-bold mb-0">{{ $stats['lancar_pct'] }}%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Chart Tren -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0">Tren Setoran 7 Hari Terakhir</h6>
                        <small class="text-muted">Aktivitas Mingguan</small>
                    </div>
                    <div style="width: 150px;">
                        <select id="filterChartKelas" class="form-select form-select-sm rounded-pill border-light bg-light">
                            <option value="">Semua Kelas</option>
                            @foreach ($kelas as $k)
                                <option value="{{ $k }}">{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body p-4">
                    <canvas id="trendChart" style="height: 300px; width: 100%;"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Recent -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Akses Cepat</h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('setoran.index') }}" class="btn btn-primary py-3 rounded-3 shadow-sm border-0">
                            <i class="bi bi-plus-circle me-2"></i> Input Setoran Baru
                        </a>
                        <a href="{{ route('prediction.index') }}"
                            class="btn btn-outline-primary py-3 rounded-3 border-dashed">
                            <i class="bi bi-cpu me-2"></i> Analisis Performa AI
                        </a>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold mb-0">Setoran Terkini</h6>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="list-group list-group-flush">
                        @forelse($recentSetoran as $setoran)
                            <div class="list-group-item px-0 py-3 border-0 border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold small text-truncate" style="max-width: 150px;">
                                            {{ $setoran->siswa->nama }}</h6>
                                        <p class="mb-0 text-muted smaller">Surah {{ $setoran->surah->nama_latin }}
                                            ({{ $setoran->ayat_dari }}-{{ $setoran->ayat_sampai }})
                                        </p>
                                    </div>
                                    <div class="text-end">
                                        <span
                                            class="badge {{ $setoran->status == 'lancar' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} rounded-pill smaller px-2">
                                            {{ ucfirst($setoran->status) }}
                                        </span>
                                        <p class="mb-0 smaller text-muted mt-1" style="font-size: 0.7rem;">
                                            {{ $setoran->tanggal_human }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-muted my-4">Belum ada setoran masuk.</p>
                        @endforelse
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('setoran.data') }}" class="small text-decoration-none">Lihat Semua Riwayat <i
                                class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            $(function() {
                const ctx = document.getElementById('trendChart').getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, 'rgba(45, 106, 79, 0.2)');
                gradient.addColorStop(1, 'rgba(45, 106, 79, 0)');

                let trendChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Jumlah Setoran',
                            data: [],
                            borderColor: '#2d6a4f',
                            backgroundColor: gradient,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#2d6a4f',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#1a1a1a',
                                titleFont: {
                                    size: 13
                                },
                                bodyFont: {
                                    size: 13
                                },
                                padding: 12,
                                cornerRadius: 8,
                                displayColors: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    color: '#94a3b8'
                                },
                                grid: {
                                    color: '#f1f5f9'
                                }
                            },
                            x: {
                                ticks: {
                                    color: '#94a3b8'
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });

                function loadChartData(kelas = '') {
                    $.get("{{ route('dashboard.chart') }}", {
                        kelas: kelas
                    }, function(res) {
                        trendChart.data.labels = res.labels;
                        trendChart.data.datasets[0].data = res.values;
                        trendChart.update();
                    });
                }

                // Initial Load
                loadChartData();

                // On Change Filter
                $('#filterChartKelas').on('change', function() {
                    loadChartData($(this).val());
                });
            });
        </script>
    @endpush
@endsection
