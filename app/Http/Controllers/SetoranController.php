<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Surah;
use App\Models\Setoran;
use Illuminate\Http\Request;

class SetoranController extends Controller
{
    public function index()
    {
        $kelas = config('kelas');
        $surahs = Surah::orderBy('nomor', 'asc')->get();
        return view('setoran.index', compact('kelas', 'surahs'));
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $data = Setoran::with(['siswa', 'surah'])->latest('tanggal')->latest('id')->get();
            return response()->json(['data' => $data]);
        }

        $kelas = config('kelas');
        return view('setoran.data', compact('kelas'));
    }

    public function getSiswaByKelas(Request $request)
    {
        $siswa = Siswa::where('kelas', $request->kelas)->orderBy('nama', 'asc')->get();
        return response()->json($siswa);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'surah_id' => 'required|exists:surah,id',
            'ayat_dari' => 'required|integer|min:1',
            'ayat_sampai' => 'required|integer|min:1|gte:ayat_dari',
            'jenis_setoran' => 'required|in:ziyadah,murojaah',
            'status' => 'required|in:lancar,cukup,kurang',
            'tanggal' => 'required|date',
        ]);

        // Validasi jumlah ayat tidak melebihi total ayat surah
        $surah = Surah::find($request->surah_id);
        if ($request->ayat_sampai > $surah->jumlah_ayat) {
            return response()->json([
                'status' => 'error',
                'message' => "Ayat sampai tidak boleh melebihi jumlah ayat surah {$surah->nama_latin} ({$surah->jumlah_ayat} ayat)."
            ], 422);
        }

        Setoran::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data setoran berhasil disimpan'
        ]);
    }

    public function history(int $siswa_id)
    {
        $history = Setoran::with('surah')
            ->where('siswa_id', $siswa_id)
            ->latest('tanggal')
            ->latest('id')
            ->get();
        return response()->json($history);
    }

    public function destroy(Setoran $setoran)
    {
        $setoran->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Data setoran berhasil dihapus'
        ]);
    }
}
