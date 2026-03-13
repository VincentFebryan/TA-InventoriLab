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

<div class="container py-4">
    <h3 class="mb-4">Proyek</h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('proyek.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="bom_id" class="form-label">Pilih BOM</label>
            <select name="bom_id" id="bom_id" class="form-control">
                <option value="">-- Pilih BOM --</option>
                @foreach($bom as $b)
                    <option value="{{ $b->id }}" data-kode="{{ $b->kode_bom }}" data-nama="{{ $b->nama_bom }}" data-keterangan="{{ $b->keterangan }}">
                        {{ $b->kode_bom }} - {{ $b->nama_bom }} - {{ $b->keterangan }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="kode_bom" class="form-label">Kode BOM</label>
            <input type="text" name="kode_bom" id="kode_bom" class="form-control" readonly required>
        </div>

        <div class="mb-3">
            <label for="nama_bom" class="form-label">Nama BOM</label>
            <input type="text" name="nama_bom" id="nama_bom" class="form-control" readonly required>
        </div>

        <div class="mb-3">
            <label for="keterangan" class="form-label">Keterangan</label>
            <textarea name="keterangan" id="keterangan" class="form-control" rows="3" readonly></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label d-block">Status BOM</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="status_bom" id="Aktif" value="Aktif" required>
                <label class="form-check-label" for="Aktif">Aktif</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="status_bom" id="Non-Aktif" value="Non-Aktif">
                <label class="form-check-label" for="Non-Aktif">Non-Aktif</label>
            </div>
        </div>        

        <hr>
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
            <tbody></tbody>
        </table>

        <button type="button" class="btn btn-outline-primary mb-4" onclick="bukaModalBarang()">+ Tambah Item Baru</button>
        <button type="submit" class="btn btn-success w-100">Simpan Proyek</button>
    </form>
</div>

<div class="modal fade" id="modalBarang" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Barang</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="tableBarang">
                    <thead>
                        <tr>
                            <th>Brand</th>
                            <th>Nama Barang</th>
                            <th>No Catalog</th>
                            <th>Jenis Barang</th>
                            <th>Stok</th>
                            <th>Kadaluarsa</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Plate(Kode Barang)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    let itemIndex = 0;

    //mengambil data-* dari <option> yang dipilih.
    document.getElementById('bom_id').addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        document.getElementById('kode_bom').value = selected.getAttribute('data-kode');
        document.getElementById('nama_bom').value = selected.getAttribute('data-nama');
        document.getElementById('keterangan').value = selected.getAttribute('data-keterangan');
    });

    //menampilkan modal barang
    function bukaModalBarang() {
        $('#modalBarang').modal('show');
        $('#tableBarang tbody').empty();

        $.get("/get-barang", function(data) {
            data.forEach(function(item) {
                let row = `
                    <tr>
                        <td>${item.brand}</td>
                        <td>${item.nama_barang}</td>
                        <td>${item.no_catalog}</td>
                        <td>${item.jenis_barang_nama}(${item.satuan_stok})</td>
                        <td>${item.stok}</td>
                        <td>${item.kadaluarsa}</td>
                        <td>${item.lokasi}</td>
                        <td>${item.status_barang}</td>
                        <td>${item.plate}</td>
                        <td><button type="button" class="btn btn-success btn-sm" onclick='pilihBarang(${JSON.stringify(item)})'>Pilih</button></td>
                    </tr>
                `;
                $('#tableBarang tbody').append(row);
            });
        });
    }

    //menambahkan barang ke tabel utama
    function pilihBarang(barang) {
        const table = document.querySelector('#itemsTable tbody');
        const row = document.createElement('tr');

        row.innerHTML = `
            <td>${barang.brand}<input type="hidden" name="items[${itemIndex}][barang_id]" value="${barang.id}"></td>
            <td>${barang.nama_barang}</td>
            <td>${barang.no_catalog}</td>
            <td>${barang.jenis_barang_nama} ${barang.satuan_stok}</td>
            <td>${barang.stok}</td>
            <td><input type="number" name="items[${itemIndex}][jumlah_digunakan]" class="form-control" min="0" max="${barang.stok}" step="any" required></td>
            <td>${barang.kadaluarsa}</td>
            <td>${barang.lokasi}</td>
            <td>${barang.status_barang}</td>
            <td>${barang.plate}</td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">X</button></td>
        `;

        table.appendChild(row);
        itemIndex++;

        $('#modalBarang').modal('hide');
    }
</script>
@endsection
