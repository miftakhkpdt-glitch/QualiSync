<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterItem; 
use App\Models\TraceabilityLog;
use Illuminate\Support\Facades\DB;

class InprosesFgController extends Controller
{
    // ==========================================
    // MENU UTAMA FG & INPUT BARU
    // ==========================================
    public function menu()
    {
        return view('quality.inproses.fg.fg-menu');
    }

    public function index()
{
    // 1. Query master materials khusus Finish Good
    $masterItems = DB::table('master_materials')
                ->where('kategori', 'Finish Good')
                ->orderBy('no_mm', 'asc')
                ->get();

    // 2. Ambil data customer dari tabel master_customers
    $customers = DB::table('master_customers')
                ->orderBy('nama_customer', 'asc')
                ->get();
                
    // 3. Ambil data master defects
    $defects = DB::table('master_defects')->orderBy('nama_defect', 'asc')->get();

    // PERBAIKAN: Tambahkan 'defects' ke dalam compact()
    return view('quality.inproses.fg.fg-create', compact('masterItems', 'customers', 'defects'));
}

    // ==========================================
    // SIMPAN DATA INSPEKSI (HEADER & DETAIL)
    // ==========================================
    public function store(Request $request)
    {
        $request->validate([
            'tanggal'       => 'required|date',
            'no_batch'      => 'required',
            'no_mm'         => 'required',
            'customer_id'   => 'required',
            'pic'           => 'required|array', 
            'pic.*'         => 'required',
            'no_box'        => 'nullable|array',
            'no_box.*'      => 'nullable',          
            'jml_box'       => 'nullable|array',
            'jml_box.*'     => 'nullable|numeric'   
        ]);

        DB::beginTransaction();
        try {
            $headerId = DB::table('fg_inspections')->insertGetId([
    'tanggal'         => $request->tanggal,
    'no_batch'        => $request->no_batch,
    'no_mm'           => $request->no_mm,
    'item_name'       => $request->customer_id, // atau ganti sesuai data yang dikirim dari form select customer
    'shift'           => $request->shift ?? '-',
    'line_produksi'   => $request->line_produksi ?? '-',
    'inspection_type' => $request->inspection_type ?? 'Normal',
    'created_at'      => now(),
    'updated_at'      => now(),
]);

            $details = [];
            $pics = $request->pic;

            for ($i = 0; $i < count($pics); $i++) {
                if (!empty($pics[$i])) {
                    $details[] = [
                        'inspection_id' => $headerId,
                        'pic'           => $pics[$i],
                        'no_box'        => $request->no_box[$i] ?? '-',
                        'defect'        => $request->defect[$i] ?? null,
                        // REVISI: Disesuaikan dengan name="aql_col_X[]" yang dikirim oleh skrip JS dinamis form
                        'critical'      => is_numeric($request->aql_col_0[$i] ?? null) ? $request->aql_col_0[$i] : 0, 
                        'major'         => is_numeric($request->aql_col_1[$i] ?? null) ? $request->aql_col_1[$i] : 0, 
                        'minor'         => is_numeric($request->aql_col_2[$i] ?? null) ? $request->aql_col_2[$i] : 0, 
                        'decision'      => $request->decision[$i] ?? 'OK',
                        'jml_box'       => $request->jml_box[$i] ?? 0,
                        'sortir_ok'     => $request->sortir_ok[$i] ?? 0,
                        'sortir_ng'     => $request->sortir_ng[$i] ?? 0,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];
                }
            }

            if (count($details) > 0) {
                DB::table('fg_inspection_details')->insert($details);
            }

            DB::commit(); 
            return redirect('/in-proses/fg/riwayat')->with('success', 'Data Inspeksi berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack(); 
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }

    public function riwayat()
{
    $riwayat = DB::table('fg_inspections')
        ->leftJoin('master_materials', 'fg_inspections.no_mm', '=', 'master_materials.no_mm')
        ->select(
            'fg_inspections.*',
            'master_materials.nama_material as nama_item'
        )
        ->orderBy('fg_inspections.tanggal', 'desc')
        ->get();

    return view('quality.inproses.fg.fg-history', compact('riwayat'));
}

    // ==========================================
    // HALAMAN DETAIL / CETAK FG
    // ==========================================
    public function detail($id)
    {
        $header = DB::table('fg_inspections')
            ->where('id', $id)
            ->first();
            
        if (!$header) abort(404, 'Data Inspeksi tidak ditemukan');

        $details = DB::table('fg_inspection_details')->where('inspection_id', $id)->get();
        $masterItem = DB::table('master_materials')->where('no_mm', $header->no_mm)->first();
        
        $namaItem = $masterItem ? ($masterItem->nama_material ?? 'Nama Item Tidak Ditemukan') : '-'; 

        return view('quality.inproses.fg.fg-detail', compact('header', 'details', 'namaItem'));
    }

    // ==========================================
    // LAPORAN WEEKLY (PARETO CHART PER LINE)
    // ==========================================
    public function laporanWeekly(Request $request)
    {
        $startDate = $request->start_date ?? now()->subDays(7)->format('Y-m-d');
        $endDate = $request->end_date ?? now()->format('Y-m-d');
        $selectedLine = $request->input('line');
        $selectedItem = $request->input('item');
        $selectedShift = $request->input('shift');

        // 1. Ambil Data untuk Opsi Dropdown Filter
        $listLines = DB::table('fg_inspections')
            ->whereNotNull('line_produksi')
            ->distinct()
            ->pluck('line_produksi');

        $listItems = DB::table('fg_inspections')
            ->leftJoin('master_materials', 'fg_inspections.no_mm', '=', 'master_materials.no_mm')
            ->select('fg_inspections.no_mm', 'master_materials.nama_material')
            ->distinct()
            ->get();

        // 2. Query Utama Data Defect dengan tambahan filter dinamis
        $queryDefect = DB::table('fg_inspection_details')
            ->join('fg_inspections', 'fg_inspection_details.inspection_id', '=', 'fg_inspections.id')
            ->leftJoin('master_materials', 'fg_inspections.no_mm', '=', 'master_materials.no_mm')
            ->whereBetween('fg_inspections.tanggal', [$startDate, $endDate])
            ->whereNotNull('fg_inspection_details.defect')
            ->where('fg_inspection_details.defect', '!=', '');

        if (!empty($selectedLine)) {
            $queryDefect->where('fg_inspections.line_produksi', $selectedLine);
        }
        if (!empty($selectedItem)) {
            $queryDefect->where('fg_inspections.no_mm', $selectedItem);
        }
        if (!empty($selectedShift)) {
            $queryDefect->where('fg_inspections.shift', $selectedShift);
        }

        $defectData = (clone $queryDefect)
            ->select(
                'fg_inspections.line_produksi',
                'fg_inspections.no_mm',
                'master_materials.nama_material',
                'fg_inspection_details.defect',
                DB::raw('SUM(COALESCE(fg_inspection_details.critical, 0) + COALESCE(fg_inspection_details.major, 0) + COALESCE(fg_inspection_details.minor, 0)) as total_qty')
            )
            ->groupBy(
                'fg_inspections.line_produksi', 
                'fg_inspections.no_mm', 
                'master_materials.nama_material', 
                'fg_inspection_details.defect'
            )
            ->having('total_qty', '>', 0) 
            ->orderBy('fg_inspections.line_produksi') 
            ->orderByDesc('total_qty') 
            ->get();

        // 3. Query untuk mencari Top Item per Line
        $queryTopItems = DB::table('fg_inspection_details')
            ->join('fg_inspections', 'fg_inspection_details.inspection_id', '=', 'fg_inspections.id')
            ->leftJoin('master_materials', 'fg_inspections.no_mm', '=', 'master_materials.no_mm')
            ->whereBetween('fg_inspections.tanggal', [$startDate, $endDate]);

        if (!empty($selectedLine)) {
            $queryTopItems->where('fg_inspections.line_produksi', $selectedLine);
        }
        if (!empty($selectedItem)) {
            $queryTopItems->where('fg_inspections.no_mm', $selectedItem);
        }
        if (!empty($selectedShift)) {
            $queryTopItems->where('fg_inspections.shift', $selectedShift);
        }

        $topItemsData = $queryTopItems
            ->select(
                'fg_inspections.line_produksi',
                'fg_inspections.no_mm',
                'master_materials.nama_material',
                DB::raw('SUM(COALESCE(fg_inspection_details.critical, 0) + COALESCE(fg_inspection_details.major, 0) + COALESCE(fg_inspection_details.minor, 0)) as total_item_qty')
            )
            ->groupBy('fg_inspections.line_produksi', 'fg_inspections.no_mm', 'master_materials.nama_material')
            ->having('total_item_qty', '>', 0)
            ->orderBy('fg_inspections.line_produksi')
            ->orderByDesc('total_item_qty')
            ->get();

        $topItemPerLine = [];
        foreach ($topItemsData as $item) {
            if (!isset($topItemPerLine[$item->line_produksi])) {
                $namaFix = $item->nama_material ?? $item->no_mm; 
                $topItemPerLine[$item->line_produksi] = [
                    'nama' => $namaFix,
                    'qty'  => $item->total_item_qty
                ];
            }
        }

        $groupedData = $defectData->groupBy('line_produksi');
        $chartData = [];

        foreach ($groupedData as $line => $defects) {
            $labels = [];
            $dataQty = [];
            $dataCumulative = [];
            $cumulativeSum = 0;
            $totalDefects = $defects->sum('total_qty');
            $detailList = [];

            foreach ($defects as $item) {
                $labels[] = $item->defect; 
                $dataQty[] = $item->total_qty; 
                $cumulativeSum += $item->total_qty;
                $percentage = $totalDefects > 0 ? round(($cumulativeSum / $totalDefects) * 100, 2) : 0;
                $dataCumulative[] = $percentage;

                $detailList[] = [
                    'no_mm'        => $item->no_mm,
                    'nama_material'=> $item->nama_material ?? '-',
                    'defect'       => $item->defect,
                    'total_qty'    => $item->total_qty
                ];
            }

            $chartData[$line] = [
                'labels'         => $labels,
                'dataQty'        => $dataQty,
                'dataCumulative' => $dataCumulative,
                'totalDefects'   => $totalDefects,
                'topItem'        => $topItemPerLine[$line]['nama'] ?? '-',
                'topItemQty'     => $topItemPerLine[$line]['qty'] ?? 0,
                'details'        => $detailList 
            ];
        }

        return view('quality.inproses.fg.laporan_weekly', compact(
            'startDate', 'endDate', 'chartData', 
            'listLines', 'listItems', 
            'selectedLine', 'selectedItem', 'selectedShift'
        ));
    }

    // ==========================================
    // APPROVAL TRACEABILITY
    // ==========================================
    public function approvalTraceability()
    {
        $data_traceability = TraceabilityLog::with('workOrder')
            ->whereNull('qa_status')
            ->orWhere('qa_status', 'Pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('quality.inproses.approval-traceability', compact('data_traceability'));
    }

    public function approveTraceability(Request $request, $id)
    {
        $log = TraceabilityLog::findOrFail($id);
        $log->update([
            'qa_status' => 'Pass', 
            'qa_checked_by' => auth()->user()->name ?? 'Quality Admin',
            'qa_checked_at' => now(),
        ]);
        return back()->with('success', 'Traceability berhasil disetujui (Pass)!');
    }

    public function rejectTraceability(Request $request, $id)
    {
        $log = TraceabilityLog::findOrFail($id);
        $log->update([
            'qa_status' => 'NG',
            'qa_checked_by' => auth()->user()->name ?? 'Quality Admin',
            'qa_checked_at' => now(),
        ]);
        return back()->with('error', 'Traceability ditolak (NG)!');
    }

    // ==========================================
    // HAPUS DATA INSPEKSI
    // ==========================================
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            DB::table('fg_inspection_details')->where('inspection_id', $id)->delete();
            DB::table('fg_inspections')->where('id', $id)->delete();
            DB::commit();
            return redirect('/in-proses/fg/riwayat')->with('success', 'Data inspeksi berhasil dihapus permanen!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/in-proses/fg/riwayat')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    // ==========================================
    // HALAMAN FORM EDIT & UPDATE
    // ==========================================
    public function edit($id)
    {
        $header = DB::table('fg_inspections')->where('id', $id)->first();
        if (!$header) abort(404, 'Data tidak ditemukan');

        $details = DB::table('fg_inspection_details')->where('inspection_id', $id)->get();
        $masterItems = DB::table('master_materials')->where('kategori', 'Finish Good')->get();
        $customers = DB::table('master_customers')->orderBy('nama_customer', 'asc')->get();
        $defects = DB::table('master_defects')->orderBy('nama_defect', 'asc')->get();

        // Tambahkan 'defects' ke dalam compact
        return view('quality.inproses.fg.fg-edit', compact('header', 'details', 'masterItems', 'customers', 'defects'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // 1. Update bagian Header (kolom customer_id dihapus karena tidak ada di tabel fg_inspections)
            DB::table('fg_inspections')->where('id', $id)->update([
                'tanggal'         => $request->tanggal,
                'no_batch'        => $request->no_batch,
                'no_mm'           => $request->no_mm,
                'line_produksi'   => $request->line_produksi,
                'shift'           => $request->shift,
                'inspection_type' => $request->inspection_type,
                'updated_at'      => now(),
            ]);

            // 2. Hapus detail lama, lalu masukkan kembali detail yang baru
            DB::table('fg_inspection_details')->where('inspection_id', $id)->delete();

            // 3. Masukkan ulang data detail
            if ($request->has('pic')) {
                foreach ($request->pic as $index => $pic) {
                    if (!empty($pic) || !empty($request->defect[$index])) {
                        DB::table('fg_inspection_details')->insert([
                            'inspection_id' => $id,
                            'pic'           => $pic,
                            'no_box'        => $request->no_box[$index] ?? '-',
                            'defect'        => $request->defect[$index] ?? null,
                            'critical'      => is_numeric($request->aql_col_0[$index] ?? ($request->critical[$index] ?? null)) ? ($request->aql_col_0[$index] ?? $request->critical[$index]) : 0,
                            'major'         => is_numeric($request->aql_col_1[$index] ?? ($request->major[$index] ?? null)) ? ($request->aql_col_1[$index] ?? $request->major[$index]) : 0,
                            'minor'         => is_numeric($request->aql_col_2[$index] ?? ($request->minor[$index] ?? null)) ? ($request->aql_col_2[$index] ?? $request->minor[$index]) : 0,
                            'decision'      => $request->decision[$index] ?? 'OK',
                            'jml_box'       => $request->jml_box[$index] ?? 0,
                            'sortir_ok'     => $request->sortir_ok[$index] ?? 0,
                            'sortir_ng'     => $request->sortir_ng[$index] ?? 0,
                            'created_at'    => now(),
                            'updated_at'    => now(),
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->to('/in-proses/fg/riwayat')->with('success', 'Data berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }

    public function getDefectCategories($customer_id)
{
    $customer = DB::table('master_customers')->where('id', $customer_id)->first();

    $labels = [];
    $inputNames = [];
    $aqls = [];

    if ($customer) {
        $masterAql = DB::table('master_aql_standards')
                    ->where('customer_id', $customer_id)
                    ->orWhere('customer_name', $customer->nama_customer) 
                    ->first();

        if ($masterAql) {
            // Gunakan pengecekan eksplisit (!== null) agar angka 0 tetap terbaca dan tidak dianggap kosong
            if ($masterAql->aql_zero_defect !== null && $masterAql->aql_zero_defect !== '' && $masterAql->aql_zero_defect !== '-') {
                $labels[]     = 'Zero Defect';
                $inputNames[] = 'aql_zero_defect';
                $aqls[]       = $masterAql->aql_zero_defect;
            }

            if ($masterAql->aql_critical !== null && $masterAql->aql_critical !== '' && $masterAql->aql_critical !== '-') {
                $labels[]     = 'Critical';
                $inputNames[] = 'aql_critical';
                $aqls[]       = $masterAql->aql_critical;
            }

            if ($masterAql->aql_major !== null && $masterAql->aql_major !== '' && $masterAql->aql_major !== '-') {
                $labels[]     = 'Major';
                $inputNames[] = 'aql_major';
                $aqls[]       = $masterAql->aql_major;
            }

            if ($masterAql->aql_minor !== null && $masterAql->aql_minor !== '' && $masterAql->aql_minor !== '-') {
                $labels[]     = 'Minor';
                $inputNames[] = 'aql_minor';
                $aqls[]       = $masterAql->aql_minor;
            }

            // Untuk format CRQS / Unilever
            if ($masterAql->crqs_amber !== null && $masterAql->crqs_amber !== '') {
                $labels[]     = 'Amber';
                $inputNames[] = 'crqs_amber';
                $aqls[]       = $masterAql->crqs_amber;
            }
            if ($masterAql->crqs_red !== null && $masterAql->crqs_red !== '') {
                $labels[]     = 'Red';
                $inputNames[] = 'crqs_red';
                $aqls[]       = $masterAql->crqs_red;
            }
        }
    }

    // Fallback default jika data master sama sekali tidak ditemukan
    if (empty($labels)) {
        $labels     = ['Zero Defect', 'Critical', 'Major', 'Minor'];
        $inputNames = ['aql_zero_defect', 'aql_critical', 'aql_major', 'aql_minor'];
        $aqls       = [0, 0.65, 2.5, 4.0];
    }

    return response()->json([
        'status'      => 'success',
        'input_names' => $inputNames,
        'labels'      => $labels,
        'aqls'        => $aqls,
        'data'        => $labels 
    ]);
}
}