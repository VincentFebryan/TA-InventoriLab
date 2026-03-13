@extends('layouts.app')

@section('title', 'Daftar Proyek')

@section('content')
<script src="{{ asset('template/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('template/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('template/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('template/js/sb-admin-2.min.js') }}"></script>
<script src="{{ asset('template/vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('template/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<div class="container">
    <h1>Daftar Proyek</h1>
    <a href="{{ url('/add-proyek') }}" class="btn btn-primary mb-3">Tambah Proyek</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Kode BOM</th>
                <th>Nama BOM</th>
                <th>Keterangan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proyeks as $proyek)
            <tr>
                <td>{{ $proyek->kode_bom }}</td>
                <td>{{ $proyek->nama_bom }}</td>
                <td>{{ $proyek->keterangan }}</td>
                <td>{{ $proyek->status_bom }}</td>
                <td>
                    <a href="{{ url('/edit-proyek/'.$proyek->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('proyek.destroy', $proyek->id) }}" row = "6" method="POST" onsubmit="return confirm('Yakin ingin menghapus?, note: sebelum menghapus pastikan pemakaian sudah dikurangi jika salah input');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
