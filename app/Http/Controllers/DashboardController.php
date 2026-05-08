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
            'lancar_pct'    => Setoran::count() > 0
                ? round((Setoran::where('status', 'lancar')->count() / Setoran::count()) * 100)
                : 0
        ];

        $kelas = config('kelas');

        // 2. Aktivitas Terbaru (5 terakhir)
        $recentSetoran = Setoran::with(['siswa', 'surah'])
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact('stats', 'recentSetoran', 'kelas'));
    }

    public function chartData(Request $request)
    {
        $selectedKelas = $request->kelas;

        $trendData = Setoran::select(DB::raw('DATE(tanggal) as date'), DB::raw('count(*) as total'))
            ->when($selectedKelas, function($query) use ($selectedKelas) {
                return $query->whereHas('siswa', function($q) use ($selectedKelas) {
                    $q->where('kelas', $selectedKelas);
                });
            })
            ->where('tanggal', '>=', Carbon::now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        $chartLabels = [];
        $chartValues = [];

        $days = [
            'Sun' => 'Min',
            'Mon' => 'Sen',
            'Tue' => 'Sel',
            'Wed' => 'Rab',
            'Thu' => 'Kam',
            'Fri' => 'Jum',
            'Sat' => 'Sab'
        ];

        for ($i = 6; $i >= 0; $i--) {
            $carbon = Carbon::now()->subDays($i);
            $date = $carbon->format('Y-m-d');
            $dayLabel = $days[$carbon->format('D')];
            
            $chartLabels[] = $dayLabel;

            $found = $trendData->firstWhere('date', $date);
            $chartValues[] = $found ? $found->total : 0;
        }

        return response()->json([
            'labels' => $chartLabels,
            'values' => $chartValues
        ]);
    }
}
