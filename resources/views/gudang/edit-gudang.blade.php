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
    <div class="card mx-auto" style="max-width: 500px;">
        <div class="card-header text-center">Edit Gudang</div>

        <div class="card-body">
            <form action="{{ route('gudang.update', $gudang->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="kode_gudang" class="form-label">Kode Gudang</label>
                    <input type="text" name="kode_gudang" id="kode_gudang" class="form-control" value="{{ old('kode_gudang', $gudang->kode_gudang) }}" required>
                    @error('kode_gudang')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
        
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" name="nama" id="nama" class="form-control" value="{{ old('nama', $gudang->nama) }}" required>
                    @error('nama')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
        
                <div class="mb-3">
                    <label for="alamat_lengkap" class="form-label">Alamat</label>
                    <textarea name="alamat_lengkap" id="alamat_lengkap" class="form-control" required>{{ old('alamat_lengkap', $gudang->alamat_lengkap) }}</textarea>
                    @error('alamat_lengkap')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
        
                <div class="mb-3">
                    <label for="jenis_gudang" class="form-label">Jenis Gudang</label>
                    <input type="text" name="jenis_gudang" id="jenis_gudang" class="form-control" value="{{ old('jenis_gudang', $gudang->jenis_gudang) }}">
                    @error('jenis_gudang')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
        
                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" class="form-control" rows="3">{{ old('keterangan', $gudang->keterangan) }}</textarea>
                    @error('keterangan')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-2">Simpan</button>
                <a href="{{ route('gudang.index') }}" class="btn btn-danger w-100">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection
