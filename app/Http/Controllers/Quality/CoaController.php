<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Coa;
use App\Models\CoaDetail;

class CoaController extends Controller
{
    private const MAX_COA_BATCHES = 50;
    private const MAX_BATCH_NUMBER_LENGTH = 50;

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

    public function getBatchesByMaterial($noMm)
    {
        $batches = DB::table('qir_records')
            ->where('no_mm', $noMm)
            ->select('no_batch')
            ->distinct()
            ->orderBy('no_batch', 'asc')
            ->pluck('no_batch')
            ->map(fn ($batch) => (string) $batch)
            ->values()
            ->all();

        return response()->json($batches);
    }

    public function process(Request $request)
    {
        $batchInput = $request->input('no_batch');
        $mm = $request->input('no_mm');

        if ((!is_string($batchInput) && !is_array($batchInput)) || !is_string($mm) || trim($mm) === '') {
            return redirect()->back()->with('error', 'Nomor Batch dan No. MM wajib diisi!');
        }

        $batchValues = is_array($batchInput) ? $batchInput : explode(',', $batchInput);
        $batchArray = [];
        foreach ($batchValues as $value) {
            if (!is_string($value) && !is_numeric($value)) {
                return redirect()->back()->with('error', 'Format Nomor Batch tidak valid.');
            }

            $value = trim((string) $value);
            if ($value !== '') {
                if (strlen($value) > self::MAX_BATCH_NUMBER_LENGTH) {
                    return redirect()->back()->with('error', 'Nomor Batch melebihi batas panjang yang diizinkan.');
                }

                $batchArray[] = $value;
            }
        }

        $batchArray = array_values(array_unique($batchArray));
        $mm = trim($mm);

        if ($batchArray === []) {
            return redirect()->back()->with('error', 'Nomor Batch dan No. MM wajib diisi!');
        }

        if (count($batchArray) > self::MAX_COA_BATCHES) {
            return redirect()->back()->with('error', 'Maksimal pilih 50 nomor batch dalam satu dokumen COA.');
        }

        // Cek apakah QIR untuk batch tersebut ada
        $records = DB::table('qir_records')
            ->where('no_mm', $mm)
            ->whereIn('no_batch', $batchArray)
            ->get();

        $foundBatches = $records->pluck('no_batch')->unique()->all();
        $missingBatches = array_diff($batchArray, $foundBatches);
        if ($missingBatches !== []) {
            return redirect()->back()->with('error', 'Data QIR untuk Nomor Batch dan MM tersebut belum diinput atau tidak ditemukan!');
        }

        $batch = implode(',', $batchArray);

        return redirect('/coa/form-input?' . http_build_query([
            'batch' => $batch,
            'mm' => $mm,
        ]));
    }

    public function formInput(Request $request)
    {
        $validated = $request->validate([
            'batch' => 'required|string|max:2549',
            'mm' => 'required|string|max:50',
        ]);

        $batchArray = array_values(array_unique(array_filter(
            array_map('trim', explode(',', $validated['batch'])),
            fn ($value) => $value !== ''
        )));
        $mm = trim($validated['mm']);

        if (
            $batchArray === []
            || count($batchArray) > self::MAX_COA_BATCHES
            || $mm === ''
            || collect($batchArray)->contains(fn ($value) => strlen($value) > self::MAX_BATCH_NUMBER_LENGTH)
        ) {
            abort(404, 'Nomor Batch atau No. MM tidak valid.');
        }

        $foundBatches = DB::table('qir_records')
            ->where('no_mm', $mm)
            ->whereIn('no_batch', $batchArray)
            ->distinct()
            ->pluck('no_batch')
            ->all();

        if (array_diff($batchArray, $foundBatches) !== []) {
            abort(404, 'Satu atau beberapa batch tidak ditemukan untuk material ini.');
        }

        $batch = implode(',', $batchArray);

        // 1. Ambil hasil QIR seluruh batch terpilih untuk material ini.
        $hasilQir = DB::table('qir_records')
            ->join('qir_details', 'qir_records.id', '=', 'qir_details.qir_id')
            ->join('qir_results', 'qir_details.id', '=', 'qir_results.qir_detail_id')
            ->join('master_parameters', 'qir_results.parameter_id', '=', 'master_parameters.id')
            ->whereIn('qir_records.no_batch', $batchArray)
            ->where('qir_records.no_mm', $mm)
            ->select(
                'qir_results.parameter_id',
                'qir_results.hasil_aktual',
                'qir_results.status',
                'master_parameters.tipe_input'
            )
            ->get()
            ->groupBy('parameter_id');

        $rekomendasiHasil = [];
        foreach ($hasilQir as $parameterId => $hasilParameter) {
            if ($hasilParameter->first()->tipe_input === 'Angka') {
                $nilaiAngka = $hasilParameter
                    ->pluck('hasil_aktual')
                    ->filter(fn ($nilai) => is_numeric($nilai));

                $rekomendasiHasil[$parameterId] = [
                    'min' => $nilaiAngka->isNotEmpty() ? number_format($nilaiAngka->min(), 2, '.', '') : '',
                    'avg' => $nilaiAngka->isNotEmpty() ? number_format($nilaiAngka->avg(), 2, '.', '') : '',
                    'max' => $nilaiAngka->isNotEmpty() ? number_format($nilaiAngka->max(), 2, '.', '') : '',
                ];
                continue;
            }

            $rekomendasiHasil[$parameterId] = $hasilParameter->contains(function ($hasil) {
                return strtoupper((string) $hasil->hasil_aktual) === 'NG'
                    || strtoupper((string) $hasil->status) === 'NG';
            }) ? 'NG' : 'OK';
        }

        // 2. Ambil Master Parameter Spesifikasi Item.
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
        $masterMaterial = DB::table('master_materials')->where('no_mm', $mm)->first();
        $itemCodeCustomer = $masterMaterial->item_code_customer ?? '';

        return view('quality.coa.coa-form-input', compact('batch', 'mm', 'parameters', 'rekomendasiHasil', 'customers', 'itemCodeCustomer'));
    }

    public function store(Request $request)
{
    $request->validate([
        'status_decision' => 'required|in:PASSED,PASSED WITH NOTE',
    ]);

    DB::beginTransaction();
    try {
        // 1. Generate Nomor COA (Format: COA/Bulan/Tahun/0001)
        $bulan = date('m'); 
        $tahun = date('Y'); 
        $lastRecord = Coa::orderBy('id', 'desc')->first();
        $nextNoUrut = $lastRecord ? ($lastRecord->id + 1) : 1;
        $autoNoCoa  = "COA/{$bulan}/{$tahun}/" . str_pad($nextNoUrut, 4, '0', STR_PAD_LEFT);
        $itemCodeCustomer = DB::table('master_materials')
            ->where('no_mm', $request->input('no_mm'))
            ->value('item_code_customer');

        // 2. Simpan Header COA (Snapshot)
        $coa = Coa::create([
            'no_coa'             => $autoNoCoa,
            'no_mm'              => $request->input('no_mm'),
            'no_batch'           => $request->input('no_batch'),
            'template_type'      => $request->input('template_type', 'GENERAL'),
            'customer_name'      => $request->input('customer_name'),
            'item_code_customer' => $itemCodeCustomer,
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
                    'result_min'     => $param['result_min'] ?? null,
                    'result_max'     => $param['result_max'] ?? null,

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

    public function edit($id)
    {
        $coa = Coa::with('details')->findOrFail($id);
        $customers = DB::table('master_customers')->get();

        return view('quality.coa.coa-edit', compact('coa', 'customers'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'template_type' => 'required|in:GENERAL,YASULOR',
            'customer_name' => 'nullable|string|max:255',
            'delivery_quantity' => 'nullable|integer|min:0',
            'delivery_date' => 'nullable|date',
            'machine_no' => 'nullable|string|max:255',
            'sample_quantity' => 'nullable|integer|min:0',
            'status_decision' => 'required|in:PASSED,PASSED WITH NOTE',
            'remark' => 'nullable|string',
            'box_qty_note' => 'nullable|string|max:255',
            'prepared_by' => 'required|string|max:255',
            'approved_by' => 'required|string|max:255',
            'parameters' => 'nullable|array',
            'parameters.*.id' => 'required|integer|distinct',
            'parameters.*.result_avg' => 'nullable|string|max:255',
            'parameters.*.standar_text' => 'nullable|string|max:255',
            'parameters.*.control_method' => 'nullable|string|max:255',
            'parameters.*.insp_level' => 'nullable|string|max:255',
            'parameters.*.aql' => 'nullable|string|max:255',
            'parameters.*.frequency' => 'nullable|string|max:255',
            'parameters.*.n_sampling' => 'nullable|integer|min:0',
            'parameters.*.decision' => 'nullable|in:OK,NG',
            'parameters.*.remark' => 'nullable|string|max:255',
        ]);

        $coa = Coa::with('details')->findOrFail($id);
        $detailsById = $coa->details->keyBy('id');
        $submittedIds = collect($validated['parameters'] ?? [])
            ->pluck('id')
            ->map(fn ($detailId) => (int) $detailId)
            ->sort()
            ->values()
            ->all();
        $existingIds = $detailsById->keys()->map(fn ($detailId) => (int) $detailId)
            ->sort()
            ->values()
            ->all();

        if ($submittedIds !== $existingIds) {
            abort(422, 'Detail parameter COA tidak sesuai.');
        }

        DB::transaction(function () use ($coa, $validated, $detailsById) {
            $coa->update([
                'template_type' => $validated['template_type'],
                'customer_name' => $validated['customer_name'] ?? null,
                'delivery_quantity' => $validated['delivery_quantity'] ?? null,
                'delivery_date' => $validated['delivery_date'] ?? null,
                'machine_no' => $validated['machine_no'] ?? null,
                'sample_quantity' => $validated['sample_quantity'] ?? null,
                'status_decision' => $validated['status_decision'],
                'remark' => $validated['remark'] ?? null,
                'box_qty_note' => $validated['box_qty_note'] ?? null,
                'prepared_by' => $validated['prepared_by'],
                'approved_by' => $validated['approved_by'],
            ]);

            foreach ($validated['parameters'] ?? [] as $parameter) {
                $detailsById->get($parameter['id'])->update([
                    'result_avg' => $parameter['result_avg'] ?? null,
                    'standar_text' => $parameter['standar_text'] ?? null,
                    'control_method' => $parameter['control_method'] ?? null,
                    'insp_level' => $parameter['insp_level'] ?? null,
                    'aql' => $parameter['aql'] ?? null,
                    'frequency' => $parameter['frequency'] ?? null,
                    'n_sampling' => $parameter['n_sampling'] ?? null,
                    'decision' => $parameter['decision'] ?? null,
                    'remark' => $parameter['remark'] ?? null,
                ]);
            }
        });

        return redirect()->route('coa.index')->with('success', 'Dokumen COA berhasil diperbarui.');
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