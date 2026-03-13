<?php

namespace App\Http\Controllers;

use App\Models\Konsumen;
use Illuminate\Http\Request;

class KonsumenController extends Controller
{
    public function index()
    {
        $konsumens = Konsumen::all(); 
        return view('konsumen.index-konsumen', compact('konsumens'));
    }

    public function create()
    {
        return view('konsumen.add-konsumen');
    }

    public function edit($id)
    {
        $konsumen = Konsumen::findOrFail($id);
        return view('konsumen.edit-konsumen', compact('konsumen'));
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

        $konsumen = Konsumen::findOrFail($id);
        $konsumen->update($request->only(['nama', 'alamat', 'kota', 'telepon', 'email']));

        return redirect()->route('konsumen.index')->with('success', 'Konsumen berhasil diperbarui.');
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

        Konsumen::create($request->only(['nama', 'alamat', 'kota', 'telepon', 'email']));

        return redirect()->back()->with('success', 'Konsumen berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $konsumen = Konsumen::findOrFail($id);
        $konsumen->delete();

        return redirect()->route('konsumen.index')->with('success', 'Kosumen berhasil dihapus.');
    }
}
