@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
<script src="{{ asset('template/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('template/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('template/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('template/js/sb-admin-2.min.js') }}"></script>
<script src="{{ asset('template/vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('template/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<div class="container py-4">
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-header text-center">Edit Supplier</div>
        <div class="card-body">
            <form action="{{ route('supplier.update', $supplier->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $supplier->nama) }}" required>
                </div>

                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control" required>{{ old('alamat', $supplier->alamat) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="kota" class="form-label">Kota</label>
                    <input type="text" name="kota" class="form-control" value="{{ old('kota', $supplier->kota) }}" required>
                </div>

                <div class="mb-3">
                    <label for="telepon" class="form-label">Telepon</label>
                    <input type="text" name="telepon" class="form-control" value="{{ old('telepon', $supplier->telepon) }}">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $supplier->email) }}">
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-2">Simpan Perubahan</button>
                <a href="{{ route('supplier.index') }}" class="btn btn-danger w-100">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection
