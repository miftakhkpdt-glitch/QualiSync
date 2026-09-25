<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MasterItem;

class QirController extends Controller
{
    // ==========================================
    // 1. MENU UTAMA QIR
    // ==========================================
    public function menu()
    {
        return view('quality.inproses.qir.qir-menu');
    }

    // ==========================================
    // 2. TAMPILKAN FORM INPUT BARU
    // ==========================================
    public function create()
    {
        // Hanya ambil material dengan kategori 'Finish Good'
        $masterItems = DB::table('master_materials')
                        ->where('kategori', 'Finish Good')
                        ->get(); 
        
        return view('quality.inproses.qir.qir-create', compact('masterItems'));
    }

    // ==========================================
    // PROSES SIMPAN DATA QIR (SISTEM DINAMIS EAV)
    // ==========================================
    public function store(Request $request)
    {
        // 1. Validasi Header Dasar
        $request->validate([
            'tanggal'       => 'required|date',
            'no_batch'      => 'required',
            'no_mm'         => 'required',
            'shift'         => 'required',
            'line_produksi' => 'required',
        ]);
        
        // CEK BATCH KHUSUS UNTUK ITEM (NO MM) INI SAJA
        $cekBatch = DB::table('qir_records')
                        ->where('no_batch', $request->no_batch)
                        ->where('no_mm', $request->no_mm) // <-- PERBAIKAN DI SINI
                        ->first();
                        
        if ($cekBatch) {
            return redirect()->back()->withInput()->with('error', 'GAGAL SIMPAN! No. Batch "' . $request->no_batch . '" untuk item ini sudah pernah diinput sebelumnya.');
        }

        DB::beginTransaction();
        try {
            // 2. Simpan Header ke qir_records
            $qir_id = DB::table('qir_records')->insertGetId([
                'tanggal'       => $request->tanggal,
                'no_batch'      => $request->no_batch,
                'no_mm'         => $request->no_mm,
                'shift'         => $request->shift,
                'line_produksi' => $request->line_produksi,
                'status'        => 'Approved',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            // 3. Ambil Standar Parameter untuk Validasi (Gerbang Blokir)
            $standards = DB::table('master_item_standards')
                ->join('master_parameters', 'master_item_standards.parameter_id', '=', 'master_parameters.id')
                ->where('master_item_standards.no_mm', $request->no_mm)
                ->select('master_item_standards.*', 'master_parameters.nama_parameter', 'master_parameters.tipe_input')
                ->get()
                ->keyBy('parameter_id');

            // 4. Proses Looping Sampel (LOGIKA BARU YANG LEBIH PINTAR & AKURAT)
            $hasilAktual = $request->input('hasil_aktual');

            // Kita langsung mengecek apakah ada data input (kotak isian) yang dikirim dari browser
            if (!empty($hasilAktual) && is_array($hasilAktual)) {
                
                // Looping per baris sampel (Misal: Baris 1, Baris 2, dst)
                foreach ($hasilAktual as $sampleNo => $hasilSampelIni) {
                    
                    // A. Buat Kepala Sampel di qir_details
                    $detail_id = DB::table('qir_details')->insertGetId([
                        'qir_id'     => $qir_id,
                        'sample_no'  => $sampleNo,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // B. Looping setiap parameter (kotak input) di dalam baris tersebut
                    foreach ($hasilSampelIni as $param_id => $nilai_aktual) {
                        $std = $standards->get($param_id);
                        $status_hasil = 'OK';

                        // LOGIKA GERBANG BLOKIR
                        if ($std) {
                            $nama_param = $std->nama_parameter;

                            if ($std->tipe_input === 'Angka') {
                                $nilai_angka = (float) $nilai_aktual;
                                
                                if ($std->min_value !== null && $nilai_angka < $std->min_value) {
                                    throw new \Exception("Sampel $sampleNo: $nama_param ($nilai_angka) di bawah standar minimum ($std->min_value).");
                                }
                                if ($std->max_value !== null && $nilai_angka > $std->max_value) {
                                    throw new \Exception("Sampel $sampleNo: $nama_param ($nilai_angka) melebihi standar maksimum ($std->max_value).");
                                }
                            } else { // Jika tipe Teks/Visual (OK/NG)
                                if ($nilai_aktual == 'NG') {
                                    throw new \Exception("Sampel $sampleNo: Pengecekan $nama_param berstatus NG (Gagal).");
                                }
                            }
                        }

                        // Jika lolos validasi, simpan ke qir_results
                        DB::table('qir_results')->insert([
                            'qir_detail_id' => $detail_id,
                            'parameter_id'  => $param_id,
                            'hasil_aktual'  => $nilai_aktual,
                            'status'        => $status_hasil,
                            'created_at'    => now(),
                            'updated_at'    => now(),
                        ]);
                    }
                }
            } else {
                throw new \Exception("Sistem gagal mendeteksi baris sampel! Minimal harus ada 1 baris sampel pengujian yang diisi.");
            }

            DB::commit();
            return redirect('/qir')->with('success', 'Dokumen QIR Dinamis berhasil disimpan dan lolos standar!');

        } catch (\Exception $e) {
            DB::rollBack();
            // Jika ditolak oleh gerbang blokir, tampilkan pop-up error
            return redirect()->back()->withInput()->with('error', 'GAGAL SIMPAN! ' . $e->getMessage());
        }
    }

    // ==========================================
    // 4. TAMPILKAN RIWAYAT QIR (DENGAN FILTER)
    // ==========================================
    public function history(Request $request) // <-- Tambahkan Request $request di sini
    {
        // Mulai query dasar
        $query = DB::table('qir_records')
            ->leftJoin('master_materials', 'qir_records.no_mm', '=', 'master_materials.no_mm')
            ->select(
                'qir_records.*', 
                'master_materials.nama_material'
            );

        // --- MULAI LOGIKA FILTER ---
        // Jika user mengisi filter Tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('qir_records.tanggal', $request->tanggal);
        }

        // Jika user mengisi filter No Batch (gunakan LIKE agar bisa cari sebagian angka)
        if ($request->filled('no_batch')) {
            $query->where('qir_records.no_batch', 'like', '%' . $request->no_batch . '%');
        }

        // Jika user mengisi filter No MM
        if ($request->filled('no_mm')) {
            $query->where('qir_records.no_mm', 'like', '%' . $request->no_mm . '%');
        }
        // --- SELESAI LOGIKA FILTER ---

        // Eksekusi query dan urutkan dari yang terbaru
        $riwayat = $query->orderBy('qir_records.created_at', 'desc')->get();

        return view('quality.inproses.qir.qir-history', compact('riwayat'));
    }

    // ==========================================
    // 5. HAPUS DOKUMEN QIR
    // ==========================================
    // ==========================================
    // HAPUS DATA QIR
    // ==========================================
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            // Karena di migrasi kita sudah pakai onDelete('cascade'), 
            // menghapus Header akan otomatis menghapus Detailnya.
            DB::table('qir_records')->where('id', $id)->delete();

            DB::commit();
            return redirect('/qir/riwayat')->with('success', 'Dokumen QIR berhasil dihapus permanen!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/qir/riwayat')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
    
    // ==========================================
    // API UNTUK AUTO-FILL STANDAR ITEM (AJAX)
    // ==========================================
    public function getMasterStandard($no_mm)
    {
        // UBAH master_items MENJADI master_materials
        $item = \Illuminate\Support\Facades\DB::table('master_materials')
                ->where('no_mm', $no_mm)
                ->first();
        
        if ($item) {
            return response()->json($item);
        } else {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }
    }
    
    // ==========================================
    // HALAMAN EDIT QIR (SISTEM DINAMIS)
    // ==========================================
    public function edit($id)
    {
        $qir = DB::table('qir_records')->where('id', $id)->first();
        if (!$qir) {
            return redirect('/qir')->with('error', 'Dokumen QIR tidak ditemukan!');
        }

        $masterItems = DB::table('master_materials')
                        ->where('kategori', 'Finish Good')
                        ->get();

        // 1. Ambil standar parameter saat ini untuk produk tersebut
        $parameters = DB::table('master_item_standards')
            ->join('master_parameters', 'master_item_standards.parameter_id', '=', 'master_parameters.id')
            ->where('master_item_standards.no_mm', $qir->no_mm)
            ->select('master_parameters.id', 'master_parameters.nama_parameter', 'master_parameters.tipe_input', 'master_parameters.satuan', 'master_item_standards.min_value', 'master_item_standards.max_value', 'master_item_standards.standar_teks')
            ->orderBy('master_item_standards.urutan', 'asc')
            ->get();

        // 2. Ambil data sampel dan hasil nilainya
        $details = DB::table('qir_details')->where('qir_id', $id)->orderBy('sample_no', 'asc')->get();
        $detailIds = $details->pluck('id');
        $results = DB::table('qir_results')->whereIn('qir_detail_id', $detailIds)->get();

        // 3. Susun data agar mudah dibaca oleh Javascript di HTML (Bentuk Array)
        $existingData = [];
        foreach ($details as $det) {
            $existingData[$det->sample_no] = [];
            $res = $results->where('qir_detail_id', $det->id);
            foreach ($res as $r) {
                $existingData[$det->sample_no][$r->parameter_id] = $r->hasil_aktual;
            }
        }

        return view('quality.inproses.qir.qir-edit', compact('qir', 'masterItems', 'parameters', 'existingData'));
    }

    // ==========================================
    // HALAMAN DETAIL & CETAK QIR (SISTEM DINAMIS)
    // ==========================================
    public function show($id)
    {
        // 1. Ambil Data Header Dokumen
        $qir = DB::table('qir_records')->where('id', $id)->first();
        if (!$qir) {
            return redirect('/qir')->with('error', 'Dokumen QIR tidak ditemukan!');
        }

        // Ambil nama item agar lebih informatif saat dicetak
        $item = DB::table('master_materials')->where('no_mm', $qir->no_mm)->first();
        $nama_item = $item ? ($item->nama_material ?? $item->deskripsi ?? '-') : '-';

        // 2. Ambil Standar Parameter untuk produk ini (Kolom Tabel)
        $parameters = DB::table('master_item_standards')
            ->join('master_parameters', 'master_item_standards.parameter_id', '=', 'master_parameters.id')
            ->where('master_item_standards.no_mm', $qir->no_mm)
            ->select('master_parameters.id', 'master_parameters.nama_parameter', 'master_parameters.tipe_input', 'master_parameters.satuan', 'master_item_standards.min_value', 'master_item_standards.max_value', 'master_item_standards.standar_teks')
            ->orderBy('master_item_standards.urutan', 'asc')
            ->get();

        // 3. Ambil Data Sampel & Hasil Aktualnya
        $details = DB::table('qir_details')->where('qir_id', $id)->orderBy('sample_no', 'asc')->get();
        $detailIds = $details->pluck('id');
        $results = DB::table('qir_results')->whereIn('qir_detail_id', $detailIds)->get();

        // 4. Susun ke dalam Array agar mudah digambar oleh HTML
        $existingData = [];
        foreach ($details as $det) {
            $existingData[$det->sample_no] = [];
            $res = $results->where('qir_detail_id', $det->id);
            foreach ($res as $r) {
                $existingData[$det->sample_no][$r->parameter_id] = $r->hasil_aktual;
            }
        }

        return view('quality.inproses.qir.qir-detail', compact('qir', 'nama_item', 'parameters', 'existingData'));
    }
    
    // ==========================================
    // PROSES UPDATE DATA QIR (SISTEM DINAMIS)
    // ==========================================
    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal'       => 'required|date',
            'no_batch'      => 'required',
            'no_mm'         => 'required',
        ]);
        
        // CEK BATCH KHUSUS UNTUK ITEM (NO MM) INI SAJA, KECUALI ID DOKUMEN INI SENDIRI
        $cekBatch = DB::table('qir_records')
                        ->where('no_batch', $request->no_batch)
                        ->where('no_mm', $request->no_mm) // <-- PERBAIKAN DI SINI
                        ->where('id', '!=', $id) // <-- Kunci pentingnya di sini
                        ->first();
                        
        if ($cekBatch) {
            return redirect()->back()->withInput()->with('error', 'GAGAL UPDATE! No. Batch "' . $request->no_batch . '" sudah terpakai oleh dokumen QIR lain pada item ini.');
        }

        DB::beginTransaction();
        try {
            // 1. Update Header
            DB::table('qir_records')->where('id', $id)->update([
                'tanggal'       => $request->tanggal,
                'no_batch'      => $request->no_batch,
                'no_mm'         => $request->no_mm,
                'shift'         => $request->shift,
                'line_produksi' => $request->line_produksi,
                'updated_at'    => now(),
            ]);

            $standards = DB::table('master_item_standards')
                ->join('master_parameters', 'master_item_standards.parameter_id', '=', 'master_parameters.id')
                ->where('master_item_standards.no_mm', $request->no_mm)
                ->get()->keyBy('parameter_id');

            $hasilAktual = $request->input('hasil_aktual');

            if (!empty($hasilAktual) && is_array($hasilAktual)) {
                
                // TRIK AMAN: Hapus semua data sampel lama untuk dokumen ini
                $oldDetailIds = DB::table('qir_details')->where('qir_id', $id)->pluck('id');
                DB::table('qir_results')->whereIn('qir_detail_id', $oldDetailIds)->delete();
                DB::table('qir_details')->where('qir_id', $id)->delete();

                // Insert ulang data sampel yang baru diedit (Sama seperti logika di form Create)
                foreach ($hasilAktual as $sampleNo => $hasilSampelIni) {
                    
                    $detail_id = DB::table('qir_details')->insertGetId([
                        'qir_id'     => $id,
                        'sample_no'  => $sampleNo,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    foreach ($hasilSampelIni as $param_id => $nilai_aktual) {
                        $std = $standards->get($param_id);
                        
                        if ($std) {
                            if ($std->tipe_input === 'Angka') {
                                $nilai_angka = (float) $nilai_aktual;
                                if ($std->min_value !== null && $nilai_angka < $std->min_value) throw new \Exception("Sampel $sampleNo: $std->nama_parameter ($nilai_angka) di bawah standar ($std->min_value).");
                                if ($std->max_value !== null && $nilai_angka > $std->max_value) throw new \Exception("Sampel $sampleNo: $std->nama_parameter ($nilai_angka) melebihi standar ($std->max_value).");
                            } else {
                                if ($nilai_aktual == 'NG') throw new \Exception("Sampel $sampleNo: Pengecekan $std->nama_parameter berstatus NG.");
                            }
                        }

                        DB::table('qir_results')->insert([
                            'qir_detail_id' => $detail_id,
                            'parameter_id'  => $param_id,
                            'hasil_aktual'  => $nilai_aktual,
                            'status'        => 'OK',
                        ]);
                    }
                }
            } else {
                throw new \Exception("Minimal harus ada 1 baris sampel pengujian!");
            }

            DB::commit();
            return redirect('/qir')->with('success', 'Dokumen QIR berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'GAGAL UPDATE! ' . $e->getMessage());
        }
    }
    
    // ==========================================
    // AJAX: AMBIL PARAMETER DINAMIS BERDASARKAN NO MM
    // ==========================================
    public function getParameters($no_mm)
    {
        $parameters = DB::table('master_item_standards')
            ->join('master_parameters', 'master_item_standards.parameter_id', '=', 'master_parameters.id')
            ->where('master_item_standards.no_mm', $no_mm)
            ->select(
                'master_parameters.id', 
                'master_parameters.nama_parameter', 
                'master_parameters.tipe_input', 
                'master_parameters.satuan',
                'master_item_standards.min_value', 
                'master_item_standards.max_value', 
                'master_item_standards.standar_teks'
            )
            ->orderBy('master_item_standards.urutan', 'asc')
            ->get();

        return response()->json($parameters);
    }
}