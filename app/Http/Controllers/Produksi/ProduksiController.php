<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduksiController extends Controller
{
    // ========================================================
    // DASHBOARD UTAMA (OPERASIONAL HARI INI)
    // ========================================================
    public function dashboardUtama()
    {
        $today = date('Y-m-d');
        
        // 1. KARTU STATISTIK (HARI INI)
        // Kita hitung jumlah WO, atau jika belum ada status 'Selesai', kita hitung semua WO terbaru
        $wo_aktif = DB::table('work_orders')->count(); 
        
        $output_hari_ini = \App\Models\DailyReport::where('tanggal', $today)->sum('output_actual');
        $downtime_hari_ini = \App\Models\DailyReport::where('tanggal', $today)->sum('total_downtime_menit');
        
        $reject_process = \App\Models\DailyReport::where('tanggal', $today)->sum('total_reject_process');
        $reject_printing = \App\Models\DailyReport::where('tanggal', $today)->sum('total_reject_printing');
        $reject_hari_ini = $reject_process + $reject_printing;

        // 2. TABEL 5 WORK ORDER TERBARU
        $wo_berjalan = DB::table('work_orders')
            ->leftJoin('master_materials', 'work_orders.no_mm', '=', 'master_materials.no_mm')
            ->select('work_orders.*', 'master_materials.nama_material')
            ->orderBy('work_orders.id', 'desc')
            ->limit(5)
            ->get();

        // 3. GRAFIK PRODUKSI 7 HARI TERAKHIR
        $tujuh_hari_lalu = date('Y-m-d', strtotime('-6 days'));
        $chart_data = \App\Models\DailyReport::select('tanggal', DB::raw('SUM(output_actual) as total_output'))
            ->whereBetween('tanggal', [$tujuh_hari_lalu, $today])
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();
            
        $chart_labels = [];
        $chart_values = [];
        foreach($chart_data as $data) {
            // Ubah format tanggal jadi lebih enak dibaca (contoh: 30 Aug)
            $chart_labels[] = \Carbon\Carbon::parse($data->tanggal)->format('d M');
            $chart_values[] = $data->total_output;
        }

        return view('produksi.dashboard', compact(
            'wo_aktif', 'output_hari_ini', 'downtime_hari_ini', 'reject_hari_ini',
            'wo_berjalan', 'chart_labels', 'chart_values'
        ));
    }
    // 1. MENU TUGAS PRODUKSI (Menampilkan antrean WO Baru/Pending dari PPIC)
    public function index()
    {
        $workOrders = DB::table('work_orders')
            ->leftJoin('master_materials', 'work_orders.no_mm', '=', 'master_materials.no_mm')
            ->select('work_orders.*', 'master_materials.nama_material')
            // Hanya tampilkan WO yang berstatus Pending atau belum ada status
            ->where(function($query) {
                $query->where('work_orders.status', 'Pending')
                      ->orWhereNull('work_orders.status');
            })
            ->orderBy('work_orders.start_date', 'asc')
            ->get();

        return view('produksi.dashboard-wo', compact('workOrders'));
    }

    // 2. AKSI PINDAH KE ON PROGRESS (Saat tombol mulai diklik di Tugas Produksi)
    public function mulaiProses($id)
    {
        DB::table('work_orders')->where('id', $id)->update([
            'status' => 'On Progress',
            'updated_at' => now(),
        ]);

        return redirect()->route('produksi.proses.index')->with('success', 'WO berhasil dijalankan! Silakan isi form Traceability di menu Proses Produksi.');
    }

    // 3. MENU PROSES PRODUKSI (Menampilkan daftar WO yang Sedang Berjalan Lintas Shift)
    public function daftarProses()
    {
        $workOrders = DB::table('work_orders')
            ->leftJoin('master_materials', 'work_orders.no_mm', '=', 'master_materials.no_mm')
            ->select('work_orders.*', 'master_materials.nama_material')
            ->where('work_orders.status', 'On Progress') // Khusus yang sedang dikerjakan
            ->orderBy('work_orders.updated_at', 'desc')
            ->get();

        return view('produksi.work_order.daftar-proses', compact('workOrders'));
    }

    // 2. Fungsi Lapor Hasil FG (Bisa Parsial / Bisa Closing)
    public function startProduksi(Request $request, $id)
    {
        $qtyGood = $request->qty_good ?? 0;
        $qtyReject = $request->qty_reject ?? 0;
        $actionType = $request->input('action_type'); // Menangkap tombol mana yang dipencet
        
        // Menangkap data dari hidden input (diambil dari localstorage browser)
        $shift = $request->shift_fg ?? 1; // Default shift 1 jika kosong
        $operator = $request->operator_fg ?? 'Tidak Diketahui';

        // A. Ambil data WO 
        $wo = DB::table('work_orders')->where('id', $id)->first();
        if(!$wo) return back()->with('error', 'WO tidak ditemukan');

        // B. CATAT LOG SETORAN SHIFT KE TABEL BARU
        \App\Models\ProduksiFgLog::create([
            'work_order_id' => $id,
            'shift' => $shift,
            'operator' => $operator,
            'qty_good' => $qtyGood,
            'qty_reject' => $qtyReject,
            'jenis_reject' => $qtyReject > 0 ? $request->jenis_reject : null,
            'status_laporan' => $actionType == 'closing' ? 'Closing' : 'Parsial',
        ]);

        // C. AKUMULASI (INCREMENT) Qty Good dan Reject di tabel WO utama
        DB::table('work_orders')->where('id', $id)->update([
            'qty_good' => DB::raw('qty_good + ' . $qtyGood),
            'qty_reject' => DB::raw('qty_reject + ' . $qtyReject),
            'updated_at' => now(),
        ]);

        // D. CEK TOMBOL APA YANG DIPENCET (Parsial atau Closing?)
        if ($actionType == 'closing') {
            DB::table('work_orders')->where('id', $id)->update(['status' => 'Selesai']);
        } else {
            DB::table('work_orders')->where('id', $id)->update(['status' => 'On Progress']);
        }

        // E. TAMBAH STOK FG KE AREA PRODUKSI (DIPISAH BERDASARKAN BATCH)
        
        $lastLog = DB::table('traceability_logs')
                     ->where('work_order_id', $id)
                     ->orderBy('id', 'desc')
                     ->first();
                     
        $batchNum = $lastLog ? ($lastLog->batch_num ?? 'Unknown') : 'Unknown';

        // MENGGUNAKAN MODEL (UpdateOrCreate)
        // Fungsi ini otomatis mencari data. Jika ada, qty akan di-update. Jika tidak ada, data baru akan dibuat.
        \App\Models\ProduksiStock::updateOrCreate(
            ['no_mm' => $wo->no_mm, 'batch' => $batchNum], // Kondisi pencarian
            ['qty' => DB::raw('qty + ' . $qtyGood), 'status' => 'Aktif'] // Aksi jika ditemukan/dibuat
        );

        // F. PENGALIHAN HALAMAN BERDASARKAN TOMBOL
        if ($actionType == 'closing') {
            return redirect()->route('produksi.wo.list')->with('success', 'WO Ditutup! Total ' . number_format($qtyGood) . ' Pcs FG dari Shift '.$shift.' disetorkan ke Produksi.');
        } else {
            return redirect()->back()->with('success', 'Setoran Shift '.$shift.' ('.$operator.') berhasil! ' . number_format($qtyGood) . ' Pcs FG ditambahkan ke stok.');
        }
    }
    // Menampilkan Halaman Proses Produksi (Buku Log Traceability & FG)
    public function proses($id)
    {
        $wo = DB::table('work_orders')
            ->leftJoin('master_materials', 'work_orders.no_mm', '=', 'master_materials.no_mm')
            ->select('work_orders.*', 'master_materials.nama_material')
            ->where('work_orders.id', $id)
            ->first();

        if (!$wo) {
            return redirect()->back()->with('error', 'Gagal! Data Work Order dengan ID tersebut tidak ditemukan.');
        }

        // 🔥 PERUBAHAN: Tarik data log yang sudah disimpan oleh operator berdasarkan ID WO ini
        $logs = \App\Models\TraceabilityLog::where('work_order_id', $id)->orderBy('created_at', 'asc')->get(); 

        return view('produksi.work_order.proses', compact('wo', 'logs'));
    }

    // 🔥 FUNGSI BARU: Menyimpan data dari Pop-Up Modal ke Database
    public function storeTraceability(Request $request, $id)
    {
        \App\Models\TraceabilityLog::create([
            'work_order_id' => $id,
            'tanggal' => $request->tanggal,
            'shift' => $request->shift,
            'grup' => $request->grup,
            'batch_num' => $request->batch_num,
            
            // Material: Web
            'web_incoming_date' => $request->web_incoming_date,
            'web_item_name' => $request->web_item_name,
            'web_lot_num' => $request->web_lot_num,
            'web_qty' => $request->web_qty,
            
            // Material: Cap
            'cap_incoming_date' => $request->cap_incoming_date,
            'cap_lot_num' => $request->cap_lot_num,
            'cap_color' => $request->cap_color,
            'cap_qty' => $request->cap_qty,
            
            // Resin & Operator
            'lot_master_batch' => $request->lot_master_batch,
            'lot_hdpe' => $request->lot_hdpe,
            'operator' => $request->operator,
        ]);

        return redirect()->back()->with('success', 'Log Material Traceability berhasil ditambahkan!');
    }
    // 4. MENU RIWAYAT PRODUKSI (Menampilkan daftar WO yang sudah Selesai)
    public function riwayatProses()
    {
        $workOrders = DB::table('work_orders')
            ->leftJoin('master_materials', 'work_orders.no_mm', '=', 'master_materials.no_mm')
            ->select('work_orders.*', 'master_materials.nama_material')
            ->where('work_orders.status', 'Selesai') // Khusus yang sudah berstatus Selesai
            ->orderBy('work_orders.updated_at', 'desc') // Urutkan dari yang paling baru selesai
            ->get();

        return view('produksi.work_order.riwayat', compact('workOrders'));
    }
    // 6. FUNGSI UPDATE LOG TRACEABILITY (EDIT)
    public function updateTraceability(Request $request, $id, $log_id)
    {
        \App\Models\TraceabilityLog::where('id', $log_id)->where('work_order_id', $id)->update([
            'tanggal' => $request->tanggal,
            'shift' => $request->shift,
            'grup' => $request->grup,
            'batch_num' => $request->batch_num,
            
            // Material: Web
            'web_incoming_date' => $request->web_incoming_date,
            'web_lot_num' => $request->web_lot_num,
            'web_qty' => $request->web_qty,
            
            // Material: Cap
            'cap_incoming_date' => $request->cap_incoming_date,
            'cap_lot_num' => $request->cap_lot_num,
            'cap_color' => $request->cap_color,
            'cap_qty' => $request->cap_qty,
            
            // Resin & Operator
            'lot_master_batch' => $request->lot_master_batch,
            'lot_hdpe' => $request->lot_hdpe,
            'operator' => $request->operator,
        ]);

        return redirect()->back()->with('success', 'Berhasil! Data Log Material telah diperbarui.');
    }
    // Menampilkan daftar WO yang sedang berjalan (khusus untuk pintu masuk Traceability)
    public function traceabilityIndex()
    {
        // Join dengan tabel master_materials untuk mengambil nama_material
        $wo_berjalan = DB::table('work_orders')
            ->leftJoin('master_materials', 'work_orders.no_mm', '=', 'master_materials.no_mm')
            ->select('work_orders.*', 'master_materials.nama_material')
            ->where('work_orders.status', 'On Progress')
            ->get(); 
        
        return view('produksi.traceability.index', compact('wo_berjalan'));
    }

    // Menampilkan halaman Form Traceability untuk WO tertentu
    public function traceabilityForm($id)
    {
        // 1. Ambil data WO (Join dengan master_materials agar Nama Produk muncul)
        $wo = DB::table('work_orders')
            ->leftJoin('master_materials', 'work_orders.no_mm', '=', 'master_materials.no_mm')
            ->select('work_orders.*', 'master_materials.nama_material')
            ->where('work_orders.id', $id)
            ->first();
        
        // 2. Ambil data Log Traceability (Menggunakan kolom work_order_id yang benar)
        $logs = \App\Models\TraceabilityLog::where('work_order_id', $id)->orderBy('created_at', 'asc')->get(); 
        
        // 3. Kirim $wo dan $logs ke view
        return view('produksi.traceability.form', compact('wo', 'logs'));
    }
    // Menampilkan Dashboard Mini untuk monitoring detail suatu WO
    public function detailWo($id)
    {
        // Ambil data WO beserta nama materialnya
        $wo = DB::table('work_orders')
            ->leftJoin('master_materials', 'work_orders.no_mm', '=', 'master_materials.no_mm')
            ->select('work_orders.*', 'master_materials.nama_material')
            ->where('work_orders.id', $id)
            ->first();

        if (!$wo) {
            return redirect()->back()->with('error', 'Work Order tidak ditemukan.');
        }

        // Ambil Riwayat Traceability untuk WO ini
        $traceability_logs = \App\Models\TraceabilityLog::where('work_order_id', $id)
                                ->orderBy('created_at', 'desc')
                                ->get();
                                
        // (Nantinya kita juga bisa me-load Riwayat Laporan FG di sini)

        return view('produksi.work_order.detail', compact('wo', 'traceability_logs'));
    }
    // ========================================================
    // MENU 3: DAILY REPORT / CHECK SHEET (OEE)
    // ========================================================
    
    // 1. Menampilkan daftar WO berjalan untuk Daily Report
    public function dailyReportIndex()
    {
        $wo_berjalan = DB::table('work_orders')
            ->leftJoin('master_materials', 'work_orders.no_mm', '=', 'master_materials.no_mm')
            ->select('work_orders.*', 'master_materials.nama_material')
            ->where('work_orders.status', 'On Progress')
            ->get(); 
        
        return view('produksi.daily_report.index', compact('wo_berjalan'));
    }

    // 2. Menampilkan Form Check Sheet untuk WO tertentu
    public function dailyReportForm($id)
    {
        $wo = DB::table('work_orders')
            ->leftJoin('master_materials', 'work_orders.no_mm', '=', 'master_materials.no_mm')
            ->select('work_orders.*', 'master_materials.nama_material')
            ->where('work_orders.id', $id)
            ->first();
            
        if (!$wo) {
            return redirect()->back()->with('error', 'Work Order tidak ditemukan.');
        }

        // AMBIL DATA MASTER UNTUK DROPDOWN DI FORM
        $master_rejects = \App\Models\MasterReject::where('status', 'Aktif')->orderBy('kategori')->get();
        $master_downtimes = \App\Models\MasterDowntime::where('status', 'Aktif')->orderBy('kategori')->get();
        
        return view('produksi.daily_report.form', compact('wo', 'master_rejects', 'master_downtimes'));
    }
    // 3. Menyimpan data Check Sheet ke 3 Tabel Sekaligus
    public function dailyReportStore(Request $request, $id)
    {
        // Sabuk Pengaman: Mulai Transaksi Database
        DB::beginTransaction();

        try {
            // 1. SIMPAN KE TABEL INDUK (daily_reports)
            $report = \App\Models\DailyReport::create([
                'work_order_id' => $id,
                'tanggal' => $request->tanggal,
                'shift' => $request->shift,
                'mesin' => $request->mesin,
                'operator' => $request->operator,
                'output_actual' => $request->output_actual,
                'output_standard' => $request->output_standard,
                'status_laporan' => 'Disetujui', // Langsung disetujui atau bisa 'Draft'
            ]);

            $total_reject = 0;
            $total_downtime = 0;

            // 2. SIMPAN RINCIAN REJECT (Jika Ada)
            if ($request->has('reject_id')) {
                foreach ($request->reject_id as $key => $reject_id) {
                    if (!empty($reject_id) && !empty($request->reject_qty[$key])) {
                        \App\Models\DailyReportReject::create([
                            'daily_report_id' => $report->id,
                            'master_reject_id' => $reject_id,
                            'qty' => $request->reject_qty[$key],
                        ]);
                        $total_reject += $request->reject_qty[$key]; // Akumulasi total
                    }
                }
            }

            // 3. SIMPAN RINCIAN DOWNTIME (Jika Ada)
            if ($request->has('downtime_id')) {
                foreach ($request->downtime_id as $key => $downtime_id) {
                    if (!empty($downtime_id) && !empty($request->downtime_menit[$key])) {
                        \App\Models\DailyReportDowntime::create([
                            'daily_report_id' => $report->id,
                            'master_downtime_id' => $downtime_id,
                            'durasi_menit' => $request->downtime_menit[$key],
                        ]);
                        $total_downtime += $request->downtime_menit[$key]; // Akumulasi total
                    }
                }
            }

            // 4. UPDATE TOTAL REJECT & DOWNTIME KE TABEL INDUK
            $report->update([
                'total_reject_process' => $total_reject,
                'total_downtime_menit' => $total_downtime,
            ]);

            // Jika semua aman, Permanenkan penyimpanan!
            DB::commit();

            return redirect()->route('produksi.daily_report.index')->with('success', 'Laporan Check Sheet Shift ' . $request->shift . ' berhasil disimpan!');

        } catch (\Exception $e) {
            // Jika ada yang error/gagal, batalkan semua simpanan!
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
    // ========================================================
    // MENU 4: DASHBOARD OEE (EVALUASI KINERJA)
    // ========================================================
    public function oeeDashboard(Request $request)
    {
        // 1. ATUR PERIODE TANGGAL BERDASARKAN FILTER (Default: Bulan Ini)
        $periode = $request->input('periode', 'bulan'); // Default ke 'bulan'
        $now = \Carbon\Carbon::now();
        
        if ($periode == 'minggu') {
            $startDate = $now->startOfWeek()->format('Y-m-d');
            $endDate = $now->copy()->endOfWeek()->format('Y-m-d');
        } elseif ($periode == 'bulan') {
            $startDate = $now->startOfMonth()->format('Y-m-d');
            $endDate = $now->copy()->endOfMonth()->format('Y-m-d');
        } elseif ($periode == 'semester') {
            $semester = $now->month <= 6 ? 1 : 2;
            $startDate = $semester == 1 ? $now->startOfYear()->format('Y-m-d') : $now->copy()->month(7)->startOfMonth()->format('Y-m-d');
            $endDate = $semester == 1 ? $now->copy()->month(6)->endOfMonth()->format('Y-m-d') : $now->copy()->endOfYear()->format('Y-m-d');
        } elseif ($periode == 'tahun') {
            $startDate = $now->startOfYear()->format('Y-m-d');
            $endDate = $now->copy()->endOfYear()->format('Y-m-d');
        } else {
            // 'semua' waktu
            $startDate = '2000-01-01';
            $endDate = '2100-12-31';
        }

        // 2. AMBIL DATA HANYA DALAM RENTANG TANGGAL TERSEBUT
        $reports = \App\Models\DailyReport::whereBetween('tanggal', [$startDate, $endDate])->get();

        // --- KODE METRIK UTAMA TETAP SAMA ---
        $total_planned_time = $reports->sum('planned_time_menit');
        $total_downtime = $reports->sum('total_downtime_menit');
        $total_fg = $reports->sum('output_actual');
        $total_reject = $reports->sum('total_reject_process') + $reports->sum('total_reject_printing');
        $total_gross = $total_fg + $total_reject;
        $total_target = $reports->sum('output_standard');

        $operating_time = $total_planned_time - $total_downtime;
        $availability = $total_planned_time > 0 ? ($operating_time / $total_planned_time) * 100 : 0;
        $performance = $total_target > 0 ? ($total_gross / $total_target) * 100 : 0;
        $quality = $total_gross > 0 ? ($total_fg / $total_gross) * 100 : 0;
        $oee = ($availability / 100) * ($performance / 100) * ($quality / 100) * 100;

        // --- FUNGSI BANTUAN PARETO ---
        $getParetoData = function($query, $total_item) {
            $data = $query->limit(10)->get();
            $labels = []; $qty = []; $kumulatif = []; $running_total = 0;
            foreach($data as $d) {
                $labels[] = $d->nama;
                $qty[] = $d->total;
                $running_total += $d->total;
                $kumulatif[] = $total_item > 0 ? round(($running_total / $total_item) * 100, 1) : 0;
            }
            return ['labels' => $labels, 'qty' => $qty, 'kumulatif' => $kumulatif];
        };

        // 3. PARETO (DISERTAI FILTER TANGGAL MELALUI JOIN KE DAILY_REPORTS)
        $queryAisa = DB::table('daily_report_rejects')
            ->join('master_rejects', 'daily_report_rejects.master_reject_id', '=', 'master_rejects.id')
            ->join('daily_reports', 'daily_report_rejects.daily_report_id', '=', 'daily_reports.id')
            ->whereBetween('daily_reports.tanggal', [$startDate, $endDate])
            ->where('master_rejects.mesin', 'AISA')
            ->select('master_rejects.nama_reject as nama', DB::raw('SUM(daily_report_rejects.qty) as total'))
            ->groupBy('master_rejects.id', 'master_rejects.nama_reject')->orderByDesc('total');
        $totalAisa = DB::table('daily_report_rejects')->join('master_rejects', 'daily_report_rejects.master_reject_id', '=', 'master_rejects.id')->join('daily_reports', 'daily_report_rejects.daily_report_id', '=', 'daily_reports.id')->whereBetween('daily_reports.tanggal', [$startDate, $endDate])->where('master_rejects.mesin', 'AISA')->sum('qty');
        $paretoAisa = $getParetoData($queryAisa, $totalAisa);

        $queryCombi = DB::table('daily_report_rejects')
            ->join('master_rejects', 'daily_report_rejects.master_reject_id', '=', 'master_rejects.id')
            ->join('daily_reports', 'daily_report_rejects.daily_report_id', '=', 'daily_reports.id')
            ->whereBetween('daily_reports.tanggal', [$startDate, $endDate])
            ->where('master_rejects.mesin', 'COMBITOOL')
            ->select('master_rejects.nama_reject as nama', DB::raw('SUM(daily_report_rejects.qty) as total'))
            ->groupBy('master_rejects.id', 'master_rejects.nama_reject')->orderByDesc('total');
        $totalCombi = DB::table('daily_report_rejects')->join('master_rejects', 'daily_report_rejects.master_reject_id', '=', 'master_rejects.id')->join('daily_reports', 'daily_report_rejects.daily_report_id', '=', 'daily_reports.id')->whereBetween('daily_reports.tanggal', [$startDate, $endDate])->where('master_rejects.mesin', 'COMBITOOL')->sum('qty');
        $paretoCombi = $getParetoData($queryCombi, $totalCombi);

        $queryDt = DB::table('daily_report_downtimes')
            ->join('master_downtimes', 'daily_report_downtimes.master_downtime_id', '=', 'master_downtimes.id')
            ->join('daily_reports', 'daily_report_downtimes.daily_report_id', '=', 'daily_reports.id')
            ->whereBetween('daily_reports.tanggal', [$startDate, $endDate])
            ->select('master_downtimes.nama_masalah as nama', DB::raw('SUM(daily_report_downtimes.durasi_menit) as total'))
            ->groupBy('master_downtimes.id', 'master_downtimes.nama_masalah')->orderByDesc('total');
        $totalDt = $reports->sum('total_downtime_menit');
        $paretoDt = $getParetoData($queryDt, $totalDt);

        // --- KODE TAHAP 2 (KPI OPERATOR & TREND) TETAP SAMA ---
        $kpiData = [];
        $groupedByOperator = $reports->groupBy('operator');
        foreach ($groupedByOperator as $operator => $opsData) {
            $opActual = $opsData->sum('output_actual');
            $opTarget = $opsData->sum('output_standard');
            $persen = $opTarget > 0 ? ($opActual / $opTarget) * 100 : 0;
            $kpiData[strtoupper($operator)] = round($persen, 1);
        }
        arsort($kpiData); 
        $kpiLabels = array_keys($kpiData);
        $kpiValues = array_values($kpiData);

        $trendMonths = []; $trendRejectRate = []; $trendDowntimeHr = []; $trendBadPrinting = [];
        $groupedByMonth = $reports->groupBy(function($date) {
            return \Carbon\Carbon::parse($date->tanggal)->format('M'); 
        });
        $allMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        foreach ($allMonths as $m) {
            if ($groupedByMonth->has($m)) {
                $trendMonths[] = strtoupper($m);
                $mData = $groupedByMonth[$m];
                $mFg = $mData->sum('output_actual');
                $mRejProc = $mData->sum('total_reject_process');
                $mRejPrint = $mData->sum('total_reject_printing');
                $mRejTotal = $mRejProc + $mRejPrint;
                $mGross = $mFg + $mRejTotal;
                $mDtMenit = $mData->sum('total_downtime_menit');

                $trendRejectRate[] = $mGross > 0 ? round(($mRejTotal / $mGross) * 100, 1) : 0;
                $trendBadPrinting[] = $mGross > 0 ? round(($mRejPrint / $mGross) * 100, 1) : 0;
                $trendDowntimeHr[] = round($mDtMenit / 60, 1); 
            }
        }

        return view('produksi.oee.dashboard', compact(
            'availability', 'performance', 'quality', 'oee', 
            'paretoAisa', 'paretoCombi', 'paretoDt',
            'kpiLabels', 'kpiValues', 
            'trendMonths', 'trendRejectRate', 'trendDowntimeHr', 'trendBadPrinting',
            'periode' // <-- Variabel baru untuk mempertahankan pilihan di form
        ));
    }
}