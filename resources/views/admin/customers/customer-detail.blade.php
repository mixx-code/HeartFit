@extends('layouts.app')

@section('title', 'Detail Customer')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4>Data Customer</h4>

  <table class="table table-bordered">
    <tr><th>Nama</th><td>{{ $detail->user->name }}</td></tr>
    <tr><th>Email</th><td>{{ $detail->user->email }}</td></tr>
    <tr><th>Role</th><td>{{ $detail->user->role }}</td></tr>
    <tr><th>Password (hash)</th><td>{{ $detail->user->password }}</td></tr>
    <tr><th>MR</th><td>{{ $detail->mr }}</td></tr>
    <tr><th>NIK</th><td>{{ $detail->nik }}</td></tr>
    <tr><th>Alamat</th><td>{{ $detail->alamat }}</td></tr>
    <tr><th>Jenis Kelamin</th><td>{{ $detail->jenis_kelamin }}</td></tr>
    <tr><th>TTL</th><td>{{ $detail->tempat_lahir }}, {{ $detail->tanggal_lahir }}</td></tr>
    <tr><th>BB/TB</th><td>{{ $detail->bb_tb }}</td></tr>
    <tr><th>Usia</th><td>{{ $detail->usia }}</td></tr>
    <tr><th>No HP</th><td>{{ $detail->hp }}</td></tr>
    <tr>
      <th>Foto KTP</th>
      <td>
        @if($fotoKtp)
          @if(Str::startsWith($fotoKtp, 'data:'))
            <img src="{{ $fotoKtp }}" alt="KTP" style="max-width:300px;">
          @else
            <img src="data:image/png;base64,{{ $fotoKtp }}" alt="KTP" style="max-width:300px;">
          @endif
        @else
          <em>Tidak ada foto</em>
        @endif
      </td>
    </tr>
  </table>
</div>
@endsection
