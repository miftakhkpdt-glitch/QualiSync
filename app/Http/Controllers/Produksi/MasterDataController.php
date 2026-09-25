<?php

namespace App\Http\Controllers\Produksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterReject;
use App\Models\MasterDowntime;

class MasterDataController extends Controller
{
    // ==========================================
    // 1. MASTER REJECT
    // ==========================================
    public function rejectIndex()
    {
        $rejects = MasterReject::orderBy('mesin')->orderBy('kategori')->get();
        return view('produksi.master.reject', compact('rejects'));
    }

    public function rejectStore(Request $request)
    {
        MasterReject::create($request->all());
        return redirect()->back()->with('success', 'Data Master Reject berhasil ditambahkan!');
    }

    public function rejectDestroy($id)
    {
        MasterReject::destroy($id);
        return redirect()->back()->with('success', 'Data Master Reject berhasil dihapus!');
    }

    // ==========================================
    // 2. MASTER DOWNTIME
    // ==========================================
    public function downtimeIndex()
    {
        $downtimes = MasterDowntime::orderBy('mesin')->orderBy('kategori')->get();
        return view('produksi.master.downtime', compact('downtimes'));
    }

    public function downtimeStore(Request $request)
    {
        MasterDowntime::create($request->all());
        return redirect()->back()->with('success', 'Data Master Downtime berhasil ditambahkan!');
    }

    public function downtimeDestroy($id)
    {
        MasterDowntime::destroy($id);
        return redirect()->back()->with('success', 'Data Master Downtime berhasil dihapus!');
    }
}