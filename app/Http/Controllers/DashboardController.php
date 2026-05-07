<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Setoran;
use App\Models\Surah;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Statistik Utama
        $stats = [
            'total_siswa'   => Siswa::count(),
            'setoran_today' => Setoran::whereDate('tanggal', Carbon::today())->count(),
            'total_surah'   => Surah::count(),
            'lancar_pct'    => Setoran::where('status', 'lancar')->count() > 0
                ? round((Setoran::where('status', 'lancar')->count() / Setoran::count()) * 100)
                : 0
        ];

        // 2. Data Grafik Tren (7 Hari Terakhir)
        $trendData = Setoran::select(DB::raw('DATE(tanggal) as date'), DB::raw('count(*) as total'))
            ->where('tanggal', '>=', Carbon::now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        $chartLabels = [];
        $chartValues = [];

        // Isi data kosong jika ada hari yang tidak ada setoran
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $label = Carbon::now()->subDays($i)->format('D'); // Mon, Tue, etc
            $chartLabels[] = $label;

            $found = $trendData->firstWhere('date', $date);
            $chartValues[] = $found ? $found->total : 0;
        }

        // 3. Aktivitas Terbaru (5 terakhir)
        $recentSetoran = Setoran::with(['siswa', 'surah'])
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact('stats', 'chartLabels', 'chartValues', 'recentSetoran'));
    }
}
