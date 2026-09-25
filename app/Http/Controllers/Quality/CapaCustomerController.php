<?php

namespace App\Http\Controllers\Quality;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CapaCustomer;

class CapaCustomerController extends Controller
{
    public function index()
    {
        $capaCustomers = CapaCustomer::latest()->get();
        return view('quality.capa.customer.index', compact('capaCustomers'));
    }

    public function create()
    {
        return view('quality.capa.customer.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer' => 'required',
            'no_capa_customer' => 'required',
            'item' => 'required',
        ]);

        CapaCustomer::create([
            'complaint_month' => $request->complaint_month,
            'customer' => $request->customer,
            'no_capa_customer' => $request->no_capa_customer,
            'mm' => $request->mm,
            'item' => $request->item,
            'defect' => $request->defect,
            'source' => $request->source,
            'category_defect' => $request->category_defect,
            'sncr' => $request->sncr,
            'status' => 'Open',
        ]);

        return redirect('/capa-8d/customer')->with('success', 'Data CAPA Customer berhasil ditambahkan!');
    }
}