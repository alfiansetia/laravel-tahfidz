<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('setoran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')
                ->constrained('siswa')
                ->cascadeOnDelete();

            $table->foreignId('surah_id')
                ->constrained('surah')
                ->cascadeOnDelete();

            $table->date('tanggal')->useCurrent();

            $table->integer('ayat_dari');
            $table->integer('ayat_sampai');

            $table->enum('jenis_setoran', [
                'ziyadah',
                'murojaah'
            ]);

            // label klasifikasi
            $table->enum('status', [
                'lancar',
                'cukup',
                'kurang'
            ])->default('lancar');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setoran');
    }
};
