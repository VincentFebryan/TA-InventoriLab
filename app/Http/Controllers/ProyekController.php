<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use App\Models\PenggunaanBarang;
use App\Models\barang;
use App\Models\BillOfMaterial;
use Illuminate\Http\Request;

class ProyekController extends Controller
{
    public function index()
    {
        $proyeks = Proyek::all();
        return view('proyek.index-proyek', compact('proyeks'));
    }

    public function create()
    {
        $bom = BillOfMaterial::all();
        $barangs = barang::all();
        return view('proyek.add-proyek', [
            'bom' => $bom,
            'barangs' => $barangs,
        ]);
    }

    public function edit($id)
    {
        $proyek = Proyek::findOrFail($id);
        $items = PenggunaanBarang::with('barang.jenisBarang')->where('proyek_id', $id)->get();

        return view('proyek.edit-proyek', compact('proyek', 'items'));
    }

    public function update(Request $request, $id) 
    {
        $request->validate([
            'status_bom' => 'required|in:Aktif,Non-Aktif',
            'items' => 'required|array',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.jumlah_digunakan' => 'required|numeric|min:0',
        ]);

        $proyek = Proyek::findOrFail($id);

        // Kembalikan stok dari data lama - berdasarkan pemakaian sebelumnya
        $itemsLama = PenggunaanBarang::where('proyek_id', $id)->get();
        foreach ($itemsLama as $itemLama) {
            $barang = Barang::find($itemLama->barang_id);
            $barang->stok += $itemLama->jumlah_digunakan;
            $barang->save();
        }

        PenggunaanBarang::where('proyek_id', $id)->delete();

        $proyek->update([
            'status_bom' => $request->status_bom,
        ]);

        // Simpan item baru dengan pengecekan stok
        foreach ($request->items as $item) {
            $barang = Barang::find($item['barang_id']);
            
            // Cek apakah stok cukup
            if ($barang->stok < $item['jumlah_digunakan']) {
                return back()->with('error', 'Stok tidak mencukupi untuk barang: ' . $barang->nama_barang);
            }

            // Kurangi stok barang
            $barang->stok -= $item['jumlah_digunakan'];
            $barang->save();

            // Simpan penggunaan barang
            PenggunaanBarang::create([
                'proyek_id' => $proyek->id,
                'barang_id' => $item['barang_id'],
                'jumlah_digunakan' => $item['jumlah_digunakan'],
            ]);
        }

        return redirect()->route('proyek.index')->with('success', 'Proyek berhasil diperbarui.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_bom' => 'required',
            'nama_bom' => 'required',
            'keterangan' => 'nullable',
            'status_bom' => 'required|in:Aktif,Non-Aktif',
            'items' => 'required|array',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.jumlah_digunakan' => 'required|numeric|min:0',
        ]);

        $proyek = Proyek::create([
            'kode_bom' => $request->kode_bom,
            'nama_bom' => $request->nama_bom,
            'keterangan' => $request->keterangan,
            'status_bom' => $request->status_bom,
        ]);

        foreach ($request->items as $item) {
            PenggunaanBarang::create([
                'proyek_id' => $proyek->id,
                'barang_id' => $item['barang_id'],
                'jumlah_digunakan' => $item['jumlah_digunakan'],
            ]);

            $barang = barang::find($item['barang_id']);
            if ($barang->stok < $item['jumlah_digunakan']) {
                return back()->with('error', 'Stok tidak mencukupi untuk barang: ' . $barang->nama_barang);
            }
            $barang->stok -= $item['jumlah_digunakan'];
            $barang->save();
        }

        return redirect()->route('proyek.index')->with('success', 'Proyek berhasil disimpan.');
    }

    public function destroy($id)
    {
        $proyek = Proyek::findOrFail($id);
        $proyek->delete();

        return redirect()->route('proyek.index')->with('success', 'Data proyek berhasil dihapus.');
    }

}
