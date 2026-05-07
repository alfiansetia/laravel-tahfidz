<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Setoran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Rubix\ML\Classifiers\RandomForest;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\Classifiers\ClassificationTree;

class PredictionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // 1. Data Training dengan Variabel Ternormalisasi
            // Fitur: [Avg Ayat, % Kelancaran (0-1), % Konsistensi Kehadiran (0-1)]
            $samples = [
                [25, 0.90, 0.90],
                [30, 0.95, 0.95],
                [20, 1.00, 0.85], // Sangat Baik
                [10, 0.70, 0.60],
                [12, 0.80, 0.50],
                [8, 0.75, 0.55],  // Cukup
                [3, 0.40, 0.20],
                [5, 0.50, 0.25],
                [2, 0.30, 0.15],  // Perlu Perhatian
            ];
            $labels = [
                'Sangat Baik',
                'Sangat Baik',
                'Sangat Baik',
                'Cukup',
                'Cukup',
                'Cukup',
                'Perlu Perhatian',
                'Perlu Perhatian',
                'Perlu Perhatian'
            ];

            $estimator = new RandomForest(new ClassificationTree(10), 50);
            mt_srand(42);
            $dataset = new Labeled($samples, $labels);
            $estimator->train($dataset);

            // 2. Tentukan Rentang Hari untuk Menghitung Konsistensi
            $start = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(29);
            $end = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
            $totalDays = $start->diffInDays($end) + 1;

            $siswaQuery = Siswa::query();
            if ($request->kelas) {
                $siswaQuery->where('kelas', $request->kelas);
            }
            $siswas = $siswaQuery->get();

            $results = [];

            foreach ($siswas as $siswa) {
                $setoranQuery = Setoran::where('siswa_id', $siswa->id);
                if ($request->start_date && $request->end_date) {
                    $setoranQuery->whereBetween('tanggal', [$request->start_date, $request->end_date]);
                }

                $setorans = $setoranQuery->get();
                $totalSetoran = $setorans->count();

                if ($totalSetoran > 0) {
                    $totalAyat = 0;
                    $lancarCount = 0;
                    foreach ($setorans as $s) {
                        $totalAyat += ($s->ayat_sampai - $s->ayat_dari + 1);
                        if ($s->status == 'lancar') $lancarCount++;
                    }

                    $avgAyat = $totalAyat / $totalSetoran;
                    $pctLancar = $lancarCount / $totalSetoran;

                    // Normalisasi Frekuensi menjadi Konsistensi (0 - 1)
                    $consistency = $totalSetoran / $totalDays;
                    if ($consistency > 1) $consistency = 1; // Jika sehari setor berkali-kali

                    // 3. Prediksi
                    $prediction = $estimator->predict(new Unlabeled([
                        [$avgAyat, $pctLancar, $consistency]
                    ]));

                    $results[] = [
                        'nama'          => $siswa->nama,
                        'kelas'         => $siswa->kelas,
                        'avg_ayat'      => round($avgAyat, 1),
                        'pct_lancar'    => round($pctLancar * 100) . '%',
                        'consistency'   => round($consistency * 100) . '%',
                        'total_setoran' => $totalSetoran,
                        'prediksi'      => $prediction[0]
                    ];
                }
            }

            return response()->json(['data' => $results]);
        }

        $kelas = config('kelas');
        return view('prediction.index', compact('kelas'));
    }
}
