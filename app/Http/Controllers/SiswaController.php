<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $siswa = Siswa::latest()->get();
            return response()->json(['data' => $siswa]);
        }
        $kelas = config('kelas');
        return view('siswa.index', compact('kelas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kelas' => 'required|string|max:100',
        ]);

        $siswa = Siswa::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Siswa berhasil ditambahkan',
            'data' => $siswa
        ]);
    }

    public function show(Siswa $siswa)
    {
        return response()->json($siswa);
    }

    public function update(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kelas' => 'required|string|max:100',
        ]);

        $siswa->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Siswa berhasil diperbarui',
            'data' => $siswa
        ]);
    }

    public function destroy(Siswa $siswa)
    {
        $siswa->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data Siswa berhasil dihapus'
        ]);
    }
}
