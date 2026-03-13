<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\barang;
use App\Models\DetailPengeluaranBarang;
use App\Models\Supplier;
use App\Models\Konsumen;
use Illuminate\Http\Request;
use App\Models\JenisPengeluaran;
use App\Models\PengeluaranBarang;
use App\Models\pengeluaran_barang;
use App\Models\SaldoAwal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Testing\Fakes\PendingMailFake;
use Illuminate\Support\Facades\DB; 


class PengeluaranBarangController extends Controller
{
 
    public function loadAllPengeluaranBarang(Request $request)
    {
        // Ambil jumlah item per halaman dari request, default 5
        $perPage = $request->input('perPage', 5);

        // Ambil data pengeluaran barang detail beserta relasinya dengan pagination
        $all_detail_pengeluarans = DetailPengeluaranBarang::with([
                'barang',
                'masterPengeluaran.user',
                'masterPengeluaran.supplier',
                'masterPengeluaran.konsumen',
                'masterPengeluaran.jenisPengeluaran'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends(request()->except('page'));

        // Ambil data untuk dropdown atau filter jika diperlukan di view
        $all_suppliers = Supplier::all();
        $all_konsumens = Konsumen::all();
        $all_users = User::all();
        $all_jenis_pengeluarans = JenisPengeluaran::all();

        return view('barang-keluar.index', compact(
            'all_detail_pengeluarans',
            'all_suppliers',
            'all_konsumens',
            'all_users',
            'all_jenis_pengeluarans'
        ));
    }

    // ubah
    public function BarangKeluarSearch(Request $request)
    {
        // Ambil input pencarian dan jumlah item per halaman
        $query = $request->input('query');
        $perPage = $request->input('perPage', 5);

        // Pisahkan query menjadi kata kunci individual
        $keywords = explode(' ', $query);

        // Lakukan pencarian pada detail pengeluaran barang dan relasinya
        $all_detail_pengeluarans = DetailPengeluaranBarang::with([
                'barang',
                'masterPengeluaran.user',
                'masterPengeluaran.supplier',
                'masterPengeluaran.konsumen',
                'masterPengeluaran.jenisPengeluaran'
            ])
            ->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->where(function ($subQuery) use ($word) {
                        $subQuery->whereHas('masterPengeluaran', function ($mq) use ($word) {
                                $mq->where('invoice', 'like', "%$word%")
                                ->orWhere('tanggal', 'like', "%$word%")
                                ->orWhere('keterangan', 'like', "%$word%")
                                ->orWhere('nama_pengambil', 'like', "%$word%")
                                ->orWhereHas('supplier', function ($rq) use ($word) {
                                    $rq->where('nama', 'like', "%$word%");
                                })
                                ->orWhereHas('konsumen', function ($rq) use ($word) {
                                    $rq->where('nama', 'like', "%$word%");
                                })
                                ->orWhereHas('user', function ($rq) use ($word) {
                                    $rq->where('name', 'like', "%$word%");
                                })
                                ->orWhereHas('jenisPengeluaran', function ($rq) use ($word) {
                                    $rq->where('jenis', 'like', "%$word%");
                                });
                            })
                            ->orWhereHas('barang', function ($bq) use ($word) {
                                $bq->where('nama_barang', 'like', "%$word%");
                            })
                            ->orWhere('jumlah_keluar', 'like', "%$word%")
                            ->orWhere('harga', 'like', "%$word%")
                            ->orWhere('total_harga', 'like', "%$word%");
                    });
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends(request()->except('page'));

        return view('barang-keluar.index', compact('all_detail_pengeluarans'));
    }


    public function loadAddBarangKeluarForm()
    {
        $all_master_pengeluarans = PengeluaranBarang::all();
        $all_suppliers = Supplier::all();  // Fetch all suppliers
        $all_konsumens = Konsumen::all();  // Fetch all konsumens
        $all_users = User::all();
        $all_jenis_pengeluarans = JenisPengeluaran::all();
        $user = Auth::user();
        $all_barangs = Barang::all()->groupBy('nama_barang')->map(function ($barangGroup) {
            return $barangGroup->sortBy('kadaluarsa')->first();  // Get the item with the closest expiration
        });

        // Pass all the data to the view
        return view('barang-keluar.add-barang-keluar', compact(
            'all_master_pengeluarans',
            'all_users',
            'all_jenis_pengeluarans',
            'all_suppliers',
            'all_konsumens',
            'user',
            'all_barangs'
        ));
    }

    public function addBarangKeluar(Request $request)
    {
        $request->validate([
            'jenis_id' => 'required',
            'user_id' => 'required|exists:users,id',
            'nama_pengambil' => 'required|string',
            'supplier_id' => 'required|exists:suppliers,id',   // validasi supplier
            'konsumen_id' => 'required|exists:konsumens,id',   // validasi konsumen
            'barang_id' => 'required|array',
            'jumlah_keluar' => ['required', 'array', function ($attribute, $value, $fail) {
                foreach ($value as $jumlah_keluar) {
                    if ($jumlah_keluar <= 0) {
                        $fail('Jumlah keluar harus lebih dari 0.');
                    }
                }
            }],
            'harga' => 'required|array',
            'total_harga' => 'required|array',
            'tanggal' => 'required|date',
            'invoice' => 'required|string',
            'keterangan' => 'required|string',
            'harga_invoice' => 'required',
        ]);

        $jenisBarang = JenisPengeluaran::find($request->jenis_id);
        if ($jenisBarang && $jenisBarang->id === 0) {

        }

        // Menyimpan data pengeluaran barang
        $barangKeluar = new PengeluaranBarang();
        $barangKeluar->jenis_id = $request->jenis_id;
        $barangKeluar->nama_pengambil = $request->nama_pengambil;
        $barangKeluar->tanggal = $request->tanggal;
        $barangKeluar->invoice = $request->invoice;
        $barangKeluar->keterangan = $request->keterangan;
        $barangKeluar->user_id = $request->user_id;
        $barangKeluar->supplier_id = $request->supplier_id;  // simpan supplier_id
        $barangKeluar->konsumen_id = $request->konsumen_id;  // simpan konsumen_id
        $barangKeluar->harga_invoice = str_replace(',', '', $request->harga_invoice);
        $barangKeluar->save();

        // Simpan detail pengeluaran dan update stok (tidak berubah)
        foreach ($request->barang_id as $key => $barangId) {
            $detail = new DetailPengeluaranBarang();
            $detail->master_pengeluaran_barang_id = $barangKeluar->id;
            $detail->barang_id = $barangId;
            $detail->jumlah_keluar = $request->jumlah_keluar[$key];
            $detail->harga = str_replace(',', '', $request->harga[$key]);
            $detail->total_harga = str_replace(',', '', $request->total_harga[$key]);
            $detail->save();

            // Update stok barang
            $barang = Barang::findOrFail($barangId);
            $barang->stok -= $request->jumlah_keluar[$key];
            $barang->save();
        }

        return redirect()->route('master-barang-keluar')->with('success', 'Barang Keluar berhasil ditambahkan.');
    }


    public function generateInvoicePengeluaran(Request $request)
    {
        $tanggal = $request->tanggal;

        // Validasi format tanggal
        if (!$tanggal) {
            return response()->json(['error' => 'Tanggal tidak valid'], 400);
        }

        // Ambil tanggal dalam format Y-m-d
        $date = \Carbon\Carbon::parse($tanggal);

        // Hitung jumlah penerimaan barang yang sudah ada pada tanggal tersebut
        $count = PengeluaranBarang::whereDate('tanggal', $date->toDateString())->count();

        // Nomor urut dimulai dari 1
        $noUrut = str_pad($count + 1, 2, '0', STR_PAD_LEFT); // Contoh: 01, 02, ...

        // Format invoice: YYMMDD + noUrut
        $formattedDate = $date->format('ymd'); // Format menjadi YYMMDD
        $invoiceNumber = $formattedDate . $noUrut;

        return response()->json(['invoice' => $invoiceNumber]);
    }

    public function deletePengeluaranBarang($id)
    {
         try {
                // Begin a transaction to ensure data consistency
                DB::beginTransaction();
        
                // Find the detail pengeluaran barang record
                $detail = DetailPengeluaranBarang::findOrFail($id);
        
                // Find the corresponding master pengeluaran barang
                $masterPengeluaran = PengeluaranBarang::findOrFail($detail->master_pengeluaran_barang_id);
        
                // Adjust the stock of the corresponding barang
                $barang = Barang::find($detail->barang_id);
                if ($barang) {
                    $barang->stok += $detail->jumlah_keluar;  // Increase stock by jumlah_keluar (because we're deleting)
                    $barang->save();  // Save updated stock
                }
        
                // Delete the detail record
                $detail->delete();

                $remainingDetails = DetailPengeluaranBarang::where('master_pengeluaran_barang_id', $masterPengeluaran->id)->count();

                if ($remainingDetails === 0) {
                    $masterPengeluaran->delete();
                }

                // // Now, adjust the total_terima and total_keluar on saldo_awals table
                // // Get the correct month and year from master pengeluaran barang
                // $tanggalMaster = \Carbon\Carbon::parse($masterPengeluaran->tanggal);
                // $bulan = $tanggalMaster->month;
                // $tahun = $tanggalMaster->year;
        
                // // Find the saldo_awal record based on barang_id, bulan, and tahun
                // $saldoAwal = SaldoAwal::where('barang_id', $detail->barang_id)
                //     ->where('bulan', $bulan)  // Correctly use month from master pengeluaran
                //     ->where('tahun', $tahun)  // Correctly use year from master pengeluaran
                //     ->first();
        
                // if ($saldoAwal) {
                //     // Subtract jumlah_keluar from total_keluar (as we're deleting the record)
                //     $saldoAwal->total_keluar -= $detail->jumlah_keluar;
        
                //     // Recalculate saldo_akhir: total_terima - total_keluar
                //     $saldoAwal->saldo_akhir = $saldoAwal->total_terima - $saldoAwal->total_keluar;
        
                //     // Save the updated saldo_awal record
                //     $saldoAwal->save();
                // }
        
                // Commit the transaction
                DB::commit();
        
                return redirect('/master-barang-keluar')->with('success', 'Detail pengeluaran barang berhasil dihapus.');
            } catch (\Exception $e) {
                // Rollback the transaction on failure
                DB::rollBack();
                return redirect('/master-barang-keluar')->with('fail', 'Gagal menghapus detail pengeluaran barang: ' . $e->getMessage());
            }   
    }    
        
        
    public function detailPengeluaranBarang($id)
    {
        $master_pengeluaran = PengeluaranBarang::findOrFail($id);
        $user = User::findOrFail($master_pengeluaran->user_id);
        $jenis_pengeluaran = JenisPengeluaran::findOrFail($master_pengeluaran->jenis_id);
        $detail_pengeluaran = DetailPengeluaranBarang::where('master_pengeluaran_barang_id', $id)->get();

        return view('barang-keluar.detail-barang-keluar', compact(
            'master_pengeluaran', 'user', 'jenis_pengeluaran', 'detail_pengeluaran',
        ));
    }
    
    public function loadAllDetailPengeluaranBarang(){
        $all_detail_pengeluarans= DetailPengeluaranBarang::all();
        $all_master_pengeluarans = PengeluaranBarang::all();
        $all_users = User::all();
        $all_jenis_pengeluaran = JenisPengeluaran::all();
        $all_barangs = barang::all();
        
        return view('barang-keluar.index-detail',compact('all_detail_pengeluarans',
                    'all_master_pengeluarans', 
                    'all_users', 'all_jenis_pengeluaran', 'all_barangs'));
    }
    
    public function DetailBarangKeluarSearch(Request $request)
    {
        $query = $request->input('query');

        $all_detail_pengeluarans = detailPengeluaranBarang::whereHas('PengeluaranBarang', function ($q) use ($query) {
                $q->where('id', 'like', "%$query%");
            })
            ->orWhereHas('barang', function ($q) use ($query) {
                $q->where('nama', 'like', "%$query%");
            })
            ->orWhere('jumlah_keluar', 'like', "%$query%")
            ->orWhere('harga', 'like', "%$query%")
            ->orWhere('total_harga', 'like', "%$query%")
            ->get();

        return view('barang-keluar.index-detail', compact('all_detail_pengeluarans'));
    }

    public function loadAllJenisPengeluaranBarang(){
        $all_jenis_pengeluarans= JenisPengeluaran::all();

        $used_jenis_ids = DB::table('master_pengeluaran_barangs')->pluck('jenis_id')->toArray();

        return view('barang-keluar.jenis-barang-keluar',compact('all_jenis_pengeluarans','used_jenis_ids'));
    }

    public function loadAddJenisBarangKeluarForm()
    {
        return view('barang-keluar.add-jenis-barang-keluar');
    }

    public function AddJenisBarangKeluar(Request $request){
        $request->validate([
            'jenis' => 'required|string',
        ]);

        try {
            $new_jenisBarangKeluar = new JenisPengeluaran();
            $new_jenisBarangKeluar->jenis = $request->jenis;
            $new_jenisBarangKeluar->save();

            return redirect('/jenis-barang-keluar/')->with('success', 'Data Added Successfully');
        } catch (\Exception $e) {
            return redirect('/jenis-barang-keluar')->with('fail', $e->getMessage());
        }
    }

    public function deleteJenisBarangKeluar($id){
        try {
            JenisPengeluaran::where('id',$id)->delete();
            return redirect('/jenis-barang-keluar')->with('success','Deleted successfully!');
        } catch (\Exception $e) {
            return redirect('/jenis-barang-keluar')->with('fail',$e->getMessage());
            
        }
    }

    public function loadEditJenisBarangKeluarForm($id)
    {
        $JenisPengeluaran = JenisPengeluaran::findOrFail($id);
        return view('barang-keluar.edit-jenis-barang-keluar', compact('JenisPengeluaran'));
    }

    public function EditJenisBarangKeluar(Request $request)
    {
        $request->validate([
            'jenis' => 'required|string',
            'jenis_id' => 'required|integer'
        ]);

        try {
            $update_jenisBarangKeluar = JenisPengeluaran::where('id', $request->jenis_id)->update([
                'jenis' => $request->jenis,
            ]);

            return redirect('/jenis-barang-keluar')->with('success', 'Edit Successfully');
        } catch (\Exception $e) {
            return redirect('/jenis-barang-keluar')->with('fail', $e->getMessage());
        }
    }

    public function loadEditBarangKeluarForm($id)
    {
        $detail_pengeluaran = DetailPengeluaranBarang::findOrFail($id);

        // Pastikan relasi 'pengeluaranBarang' sudah terdefinisi di model DetailPengeluaranBarang
        $masterPengeluaran = $detail_pengeluaran->pengeluaranBarang;

        $all_users = User::all();
        $all_jenis_pengeluarans = JenisPengeluaran::all();
        $all_suppliers = Supplier::all();     // Tambahkan supplier
        $all_konsumens = Konsumen::all();     // Tambahkan konsumen
        $user = Auth::user(); 
        $all_barangs = Barang::all();

        return view('barang-keluar.edit-barang-keluar', compact(
            'masterPengeluaran', 'all_users', 'all_jenis_pengeluarans', 'user',
            'all_barangs', 'detail_pengeluaran', 'all_suppliers', 'all_konsumens'
        ));
    }

    public function EditPengeluaranBarang(Request $request)
    {   
        $request->validate([
            'masterPengeluaran_id' => 'required|exists:master_pengeluaran_barangs,id',
            'detail_pengeluaran_id' => 'required|exists:detail_pengeluaran_barangs,id',
            'barang_id' => 'required|exists:barangs,id',
            'jumlah_keluar' => 'required',
            'harga' => 'required',
            'total_harga' => 'required',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'konsumen_id' => 'nullable|exists:konsumens,id',
        ]);

        // Hapus format angka (titik) pada harga dan total_harga
        $harga = str_replace('.', '', $request->input('harga'));
        $total_harga = str_replace('.', '', $request->input('total_harga'));

        try {
            $detailPengeluaranBarang = DetailPengeluaranBarang::findOrFail($request->detail_pengeluaran_id);
            $masterPengeluaran = PengeluaranBarang::findOrFail($request->masterPengeluaran_id);

            // Hitung selisih total harga
            $selisihTotalHarga = $total_harga - $detailPengeluaranBarang->total_harga;

            // Update detail pengeluaran
            $detailPengeluaranBarang->update([
                'jumlah_diterima' => $request->jumlah_keluar,
                'harga' => $harga,
                'total_harga' => $total_harga,
            ]);

            // Update master pengeluaran (harga_invoice + supplier & konsumen)
            $masterPengeluaran->harga_invoice += $selisihTotalHarga;
            $masterPengeluaran->supplier_id = $request->supplier_id;
            $masterPengeluaran->konsumen_id = $request->konsumen_id;
            $masterPengeluaran->save();

            // Update stok barang
            $barang = Barang::findOrFail($request->barang_id);
            $selisihJumlah = $request->jumlah_keluar - $detailPengeluaranBarang->jumlah_keluar;
            $barang->stok += $selisihJumlah;
            $barang->save();

            return redirect('/master-barang-keluar/')->with('success', 'Edit Successfully');
        } catch (\Exception $e) {
            return redirect('/edit-pengeluaran-barang/' . $request->detail_pengeluaran_id)->with('fail', $e->getMessage());
        }
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $konsumens = Konsumen::all();
        $jenis_pengeluarans = JenisPengeluaran::all();

        return view('barang-keluar.create', compact('suppliers', 'konsumens', 'jenis_pengeluarans'));
    }


    public function store(Request $request) 
    {
        $request->validate([
            'masterPengeluaran_id' => 'required|exists:master_pengeluaran_barangs,id',
            'detail_pengeluaran_id' => 'required|exists:detail_pengeluaran_barangs,id',
            'barang_id' => 'required|exists:barangs,id',
            'jumlah_keluar' => 'required|numeric|min:1',
            'harga' => 'required|numeric|min:0',
            'total_harga' => 'required|numeric|min:0',
            'supplier_id' => 'required|exists:suppliers,id',
            'konsumen_id' => 'required|exists:konsumens,id',
        ]);

        PengeluaranBarang::create([
            'master_pengeluaran_id' => $request->masterPengeluaran_id,
            'detail_pengeluaran_id' => $request->detail_pengeluaran_id,
            'barang_id' => $request->barang_id,
            'jumlah_keluar' => $request->jumlah_keluar,
            'harga' => $request->harga,
            'total_harga' => $request->total_harga,
            'supplier_id' => $request->supplier_id,
            'konsumen_id' => $request->konsumen_id,
        ]);

        return redirect()->route('barangkeluar.index')->with('success', 'Barang berhasil dikeluarkan');
    }

}
