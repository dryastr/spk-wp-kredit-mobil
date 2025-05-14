@extends('layouts.main')

@section('title', 'Hasil WP')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Hasil Perhitungan Weighted Product</h4>
                        @if ($hasResults)
                            <form action="{{ route('hasil-wp.store') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Simpan Semua Hasil Perhitungan ke Database
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>Nama Nasabah</th>
                                        <th>Nilai Bobot Preferensi (V)</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($hasResults)
                                        @foreach ($nasabahs as $nasabah)
                                            @if ($nasabah->hasilWP)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $nasabah->kode }}</td>
                                                    <td>{{ $nasabah->nama }}</td>
                                                    <td>{{ number_format($nasabah->hasilWP->vektor_v, 9) }}</td>
                                                    <td>
                                                        <span
                                                            class="badge bg-{{ $nasabah->hasilWP->layak ? 'success' : 'danger' }}">
                                                            {{ $nasabah->hasilWP->layak ? 'Layak' : 'Tidak Layak' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @else
                                        @foreach ($results as $result)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $result['kode'] }}</td>
                                                <td>{{ $result['nama'] }}</td>
                                                <td>{{ number_format($result['vektor_v'], 9) }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $result['layak'] ? 'success' : 'danger' }}">
                                                        {{ $result['layak'] ? 'Layak' : 'Tidak Layak' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
