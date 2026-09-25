<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CapaSupplier;

class CapaSupplierController extends Controller
{
    public function index()
    {
        $capas = CapaSupplier::latest()->get();
        return view('quality.capa.supplier.index', compact('capas'));
    }

    public function create()
    {
        return view('quality.capa.supplier.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tema_masalah' => 'required',
            'nama_supplier' => 'required',
            'tanggal_temuan' => 'required|date',
            'deskripsi_masalah' => 'required',
            'foto_masalah' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $namaFoto = null;
        if ($request->hasFile('foto_masalah')) {
            $file = $request->file('foto_masalah');
            $namaFoto = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $namaFoto);
        }

        $count = CapaSupplier::count() + 1;
        $noCapa = str_pad($count, 2, '0', STR_PAD_LEFT) . '/QA/SUPPLIER/' . date('m/Y');

        CapaSupplier::create([
            'no_capa' => $noCapa,
            'tema_masalah' => $request->tema_masalah,
            'nama_supplier' => $request->nama_supplier,
            'tanggal_temuan' => $request->tanggal_temuan,
            'deskripsi_masalah' => $request->deskripsi_masalah,
            'foto_masalah' => $namaFoto,
            'status' => 'Open',
        ]);

        return redirect('/capa-8d/supplier')->with('success', 'Data CAPA Supplier & Foto berhasil disimpan!');
    }

    public function print($id)
    {
        $capa = CapaSupplier::findOrFail($id);
        return view('quality.capa.supplier.print', compact('capa'));
    }

    public function supplierForm($id)
    {
        $capa = CapaSupplier::findOrFail($id);
        return view('quality.capa.supplier.form_step', compact('capa'));
    }

    public function supplierSubmit(Request $request, $id)
    {
        $capa = CapaSupplier::findOrFail($id);

        $kategoriList   = [];
        $why1List       = [];
        $why2List       = [];
        $why3List       = [];
        $why4List       = [];
        $why5List       = [];
        $kondisiStdList = [];
        $kondisiActList = [];
        $verifikasiList = [];
        $kesimpulanList = [];

        if ($request->has('why_analysis')) {
            foreach ($request->why_analysis as $item) {
                if (!empty($item['kategori'])) {
                    $kategoriList[]   = $item['kategori'];
                    $why1List[]       = '[' . $item['kategori'] . '] ' . ($item['w1'] ?? '');
                    $why2List[]       = '[' . $item['kategori'] . '] ' . ($item['w2'] ?? '');
                    $why3List[]       = '[' . $item['kategori'] . '] ' . ($item['w3'] ?? '');
                    $why4List[]       = '[' . $item['kategori'] . '] ' . ($item['w4'] ?? '');
                    $why5List[]       = '[' . $item['kategori'] . '] ' . ($item['w5'] ?? '');
                    
                    // Dikosongkan tanpa nilai default
                    $kondisiStdList[] = $item['kondisi_std'] ?? '';
                    $kondisiActList[] = $item['kondisi_act'] ?? '';
                    $verifikasiList[] = $item['verifikasi'] ?? '';
                    $kesimpulanList[] = $item['kesimpulan'] ?? '';
                }
            }
        }

        $caFormatted = [];
        if ($request->has('corrective_options')) {
            foreach ($request->corrective_options as $opt) {
                $detail = $request->corrective_details[$opt] ?? '';
                $caFormatted[] = "☑ " . $opt . ($detail ? " : " . $detail : "");
            }
        }

        $paFormatted = [];
        if ($request->has('preventive_options')) {
            foreach ($request->preventive_options as $opt) {
                $detail = $request->preventive_details[$opt] ?? '';
                $paFormatted[] = "☑ " . $opt . ($detail ? " : " . $detail : "");
            }
        }

        $docTypes = $request->has('doc_types') ? implode(', ', $request->doc_types) : '';

        // Proses Upload File Lampiran dari Supplier jika ada
        $namaFileSupplier = $capa->file_supplier; 
        if ($request->hasFile('file_supplier')) {
            $file = $request->file('file_supplier');
            $namaFileSupplier = time() . '_supplier_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/capa_supplier'), $namaFileSupplier);
        }

        $capa->update([
            'nama_produk'            => $request->nama_produk,
            'no_lot'                 => $request->no_lot,
            'tanggal_produksi_shift' => $request->tanggal_produksi_shift,
            'no_mesin_line'          => $request->no_mesin_line,
            'history_man'            => $request->history_man,
            'history_machine'        => $request->history_machine,
            'history_method'         => $request->history_method,
            'history_mold'           => $request->history_mold,
            'history_material'       => $request->history_material,
            'history_env'            => $request->history_env,
            'containment_action'     => $request->containment_action,
            'root_cause_category'    => implode(', ', $kategoriList),
            'why_1'                  => implode("\n", $why1List),
            'why_2'                  => implode("\n", $why2List),
            'why_3'                  => implode("\n", $why3List),
            'why_4'                  => implode("\n", $why4List),
            'why_5'                  => implode("\n", $why5List),
            'kondisi_std'            => implode("\n", $kondisiStdList),
            'kondisi_act'            => implode("\n", $kondisiActList),
            'verifikasi'             => implode("\n", $verifikasiList),
            'kesimpulan_verifikasi'  => implode("\n", $kesimpulanList),
            'root_cause'             => $request->root_cause,
            'corrective_action'      => implode("\n", $caFormatted),
            'preventive_action'      => implode("\n", $paFormatted),
            'val_hasil_ca'           => $request->val_hasil_ca,
            'val_status_ca'          => $request->val_status_ca,
            'val_pic_ca'             => $request->val_pic_ca,
            'val_hasil_pa'           => $request->val_hasil_pa,
            'val_status_pa'          => $request->val_status_pa,
            'val_pic_pa'             => $request->val_pic_pa,
            'doc_types'              => $docTypes,
            'deskripsi_dokumen'      => $request->deskripsi_dokumen,
            'doc_plan_date'          => $request->doc_plan_date,
            'doc_act_date'           => $request->doc_act_date,
            'control_chart'          => $request->control_chart,
            'team_celebration'       => $request->team_celebration,
            'pic_supplier'           => $request->pic_supplier,
            'tanggal_implementasi'   => $request->tanggal_implementasi,
            'tanggal_submit_supplier'=> date('Y-m-d'),
            'file_supplier'          => $namaFileSupplier,
            'status'                 => 'Review', 
        ]);

        return redirect()->back()->with('success', 'Terima kasih, balasan CAPA 8D berhasil dikirim ke QC!');
    }

    public function toggleClose($id)
    {
        $capa = CapaSupplier::findOrFail($id);
        $capa->status = ($capa->status == 'Closed') ? 'Review' : 'Closed';
        $capa->save();

        return redirect()->back()->with('success', 'Status CAPA berhasil diperbarui!');
    }

    public function edit($id)
    {
        $capa = CapaSupplier::findOrFail($id);
        return view('quality.capa.supplier.edit', compact('capa'));
    }

    public function update(Request $request, $id)
    {
        $capa = CapaSupplier::findOrFail($id);

        $updateData = [
            'nama_supplier'    => $request->nama_supplier,
            'tema_masalah'     => $request->tema_masalah,
            'deskripsi_masalah'=> $request->deskripsi_masalah,
            'tanggal_temuan'   => $request->tanggal_temuan,
        ];

        if ($request->hasFile('foto_masalah')) {
            $file = $request->file('foto_masalah');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $filename);
            $updateData['foto_masalah'] = $filename;
        }

        $capa->update($updateData);

        return redirect('/capa-8d/supplier')->with('success', 'Data CAPA Supplier berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $capa = CapaSupplier::findOrFail($id);
        $capa->delete();

        return redirect('/capa-8d/supplier')->with('success', 'Data CAPA Supplier berhasil dihapus!');
    }
}