@extends('layouts.main')

@section('title', 'Nasabah')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Daftar Nasabah</h4>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addNasabahModal">
                            Tambah Nasabah Baru
                        </button>
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-xl">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>Nama</th>
                                        <th>Alamat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($nasabahs as $nasabah)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $nasabah->kode }}</td>
                                            <td>{{ $nasabah->nama }}</td>
                                            <td>{{ Str::limit($nasabah->alamat, 50) }}</td>
                                            <td class="text-nowrap">
                                                <div class="dropdown dropup">
                                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                                        id="dropdownMenuButton-{{ $nasabah->id }}"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu"
                                                        aria-labelledby="dropdownMenuButton-{{ $nasabah->id }}">
                                                        <li>
                                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                                data-bs-target="#editNasabahModal"
                                                                onclick="editNasabah({{ json_encode($nasabah) }})">Ubah</a>
                                                        </li>
                                                        <li>
                                                            <form
                                                                action="{{ route('nasabah.destroy', $nasabah->id) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Yakin ingin menghapus nasabah ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item">Hapus</button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addNasabahModal" tabindex="-1" aria-labelledby="addNasabahModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNasabahModalLabel">Tambah Nasabah Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('nasabah.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="kode" class="form-label">Kode Nasabah</label>
                                <input type="text" class="form-control" id="kode" name="kode" value="{{ $kode }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nama" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama" name="nama" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editNasabahModal" tabindex="-1" aria-labelledby="editNasabahModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editNasabahModalLabel">Ubah Data Nasabah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editNasabahForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_kode" class="form-label">Kode Nasabah</label>
                                <input type="text" class="form-control" id="edit_kode" name="kode" required readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_nama" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="edit_nama" name="nama" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="edit_alamat" name="alamat" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function editNasabah(nasabah) {
            document.getElementById('editNasabahForm').action = `/nasabah/${nasabah.id}`;
            document.getElementById('edit_kode').value = nasabah.kode;
            document.getElementById('edit_nama').value = nasabah.nama;
            document.getElementById('edit_alamat').value = nasabah.alamat;
            $('#editNasabahModal').modal('show');
        }
    </script>
@endpush
