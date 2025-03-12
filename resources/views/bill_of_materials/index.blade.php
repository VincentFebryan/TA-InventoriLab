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
    <h2>Bill of Materials</h2>
    <a href="{{ route('bill_of_materials.create') }}" class="btn btn-primary">Tambah BOM</a>
    <table class="table">
        <thead>
            <tr>
                <th>Kode BOM</th>
                <th>Nama Material</th>
                <th>Jumlah</th>
                <th>Satuan</th>
                <th>Harga/Unit</th>
                <th>Total Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bill_of_materials as $bom)
            <tr>
                <td>{{ $bom->kode_bom }}</td>
                <td>{{ $bom->nama_material }}</td>
                <td>{{ $bom->jumlah }}</td>
                <td>{{ $bom->satuan }}</td>
                <td>{{ number_format($bom->harga_per_unit, 2) }}</td>
                <td>{{ number_format($bom->total_harga, 2) }}</td>
                <td>
                    <a href="{{ route('bill_of_materials.edit', $bom->id) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('bill_of_materials.destroy', $bom->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Hapus data ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
