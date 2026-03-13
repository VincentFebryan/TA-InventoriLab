<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use Illuminate\Http\Request;

class GudangController extends Controller
{
    public function index()
    {
        $gudangs = Gudang::all();
        return view('gudang.index-gudang', compact('gudangs'));
    }

    public function create()
    {
        return view('gudang.add-gudang');
    }

    public function edit($id)
    {
        $gudang = Gudang::findOrFail($id);
        return view('gudang.edit-gudang', compact('gudang'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_gudang'=> 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'alamat_lengkap' => 'required|string|max:255',
            'jenis_gudang' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $gudang = Gudang::findOrFail($id);
        $gudang->update($request->only(['kode_gudang', 'nama', 'alamat_lengkap', 'jenis_gudang','keterangan']));

        return redirect()->route('gudang.index')->with('success', 'Proyek berhasil diperbarui.');
    }

    public function store(Request $request)
    {
        // Validasi data form 
        $request->validate([
            'kode_gudang' => 'required|unique:gudangs,kode_gudang',
            'nama' => 'required',
            'alamat_lengkap' => 'required',
            'jenis_gudang' => 'required',
            'keterangan' => 'nullable',
        ]);

        // Simpan proyek
        $gudangs = Gudang::create([
            'kode_gudang' => $request->kode_gudang,
            'nama' => $request->nama,
            'alamat_lengkap' => $request->alamat_lengkap,
            'jenis_gudang' => $request->jenis_gudang,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->back()->with('success', 'Gudang berhasil disimpan.');
    }

    public function destroy($id)
    {
        $gudang = Gudang::findOrFail($id);
        $gudang->delete();

        return redirect()->route('gudang.index')->with('success', 'Gudang berhasil dihapus.');
    }

}