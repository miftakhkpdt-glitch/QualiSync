<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterStandardController extends Controller
{
    // ==========================================
    // 1. HALAMAN DAFTAR ITEM UNTUK DISETTING
    // ==========================================
    public function index()
    {
        // Mengambil semua master material dari R&D / Development
        $items = DB::table('master_materials')
                    ->where('kategori', 'Finish Good')
                    ->get(); 
        
        return view('quality.master-standar.index', compact('items'));
    }

    // ==========================================
    // 2. HALAMAN KELOLA PARAMETER (MAPPING)
    // ==========================================
    public function manage($no_mm)
    {
        $item = DB::table('master_materials')->where('no_mm', $no_mm)->first();
        if (!$item) {
            return redirect()->back()->with('error', 'Item tidak ditemukan!');
        }

        // Ambil semua parameter yang tersedia di "Gudang Parameter"
        $semuaParameter = DB::table('master_parameters')->get();

        // Ambil standar yang SUDAH TERSIMPAN untuk item ini (jika ada)
        $standarTersimpan = DB::table('master_item_standards')
            ->join('master_parameters', 'master_item_standards.parameter_id', '=', 'master_parameters.id')
            ->where('master_item_standards.no_mm', $no_mm)
            ->select('master_item_standards.*', 'master_parameters.nama_parameter', 'master_parameters.tipe_input', 'master_parameters.satuan')
            ->orderBy('urutan', 'asc')
            ->get();

        return view('quality.master-standar.manage', compact('item', 'semuaParameter', 'standarTersimpan'));
    }

    // ==========================================
    // 3. SIMPAN PENGATURAN PARAMETER
    // ==========================================
    public function store(Request $request, $no_mm)
    {
        DB::beginTransaction();
        try {
            // Trik Paling Aman: Hapus semua settingan lama untuk produk ini
            DB::table('master_item_standards')->where('no_mm', $no_mm)->delete();

            $dataToInsert = [];
            
            // Cek apakah ada parameter yang dikirim dari form
            if ($request->has('parameter_id')) {
                for ($i = 0; $i < count($request->parameter_id); $i++) {
                    // Pastikan parameter_id tidak kosong (user mungkin menekan tambah baris tapi lupa memilih parameter)
                    if(!empty($request->parameter_id[$i])){
                        $dataToInsert[] = [
                            'no_mm'          => $no_mm,
                            'parameter_id'   => $request->parameter_id[$i],
                            'min_value'      => $request->min_value[$i] ?? null,
                            'max_value'      => $request->max_value[$i] ?? null,
                            'standar_teks'   => $request->standar_teks[$i] ?? null,
                            
                            // --- TAMBAHAN UNTUK YASULOR (Mengambil dari Request) ---
                            'control_method' => $request->control_method[$i] ?? null,
                            'insp_level'     => $request->insp_level[$i] ?? null,
                            'aql'            => $request->aql[$i] ?? null,
                            'freq'           => $request->freq[$i] ?? null,
                            'n'              => $request->n[$i] ?? null,
                            // -------------------------------------------------------

                            'urutan'         => $i + 1, // Agar posisinya tidak tertukar
                            'created_at'     => now(),
                            'updated_at'     => now(),
                        ];
                    }
                }
                
                // Simpan settingan baru (hanya jika ada data yang valid untuk diinsert)
                if (count($dataToInsert) > 0) {
                    DB::table('master_item_standards')->insert($dataToInsert);
                }
            }

            // Arahkan kembali ke halaman index master standar
            DB::commit();
            return redirect('/master-standar')->with('success', 'Standar parameter untuk Item ' . $no_mm . ' berhasil diperbarui!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
}