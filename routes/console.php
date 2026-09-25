<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Coa;
use App\Models\CoaDetail;
use Carbon\Carbon;

// Command Bawaan Laravel
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


/**
 * Command & Task Scheduler: Hapus COA > 2 Tahun
 * Dijalankan otomatis setiap hari jam 00:00 (tengah malam)
 */
Artisan::command('coa:clean-old', function () {
    $duaTahunLalu = Carbon::now()->subYears(2);

    // Ambil semua ID COA yang dibuat lebih dari 2 tahun lalu
    $expiredCoaIds = Coa::where('created_at', '<', $duaTahunLalu)->pluck('id');

    if ($expiredCoaIds->isNotEmpty()) {
        $jumlah = $expiredCoaIds->count();

        // Hapus detail terlebih dahulu, lalu header
        CoaDetail::whereIn('coa_id', $expiredCoaIds)->delete();
        Coa::whereIn('id', $expiredCoaIds)->delete();

        $this->info("Pembersihan selesai. Berhasil menghapus {$jumlah} dokumen COA lama (> 2 tahun).");
    } else {
        $this->info("Tidak ada dokumen COA yang lebih dari 2 tahun.");
    }
})->purpose('Menghapus otomatis dokumen COA yang berusia lebih dari 2 tahun')->daily();