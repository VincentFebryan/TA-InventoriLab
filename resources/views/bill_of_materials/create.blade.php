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
    <h3>Tambah BOM</h3>
    <form action="{{ route('bill_of_materials.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="kode_bom" class="form-label">Kode BOM</label>
            <input type="text" name="kode_bom" class="form-control @error('kode_bom') is-invalid @enderror" value="{{ old('kode_bom') }}" required>
            @error('kode_bom')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="nama_bom" class="form-label">Nama BOM</label>
            <input type="text" name="nama_bom" class="form-control @error('nama_bom') is-invalid @enderror" value="{{ old('nama_bom') }}" required>
            @error('nama_bom')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-3">
            <label for="keterangan" class="form-label">Keterangan</label>
            <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3" required>{{ old('keterangan') }}</textarea>
            @error('keterangan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('bill_of_materials.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
