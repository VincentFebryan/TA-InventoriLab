@extends('layouts.app')

@section('content')
<script src="{{ asset('template/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('template/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('template/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('template/js/sb-admin-2.min.js') }}"></script>
<script src="{{ asset('template/vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('template/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('template/js/demo/datatables-demo.js') }}"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<div class="container">
    <h2>Tambah Bill of Material</h2>
    <form action="{{ route('bill_of_materials.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Kode BOM</label>
            <input type="text" name="kode_bom" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Nama Material</label>
            <input type="text" name="nama_material" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Jumlah</label>
            <input type="number" name="jumlah" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Satuan</label>
            <input type="text" name="satuan" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Harga per Unit</label>
            <input type="number" step="0.01" name="harga_per_unit" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('bill_of_materials.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
