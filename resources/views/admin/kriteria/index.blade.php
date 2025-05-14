@extends('layouts.main')

@section('title', 'Kriteria')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Daftar Kriteria</h4>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal"
                            data-bs-target="#addKriteriaModal">
                            Tambah Kriteria Baru
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
                                        <th>Bobot</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kriterias as $kriteria)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $kriteria->kode }}</td>
                                            <td>{{ $kriteria->nama }}</td>
                                            <td>{{ number_format($kriteria->bobot, 2) }}</td>
                                            <td>
                                                @if (is_array($kriteria->keterangan))
                                                    <ul>
                                                        @foreach ($kriteria->keterangan as $key => $value)
                                                            <li>{{ $key }}: {{ $value }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-nowrap">
                                                <div class="dropdown dropup">
                                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                                        id="dropdownMenuButton-{{ $kriteria->kode }}"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu"
                                                        aria-labelledby="dropdownMenuButton-{{ $kriteria->kode }}">
                                                        <li>
                                                            <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                                data-bs-target="#editKriteriaModal"
                                                                onclick="editKriteria({{ json_encode($kriteria) }})">Ubah</a>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('kriteria.destroy', $kriteria->kode) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Yakin ingin menghapus kriteria ini?')">
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

    <div class="modal fade" id="addKriteriaModal" tabindex="-1" aria-labelledby="addKriteriaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addKriteriaModalLabel">Tambah Kriteria Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('kriteria.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="kode" class="form-label">Kode Kriteria</label>
                                <input type="text" class="form-control" id="kode" name="kode"
                                    value="{{ $nextCode }}" readonly>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label for="nama" class="form-label">Nama Kriteria</label>
                                <input type="text" class="form-control" id="nama" name="nama" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="bobot" class="form-label">Bobot</label>
                                <input type="number" name="bobot" class="form-control" placeholder="0.10" step="0.01"
                                    min="0" max="1" required>
                                <small class="text-muted">Total bobot semua kriteria tidak boleh melebihi 1</small>
                            </div>
                        </div>

                        <!-- Dynamic Keterangan Input -->
                        <div class="mb-3">
                            <label class="form-label">Keterangan (Key-Value Pairs)</label>
                            <div id="keterangan-container">
                                <div class="row mb-2 keterangan-row">
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="keterangan_keys[]"
                                            placeholder="Key (contoh: 3)">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="keterangan_values[]"
                                            placeholder="Value (contoh: < 5 Tahun (3))">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger remove-row">Hapus</button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="add-keterangan" class="btn btn-sm btn-primary">Tambah
                                Keterangan</button>
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

    <div class="modal fade" id="editKriteriaModal" tabindex="-1" aria-labelledby="editKriteriaModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editKriteriaModalLabel">Ubah Data Kriteria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editKriteriaForm" method="POST" action="{{ route('kriteria.update', $kriteria->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="edit_kode" class="form-label">Kode Kriteria</label>
                                <input type="text" class="form-control" id="edit_kode" name="kode" readonly>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label for="edit_nama" class="form-label">Nama Kriteria</label>
                                <input type="text" class="form-control" id="edit_nama" name="nama" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_bobot" class="form-label">Bobot</label>
                                <input type="number" step="0.01" min="0" max="1" class="form-control"
                                    id="edit_bobot" name="bobot" required>
                                <small class="text-muted">Total bobot semua kriteria tidak boleh melebihi 1</small>
                            </div>
                        </div>

                        <!-- Dynamic Keterangan Input -->
                        <div class="mb-3">
                            <label class="form-label">Keterangan (Key-Value Pairs)</label>
                            <div id="edit_keterangan-container">
                                <!-- Rows will be added dynamically by JavaScript -->
                            </div>
                            <button type="button" id="edit_add-keterangan" class="btn btn-sm btn-primary">Tambah
                                Keterangan</button>
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
        $(document).ready(function() {

            // Tambah baris keterangan (CREATE)
            $('#add-keterangan').click(function() {
                const newRow = `
                <div class="row mb-2 keterangan-row">
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="keterangan_keys[]" placeholder="Key (contoh: 3)" required>
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="keterangan_values[]" placeholder="Value (contoh: < 5 Tahun (3))" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-row">Hapus</button>
                    </div>
                </div>`;
                $('#keterangan-container').append(newRow);
            });

            // Tambah baris keterangan (EDIT)
            $('#edit_add-keterangan').click(function() {
                const row = `
                <div class="row mb-2 keterangan-row">
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="keterangan_keys[]" placeholder="Key">
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="keterangan_values[]" placeholder="Value">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-row">Hapus</button>
                    </div>
                </div>`;
                $('#edit_keterangan-container').append(row);
            });

            // Hapus baris
            $(document).on('click', '.remove-row', function() {
                $(this).closest('.keterangan-row').remove();
            });

            // Validasi sebelum submit
            $('form').submit(function(e) {
                // Hapus semua row keterangan yang kosong
                $('.keterangan-row').each(function() {
                    const key = $(this).find('input[name="keterangan_keys[]"]').val();
                    const value = $(this).find('input[name="keterangan_values[]"]').val();

                    if (!key.trim() || !value.trim()) {
                        $(this).remove(); // Hapus row yang kosong
                    }
                });

                // Lanjutkan validasi kalau ternyata setelah dibersihkan tetap ada yang kosong
                const emptyKeys = $('input[name="keterangan_keys[]"]').filter(function() {
                    return $(this).val().trim() === '';
                });

                if (emptyKeys.length > 0) {
                    alert('Semua key keterangan harus diisi!');
                    e.preventDefault();
                }
            });

        });

        // Buat fungsi ini GLOBAL supaya bisa dipanggil dari tombol edit
        function editKriteria(kriteria) {
            document.getElementById('editKriteriaForm').action = `/kriteria/${kriteria.id}`;

            document.getElementById('edit_kode').value = kriteria.kode;
            document.getElementById('edit_nama').value = kriteria.nama;
            document.getElementById('edit_bobot').value = kriteria.bobot;

            $('#edit_keterangan-container').empty();

            let keteranganObj = kriteria.keterangan;
            if (typeof keteranganObj === 'string') {
                try {
                    keteranganObj = JSON.parse(keteranganObj);
                } catch (e) {
                    keteranganObj = {};
                }
            }

            if (keteranganObj) {
                Object.entries(keteranganObj).forEach(([key, value]) => {
                    const row = `
                    <div class="row mb-2 keterangan-row">
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="keterangan_keys[]" value="${key}">
                        </div>
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="keterangan_values[]" value="${value.replace(` (${key})`, '')}">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove-row">Hapus</button>
                        </div>
                    </div>`;
                    $('#edit_keterangan-container').append(row);
                });
            }

            $('#editKriteriaModal').modal('show');
        }
    </script>
@endpush
