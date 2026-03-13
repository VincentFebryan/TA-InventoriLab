<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all(); 
        return view('supplier.index-supplier', compact('suppliers'));
    }

    public function create()
    {
        return view('supplier.add-supplier');
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('supplier.edit-supplier', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:100',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $supplier = Supplier::findOrFail($id);
        $supplier->update($request->only(['nama', 'alamat', 'kota', 'telepon', 'email']));

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil diperbarui.');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:100',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        Supplier::create($request->only(['nama', 'alamat', 'kota', 'telepon', 'email']));

        return redirect()->back()->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil dihapus.');
    }
}
