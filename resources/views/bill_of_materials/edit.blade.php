@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit BOM</h3>
    <form action="{{ route('bill_of_materials.update', $bom->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label for="kode_bom" class="form-label">Kode BOM</label>
            <input type="text" name="kode_bom" class="form-control @error('kode_bom') is-invalid @enderror" value="{{ old('kode_bom', $bom->kode_bom) }}" required>
            @error('kode_bom')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="nama_bom" class="form-label">Nama BOM</label>
            <input type="text" name="nama_bom" class="form-control @error('nama_bom') is-invalid @enderror" value="{{ old('nama_bom', $bom->nama_bom) }}" required>
            @error('nama_bom')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="keterangan" class="form-label">Keterangan</label>
            <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3" required>{{ old('keterangan', $bom->keterangan) }}</textarea>
            @error('keterangan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Perbarui</button>
        <a href="{{ route('bill_of_materials.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
