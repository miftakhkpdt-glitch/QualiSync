<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Controller Utama / Departemen Lain
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DevelopmentController;
use App\Http\Controllers\HanyaSupplierCapaController;

// =========================================================================
// IMPORT CONTROLLER HRD-GA (SUDAH DIPERBAIKI KE FOLDER BARU)
// =========================================================================
use App\Http\Controllers\HrdGa\HrdGaController;
use App\Http\Controllers\HrdGa\CutiKaryawanController;
use App\Http\Controllers\HrdGa\UserController;
use App\Http\Controllers\HrdGa\LemburKaryawanController;
use App\Http\Controllers\HrdGa\PayrollController;

// Controller Khusus Departemen Quality (Subfolder Quality)
use App\Http\Controllers\Quality\QirController;
use App\Http\Controllers\Quality\CoaController;
use App\Http\Controllers\Quality\CapaSupplierController;
use App\Http\Controllers\Quality\QualityController;
use App\Http\Controllers\Quality\MasterItemController;
use App\Http\Controllers\Quality\MutasiStokController;
use App\Http\Controllers\Quality\InprosesFgController;
use App\Http\Controllers\Quality\CapaCustomerController;
use App\Http\Controllers\Quality\IncomingVerificationController;
use App\Http\Controllers\Quality\StokController as QualityStokController;
use App\Http\Controllers\Quality\SupplierPerformanceController;
use App\Http\Controllers\Quality\CpkController;
use App\Http\Controllers\Quality\MasterParameterController;
use App\Http\Controllers\Quality\MasterAqlController;
use App\Http\Controllers\Quality\MasterDefectController;


// Controller Sales (Folder Sales)
use App\Http\Controllers\Sales\SalesOrderController;
use App\Http\Controllers\Sales\PriceController;

// Controller Lainnya
use App\Http\Controllers\Ppic\PpicController;
use App\Http\Controllers\Produksi\ProduksiController;
use App\Http\Controllers\Purchasing\PurchasingController;
use App\Http\Controllers\Purchasing\VendorController;
use App\Http\Controllers\Warehouse\WarehouseController;
use App\Http\Controllers\Warehouse\IncomingMaterialController;
use App\Http\Controllers\Warehouse\OutgoingController;
use App\Http\Controllers\Development\MasterMaterialController;
use App\Http\Controllers\Development\BomController;
use App\Http\Controllers\MutasiController;

// =========================================================================
// IMPORT CONTROLLER STOK DAN MUTASI YANG BARU KITA BUAT
// =========================================================================
use App\Http\Controllers\Warehouse\StokController as WarehouseStokController;
use App\Http\Controllers\Produksi\StokController as ProduksiStokController;

use App\Http\Controllers\Warehouse\MutasiStokController as WarehouseMutasiController;
use App\Http\Controllers\Purchasing\KarantinaController;

use App\Http\Controllers\Fat\SalesInvoiceController;


use Illuminate\Support\Facades\Schema;
/*
|--------------------------------------------------------------------------
| 1. ROUTE PUBLIK (TIDAK PERLU LOGIN)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return response()->view('login')
        ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');
    
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        
        $role = Auth::user()->role;

        // ---> PENGALIHAN KHUSUS <---
        if ($role === 'supplier') {
            return redirect('/capa-8d/supplier');
        }
        
        // PENGALIHAN KHUSUS MANAGER PLAN (BARU)
        if ($role === 'manager_plan') {
            return redirect('/purchasing/purchase-request');
        }

        // =========================================================
        // [BARU] PENGALIHAN KHUSUS DIREKTUR & PRESDIR
        // =========================================================
        if ($role === 'direktur' || $role === 'presiden_direktur') {
            return redirect('/purchasing/approval-po'); 
        }

        // Default: Buang ke Home
        return redirect('/home');
    }
    
    return back()->withErrors(['email' => 'Email atau Password salah!']);
});

Route::match(['get', 'post'], '/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

/*
|--------------------------------------------------------------------------
| 2. ROUTE TERKUNCI (WAJIB LOGIN & BERDASARKAN DEPARTEMEN / ROLE)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::post('/home', [HomeController::class, 'index']);
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // === KELOMPOK PORTAL QUALITY & QC LINE & OPERATOR ===
    Route::middleware(['role:staff_quality,operator_quality,admin'])->group(function () {
        
        Route::get('/dashboard-qa', [\App\Http\Controllers\Quality\QualityController::class, 'dashboard'])->name('quality.dashboard');
        // Rute Rekap Stok Quality Baru
        Route::get('/quality/stok/rekap', [QualityStokController::class, 'rekap'])->name('quality.stok.rekap');
        Route::post('/produksi/stok/tambah', [ProduksiStokController::class, 'tambahStok'])->name('produksi.stok.tambah');

        // Rute Approval Mutasi Quality
        Route::get('/quality/approval-mutasi', [MutasiStokController::class, 'index'])->name('quality.mutasi.index');
        Route::post('/quality/approval-mutasi/approve/{id}', [MutasiStokController::class, 'approve'])->name('quality.mutasi.approve');
        Route::post('/quality/approval-mutasi/reject/{id}', [MutasiStokController::class, 'reject'])->name('quality.mutasi.reject');
        Route::get('/quality/approval-mutasi/print', [MutasiStokController::class, 'print'])->name('quality.mutasi.print');

        // Rute Verifikasi Incoming Material oleh QC
        Route::get('/quality/incoming', [IncomingVerificationController::class, 'index'])->name('quality.incoming.index');
        Route::get('/incoming-material', [IncomingVerificationController::class, 'index']);
        Route::post('/quality/incoming/update-status/{id}', [IncomingVerificationController::class, 'updateStatus']);

        Route::get('/qir', [QirController::class, 'menu']);
        Route::get('/qir/create', [QirController::class, 'create']);
        Route::post('/qir/store-session', [QirController::class, 'storeSession']);
        Route::get('/qir/sheet', [QirController::class, 'index']);
        Route::get('/qir/riwayat', [QirController::class, 'history']);
        Route::post('/qir/update/{id}', [App\Http\Controllers\Quality\QirController::class, 'update']);
        Route::post('/qir-detail', [QirController::class, 'store']);
        Route::delete('/qir/delete/{id}', [App\Http\Controllers\Quality\QirController::class, 'destroy']);
        // 1. Route untuk AJAX (Tarik data master otomatis)
        Route::get('/qir/get-master-standard/{no_mm}', [App\Http\Controllers\Quality\QirController::class, 'getMasterStandard']);
        
        // 2. Route untuk menyimpan data (Saya ubah namanya dari /qir-detail menjadi /qir/store agar seragam)
        Route::post('/qir/store', [QirController::class, 'store']);
        // Rute untuk Halaman Detail/Lihat
Route::get('/qir/detail/{id}', [App\Http\Controllers\Quality\QirController::class, 'show']);

// Rute untuk Halaman Edit
Route::get('/qir/edit/{id}', [App\Http\Controllers\Quality\QirController::class, 'edit']);
Route::get('/qir/get-parameters/{no_mm}', [App\Http\Controllers\Quality\QirController::class, 'getParameters']);



        // ==========================================
        // ROUTE IN-PROSES FINISHED GOODS (FG)
        // ==========================================
        Route::get('/in-proses/fg/menu', [InprosesFgController::class, 'menu']);
        Route::get('/in-proses/fg/create', [InprosesFgController::class, 'index']);
        
        // [BARU] Route untuk menyimpan form dinamis (Header + Detail)
        Route::post('/in-proses/fg/store', [InprosesFgController::class, 'store']); 
        
        // [UPDATE] Route riwayat (Pastikan nama fungsinya 'riwayat' sesuai di Controller)
        Route::get('/in-proses/fg/riwayat', [InprosesFgController::class, 'riwayat']); 
        // Tambahkan baris ini di bawah rute '/in-proses/fg/riwayat'
Route::get('/in-proses/fg/detail/{id}', [App\Http\Controllers\Quality\InprosesFgController::class, 'detail']);
        // Route Laporan Weekly (Pareto)
        Route::get('/in-proses/fg/laporan-weekly', [InprosesFgController::class, 'laporanWeekly'])->name('in_proses.fg.laporan_weekly');
        Route::get('/get-defect-categories/{id}', [InprosesFgController::class, 'getDefectCategories']);
        Route::get('/master-aql', [MasterAqlController::class, 'index'])->name('master-aql.index');
Route::post('/master-aql', [MasterAqlController::class, 'store'])->name('master-aql.store');
Route::get('/master-aql/{id}/edit', [MasterAqlController::class, 'edit']); // <-- Pastikan baris ini ada
Route::put('/master-aql/{id}', [MasterAqlController::class, 'update']); // <-- Untuk menyimpan hasil edit
Route::delete('/master-aql/{id}', [MasterAqlController::class, 'destroy']);
Route::get('/get-defect-categories/{id}', [MasterAqlController::class, 'getAqlByCustomer']);

Route::get('/master-defect', [MasterDefectController::class, 'index']);
Route::post('/master-defect', [MasterDefectController::class, 'store']);
Route::put('/master-defect/{id}', [MasterDefectController::class, 'update']);
Route::delete('/master-defect/{id}', [MasterDefectController::class, 'destroy']);
Route::get('/master-defect/create', [MasterDefectController::class, 'create'])->name('master-defect.create');
Route::post('/master-defect', [MasterDefectController::class, 'store'])->name('master-defect.store');
Route::get('/master-defect', [MasterDefectController::class, 'index'])->name('master-defect.index');
Route::post('/master-defect', [MasterDefectController::class, 'store'])->name('master-defect.store');
Route::delete('/master-defect/{id}', [MasterDefectController::class, 'destroy'])->name('master-defect.destroy');

// Route untuk menampilkan form (menggunakan method index)
Route::get('/in-proses/fg/create', [InprosesFgController::class, 'index'])->name('fg.create');

// Route untuk menyimpan data form
Route::post('/in-proses/fg/store', [InprosesFgController::class, 'store'])->name('fg.store');
        
        // Alias Route
        Route::get('/in-proses/fg', [InprosesFgController::class, 'menu']);
        Route::get('/inproses/fg', [InprosesFgController::class, 'menu']);
        Route::get('/in-proses/fg/edit/{id}', [App\Http\Controllers\Quality\InprosesFgController::class, 'edit']);
Route::delete('/in-proses/fg/delete/{id}', [App\Http\Controllers\Quality\InprosesFgController::class, 'destroy']);
Route::put('/in-proses/fg/update/{id}', [App\Http\Controllers\Quality\InprosesFgController::class, 'update']);
Route::get('/in-proses/fg/cpk', [CpkController::class, 'index']);
// Route untuk mengambil kategori defect secara dinamis berdasarkan customer
Route::get('/in-proses/fg/get-defect-categories/{customer_id}', [InprosesFgController::class, 'getDefectCategories']);
        // Halaman Approval Traceability (Quality)
        Route::get('/quality/approval-traceability', [\App\Http\Controllers\Quality\InprosesFgController::class, 'approvalTraceability'])->name('quality.approval_traceability');
        // Aksi Approve & Reject
        Route::post('/quality/approval-traceability/approve/{id}', [\App\Http\Controllers\Quality\InprosesFgController::class, 'approveTraceability'])->name('quality.approval_traceability.approve');
        Route::post('/quality/approval-traceability/reject/{id}', [\App\Http\Controllers\Quality\InprosesFgController::class, 'rejectTraceability'])->name('quality.approval_traceability.reject');

        Route::get('/print-coa/{id}', [CoaController::class, 'print'])->name('coa.print');
        Route::post('/coa/store', [CoaController::class, 'store'])->name('coa.store');
        // Menampilkan Riwayat & Menu Utama COA
    Route::get('/coa', [CoaController::class, 'index'])->name('coa.index');

    // Halaman Form Pemilihan Material & Batch Awal
    Route::get('/coa/create', [CoaController::class, 'create'])->name('coa.create');

    // Proses Validasi Data QIR dari Form Awal
    Route::post('/coa/process', [CoaController::class, 'process'])->name('coa.process');

    // Halaman Form Input Parameter COA (General / Yasulor)
    Route::get('/coa/form-input', [CoaController::class, 'formInput'])->name('coa.form-input');

    // Simpan Snapshot Data COA & Detail Ke Database
    Route::post('/coa/store', [CoaController::class, 'store'])->name('coa.store');

    // Cetak Dokumen PDF berdasarkan ID (Snapshot)
    Route::get('/print-coa/{id}', [CoaController::class, 'print'])->name('coa.print');

    Route::delete('/coa/{id}', [CoaController::class, 'destroy'])->name('coa.destroy');
        
        // Rute Mutasi Stok Keluar (Quality)
        Route::get('/quality/mutasi-stok', [MutasiStokController::class, 'mutasiKeluarIndex'])->name('quality.mutasi_keluar.index');
        Route::post('/quality/mutasi-stok/store', [MutasiStokController::class, 'storeMutasiKeluar'])->name('quality.mutasi_keluar.store');
        Route::get('/quality/mutasi-stok/get-item', [MutasiStokController::class, 'getItemByMm'])->name('quality.mutasi_keluar.get_item');
        Route::get('/quality/mutasi/riwayat-gabungan', [MutasiStokController::class, 'riwayatMutasiGabungan'])->name('quality.mutasi.riwayat');
    });
    

    // === KELOMPOK CAPA SUPPLIER & CUSTOMER (INTERNAL QUALITY/ADMIN) ===
    Route::middleware(['role:quality,admin'])->group(function () {
        Route::get('/capa-8d/supplier/create', [CapaSupplierController::class, 'create']);
        Route::post('/capa-8d/supplier/store', [CapaSupplierController::class, 'store']);
        Route::get('/capa-8d/supplier/print/{id}', [CapaSupplierController::class, 'print']);
        Route::get('/capa-8d/supplier/edit/{id}', [CapaSupplierController::class, 'edit']);
        Route::post('/capa-8d/supplier/update/{id}', [CapaSupplierController::class, 'update']);
        Route::delete('/capa-8d/supplier/delete/{id}', [CapaSupplierController::class, 'destroy']);
        Route::get('/capa-8d/supplier/toggle-close/{id}', [CapaSupplierController::class, 'toggleClose']);
        
        // Di dalam grup Route Quality
        Route::get('/quality/stok', [QualityStokController::class, 'index'])->name('quality.stok.index');
        // Rute CAPA Customer Dinamis
        Route::get('/capa-8d/customer', [CapaCustomerController::class, 'index']);
        Route::get('/capa-8d/customer/create', [CapaCustomerController::class, 'create']);
        Route::post('/capa-8d/customer/store', [CapaCustomerController::class, 'store']);
    });

    // === RUTE KHUSUS PORTAL SUPPLIER ===
    Route::middleware(['role:supplier,admin,quality'])->group(function () {
        Route::get('/capa-8d/supplier', [HanyaSupplierCapaController::class, 'index']);
        Route::get('/capa-8d/supplier/form/{id}', [HanyaSupplierCapaController::class, 'supplierForm']);
        Route::post('/capa-8d/supplier/submit/{id}', [HanyaSupplierCapaController::class, 'supplierSubmit']);
    });
    // ========================================================
// ROUTE MENU PERFORMA SUPPLIER (QUALITY DEPT)
// ========================================================
Route::prefix('quality/supplier-performance')->name('quality.supplier_performance.')->group(function () {
    Route::get('/', [SupplierPerformanceController::class, 'index'])->name('index');
    Route::get('/create', [SupplierPerformanceController::class, 'create'])->name('create');
    Route::post('/store', [SupplierPerformanceController::class, 'store'])->name('store');
    Route::post('/print', [SupplierPerformanceController::class, 'print'])->name('print');
});
    // Master Standart Item
    Route::middleware(['role:admin,quality'])->group(function () {
        Route::get('/quality/master-items', [App\Http\Controllers\Quality\MasterItemController::class, 'index'])->name('quality.master-item.index');
        Route::post('/quality/master-items', [App\Http\Controllers\Quality\MasterItemController::class, 'store']);
        Route::delete('/quality/master-items/{id}', [App\Http\Controllers\Quality\MasterItemController::class, 'destroy']);
        Route::post('/quality/master-items/import', [App\Http\Controllers\Quality\MasterItemController::class, 'importExcel'])->name('quality.master-item.import');
    });
    // Rute Pengaturan Master Standar (Dinamis)
Route::get('/master-standar', [App\Http\Controllers\Quality\MasterStandardController::class, 'index']);
Route::get('/master-standar/manage/{no_mm}', [App\Http\Controllers\Quality\MasterStandardController::class, 'manage']);
Route::post('/master-standar/store/{no_mm}', [App\Http\Controllers\Quality\MasterStandardController::class, 'store']);
// Rute untuk Master Parameter QIR
Route::get('/master-parameters', [MasterParameterController::class, 'index']);
Route::post('/master-parameters/store', [MasterParameterController::class, 'store']);
Route::delete('/master-parameters/delete/{id}', [MasterParameterController::class, 'destroy']);



    // === KELOMPOK DEPARTEMEN HRD-GA ===
    Route::middleware(['role:hrd_ga,admin'])->group(function () {
        Route::get('/hrd/karyawan', [HrdGaController::class, 'indexKaryawan']);
        Route::get('/hrd-ga/karyawan', [HrdGaController::class, 'indexKaryawan']);
        Route::get('/hrd/karyawan/tambah', [HrdGaController::class, 'createKaryawan']);
        Route::get('/hrd-ga/karyawan/tambah', [HrdGaController::class, 'createKaryawan']);
        Route::post('/hrd/karyawan/store', [HrdGaController::class, 'storeKaryawan']);
        Route::post('/hrd-ga/karyawan/store', [HrdGaController::class, 'storeKaryawan']);
        Route::get('/hrd/karyawan/edit/{id}', [HrdGaController::class, 'editKaryawan']);
        Route::get('/hrd-ga/karyawan/edit/{id}', [HrdGaController::class, 'editKaryawan']);
        Route::put('/hrd/karyawan/update/{id}', [HrdGaController::class, 'updateKaryawan']);
        Route::put('/hrd-ga/karyawan/update/{id}', [HrdGaController::class, 'updateKaryawan']);
        Route::delete('/hrd/karyawan/delete/{id}', [HrdGaController::class, 'deleteKaryawan']);
        Route::delete('/hrd-ga/karyawan/delete/{id}', [HrdGaController::class, 'deleteKaryawan']);
        // === ROUTE PAYROLL / SLIP GAJI ===
        Route::get('/hrd/payrolls', [\App\Http\Controllers\HrdGa\PayrollController::class, 'index'])->name('hrd.payrolls.index');
        Route::post('/hrd/payrolls/store', [\App\Http\Controllers\HrdGa\PayrollController::class, 'store'])->name('hrd.payrolls.store');
        Route::post('/hrd/payrolls/{id}/pay', [\App\Http\Controllers\HrdGa\PayrollController::class, 'pay'])->name('hrd.payrolls.pay');
        // TAMBAHKAN BARIS INI UNTUK REKAPITULASI
        Route::post('/hrd/payrolls/post-rekap', [\App\Http\Controllers\HrdGa\PayrollController::class, 'postRekap'])->name('hrd.payrolls.post_rekap');

        // Persetujuan Cuti oleh HRD
        Route::get('/hrd/cuti/approval', [CutiKaryawanController::class, 'approvalIndex']);
        Route::get('/hrd-ga/persetujuan-cuti', [CutiKaryawanController::class, 'approvalIndex']);
        Route::post('/hrd-ga/persetujuan-cuti/update/{id}', [CutiKaryawanController::class, 'updateStatusHrd']);

        // Persetujuan Lembur Khusus HRD (Final)
        Route::get('/hrd/lembur/approval', [LemburKaryawanController::class, 'hrdApprovalIndex'])->name('lembur.hrd.approval');
        Route::post('/hrd/lembur/hrd-update/{id}', [LemburKaryawanController::class, 'updateStatusHrd'])->name('lembur.update.hrd');

        // Manajemen User
        Route::get('/hrd/user', [UserController::class, 'index']);
        Route::get('/hrd-ga/users', [UserController::class, 'index']);
        Route::get('/hrd-ga/users/tambah', [UserController::class, 'create']);
        Route::post('/hrd-ga/users/store', [UserController::class, 'store']);
        Route::get('/hrd-ga/users/edit/{id}', [UserController::class, 'edit']);
        Route::put('/hrd-ga/users/update/{id}', [UserController::class, 'update']);
        Route::delete('/hrd-ga/users/delete/{id}', [UserController::class, 'delete']);
    });
    // === MANAJEMEN PENGAJUAN CUTI KARYAWAN ===
    Route::get('/hrd/cuti/pribadi', [CutiKaryawanController::class, 'index'])->name('pengajuan.cuti');
    Route::get('/pengajuan-cuti', [CutiKaryawanController::class, 'index'])->name('pengajuan.cuti.alt');
    Route::post('/pengajuan-cuti/store', [CutiKaryawanController::class, 'store'])->name('pengajuan.store');
    Route::get('/pengajuan-cuti/edit/{id}', [CutiKaryawanController::class, 'edit'])->name('pengajuan.edit');
    Route::post('/pengajuan-cuti/update/{id}', [CutiKaryawanController::class, 'update'])->name('pengajuan.update');
    Route::get('/pengajuan-cuti/delete/{id}', [CutiKaryawanController::class, 'destroy'])->name('pengajuan.delete');
    
    Route::get('/hrd/cuti/approval', [CutiKaryawanController::class, 'approvalIndex']);
    Route::get('/cuti-approval', [CutiKaryawanController::class, 'approvalIndex']);
    Route::post('/cuti-approval/update-status/{id}', [CutiKaryawanController::class, 'updateStatusDept']);

    // === MANAJEMEN PENGAJUAN LEMBUR KARYAWAN (PRIBADI) ===
    Route::get('/hrd/lembur/pribadi', [LemburKaryawanController::class, 'index'])->name('lembur.pribadi');
    Route::post('/hrd/lembur/store', [LemburKaryawanController::class, 'store'])->name('lembur.store');
    Route::get('/hrd/lembur/delete/{id}', [LemburKaryawanController::class, 'destroy'])->name('lembur.delete');

    // === RUTE APPROVAL LEMBUR (UNTUK ATASAN DEPARTEMEN) ===
    Route::middleware(['role:quality,produksi,engineering,development,ppic,fat,purchasing,sales_marketing,hrd_ga,admin'])->group(function () {
        Route::get('/departemen/lembur/approval', [LemburKaryawanController::class, 'approvalIndex'])->name('lembur.approval');
        Route::post('/departemen/lembur/approval/update/{id}', [LemburKaryawanController::class, 'updateStatusDept'])->name('lembur.update.dept');
    });
    

    // === KELOMPOK DEPARTEMEN DEVELOPMENT (TERMASUK MASTER ITEM & BOM) ===
    Route::middleware(['role:development,admin'])->group(function () {
        Route::get('/development', [DevelopmentController::class, 'index']);
        Route::get('/development/dashboard', [\App\Http\Controllers\Development\DevelopmentController::class, 'dashboard']);
        Route::post('/development/store', [DevelopmentController::class, 'store']);
        Route::delete('/development/delete/{id}', [DevelopmentController::class, 'destroy']);

        // Master Material (MM)
        Route::get('/development/master-material', [MasterMaterialController::class, 'index'])->name('development.master-material.index');
        Route::post('/development/master-material/store', [MasterMaterialController::class, 'store'])->name('development.master-material.store');

        // Bill of Materials (BOM)
        Route::get('/development/bom', [BomController::class, 'index'])->name('development.bom.index');
        Route::post('/development/bom/store', [BomController::class, 'store'])->name('development.bom.store');
        Route::delete('/development/bom/{id}', [BomController::class, 'destroy'])->name('development.bom.destroy');
        
        Route::put('/development/update/{id}', [DevelopmentController::class, 'update']);
        Route::put('/development/bom/update/{id}', [BomController::class, 'update']);
        // Sesuaikan nama Controller-nya dengan yang Anda gunakan
        Route::delete('/development/master-material/{id}', [App\Http\Controllers\Development\MasterMaterialController::class, 'destroy']);
    });

    

    

    // Dashboard pendukung
    Route::get('/ga', function() { return view('dashboards.konten'); });
    Route::get('/fat', function() { return view('dashboards.konten'); });
    Route::get('/purchasing', function() { return view('dashboards.konten'); });
    Route::get('/sales-marketing', function() { return view('dashboards.konten'); });
    Route::get('/ppic', function() { return view('dashboards.konten'); });
    Route::get('/produksi', function() { return view('dashboards.konten'); });
    Route::get('/warehouse', function() { return view('dashboards.konten'); });
    
    Route::get('/quality/cuti/pribadi', [CutiKaryawanController::class, 'index']);
    Route::get('/quality/cuti/approval', [CutiKaryawanController::class, 'approvalIndex']);
});


/*
|--------------------------------------------------------------------------
| 3. ROUTE UTILITY / PEMELIHARAAN SISTEM
|--------------------------------------------------------------------------
*/
Route::get('/fix-semua-password', function () {
    $users = \App\Models\User::all();
    foreach ($users as $user) {
        $user->password = bcrypt('12345678'); 
        $user->save();
    }
    return 'SUKSES BERAT! Semua password sudah direset ke: 12345678.';
});

Route::get('/sikat-habis', function () {
    DB::table('users')->update([
        'password' => Hash::make('12345678')
    ]);
    return 'BERHASIL! Sandi sudah ditulis ulang.';
});

Route::get('/qir/delete', [QirController::class, 'destroy'])->name('qir.destroy');

// RUTE KHUSUS TIM SALES / MARKETING (Grup Prefix '/sales')
Route::group(['prefix' => 'sales', 'middleware' => ['auth']], function () {
    
    // === RUTE MODUL SALES (ORDER CUSTOMER / PO) ===
    Route::get('/po/input', [\App\Http\Controllers\Sales\SalesOrderController::class, 'create'])->name('sales.po.input');
    Route::post('/po/store', [\App\Http\Controllers\Sales\SalesOrderController::class, 'store'])->name('sales.po.store');
    Route::get('/po/edit/{id}', [\App\Http\Controllers\Sales\SalesOrderController::class, 'edit'])->name('sales.po.edit');
    Route::put('/po/update/{id}', [\App\Http\Controllers\Sales\SalesOrderController::class, 'update'])->name('sales.po.update');
    Route::delete('/po/bulk-delete', [\App\Http\Controllers\Sales\SalesOrderController::class, 'bulkDelete'])->name('sales.po.bulk_delete'); 
    Route::delete('/po/delete/{id}', [\App\Http\Controllers\Sales\SalesOrderController::class, 'destroy'])->name('sales.po.destroy');

    // FORECAST
    Route::get('/forecast', [\App\Http\Controllers\Sales\SalesForecastController::class, 'index'])->name('sales.forecast.index');
    Route::get('/get-produk/{mm}', [\App\Http\Controllers\Sales\SalesForecastController::class, 'getProduk']);
    Route::get('/forecast/create', [\App\Http\Controllers\Sales\SalesForecastController::class, 'create'])->name('sales.forecast.create');
    Route::post('/forecast', [\App\Http\Controllers\Sales\SalesForecastController::class, 'store'])->name('sales.forecast.store');
    
    // REVISI FORECAST
    Route::get('/forecast/{id}/revisi', [\App\Http\Controllers\Sales\SalesForecastController::class, 'revisi'])->name('sales.forecast.revisi');
    Route::post('/forecast/{id}/revisi', [\App\Http\Controllers\Sales\SalesForecastController::class, 'storeRevisi'])->name('sales.forecast.storeRevisi');
    
    // OSPO
    Route::get('/ospo', [\App\Http\Controllers\Sales\SalesOrderController::class, 'ospo'])->name('sales.ospo');
    
    // PENGELOLAAN HARGA
    Route::get('/prices', [\App\Http\Controllers\Sales\PriceController::class, 'index'])->name('sales.prices.index');
    Route::post('/prices', [\App\Http\Controllers\Sales\PriceController::class, 'store'])->name('sales.prices.store');
    Route::delete('/prices/bulk-delete', [\App\Http\Controllers\Sales\PriceController::class, 'bulkDelete'])->name('sales.prices.bulk_delete');
    Route::delete('/prices/{id}', [\App\Http\Controllers\Sales\PriceController::class, 'destroy'])->name('sales.prices.destroy');

    // MASTER CUSTOMER (Lengkap dengan Edit, Update, dan Destroy)
    Route::get('/master-customer', [\App\Http\Controllers\Sales\MasterCustomerController::class, 'index'])->name('sales.master-customer.index');
    Route::get('/master-customer/create', [\App\Http\Controllers\Sales\MasterCustomerController::class, 'create'])->name('sales.master-customer.create');
    Route::post('/master-customer', [\App\Http\Controllers\Sales\MasterCustomerController::class, 'store'])->name('sales.master-customer.store');
    Route::get('/master-customer/{id}/edit', [\App\Http\Controllers\Sales\MasterCustomerController::class, 'edit'])->name('sales.master-customer.edit');
    Route::put('/master-customer/{id}', [\App\Http\Controllers\Sales\MasterCustomerController::class, 'update'])->name('sales.master-customer.update');
    Route::delete('/master-customer/{id}', [\App\Http\Controllers\Sales\MasterCustomerController::class, 'destroy'])->name('sales.master-customer.destroy');
    
});


// === RUTE PPIC (DASHBOARD PO & KALKULASI MRP) ===
Route::middleware(['auth'])->group(function () {
    Route::get('/ppic/dashboard-po', [PpicController::class, 'indexPo'])->name('ppic.po.index');
    Route::post('/ppic/dashboard-po', [PpicController::class, 'storePo'])->name('ppic.po.store');
    Route::get('/ppic/mrp-calculate/{id}', [PpicController::class, 'calculateMrp'])->name('ppic.mrp.calculate');
    Route::post('/ppic/mrp-calculate/create-pr', [PpicController::class, 'createPr'])->name('ppic.mrp.create_pr');
});

// RUTE KHUSUS PPIC
Route::group(['prefix' => 'ppic', 'middleware' => ['auth']], function () {
    // Rute Work Order (Tampilan & Aksi)
    Route::get('/work-order', [\App\Http\Controllers\PPIC\WorkOrderController::class, 'index'])->name('ppic.work_order.index');
    Route::get('/work-order/{id}/request-material', [\App\Http\Controllers\PPIC\WorkOrderController::class, 'requestMaterial'])->name('ppic.work_order.request');
    
    // [BARU] Rute untuk form Pembuatan Work Order hasil MRP
    Route::get('/create-wo', [\App\Http\Controllers\PPIC\WorkOrderController::class, 'create'])->name('ppic.wo.create');
    Route::post('/store-wo', [\App\Http\Controllers\PPIC\WorkOrderController::class, 'store'])->name('ppic.wo.store');

    // Rute MRP Dashboard & Parameter
    Route::get('/mrp-dashboard', [\App\Http\Controllers\PPIC\MrpController::class, 'index'])->name('ppic.mrp.index');
    Route::get('/mrp-parameter', [\App\Http\Controllers\PPIC\MrpParameterController::class, 'index'])->name('ppic.mrp_parameter.index');
    Route::post('/mrp-parameter/{id}', [\App\Http\Controllers\PPIC\MrpParameterController::class, 'update'])->name('ppic.mrp_parameter.update');
});
Route::get('/ppic/create-pr', [\App\Http\Controllers\Ppic\PpicController::class, 'createPrForm']);
Route::post('/ppic/store-pr', [\App\Http\Controllers\PPIC\PPICController::class, 'storePr'])->name('ppic.pr.store');
Route::get('/ppic/riwayat-pr', [\App\Http\Controllers\Ppic\PpicController::class, 'riwayatPr'])->name('ppic.riwayat_pr');
// Rute untuk Form Pembuatan WO Baru
Route::get('/ppic/work-order/create', [\App\Http\Controllers\Ppic\WorkOrderController::class, 'create']);
Route::post('/ppic/work-order', [\App\Http\Controllers\Ppic\WorkOrderController::class, 'store']);
// Rute untuk mengirim permintaan material ke Gudang
Route::post('/ppic/work-order/{id}/send-request', [App\Http\Controllers\Ppic\WorkOrderController::class, 'sendRequestToWarehouse']);

// ==========================================================
// 1. RUTE PRODUKSI (DENGAN MIDDLEWARE ROLE)
// ==========================================================
Route::middleware(['role:produksi,admin,ppic'])->group(function () {
    // Rekap & Stok Umum
    Route::get('/produksi/stok/rekap', [ProduksiStokController::class, 'rekap'])->name('produksi.stok.rekap');
    Route::get('/produksi/stok', [ProduksiStokController::class, 'index'])->name('produksi.stok.index');
    Route::post('/produksi/stok/tambah', [ProduksiStokController::class, 'tambahStok'])->name('produksi.stok.tambah');

    // Mutasi Stok Gudang & Produksi
    Route::get('/produksi/approval-mutasi', [\App\Http\Controllers\Produksi\MutasiStokController::class, 'index'])->name('produksi.mutasi.index');
    Route::post('/produksi/approval-mutasi/approve/{id}', [\App\Http\Controllers\Produksi\MutasiStokController::class, 'approve'])->name('produksi.mutasi.approve');
    Route::post('/produksi/approval-mutasi/reject/{id}', [\App\Http\Controllers\Produksi\MutasiStokController::class, 'reject'])->name('produksi.mutasi.reject');
    Route::get('/produksi/approval-mutasi/print', [\App\Http\Controllers\Produksi\MutasiStokController::class, 'print'])->name('produksi.mutasi.print');
    
    Route::get('/produksi/mutasi-stok', [\App\Http\Controllers\Produksi\MutasiStokController::class, 'mutasiKeluarIndex'])->name('produksi.mutasi_keluar.index');
    Route::post('/produksi/mutasi-stok/store', [\App\Http\Controllers\Produksi\MutasiStokController::class, 'storeMutasiKeluar'])->name('produksi.mutasi_keluar.store');
    Route::get('/produksi/mutasi-stok/get-item', [\App\Http\Controllers\Produksi\MutasiStokController::class, 'getItemByMm'])->name('produksi.mutasi_keluar.get_item');
    Route::get('/produksi/mutasi/riwayat-gabungan', [\App\Http\Controllers\Produksi\MutasiStokController::class, 'riwayatMutasiGabungan'])->name('produksi.mutasi.riwayat');
});

// ==========================================================
// 2. RUTE KHUSUS PRODUKSI (PREFIX: /produksi)
// ==========================================================
Route::group(['prefix' => 'produksi', 'middleware' => ['auth']], function () {
    
    // Dashboard & Daftar Work Order
    Route::get('/dashboard-wo', [\App\Http\Controllers\Produksi\ProduksiController::class, 'index'])->name('produksi.wo.list');
    Route::get('/work-order', [\App\Http\Controllers\Produksi\ProduksiController::class, 'daftarWorkOrder'])->name('produksi.work_order.index');
    
    // Request Material & Approval
    Route::get('/work-order/{id}/request', [\App\Http\Controllers\Produksi\ProduksiController::class, 'formRequestMaterial'])->name('produksi.work_order.request');
    Route::post('/work-order/{id}/request', [\App\Http\Controllers\Produksi\ProduksiController::class, 'submitRequestMaterial'])->name('produksi.work_order.submit');
    
    // Trigger Status Work Order
    Route::post('/wo/mulai/{id}', [\App\Http\Controllers\Produksi\ProduksiController::class, 'mulaiProses'])->name('produksi.wo.mulai');
    Route::post('/wo/finish/{id}', [\App\Http\Controllers\Produksi\ProduksiController::class, 'startProduksi'])->name('produksi.wo.finish'); 
    
    // Riwayat Keseluruhan
    Route::get('/riwayat-wo', [\App\Http\Controllers\Produksi\ProduksiController::class, 'riwayatProses'])->name('produksi.riwayat.index');

    // --------------------------------------------------------
    // MENU 1: EKSEKUSI WO BERJALAN 
    // --------------------------------------------------------
    Route::get('/proses', [\App\Http\Controllers\Produksi\ProduksiController::class, 'daftarProses'])->name('produksi.proses.index');
    Route::get('/wo/proses/{id}', [\App\Http\Controllers\Produksi\ProduksiController::class, 'proses'])->name('produksi.wo.proses');
    
    // --------------------------------------------------------
    // MENU 2: TRACEABILITY CONTROL (RUTE DIPERBAIKI)
    // --------------------------------------------------------
    Route::get('/traceability', [\App\Http\Controllers\Produksi\ProduksiController::class, 'traceabilityIndex'])->name('produksi.traceability.index');
    Route::get('/traceability/{id}', [\App\Http\Controllers\Produksi\ProduksiController::class, 'traceabilityForm'])->name('produksi.traceability.form');
    Route::post('/wo/proses/{id}/traceability', [\App\Http\Controllers\Produksi\ProduksiController::class, 'storeTraceability'])->name('produksi.wo.store_traceability');
    Route::post('/wo/proses/{id}/traceability-update/{log_id}', [\App\Http\Controllers\Produksi\ProduksiController::class, 'updateTraceability'])->name('produksi.wo.update_traceability');
    // --------------------------------------------------------
    // MENU DETAIL / MONITORING WO
    // --------------------------------------------------------
    Route::get('/wo/{id}/detail', [\App\Http\Controllers\Produksi\ProduksiController::class, 'detailWo'])->name('produksi.wo.detail');

    // --------------------------------------------------------
    // MENU 3: DAILY REPORT / CHECK SHEET (PERSIAPAN KE DEPAN)
    // --------------------------------------------------------
    Route::get('/daily-report', [\App\Http\Controllers\Produksi\ProduksiController::class, 'dailyReportIndex'])->name('produksi.daily_report.index');
    Route::get('/daily-report/{id}', [\App\Http\Controllers\Produksi\ProduksiController::class, 'dailyReportForm'])->name('produksi.daily_report.form');
    Route::post('/daily-report/{id}/store', [\App\Http\Controllers\Produksi\ProduksiController::class, 'dailyReportStore'])->name('produksi.daily_report.store');
    // --------------------------------------------------------
    // MENU MASTER DATA (REJECT & DOWNTIME)
    // --------------------------------------------------------
    Route::get('/master/reject', [\App\Http\Controllers\Produksi\MasterDataController::class, 'rejectIndex'])->name('produksi.master.reject');
    Route::post('/master/reject', [\App\Http\Controllers\Produksi\MasterDataController::class, 'rejectStore'])->name('produksi.master.reject.store');
    Route::get('/master/reject/{id}/delete', [\App\Http\Controllers\Produksi\MasterDataController::class, 'rejectDestroy'])->name('produksi.master.reject.delete');

    Route::get('/master/downtime', [\App\Http\Controllers\Produksi\MasterDataController::class, 'downtimeIndex'])->name('produksi.master.downtime');
    Route::post('/master/downtime', [\App\Http\Controllers\Produksi\MasterDataController::class, 'downtimeStore'])->name('produksi.master.downtime.store');
    Route::get('/master/downtime/{id}/delete', [\App\Http\Controllers\Produksi\MasterDataController::class, 'downtimeDestroy'])->name('produksi.master.downtime.delete');
    // --------------------------------------------------------
    // DASHBOARD OEE & REPORTING
    // --------------------------------------------------------
    Route::get('/dashboard-oee', [\App\Http\Controllers\Produksi\ProduksiController::class, 'oeeDashboard'])->name('produksi.oee.dashboard');
    // Dashboard Utama Produksi
    Route::get('/dashboard', [\App\Http\Controllers\Produksi\ProduksiController::class, 'dashboardUtama'])->name('produksi.dashboard');
});


// === RUTE PURCHASING ===
// [REVISI 1]: Tambahkan 'manager_plan' ke dalam daftar role yang diizinkan
Route::middleware(['role:purchasing,admin,manager_plan,direktur,presiden_direktur'])->group(function () {
    Route::get('/purchasing/dashboard', [PurchasingController::class, 'dashboard'])->name('purchasing.dashboard');
    Route::post('/purchasing/mark-purchased/{id}', [PurchasingController::class, 'markAsPurchased'])->name('purchasing.mark');
    Route::post('/purchasing/karantina/mutasi-keluar', [KarantinaController::class, 'mutasiKeluar']);
    
    // Rute untuk Daftar Purchase Request (PR) Masuk
    Route::get('/purchasing/purchase-request', [PurchasingController::class, 'prIndex'])->name('purchasing.pr.index');
    Route::post('/purchasing/purchase-request/proses/{id}', [PurchasingController::class, 'processPr'])->name('purchasing.pr.proses');
    
    // [REVISI 2]: Pindahkan rute Approve PR ke DALAM blok ini agar terlindungi dan bisa diakses Manager
    Route::post('/purchasing/approve-pr/{id}', [PurchasingController::class, 'approvePr']);
    
    // Rute PO 
    Route::get('/purchasing/create-po/{id}', [PurchasingController::class, 'createPo'])->name('purchasing.create_po');
    Route::post('/purchasing/store-po/{id}', [PurchasingController::class, 'storePo'])->name('purchasing.store_po');
    
    // Rute untuk form Pembuatan Purchase Order hasil MRP
    Route::get('/purchasing/create-po', [PurchasingController::class, 'create'])->name('purchasing.po.create');

    // Rute untuk melihat Riwayat PO
    Route::get('/purchasing/riwayat-po', [PurchasingController::class, 'riwayatPo'])->name('purchasing.riwayat_po');
    // Rute untuk mencetak dokumen PO
    Route::get('/purchasing/print-po/{id}', [PurchasingController::class, 'printPo'])->name('purchasing.print_po');
    Route::get('/purchasing/outstanding-po', [PurchasingController::class, 'outstandingPo'])->name('purchasing.outstanding_po');
    // --- RUTE APPROVAL PO TIERED (MANAGER, DIREKTUR, PRESDIR) ---
    Route::get('/purchasing/approval-po', [\App\Http\Controllers\Purchasing\PurchasingController::class, 'approvalPoIndex'])->name('purchasing.approval_po.index');
    Route::post('/purchasing/approval-po/approve/{id}', [\App\Http\Controllers\Purchasing\PurchasingController::class, 'approvePo'])->name('purchasing.approval_po.approve');
    });

// Sisa rute (Bawaan Anda sebelumnya)
Route::post('/purchasing/store-po', [\App\Http\Controllers\Purchasing\PurchasingController::class, 'store'])->name('purchasing.po.store');
// === RUTE FORM PR DEPARTEMEN UMUM ===
Route::get('/departemen/purchase-request', [App\Http\Controllers\DepartemenPrController::class, 'index']);
Route::post('/departemen/purchase-request', [App\Http\Controllers\DepartemenPrController::class, 'store']);
// --- Ganti route edit, update, delete vendor Anda menjadi seperti ini ---
Route::get('/purchasing/vendors/{id}/edit', [VendorController::class, 'edit'])->name('purchasing.vendors.edit');
Route::put('/purchasing/vendors/{id}', [VendorController::class, 'update'])->name('purchasing.vendors.update');
Route::delete('/purchasing/vendors/{id}', [VendorController::class, 'destroy'])->name('purchasing.vendors.destroy');

// =========================================================================
// 1. MODUL WAREHOUSE (GUDANG)
// =========================================================================

Route::middleware(['role:warehouse,admin,ppic'])->group(function () {
    
    // --- Rute Rekap & Stok ---
    Route::get('/warehouse/stok/rekap', [WarehouseStokController::class, 'rekap'])->name('warehouse.stok.rekap');
    Route::get('/warehouse/stok/raw-material', [WarehouseController::class, 'rawMaterial'])->name('warehouse.stok.raw_material');
    Route::get('/warehouse/stok/karantina', [WarehouseController::class, 'karantina'])->name('warehouse.stok.karantina');
    Route::get('/warehouse/stok/raw-material/print', [WarehouseController::class, 'printRawMaterial'])->name('warehouse.stok.raw_material.print');

    // --- Rute Mutasi Stok ---
    Route::get('/warehouse/approval-mutasi', [WarehouseMutasiController::class, 'index'])->name('warehouse.mutasi.approval.index');
    Route::post('/warehouse/approval-mutasi/approve/{id}', [WarehouseMutasiController::class, 'approve'])->name('warehouse.mutasi.approve');
    Route::post('/warehouse/approval-mutasi/reject/{id}', [WarehouseMutasiController::class, 'reject'])->name('warehouse.mutasi.reject');
    Route::get('/warehouse/approval-mutasi/print', [WarehouseMutasiController::class, 'print'])->name('warehouse.mutasi.approval.print');
    Route::get('/warehouse/mutasi-stok', [WarehouseMutasiController::class, 'mutasiKeluarIndex'])->name('warehouse.mutasi_keluar.index');
    Route::post('/warehouse/mutasi-stok/store', [WarehouseMutasiController::class, 'storeMutasiKeluar'])->name('warehouse.mutasi_keluar.store');
    Route::get('/warehouse/mutasi-stok/get-item', [WarehouseMutasiController::class, 'getItemByMm'])->name('warehouse.mutasi_keluar.get_item');
    Route::get('/warehouse/mutasi/riwayat-gabungan', [WarehouseMutasiController::class, 'riwayatMutasiGabungan'])->name('warehouse.mutasi.riwayat');
    
    // --- Dashboard & Material Tambahan ---
    Route::get('/dashboard', [WarehouseController::class, 'dashboard'])->name('warehouse.dashboard');
    Route::get('/warehouse/materials', [WarehouseController::class, 'index'])->name('warehouse.index');
    Route::post('/warehouse/materials/store', [WarehouseController::class, 'store'])->name('warehouse.store');
});

Route::middleware(['role:admin,warehouse'])->group(function () {
    
    // --- Rute Incoming Material ---
    Route::get('/warehouse/incoming', [IncomingMaterialController::class, 'index'])->name('warehouse.incoming.index');
    Route::post('/warehouse/incoming/store', [IncomingMaterialController::class, 'store'])->name('warehouse.incoming.store');
    Route::delete('/warehouse/incoming/bulk-delete', [IncomingMaterialController::class, 'bulkDelete'])->name('warehouse.incoming.bulk_delete');
    Route::delete('/warehouse/incoming/{id}', [IncomingMaterialController::class, 'destroy'])->name('warehouse.incoming.destroy');
    
    // =================================================================
    // [PERBAIKAN] RUTE OUTGOING & APPROVAL (TIDAK BENTROK LAGI)
    // =================================================================
    
    // 1. Menu Input Surat Jalan
    Route::get('/warehouse/outgoing', [OutgoingController::class, 'index'])->name('warehouse.outgoing.index');
    Route::post('/warehouse/outgoing/store', [OutgoingController::class, 'store'])->name('warehouse.outgoing.store');
    
    // 2. Menu Approval Surat Jalan (URL DIUBAH AGAR SIDEBAR NORMAL)
    Route::get('/warehouse/approval-sj', [\App\Http\Controllers\Warehouse\OutgoingController::class, 'approvalPage'])->name('warehouse.outgoing.approvalPage');
    Route::post('/warehouse/approval-sj/approve/{id}', [\App\Http\Controllers\Warehouse\OutgoingController::class, 'approveSuratJalan'])->name('warehouse.outgoing.approve');
});

// --- Rute Tambahan Warehouse yang Berada di Luar Grup ---
Route::prefix('warehouse')->name('warehouse.')->group(function () {
    Route::get('/incoming/{id}/edit', [IncomingMaterialController::class, 'edit'])->name('incoming.edit');
    Route::put('/incoming/{id}', [IncomingMaterialController::class, 'update'])->name('incoming.update');
});

Route::get('/warehouse/work-order', [\App\Http\Controllers\Warehouse\WorkOrderController::class, 'index'])->name('warehouse.work_order.index');
Route::get('/warehouse/material-request', [\App\Http\Controllers\WarehouseController::class, 'indexMaterial']);
Route::post('/warehouse/material-request/{id}/process', [\App\Http\Controllers\WarehouseController::class, 'processMaterial']);
Route::post('/warehouse/stok/mutasi-raw', [WarehouseMutasiController::class, 'storeMutasiKeluar'])->name('warehouse.stok.mutasi.store');
Route::get('/warehouse/stok/get-item', [WarehouseMutasiController::class, 'getItemByMm'])->name('warehouse.stok.get_item');
Route::delete('/warehouse/outgoing/{id}', [\App\Http\Controllers\Warehouse\OutgoingController::class, 'destroy'])->name('warehouse.outgoing.destroy');
// Rute untuk Print Surat Jalan
Route::get('/warehouse/outgoing/{id}/print', [\App\Http\Controllers\Warehouse\OutgoingController::class, 'print'])->name('warehouse.outgoing.print');

// =========================================================================
// 2. MODUL PURCHASING (DEPARTEMEN PEMBELIAN)
// =========================================================================
Route::prefix('purchasing')->name('purchasing.')->group(function () {
    Route::get('/vendors', [VendorController::class, 'index'])->name('vendors.index');
    Route::post('/vendors', [VendorController::class, 'store'])->name('vendors.store');
    
    // Rute Karantina Purchasing
    Route::get('/karantina', [KarantinaController::class, 'index'])->name('karantina.index');
    Route::post('/karantina/mutasi', [KarantinaController::class, 'mutasiKeluar'])->name('karantina.mutasi');
});


// =========================================================================
// 3. MODUL MASTER DATA (UMUM / ADMIN)
// =========================================================================
Route::group(['prefix' => 'master', 'middleware' => ['auth']], function () {
    Route::get('/customer', [\App\Http\Controllers\MasterCustomerController::class, 'index'])->name('master.customer.index');
    Route::get('/customer/create', [\App\Http\Controllers\MasterCustomerController::class, 'create'])->name('master.customer.create');
    Route::post('/customer', [\App\Http\Controllers\MasterCustomerController::class, 'store'])->name('master.customer.store');
});

// ROUTE KHUSUS DEPARTEMEN FAT (KEUANGAN)
Route::prefix('fat')->name('fat.')->group(function () {
    // Tambahkan Rute Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Fat\DashboardController::class, 'index'])->name('dashboard');
    // Tambahkan rute riwayat ini (paling atas juga boleh)
    Route::get('/invoices/history', [\App\Http\Controllers\Fat\SalesInvoiceController::class, 'history'])->name('invoices.history');
    // Halaman Antrean Penagihan (Index)
    Route::get('/invoices', [\App\Http\Controllers\Fat\SalesInvoiceController::class, 'index'])->name('invoices.index');
    // TAMBAHKAN INI: Halaman Form Buat Invoice (Membawa ID Surat Jalan)
    Route::get('/invoices/create/{sj_id}', [\App\Http\Controllers\Fat\SalesInvoiceController::class, 'create'])->name('invoices.create');
    // TAMBAHKAN INI: Rute untuk memproses penyimpanan data
    Route::post('/invoices/store', [\App\Http\Controllers\Fat\SalesInvoiceController::class, 'store'])->name('invoices.store');
    // Rute untuk melihat & mencetak detail Invoice
    Route::get('/invoices/{id}/print', [\App\Http\Controllers\Fat\SalesInvoiceController::class, 'print'])->name('invoices.print');
    // Rute untuk melihat Detail Invoice di dalam sistem
    Route::get('/invoices/{id}/detail', [\App\Http\Controllers\Fat\SalesInvoiceController::class, 'show'])->name('invoices.show');
    // Rute untuk mengubah status invoice menjadi LUNAS
    Route::post('/invoices/{id}/pay', [\App\Http\Controllers\Fat\SalesInvoiceController::class, 'markAsPaid'])->name('invoices.pay');
    // ROUTE ACCOUNTS PAYABLE (HUTANG SUPPLIER)
    Route::get('/purchase-invoices', [\App\Http\Controllers\Fat\PurchaseInvoiceController::class, 'index'])->name('purchase_invoices.index');
    Route::get('/purchase-invoices/create/{id}', [\App\Http\Controllers\Fat\PurchaseInvoiceController::class, 'create'])->name('purchase_invoices.create');
    // Rute untuk memproses penyimpanan Faktur Pembelian (Hutang)
    Route::post('/purchase-invoices/store', [\App\Http\Controllers\Fat\PurchaseInvoiceController::class, 'store'])->name('purchase_invoices.store');
    // Rute untuk melihat Riwayat Hutang Supplier
    Route::get('/purchase-invoices/history', [\App\Http\Controllers\Fat\PurchaseInvoiceController::class, 'history'])->name('purchase_invoices.history');
    // Rute untuk melihat Detail Hutang
    Route::get('/purchase-invoices/{id}/detail', [\App\Http\Controllers\Fat\PurchaseInvoiceController::class, 'show'])->name('purchase_invoices.show');
    
    // Rute untuk mengeksekusi pelunasan (ubah status jadi Paid)
    Route::post('/purchase-invoices/{id}/pay', [\App\Http\Controllers\Fat\PurchaseInvoiceController::class, 'markAsPaid'])->name('purchase_invoices.pay');
    // Rute Riwayat Hutang
    Route::get('/purchase-invoices/history', [\App\Http\Controllers\Fat\PurchaseInvoiceController::class, 'history'])->name('purchase_invoices.history');
    // Rute untuk mencetak Bukti Tagihan Supplier
    Route::get('/purchase-invoices/{id}/print', [\App\Http\Controllers\Fat\PurchaseInvoiceController::class, 'print'])->name('purchase_invoices.print');
    // ROUTE LAPORAN KEUANGAN
    Route::get('/reports/profit-loss', [\App\Http\Controllers\Fat\ReportController::class, 'profitLoss'])->name('reports.profit_loss');
    // ROUTE BIAYA OPERASIONAL (OPEX)
    Route::get('/operational-expenses', [\App\Http\Controllers\Fat\OperationalExpenseController::class, 'index'])->name('operational_expenses.index');
    Route::get('/operational-expenses/create', [\App\Http\Controllers\Fat\OperationalExpenseController::class, 'create'])->name('operational_expenses.create');
    Route::post('/operational-expenses/store', [\App\Http\Controllers\Fat\OperationalExpenseController::class, 'store'])->name('operational_expenses.store');
    // ROUTE LAPORAN KEUANGAN
    Route::get('/reports/profit-loss', [\App\Http\Controllers\Fat\ReportController::class, 'profitLoss'])->name('reports.profit_loss');
    Route::get('/reports/profit-loss/export', [\App\Http\Controllers\Fat\ReportController::class, 'exportProfitLoss'])->name('reports.profit_loss.export');
    // ROUTE KAS & BANK
    Route::get('/cash-banks', [\App\Http\Controllers\Fat\CashBankController::class, 'index'])->name('cash_banks.index');
    Route::post('/cash-banks/store', [\App\Http\Controllers\Fat\CashBankController::class, 'store'])->name('cash_banks.store');
    // ROUTE JURNAL UMUM
    Route::get('/journals', [\App\Http\Controllers\Fat\JournalController::class, 'index'])->name('journals.index');
    Route::get('/journals/create', [\App\Http\Controllers\Fat\JournalController::class, 'create'])->name('journals.create');
    Route::post('/journals/store', [\App\Http\Controllers\Fat\JournalController::class, 'store'])->name('journals.store');
    // ROUTE MASTER COA (CHART OF ACCOUNTS)
    Route::get('/coas', [\App\Http\Controllers\Fat\CoaController::class, 'index'])->name('coas.index');
    Route::post('/coas/store', [\App\Http\Controllers\Fat\CoaController::class, 'store'])->name('coas.store');
    Route::put('/coas/update/{id}', [\App\Http\Controllers\Fat\CoaController::class, 'update'])->name('coas.update');
    Route::delete('/coas/destroy/{id}', [\App\Http\Controllers\Fat\CoaController::class, 'destroy'])->name('coas.destroy');
    // ROUTE LAPORAN BUKU BESAR (GENERAL LEDGER)
    Route::get('/reports/general-ledger', [\App\Http\Controllers\Fat\ReportController::class, 'generalLedger'])->name('reports.general_ledger');
    // ROUTE LAPORAN NERACA (BALANCE SHEET)
    Route::get('/reports/balance-sheet', [\App\Http\Controllers\Fat\ReportController::class, 'balanceSheet'])->name('reports.balance_sheet');
});

// Gunakan route POST agar lebih aman dan tidak bisa diakses lewat ketik URL langsung
Route::post('/developer/reset-transaksi', function () {
    
    // Route Reset data, Daftar SEMUA tabel transaksi dari ujung ke ujung
    $tabelTransaksi = [
        // 1. Quality & Produksi
        'capa_customers', 'capa_suppliers', 'coa_records', 'coas',
        'qir_records', 'quality_stocks', 'traceability_logs',
        'incoming_materials', 'produksi_fg_logs', 'produksi_stoks', 'produksi_stocks',
        'finished_goods', 'karantina_stocks', 'mutasi_materials', 'ppic_pos',

        // 2. Gudang & Stok
        'stock_transactions', 'warehouse_outgoings', 'warehouse_stocks',

        // 3. Purchasing & Sales
        'purchase_requests', 'purchase_orders', 'purchase_invoices',
        'sales_forecasts', 'sales_orders', 'sales_order_details',
        'sales_invoices', 'work_orders',

        // 4. Finance, Laporan Harian & HRD
        'cash_bank_ledgers', 'journal_details', 'journals', 'operational_expenses',
        'daily_reports', 'daily_report_downtimes', 'daily_report_rejects',
        'development_projects', 'payrolls', 'lembur_karyawans', 'cuti_records', 'riwayat_cuti'
    ];

    DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    foreach ($tabelTransaksi as $tabel) {
        // Pengecekan cerdas: Hanya hapus jika tabelnya benar-benar ada di database
        // (Ini mencegah error Code 1146 seperti yang Anda alami sebelumnya)
        if (Schema::hasTable($tabel)) {
            DB::table($tabel)->truncate();
        }
    }

    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    // Setelah selesai, kembalikan ke halaman sebelumnya dengan pesan sukses
    return redirect()->back()->with('success', 'BAM! 💥 Semua data transaksi berhasil dibersihkan. Data master aman!');

})->name('developer.reset_transaksi');