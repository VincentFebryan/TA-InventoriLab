@extends('layouts.app')

@section('title', 'Tambah Proyek')

@section('content')
<script src="{{ asset('template/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('template/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('template/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('template/js/sb-admin-2.min.js') }}"></script>
<script src="{{ asset('template/vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('template/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<div class="container">
    <div class="card mx-auto">
        <div class="card-header text-center">Edit Proyek</div>

        @if (Session::has('fail'))
            <span class="alert alert-danger p-2">{{ Session::get('fail') }}</span>
        @endif

        <div class="card-body">
            <form action="{{ route('proyek.update', $proyek->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="kode_bom" class="form-label">Kode BOM</label>
                    <input type="text" name="kode_bom" id="kode_bom" value="{{ old('kode_bom', $proyek->kode_bom) }}" class="form-control" readonly>
                </div>

                <div class="mb-3">
                    <label for="nama_bom" class="form-label">Nama BOM</label>
                    <input type="text" name="nama_bom" id="nama_bom" value="{{ old('nama_bom', $proyek->nama_bom) }}" class="form-control" readonly>
                </div>

                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <input type="text" name="keterangan" id="nama_bom" value="{{ old('keterangan', $proyek->keterangan) }}" class="form-control" readonly>
                </div>

                <div class="mb-3">
                    <label for="status_bom" class="form-label">Status BOM</label>
                    <select name="status_bom" id="status_bom" class="form-control">
                        <option value="Aktif" {{ old('status_bom', $proyek->status_bom) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Non-Aktif" {{ old('status_bom', $proyek->status_bom) == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                    @error('status_bom')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <h5 class="mb-3">Item Proyek</h5>
                <table class="table table-bordered" id="itemsTable">
                    <thead>
                        <tr>
                            <th>Brand</th>
                            <th>Nama Barang</th>
                            <th>No Catalog</th>
                            <th>Jenis Barang</th>
                            <th>Stok</th>
                            <th>Jumlah Digunakan</th>
                            <th>Kadaluarsa</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Plate(Kode Barang)</th>
                            <th>Hapus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $i => $item)
                        <tr>
                            <td>{{ $item->barang->brand }}</td>
                            <td>{{ $item->barang->nama_barang }}</td>
                            <td>{{ $item->barang->no_catalog }}</td>
                            <td>
                                {{ ($item->barang->jenisBarang->nama_jenis_barang ?? '-') . ' ' . ($item->barang->jenisBarang->satuan_stok ?? '-') }}
                            </td>
                            <td>{{ $item->barang->stok }}</td>
                            <td>
                                <input type="hidden" name="items[{{ $i }}][barang_id]" value="{{ $item->barang_id }}">
                                <input type="number" name="items[{{ $i }}][jumlah_digunakan]" class="form-control" value="{{ $item->jumlah_digunakan }}">
                            </td>
                            <td>{{ $item->barang->kadaluarsa }}</td>
                            <td>{{ $item->barang->lokasi }}</td>
                            <td>{{ $item->barang->status_barang }}</td>
                            <td>{{ $item->barang->plate }}</td>
                            <td><button type="button" class="btn btn-danger btn-sm removeRow">Hapus</button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>              
                <button type="submit" class="btn btn-primary w-100 mb-2">Simpan</button>
                <a href="{{ url()->previous() }}" class="btn btn-danger w-100">Batal</a>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).on('click', '.removeRow', function () {
        $(this).closest('tr').remove();
    });
    </script>
@endsection
