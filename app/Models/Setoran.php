<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Setoran extends Model
{
    protected $table = 'setoran';
    protected $guarded = ['id'];
    protected $appends = ['tanggal_human'];

    public function getTanggalHumanAttribute()
    {
        $date = Carbon::parse($this->tanggal);

        if ($date->isToday()) {
            return 'Hari ini';
        }

        if ($date->isYesterday()) {
            return 'Kemarin';
        }

        return $date->diffForHumans();
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function surah()
    {
        return $this->belongsTo(Surah::class);
    }
}
