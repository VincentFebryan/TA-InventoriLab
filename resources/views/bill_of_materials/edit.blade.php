@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Bill of Material</h2>
    <form action="{{ route('bill_of_materials.update', $bom->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Kode BOM</label>
            <input type="text" name="kode_bom" class="form-control" value="{{ $bom->kode_bom }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Nama Material</label>
            <input type="text" name="nama_material" class="form-control" value="{{ $bom->nama_material }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Jumlah</label>
            <input type="number" name="jumlah" class="form-control" value="{{ $bom->jumlah }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Satuan</label>
            <input type="text" name="satuan" class="form-control" value="{{ $bom->satuan }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Harga per Unit</label>
            <input type="number" step="0.01" name="harga_per_unit" class="form-control" value="{{ $bom->harga_per_unit }}" required>
        </div>
        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('bill_of_materials.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
