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
    <h3 class="mb-4">Tambah Gudang</h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('gudang.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="kode_gudang" class="form-label">Kode Gudang</label>
            <input type="text" name="kode_gudang" id="kode_gudang" class="form-control" value="{{ old('kode_gudang') }}" required>
            @error('kode_gudang')<span class="text-danger">{{ $message }}</span>@enderror
        </div>

        <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
        </div>

        <div class="mb-3">
            <label for="alamat_lengkap" class="form-label">Alamat</label>
            <textarea name="alamat_lengkap" class="form-control" required>{{ old('alamat_lengkap') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="jenis_gudang" class="form-label">Jenis Gudang</label>
            <input type="text" name="jenis_gudang" class="form-control" value="{{ old('jenis_gudang') }}">
        </div>

        <div class="mb-3">
            <label for="keterangan" class="form-label">Keterangan</label>
            <textarea name="keterangan" id="keterangan" class="form-control" rows="3">{{ old('keterangan') }}</textarea>
        </div>

        <button type="submit" class="btn btn-success w-100">Simpan Gudang</button>
    </form>
</div>
@endsection
