<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\Models\MasterCustomer;

class MasterCustomerController extends Controller
{
    // Menampilkan daftar Customer
    public function index()
    {
        $customers = MasterCustomer::orderBy('nama_customer', 'asc')->get();
        return view('sales.customer.index', compact('customers')); 
    }

    public function create()
    {
        return view('sales.customer.create');
    }

    // Menyimpan data ke database
    public function store(Request $request)
    {
        $request->validate([
            'nama_customer' => 'required',
            'tipe_kalkulasi_mrp' => 'required'
        ]);

        MasterCustomer::create([
            'kode_customer' => $request->kode_customer,
            'nama_customer' => $request->nama_customer,
            'tipe_kalkulasi_mrp' => $request->tipe_kalkulasi_mrp,
            'batas_coverage_bulan' => $request->tipe_kalkulasi_mrp == 'Coverage' ? $request->batas_coverage_bulan : null,
            'yield_pweb' => $request->tipe_kalkulasi_mrp == 'Coverage' ? ($request->yield_pweb ?? 100) : 100,
        ]);

        return redirect()->route('sales.master-customer.index')->with('success', 'Data Customer berhasil disimpan!');
    }

    public function edit($id)
    {
        $customer = MasterCustomer::findOrFail($id);
        return view('sales.customer.edit', compact('customer'));
    }

    public function destroy($id)
    {
        $customer = MasterCustomer::findOrFail($id);
        $customer->delete();

        // DIPERBAIKI: Menambahkan prefix 'sales.' agar sesuai dengan routes/web.php
        return redirect()->route('sales.master-customer.index')->with('success', 'Customer berhasil dihapus.');
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'nama_customer' => 'required|string|max:255',
            'tipe_kalkulasi_mrp' => 'required|string',
        ]);

        // Cari data customer berdasarkan ID
        $customer = MasterCustomer::findOrFail($id);

        // Update data ke database
        $customer->update([
            'kode_customer' => $request->kode_customer,
            'nama_customer' => $request->nama_customer,
            'tipe_kalkulasi_mrp' => $request->tipe_kalkulasi_mrp,
            'batas_coverage_bulan' => $request->batas_coverage_bulan,
            'yield_pweb' => $request->yield_pweb,
        ]);

        return redirect()->route('sales.master-customer.index')->with('success', 'Data customer berhasil diperbarui.');
    }
}