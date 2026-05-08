<?php

namespace App\Http\Controllers;

use App\Models\Setoran;
use App\Models\Siswa;
use App\Models\Surah;
use Illuminate\Http\Request;

class SetoranController extends Controller
{
    public function index()
    {
        $surahs = Surah::orderBy('nomor', 'asc')->get();
        $kelas = config('kelas');
        return view('setoran.index', compact('surahs', 'kelas'));
    }

    // Fungsi baru untuk menampilkan HALAMAN riwayat
    public function history()
    {
        $kelas = config('kelas');
        $surahs = Surah::orderBy('nomor', 'asc')->get();
        return view('setoran.data', compact('kelas', 'surahs'));
    }

    // Fungsi untuk mengambil DATA mentah (JSON) untuk tabel
    public function data(Request $request)
    {
        $query = Setoran::with(['siswa', 'surah']);

        if ($request->kelas) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('kelas', $request->kelas);
            });
        }

        if ($request->jenis) {
            $query->where('jenis_setoran', $request->jenis);
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        }

        return response()->json(['data' => $query->orderBy('tanggal', 'desc')->get()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_id'      => 'required|exists:siswa,id',
            'surah_id'      => 'required|exists:surah,id',
            'tanggal'       => 'required|date',
            'jenis_setoran' => 'required|in:ziyadah,murojaah',
            'ayat_dari'     => 'required|numeric|min:1',
            'ayat_sampai'   => 'required|numeric|min:1',
            'status'        => 'required|in:lancar,cukup,kurang',
        ]);

        Setoran::create($request->all());

        return response()->json(['message' => 'Data setoran berhasil disimpan!']);
    }

    public function show($id)
    {
        $setoran = Setoran::with(['siswa', 'surah'])->findOrFail($id);
        return response()->json($setoran);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'surah_id'      => 'required|exists:surah,id',
            'tanggal'       => 'required|date',
            'jenis_setoran' => 'required|in:ziyadah,murojaah',
            'ayat_dari'     => 'required|numeric|min:1',
            'ayat_sampai'   => 'required|numeric|min:1',
            'status'        => 'required|in:lancar,cukup,kurang',
        ]);

        $setoran = Setoran::findOrFail($id);
        $setoran->update($request->all());

        return response()->json(['message' => 'Data setoran berhasil diperbarui!']);
    }

    public function destroy($id)
    {
        Setoran::findOrFail($id)->delete();
        return response()->json(['message' => 'Data setoran berhasil dihapus!']);
    }

    public function getSiswaByKelas(Request $request)
    {
        $siswas = Siswa::where('kelas', $request->kelas)->orderBy('nama', 'asc')->get();
        return response()->json($siswas);
    }
}
