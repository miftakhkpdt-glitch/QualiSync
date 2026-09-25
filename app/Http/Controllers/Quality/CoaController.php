<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Coa;
use App\Models\CoaDetail;

class CoaController extends Controller
{
    public function index(Request $request)
    {
        $query = Coa::query();

        // 1. Filter Berdasarkan Tahun Pembuatan
        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }

        // 2. Filter Berdasarkan No. MM
        if ($request->filled('mm')) {
            $query->where('no_mm', 'like', '%' . $request->mm . '%');
        }

        // Menampilkan riwayat COA dari tabel qa_coas (Snapshot utuh)
        $riwayat = $query->orderBy('updated_at', 'desc')->get();

        return view('quality.coa.coa-menu', compact('riwayat'));
    }

    public function create()
    {
        $masterItems = DB::table('master_materials')
                        ->where('kategori', 'Finish Good')
                        ->get();
                        
        return view('quality.coa.coa-create', compact('masterItems'));
    }

    public function process(Request $request)
    {
        $batch = $request->input('no_batch');
        $mm    = $request->input('no_mm');

        if (empty($batch) || empty($mm)) {
            return redirect()->back()->with('error', 'Nomor Batch dan No. MM wajib diisi!');
        }

        $batchArray = array_map('trim', explode(',', $batch));

        // Cek apakah QIR untuk batch tersebut ada
        $records = DB::table('qir_records')
            ->where('no_mm', $mm)
            ->whereIn('no_batch', $batchArray)
            ->get();

        if ($records->isEmpty()) {
            return redirect()->back()->with('error', 'Data QIR untuk Nomor Batch dan MM tersebut belum diinput atau tidak ditemukan!');
        }

        return redirect("/coa/form-input?batch=" . urlencode($batch) . "&mm={$mm}");
    }

    public function formInput(Request $request)
{
    $batch = $request->get('batch');
    $mm = $request->get('mm');

    // 1. Hitung Rata-Rata (AVG) nilai dari QIR untuk Batch dan MM ini
    $rekomendasiHasil = DB::table('qir_records')
        ->join('qir_details', 'qir_records.id', '=', 'qir_details.qir_id')
        ->join('qir_results', 'qir_details.id', '=', 'qir_results.qir_detail_id')
        ->where('qir_records.no_batch', $batch)
        ->where('qir_records.no_mm', $mm)
        ->groupBy('qir_results.parameter_id')
        ->select(
            'qir_results.parameter_id',
            DB::raw('ROUND(AVG(CAST(qir_results.hasil_aktual AS DECIMAL(10,2))), 2) as rata_rata')
        )
        ->pluck('rata_rata', 'parameter_id') // Menghasilkan array: [parameter_id => nilai_avg]
        ->toArray();

    // 2. Ambil Master Parameter Spesifikasi Item
    $parameters = DB::table('master_item_standards')
        ->join('master_parameters', 'master_item_standards.parameter_id', '=', 'master_parameters.id')
        ->where('master_item_standards.no_mm', $mm)
        ->select(
            'master_parameters.id as parameter_id',
            'master_parameters.nama_parameter',
            'master_parameters.satuan',
            'master_item_standards.*'
        )
        ->get();

    $customers = DB::table('master_customers')->get();

    return view('quality.coa.coa-form-input', compact('batch', 'mm', 'parameters', 'rekomendasiHasil', 'customers'));
}

    public function store(Request $request)
{
    DB::beginTransaction();
    try {
        // 1. Generate Nomor COA (Format: COA/Bulan/Tahun/0001)
        $bulan = date('m'); 
        $tahun = date('Y'); 
        $lastRecord = Coa::orderBy('id', 'desc')->first();
        $nextNoUrut = $lastRecord ? ($lastRecord->id + 1) : 1;
        $autoNoCoa  = "COA/{$bulan}/{$tahun}/" . str_pad($nextNoUrut, 4, '0', STR_PAD_LEFT);

        // 2. Simpan Header COA (Snapshot)
        $coa = Coa::create([
            'no_coa'             => $autoNoCoa,
            'no_mm'              => $request->input('no_mm'),
            'no_batch'           => $request->input('no_batch'),
            'template_type'      => $request->input('template_type', 'GENERAL'),
            'customer_name'      => $request->input('customer_name'),
            'item_code_customer' => $request->input('item_code_customer'),
            'po_number'          => $request->input('po_number'),
            'delivery_quantity'  => $request->input('delivery_quantity'),
            'delivery_date'      => $request->input('delivery_date'),
            
            // Field Ekstra
            'machine_no'         => $request->input('machine_no'),
            'sample_quantity'    => $request->input('sample_quantity'),
            'production_date'    => $request->input('production_date'),
            'issue_date'         => $request->input('issue_date'),
            'cavity_mandrel'     => $request->input('cavity_mandrel'),
            'expire_date'        => $request->input('expire_date'),
            
            'status_decision'    => $request->input('status_decision', 'PASSED'),
            'remark'             => $request->input('remark'),
            'box_qty_note'       => $request->input('box_qty_note'),
            
            'prepared_by'        => $request->input('prepared_by', 'Miftakh'),
            'approved_by'        => $request->input('approved_by', 'Rajib'),
        ]);

        // 3. Simpan Detail Parameter COA (Snapshot)
        $params = $request->input('parameters'); // Data array dari form
        if (!empty($params) && is_array($params)) {
            foreach ($params as $param) {

                // --- LOGIKA OTOMATIS UNTUK STANDAR TEXT ---
                $standarText = $param['standar_text'] ?? null;

                // Jika standar_text dari form kosong, rangkai otomatis dari min_val & max_val
                if (empty($standarText)) {
                    $min = $param['min_val'] ?? null;
                    $max = $param['max_val'] ?? null;
                    $uom = !empty($param['uom']) ? ' ' . $param['uom'] : '';

                    if ($min !== null && $max !== null) {
                        $standarText = $min . ' - ' . $max . $uom;
                    } elseif ($min !== null) {
                        $standarText = 'Min. ' . $min . $uom;
                    } elseif ($max !== null) {
                        $standarText = 'Max. ' . $max . $uom;
                    }
                }

                CoaDetail::create([
                    'coa_id'         => $coa->id,
                    'nama_parameter' => $param['nama_parameter'],
                    'uom'            => $param['uom'] ?? null,
                    'min_val'        => $param['min_val'] ?? null,
                    'max_val'        => $param['max_val'] ?? null,
                    'result_avg'     => $param['result_avg'] ?? null, // Untuk template General

                    // Tambahan untuk Yasulor
                    'control_method' => $param['control_method'] ?? null,
                    'insp_level'     => $param['insp_level'] ?? null,
                    'aql'            => $param['aql'] ?? null,
                    'frequency'      => $param['frequency'] ?? null,
                    'n_sampling'     => $param['n_sampling'] ?? null,
                    'standar_text'   => $standarText, // <--- Menggunakan hasil rangkaian otomatis
                    'decision'       => $param['decision'] ?? null,
                    'remark'         => $param['remark'] ?? null,
                ]);
            }
        }

        DB::commit();
        return redirect(url('/print-coa/' . $coa->id))->with('success', 'Dokumen COA Berhasil Disimpan dan Siap Dicetak!');

    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->with('error', 'Gagal menyimpan COA: ' . $e->getMessage());
    }
}

    public function print($id)
    {
        // Load data COA lengkap beserta relasi detailnya
        $coa = Coa::with('details')->findOrFail($id);
        
        // Ambil nama material dari master R&D (karena tidak disimpan di snapshot)
        $master = DB::table('master_materials')->where('no_mm', $coa->no_mm)->first();

        // Tentukan template blade berdasarkan tipe customer
        if ($coa->template_type === 'YASULOR') {
            return view('quality.coa.pdf.yasulor', compact('coa', 'master'));
        }

        // Default pakai General
        return view('quality.coa.pdf.general', compact('coa', 'master'));
    }

    public function history()
    {
        $riwayat = Coa::orderBy('updated_at', 'desc')->get();
        $pesanInfo = "*Catatan: Riwayat dokumen COA akan dihapus secara otomatis oleh sistem apabila usianya telah melebihi 2 tahun dari tanggal pembuatan.";

        return view('quality.coa.coa-history', compact('riwayat', 'pesanInfo'));
    }

    // FITUR HAPUS MANUAL DOKUMEN COA
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $coa = Coa::findOrFail($id);

            // Hapus detail parameter terlebih dahulu
            CoaDetail::where('coa_id', $coa->id)->delete();

            // Hapus data header COA
            $coa->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Dokumen COA berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menghapus COA: ' . $e->getMessage());
        }
    }
}