<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MasterAqlStandard;

class MasterAqlController extends Controller
{
    public function index(Request $request)
    {
        // Ambil data master aql beserta filter customer jika ada
        $query = DB::table('master_aql_standards');

        if ($request->filled('customer')) {
            $query->where('customer_name', $request->customer);
        }

        $aqlStandards = $query->get();
        
        // Ambil daftar customer dari tabel master_customers
        $customers = DB::table('master_customers')->get();

        return view('quality.master-aql.index', compact('aqlStandards', 'customers'));
    }

    public function store(Request $request)
{
    $request->validate([
        'customer_id'   => 'required',
        'customer_name' => 'required',
    ]);

    MasterAqlStandard::updateOrCreate(
        ['customer_id' => $request->customer_id], // Acuan pencarian data
        [
            'customer_name'   => $request->customer_name,
            'tipe_standar'    => $request->system_type, // <-- Disimpan ke kolom tipe_standar
            'aql_zero_defect' => $request->aql_zero_defect,
            'aql_critical'    => $request->aql_critical,
            'aql_major'       => $request->aql_major,
            'aql_minor'       => $request->aql_minor,
            'crqs_amber'      => $request->crqs_amber,
            'crqs_red'        => $request->crqs_red,
        ]
    );

    return redirect()->back()->with('success', 'Parameter customer berhasil disimpan!');
}
public function edit($id)
{
    // Gunakan MasterAqlStandard sesuai model yang di-import di atas
    $aqlStandard = MasterAqlStandard::findOrFail($id);
    
    // Ambil daftar customer menggunakan DB::table seperti di fungsi index
    $customers = DB::table('master_customers')->get();
    
    return view('quality.master-aql.edit', compact('aqlStandard', 'customers'));
}
public function update(Request $request, $id)
{
    $request->validate([
        'customer_id'   => 'required',
        'customer_name' => 'required',
    ]);

    // Cari data berdasarkan ID, lalu update
    $aqlStandard = MasterAqlStandard::findOrFail($id);
    
    $aqlStandard->update([
        'customer_id'     => $request->customer_id,
        'customer_name'   => $request->customer_name,
        'tipe_standar'    => $request->system_type,
        'aql_zero_defect' => $request->aql_zero_defect,
        'aql_critical'    => $request->aql_critical,
        'aql_major'       => $request->aql_major,
        'aql_minor'       => $request->aql_minor,
        'crqs_amber'      => $request->crqs_amber,
        'crqs_red'        => $request->crqs_red,
    ]);

    return redirect('/master-aql')->with('success', 'Parameter customer berhasil diperbarui!');
}
public function destroy($id)
{
    $aqlStandard = MasterAqlStandard::findOrFail($id);
    $aqlStandard->delete();

    return redirect('/master-aql')->with('success', 'Data standar AQL berhasil dihapus!');
}
}