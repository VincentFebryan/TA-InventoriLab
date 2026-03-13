<?php

namespace App\Http\Controllers;

use App\Models\barang;
use App\Models\DetailPenerimaanBarang;
use Illuminate\Support\Facades\Auth;
use App\Models\JenisPenerimaan;
use App\Models\Supplier;
use App\Models\Konsumen;
use Illuminate\Http\Request;
use App\Models\PenerimaanBarang;
use App\Models\SaldoAwal;
use App\Models\User;
use Illuminate\Support\Facades\DB; 

class PenerimaanBarangController extends Controller
{
  
    public function loadAllPenerimaanBarang(Request $request)
    {
        // Ambil jumlah item per halaman dari request, default 5
        $perPage = $request->input('perPage', 5);
        
        // Mengambil data berdasarkan pagination
        $all_detail_penerimaans = DetailPenerimaanBarang::orderBy('created_at', 'desc')
            ->paginate($perPage)  // Apply pagination based on 'perPage'
            ->appends(request()->except('page')); // Maintain other query parameters like search or filters

        // Ambil data untuk dropdown
        $all_suppliers = Supplier::select('id', 'nama')->get();
        $all_konsumens = Konsumen::select('id', 'nama')->get();
        $all_users = User::all();
        $all_jenis_penerimaans = JenisPenerimaan::all();
        $all_master_penerimaans = PenerimaanBarang::all();

        // Return view dengan data yang sudah dipaginasi
        return view('barang-masuk.index', compact(
            'all_detail_penerimaans',
            'all_suppliers',
            'all_konsumens',
            'all_users', 
            'all_jenis_penerimaans',
            'all_master_penerimaans'
        ));
    }

    public function BarangMasukSearch(Request $request)
    {
        // Get the search query from the input
        $query = $request->input('query');

        // Set the number of items per page (pagination)
        $perPage = $request->input('perPage', 5);

        // Split the query into individual keywords
        $keywords = explode(' ', $query);

        // Perform the search
        $all_detail_penerimaans = DetailPenerimaanBarang::where(function ($q) use ($keywords) {
            foreach ($keywords as $word) {
                $q->where(function ($subQuery) use ($word) {
                    $subQuery->whereHas('penerimaanBarang', function ($innerQuery) use ($word) {
                        $innerQuery->where('invoice', 'like', "%$word%")
                            ->orWhere('tanggal', 'like', "%$word%")
                            ->orWhere('keterangan', 'like', "%$word%")
                            ->orWhere('nama_pengantar', 'like', "%$word%")
                            ->orWhereHas('supplier', function ($rq) use ($word) {
                                $rq->where('nama', 'like', "%$word%");
                            })
                            ->orWhereHas('konsumen', function ($rq) use ($word) {
                                $rq->where('nama', 'like', "%$word%");
                            })
                            ->orWhereHas('user', function ($rq) use ($word) {
                                $rq->where('name', 'like', "%$word%");
                            })
                            ->orWhereHas('jenispenerimaanbarang', function ($relatedQuery) use ($word) {
                                $relatedQuery->where('jenis', 'like', "%$word%");
                            });
                    })
                    ->orWhereHas('barang', function ($innerQuery) use ($word) {
                        $innerQuery->where('nama_barang', 'like', "%$word%");
                    })
                    ->orWhere('jumlah_diterima', 'like', "%$word%")
                    ->orWhere('harga', 'like', "%$word%")
                    ->orWhere('total_harga', 'like', "%$word%");
                });
            }
        })
        ->paginate($perPage)
        ->appends(request()->except('page'));

        return view('barang-masuk.index', compact('all_detail_penerimaans'));
    }


    public function loadAddBarangMasukForm()
    {
        $all_master_penerimaans = PenerimaanBarang::all();
        $all_suppliers = Supplier::all();
        $all_konsumens = Konsumen::all();
        $all_users = User::all();
        $all_jenis_penerimaans = JenisPenerimaan::all();
        $all_barangs = Barang::all();
        $user = Auth::user();

        return view('barang-masuk.add-barang-masuk', compact(
            'all_master_penerimaans',
            'all_suppliers',
            'all_konsumens',
            'all_users',
            'all_jenis_penerimaans',
            'all_barangs',
            'user'
        ));
    }


    public function addBarangMasuk(Request $request)
    {
        // Validasi input
        $request->validate([
            'jenis_id' => 'required|exists:jenis_penerimaan_barangs,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'konsumen_id' => 'required|exists:konsumens,id',
            'user_id' => 'required|exists:users,id',
            'nama_pengantar' => 'required|string|max:255',
            'barang_id' => 'required|array',
            'barang_id.*' => 'exists:barangs,id',
            'jumlah_diterima' => ['required', 'array', function ($attribute, $value, $fail) {
                foreach ($value as $jumlah) {
                    if (!is_numeric($jumlah) || $jumlah <= 0) {
                        $fail('Setiap jumlah diterima harus berupa angka dan lebih dari 0.');
                    }
                }
            }],
            'harga' => 'required|array',
            'harga.*' => 'numeric|min:0',
            'total_harga' => 'required|array',
            'total_harga.*' => 'numeric|min:0',
            'tanggal' => 'required|date',
            'invoice' => 'required|string|max:255',
            'keterangan' => 'required|string',
            'harga_invoice' => 'required|numeric|min:0',
        ]);

        // Buat record di tabel PenerimaanBarang
        $barangMasuk = new PenerimaanBarang();
        $barangMasuk->jenis_id = $request->jenis_id;
        $barangMasuk->supplier_id = $request->supplier_id;
        $barangMasuk->konsumen_id = $request->konsumen_id;
        $barangMasuk->user_id = $request->user_id;
        $barangMasuk->nama_pengantar = $request->nama_pengantar;
        $barangMasuk->tanggal = $request->tanggal;
        $barangMasuk->invoice = $request->invoice;
        $barangMasuk->keterangan = $request->keterangan;
        $barangMasuk->harga_invoice = $request->harga_invoice;
        $barangMasuk->save();

        // Ambil bulan dan tahun dari tanggal
        $tanggal = \Carbon\Carbon::parse($request->tanggal);
        $bulan = $tanggal->month;
        $tahun = $tanggal->year;

        // Simpan detail barang dan update stok
        foreach ($request->barang_id as $key => $barangId) {
            $jumlah = $request->jumlah_diterima[$key];
            $harga = $request->harga[$key];
            $total = $request->total_harga[$key];

            // Simpan ke tabel detail
            $detail = new DetailPenerimaanBarang();
            $detail->master_penerimaan_barang_id = $barangMasuk->id;
            $detail->barang_id = $barangId;
            $detail->jumlah_diterima = $jumlah;
            $detail->harga = $harga;
            $detail->total_harga = $total;
            $detail->save();

            // Tambahkan stok barang
            $barang = Barang::findOrFail($barangId);
            $barang->stok += $jumlah;
            $barang->save();

            // Update atau insert ke saldo_awal
            $saldoAwal = SaldoAwal::firstOrNew([
                'barang_id' => $barangId,
                'bulan' => $bulan,
                'tahun' => $tahun,
            ]);

            $saldoAwal->total_terima += $jumlah;
            if (!$saldoAwal->exists) {
                $saldoAwal->saldo_awal = 0;
                $saldoAwal->total_keluar = 0;
            }
            $saldoAwal->saldo_akhir = $saldoAwal->saldo_awal + $saldoAwal->total_terima - $saldoAwal->total_keluar;
            $saldoAwal->save();
        }

        return redirect()->route('master-barang-masuk')->with('success', 'Barang Masuk berhasil ditambahkan.');
    }

    public function generateInvoicePenerimaan(Request $request)
    {
        $tanggal = $request->tanggal;

        // Validasi format tanggal
        if (!$tanggal) {
            return response()->json(['error' => 'Tanggal tidak valid'], 400);
        }

        // Ambil tanggal dalam format Y-m-d
        $date = \Carbon\Carbon::parse($tanggal);

        // Hitung jumlah penerimaan barang yang sudah ada pada tanggal tersebut
        $count = PenerimaanBarang::whereDate('tanggal', $date->toDateString())->count();

        // Nomor urut dimulai dari 1
        $noUrut = str_pad($count + 1, 2, '0', STR_PAD_LEFT); // Contoh: 01, 02, ...

        // Format invoice: YYMMDD + noUrut
        $formattedDate = $date->format('ymd'); // Format menjadi YYMMDD
        $invoiceNumber = $formattedDate . $noUrut;

        return response()->json(['invoice' => $invoiceNumber]);
    }

    public function deletePenerimaanBarang($id)
    {
        try {
            // Begin a transaction to ensure data consistency
            DB::beginTransaction();

            // Find the detail penerimaan barang record
            $detail = DetailPenerimaanBarang::findOrFail($id);

            // Find the corresponding master penerimaan barang
            $masterPenerimaan = PenerimaanBarang::findOrFail($detail->master_penerimaan_barang_id);

            // Adjust the stock of the corresponding barang
            $barang = Barang::find($detail->barang_id);
            if ($barang) {
                $barang->stok -= $detail->jumlah_diterima;  // Decrease stock by jumlah_diterima
                $barang->save();  // Save updated stock
            }

            // Delete the detail record
            $detail->delete();

            // Check if there are any remaining details for the master penerimaan
            $remainingDetails = DetailPenerimaanBarang::where('master_penerimaan_barang_id', $masterPenerimaan->id)->count();

            // If no more details exist for this master penerimaan, delete the master penerimaan
            if ($remainingDetails === 0) {
                $masterPenerimaan->delete();
            }

            // // Now, adjust the total_keluar on saldo_awals table
            // // Get the correct month and year from master penerimaan barang
            // $tanggalMaster = \Carbon\Carbon::parse($masterPenerimaan->tanggal);
            // $bulan = $tanggalMaster->month;
            // $tahun = $tanggalMaster->year;

            // // Find the saldo_awal record based on barang_id, bulan, and tahun
            // $saldoAwal = SaldoAwal::where('barang_id', $detail->barang_id)
            //     ->where('bulan', $bulan)  // Correctly use month from master penerimaan
            //     ->where('tahun', $tahun)  // Correctly use year from master penerimaan
            //     ->first();

            // if ($saldoAwal) {
            //     // Subtract jumlah_diterima from total_terima (as we're deleting the record)
            //     $saldoAwal->total_terima -= $detail->jumlah_diterima;

            //     // Recalculate saldo_akhir: total_terima - total_keluar
            //     $saldoAwal->saldo_akhir = $saldoAwal->total_terima - $saldoAwal->total_keluar;

            //     // Save the updated saldo_awal record
            //     $saldoAwal->save();
            // }

            // Commit the transaction
            DB::commit();

            return redirect('/master-barang-masuk')->with('success', 'Detail barang berhasil dihapus.');
        } catch (\Exception $e) {
            // Rollback the transaction on failure
            DB::rollBack();
            return redirect('/master-barang-masuk')->with('fail', 'Gagal menghapus detail barang: ' . $e->getMessage());
        }
    }


    public function detailMasterBarang($id) 
    {
        // Ambil data master penerimaan beserta relasi terkait
        $master_penerimaan = PenerimaanBarang::with(['supplier', 'konsumen', 'user', 'jenisPenerimaan'])->findOrFail($id);

        // Ambil detail penerimaan untuk master ini
        $detail_penerimaan = DetailPenerimaanBarang::with('barang')
                                ->where('master_penerimaan_barang_id', $id)
                                ->get();

        return view('barang-masuk.detail-barang-masuk', compact(
            'master_penerimaan', 'detail_penerimaan'
        ));
    }

    
    public function loadAllDetailPenerimaanBarang()
    {
        $all_detail_penerimaans = DetailPenerimaanBarang::with([
            'barang',
            'masterPenerimaan.supplier',
            'masterPenerimaan.konsumen',
            'masterPenerimaan.user',
            'masterPenerimaan.jenisPenerimaan'
        ])->get();

        return view('barang-masuk.index-detail', compact('all_detail_penerimaans'));
    }
  
    public function DetailBarangMasukSearch(Request $request)
    {
        $query = $request->input('query');

        $all_detail_penerimaans = DetailPenerimaanBarang::whereHas('PenerimaanBarang', function ($q) use ($query) {
                $q->where('id', 'like', "%$query%");
            })
            ->orWhereHas('barang', function ($q) use ($query) {
                $q->where('nama', 'like', "%$query%");
            })
            ->orWhere('jumlah_diterima', 'like', "%$query%")
            ->orWhere('harga', 'like', "%$query%")
            ->orWhere('total_harga', 'like', "%$query%")
            ->get();

        return view('barang-masuk.index-detail', compact('all_detail_penerimaans'));
    }

    public function loadAllJenisPenerimaanBarang(){
        $all_jenis_penerimaans = JenisPenerimaan::all();

        // Get IDs of jenis_penerimaan that are used in master_penerimaan_barangs
        $used_jenis_ids = DB::table('master_penerimaan_barangs')->pluck('jenis_id')->toArray();

        return view('barang-masuk.jenis-barang-masuk', compact('all_jenis_penerimaans', 'used_jenis_ids'));
    }

    public function loadAddJenisBarangMasukForm()
    {
        return view('barang-masuk.add-jenis-barang-masuk');
    }

    public function AddJenisBarangMasuk(Request $request){
        $request->validate([
            'jenis' => 'required|string',
        ]);

        try {
            $new_jenisBarangMasuk = new JenisPenerimaan();
            $new_jenisBarangMasuk->jenis = $request->jenis;
            $new_jenisBarangMasuk->save();

            return redirect('/jenis-barang-masuk/')->with('success', 'Data Added Successfully');
        } catch (\Exception $e) {
            return redirect('/jenis-barang-masuk')->with('fail', $e->getMessage());
        }
    }

    public function deleteJenisBarangMasuk($id){
        try {
            JenisPenerimaan::where('id',$id)->delete();
            return redirect('/jenis-barang-masuk')->with('success','Deleted successfully!');
        } catch (\Exception $e) {
            return redirect('/jenis-barang-masuk')->with('fail',$e->getMessage());
            
        }
    }

    public function loadEditJenisBarangMasukForm($id)
    {
        $JenisPenerimaan = JenisPenerimaan::findOrFail($id);
        return view('barang-masuk.edit-jenis-barang-masuk', compact('JenisPenerimaan'));
    }

    public function EditJenisBarangMasuk(Request $request)
    {
        // Validasi input dari pengguna
        $request->validate([
            'jenis' => 'required|string',
            'jenis_id' => 'required|integer' // Pastikan jenis_id diterima
        ]);

        try {
            // Lakukan update berdasarkan jenis_id
            $update_jenisBarangMasuk = JenisPenerimaan::where('id', $request->jenis_id)->update([
                'jenis' => $request->jenis,
            ]);

            return redirect('/jenis-barang-masuk')->with('success', 'Edit Successfully');
        } catch (\Exception $e) {
            return redirect('/jenis-barang-masuk')->with('fail', $e->getMessage());
        }
    }

    public function loadEditBarangMasukForm($id)
    {
        $detail_penerimaan = DetailPenerimaanBarang::findOrFail($id);

        $masterPenerimaan = $detail_penerimaan->penerimaanBarang;
        $all_suppliers = Supplier::all();
        $all_konsumens = Konsumen::all();
        $all_users = User::all();
        $all_jenis_penerimaans = JenisPenerimaan::all();
        $user = Auth::user(); 
        $all_barangs = barang::all();
        // $detail_penerimaan = DetailPenerimaanBarang::all();
        // dd($masterPenerimaan->detail_penerimaan, $detail_penerimaan);
        return view('barang-masuk.edit-barang-masuk', compact(
            'masterPenerimaan', 'all_suppliers','all_konsumens', 'all_users', 'all_jenis_penerimaans', 'user',
            'all_barangs', 'detail_penerimaan'
        ));
    }
    
    public function EditPenerimaanBarang(Request $request)
        {
            $request->validate([
                'masterPenerimaan_id' => 'required|exists:master_penerimaan_barangs,id',
                'detail_penerimaan_id' => 'required|exists:detail_penerimaan_barangs,id',
                'barang_id' => 'required|exists:barangs,id',
                'jumlah_diterima' => 'required',
                'harga' => 'required',
                'total_harga' => 'required',
            ]);
        
            // Menghapus format angka (titik) untuk harga dan total_harga
            $harga = str_replace('.', '', $request->input('harga'));
            $total_harga = str_replace('.', '', $request->input('total_harga'));
        
            try {
                // Ambil detail penerimaan yang akan diedit
                $detailPenerimaanBarang = DetailPenerimaanBarang::findOrFail($request->detail_penerimaan_id);
                $masterPenerimaan = PenerimaanBarang::findOrFail($request->masterPenerimaan_id);
        
                // Hitung selisih total harga
                $selisihTotalHarga = $total_harga - $detailPenerimaanBarang->total_harga;
        
                // Update detail penerimaan barang
                $detailPenerimaanBarang->update([
                    'jumlah_diterima' => $request->jumlah_diterima,
                    'harga' => $harga,
                    'total_harga' => $total_harga,
                ]);
        
                // Perbarui harga_invoice pada master penerimaan
                $masterPenerimaan->harga_invoice += $selisihTotalHarga;
                $masterPenerimaan->save();
        
                // Update stok barang
                $barang = Barang::findOrFail($request->barang_id);
                $selisihJumlah = $request->jumlah_diterima - $detailPenerimaanBarang->jumlah_diterima;
                $barang->stok += $selisihJumlah;
                $barang->save();
        
                return redirect('/master-barang-masuk/')->with('success', 'Edit Successfully');
            } catch (\Exception $e) {
                return redirect('/edit-penerimaan-barang/' . $request->detail_penerimaan_id)->with('fail', $e->getMessage());
            }
            // // Update saldo_awal pada total_keluar
            // $tanggal = \Carbon\Carbon::parse($request->tanggal);
            // $bulan = $tanggal->month;
            // $tahun = $tanggal->year;

            // // Cek apakah saldo_awals sudah ada untuk bulan dan tahun tersebut dan barang_id yang sama
            // $saldoAwal = SaldoAwal::where('barang_id', $request->barang_id)
            //                     ->where('bulan', $bulan)
            //                     ->where('tahun', $tahun)  // Pastikan juga sesuai dengan tahun
            //                     ->first();

            // if ($saldoAwal) {
            //     // Jika ada saldo awal, perbarui total_terima dan saldo_akhir berdasarkan perubahan jumlah
            //     $saldoAwal->total_terima += $selisihJumlah; // Tambahkan atau kurangi sesuai dengan selisih jumlah diterima
            //     $saldoAwal->saldo_akhir = $saldoAwal->saldo_awal + $saldoAwal->total_terima - $saldoAwal->total_keluar;

            //     // Simpan perubahan
            //     $saldoAwal->save();
            // }
        }

        public function create()
        {
            $suppliers = Supplier::all();
            $konsumens = Konsumen::all();
            $jenis_penerimaans = JenisPenerimaan::all();

            return view('barang-masuk.create', compact('suppliers', 'konsumens', 'jenis_penerimaans'));
        }

        public function store(Request $request) 
        {
            $request->validate([
                'barang_id' => 'required|exists:barangs,id',
                'jumlah_masuk' => 'required|numeric|min:1',
                'harga' => 'required|numeric|min:0',
                'total_harga' => 'required|numeric|min:0',
                'supplier_id' => 'required|exists:suppliers,id',
                'konsumen_id' => 'required|exists:konsumens,id',
                'jenis_id' => 'required|exists:jenis_penerimaan_barangs,id',
            ]);

            $penerimaan = PenerimaanBarang::create([
                'user_id' => Auth::id(),
                'suppliers_id' => $request->supplier_id,
                'konsumens_id' => $request->konsumen_id,
                'jenis_penerimaan_id' => $request->jenis_penerimaan_id,
                'tanggal_penerimaan' => now(),
            ]);

            DetailPenerimaanBarang::create([
                'master_penerimaan_barang_id' => $penerimaan->id,
                'barang_id' => $request->barang_id,
                'jumlah_masuk' => $request->jumlah_masuk,
                'harga' => $request->harga,
                'total_harga' => $request->total_harga,
            ]);

            // Update stok barang
            $barang = Barang::findOrFail($request->barang_id);
            $barang->stok += $request->jumlah_masuk;
            $barang->save();

            return redirect()->route('barangmasuk.index')->with('success', 'Barang berhasil diterima');
        }

}
