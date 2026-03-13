<?php

namespace App\Http\Controllers;

use App\Models\BillOfMaterial;
use Illuminate\Http\Request;

class BillOfMaterialController extends Controller
{
    public function index()
    {
        $bill_of_materials = BillOfMaterial::all();
        return view('bill_of_materials.index', compact('bill_of_materials'));
    }

    public function create()
    {
        return view('bill_of_materials.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_bom' => 'required|unique:bill_of_materials',
            'nama_bom' => 'required',
            'keterangan' => 'required',
        ]);

        BillOfMaterial::create([
            'kode_bom' => $request->kode_bom,
            'nama_bom' => $request->nama_bom,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('bill_of_materials.index')->with('success', 'Data BOM berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $bom = BillOfMaterial::findOrFail($id);
        return view('bill_of_materials.edit', compact('bom'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_bom' => 'required|unique:bill_of_materials,kode_bom,'.$id,
            'nama_bom' => 'required',
            'keterangan' => 'required',
        ]);

        $bom = BillOfMaterial::findOrFail($id);
        $bom->update([
            'kode_bom' => $request->kode_bom,
            'nama_bom' => $request->nama_bom,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('bill_of_materials.index')->with('success', 'Data BOM berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $bom = BillOfMaterial::findOrFail($id);
        $bom->delete();

        return redirect()->route('bill_of_materials.index')->with('success', 'Data BOM berhasil dihapus!');
    }


}
