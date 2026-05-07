<?php

namespace Database\Seeders;

use App\Models\Surah;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SurahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = base_path('database/surah.json');
        $surah = json_decode(file_get_contents($file), true);

        foreach ($surah['data'] as $item) {
            Surah::create([
                'nomor'         => $item['nomor'],
                'nama'          => $item['nama'],
                'nama_latin'    => $item['namaLatin'],
                'jumlah_ayat'   => $item['jumlahAyat'],
                'tempat_turun'  => $item['tempatTurun'],
                'arti'          => $item['arti']
            ]);
        }
    }
}
