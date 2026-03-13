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
    <h1>Daftar Gudang</h1>
    <a href="{{ route('gudang.create') }}" class="btn btn-primary mb-3">Tambah Gudang</a>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Kode Gudang</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Jenis Gudnag</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($gudangs as $gudang)
            <tr>
                <td>{{ $gudang->kode_gudang }}</td>
                <td>{{ $gudang->nama }}</td>
                <td>{{ $gudang->alamat_lengkap }}</td>
                <td>{{ $gudang->jenis_gudang }}</td>
                <td>{{ $gudang->keterangan }}</td>
                <td>
                    <a href="{{ route('gudang.edit', $gudang->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('gudang.destroy', $gudang->id) }}" method="POST" class="d-inline" onsubmit="return 
                    confirm('Yakin ingin menghapus?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
