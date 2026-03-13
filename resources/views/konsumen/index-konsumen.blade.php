@extends('layouts.app')

@section('title', 'Daftar Konsumen')

@section('content')
<script src="{{ asset('template/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('template/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('template/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('template/js/sb-admin-2.min.js') }}"></script>
<script src="{{ asset('template/vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('template/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<div class="container">
    <h1>Daftar Konsumen</h1>
    <a href="{{ route('konsumen.create') }}" class="btn btn-primary mb-3">Tambah Konsumen</a>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Kota</th>
                <th>Telepon</th>
                <th>Email</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($konsumens as $konsumen)
            <tr>
                <td>{{ $konsumen->nama }}</td>
                <td>{{ $konsumen->alamat }}</td>
                <td>{{ $konsumen->kota }}</td>
                <td>{{ $konsumen->telepon }}</td>
                <td>{{ $konsumen->email }}</td>
                <td>
                    <a href="{{ route('konsumen.edit', $konsumen->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('konsumen.destroy', $konsumen->id) }}" method="POST" class="d-inline" onsubmit="return 
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
