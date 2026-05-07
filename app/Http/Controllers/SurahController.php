<?php

namespace App\Http\Controllers;

use App\Models\Surah;
use Illuminate\Http\Request;

class SurahController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $surah = Surah::orderBy('nomor', 'asc')->get();
            return response()->json(['data' => $surah]);
        }

        return view('surah.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor' => 'required|integer|unique:surah,nomor',
            'nama' => 'required|string|max:255',
            'nama_latin' => 'required|string|max:255',
            'jumlah_ayat' => 'required|integer|min:1',
            'tempat_turun' => 'required|string|max:100',
            'arti' => 'required|string|max:255',
        ]);

        $surah = Surah::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Surah berhasil ditambahkan',
            'data' => $surah
        ]);
    }

    public function show(Surah $surah)
    {
        return response()->json($surah);
    }

    public function update(Request $request, Surah $surah)
    {
        $validated = $request->validate([
            'nomor' => 'required|integer|unique:surah,nomor,' . $surah->id,
            'nama' => 'required|string|max:255',
            'nama_latin' => 'required|string|max:255',
            'jumlah_ayat' => 'required|integer|min:1',
            'tempat_turun' => 'required|string|max:100',
            'arti' => 'required|string|max:255',
        ]);

        $surah->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Surah berhasil diperbarui',
            'data' => $surah
        ]);
    }

    public function destroy(Surah $surah)
    {
        $surah->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data Surah berhasil dihapus'
        ]);
    }
}
