<?php

namespace App\Http\Controllers\Fat;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CoaController extends Controller
{
    public function index()
    {
        // Tarik data COA, urutkan berdasarkan kode akun
        $coas = DB::table('coas')->orderBy('kode_akun', 'asc')->get();
        return view('fat.coas.index', compact('coas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_akun'    => 'required|string|max:20|unique:coas,kode_akun',
            'nama_akun'    => 'required|string|max:100',
            'kategori'     => 'required|in:Harta,Kewajiban,Modal,Pendapatan,Beban',
            'saldo_normal' => 'required|in:Debit,Kredit',
        ]);

        DB::table('coas')->insert([
            'kode_akun'    => $request->kode_akun,
            'nama_akun'    => $request->nama_akun,
            'kategori'     => $request->kategori,
            'saldo_normal' => $request->saldo_normal,
            'created_at'   => Carbon::now(),
            'updated_at'   => Carbon::now()
        ]);

        return redirect()->route('fat.coas.index')->with('success', 'Akun COA baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_akun'    => 'required|string|max:20|unique:coas,kode_akun,'.$id,
            'nama_akun'    => 'required|string|max:100',
            'kategori'     => 'required|in:Harta,Kewajiban,Modal,Pendapatan,Beban',
            'saldo_normal' => 'required|in:Debit,Kredit',
        ]);

        DB::table('coas')->where('id', $id)->update([
            'kode_akun'    => $request->kode_akun,
            'nama_akun'    => $request->nama_akun,
            'kategori'     => $request->kategori,
            'saldo_normal' => $request->saldo_normal,
            'updated_at'   => Carbon::now()
        ]);

        return redirect()->route('fat.coas.index')->with('success', 'Data Akun COA berhasil diperbarui.');
    }

    public function destroy($id)
    {
        // FITUR KEAMANAN ERP: Cek apakah akun ini sudah pernah dipakai di Jurnal Umum
        $terpakai = DB::table('journal_details')->where('coa_id', $id)->exists();

        if ($terpakai) {
            return redirect()->route('fat.coas.index')->with('error', 'Gagal! Akun ini tidak boleh dihapus karena sudah ada transaksi Jurnal Umum yang menggunakannya.');
        }

        DB::table('coas')->where('id', $id)->delete();
        return redirect()->route('fat.coas.index')->with('success', 'Akun COA berhasil dihapus permanen.');
    }
}