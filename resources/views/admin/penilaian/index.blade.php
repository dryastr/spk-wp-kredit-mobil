@extends('layouts.main')

@section('title', 'Penilaian Nasabah')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Daftar Penilaian Nasabah</h4>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal"
                            data-bs-target="#addPenilaianModal">
                            Tambah Penilaian Baru
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
                                        <th>Kode Nasabah</th>
                                        <th>Nama Nasabah</th>
                                        @foreach ($kriterias as $kriteria)
                                            <th>{{ $kriteria->nama }}</th>
                                        @endforeach
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($nasabahs as $nasabah)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $nasabah->kode }}</td>
                                            <td>{{ $nasabah->nama }}</td>

                                            @foreach ($kriterias as $kriteria)
                                                @if ($nasabah->penilaian && isset($nasabah->penilaian->nilai[$kriteria->kode]))
                                                    <td>
                                                        {{ $nasabah->penilaian->nilai[$kriteria->kode] }}
                                                        ({{ \App\Helpers\KriteriaHelper::getKriteriaText($kriteria->kode, $nasabah->penilaian->nilai[$kriteria->kode]) }})
                                                    </td>
                                                @else
                                                    -
                                                @endif
                                            @endforeach

                                            <td class="text-nowrap">
                                                <div class="dropdown dropup">
                                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                                        id="dropdownMenuButton-{{ $nasabah->id }}"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu"
                                                        aria-labelledby="dropdownMenuButton-{{ $nasabah->id }}">
                                                        @if ($nasabah->penilaian)
                                                            <li>
                                                                <a class="dropdown-item" href="#"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#editPenilaianModal"
                                                                    onclick="editPenilaian({{ json_encode([
                                                                        'id' => $nasabah->id,
                                                                        'kode' => $nasabah->kode,
                                                                        'nama' => $nasabah->nama,
                                                                        'penilaian' => $nasabah->penilaian,
                                                                    ]) }})">Ubah</a>
                                                            </li>
                                                            <li>
                                                                <form
                                                                    action="{{ route('penilaian.destroy', $nasabah->penilaian->id) }}"
                                                                    method="POST"
                                                                    onsubmit="return confirm('Yakin ingin menghapus penilaian ini?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="dropdown-item">Hapus</button>
                                                                </form>
                                                            </li>
                                                        @else
                                                            <li>
                                                                <a class="dropdown-item" href="#"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#addPenilaianModal"
                                                                    onclick="setNasabahId({{ $nasabah->id }})">Tambah
                                                                    Penilaian</a>
                                                            </li>
                                                        @endif
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

    <div class="modal fade" id="addPenilaianModal" tabindex="-1" aria-labelledby="addPenilaianModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPenilaianModalLabel">Tambah Penilaian Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('penilaian.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nasabah_select" class="form-label">Pilih Nasabah</label>
                            <select class="form-select" id="nasabah_select" name="nasabah_id" required>
                                <option value="">Pilih Nasabah</option>
                                @foreach ($nasabahsForm as $nasabah)
                                    @if (!$nasabah->penilaian)
                                        <option value="{{ $nasabah->id }}" data-kode="{{ $nasabah->kode }}"
                                            data-nama="{{ $nasabah->nama }}">
                                            {{ $nasabah->kode }} - {{ $nasabah->nama }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kode Nasabah</label>
                            <input type="text" class="form-control" id="show_kode" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Nasabah</label>
                            <input type="text" class="form-control" id="show_nama" readonly>
                        </div>

                        <hr>

                        @foreach ($kriterias as $kriteria)
                            <div class="mb-3">
                                <label for="kriteria_{{ $kriteria->kode }}"
                                    class="form-label">{{ $kriteria->nama }}</label>
                                <select class="form-select" id="kriteria_{{ $kriteria->kode }}"
                                    name="{{ strtolower($kriteria->kode) }}" required>
                                    <option value="">Pilih {{ $kriteria->nama }}</option>
                                    @php
                                        $options = is_array($kriteria->keterangan)
                                            ? $kriteria->keterangan
                                            : json_decode($kriteria->keterangan, true);
                                    @endphp
                                    @foreach ($options as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editPenilaianModal" tabindex="-1" aria-labelledby="editPenilaianModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPenilaianModalLabel">Ubah Penilaian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editPenilaianForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="nasabah_id" id="edit_nasabah_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Kode Nasabah</label>
                            <input type="text" class="form-control" id="edit_show_kode" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Nasabah</label>
                            <input type="text" class="form-control" id="edit_show_nama" readonly>
                        </div>

                        <hr>

                        @foreach ($kriterias as $kriteria)
                            <div class="mb-3">
                                <label for="edit_kriteria_{{ $kriteria->kode }}"
                                    class="form-label">{{ $kriteria->nama }}</label>
                                <select class="form-select" id="edit_kriteria_{{ $kriteria->kode }}"
                                    name="{{ strtolower($kriteria->kode) }}" required>
                                    <option value="">Pilih {{ $kriteria->nama }}</option>

                                    @php
                                        $options = is_array($kriteria->keterangan)
                                            ? $kriteria->keterangan
                                            : json_decode($kriteria->keterangan, true);

                                        $currentValue = $penilaian->nilai[$kriteria->kode] ?? null;
                                    @endphp

                                    @foreach ($options as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ $currentValue == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('nasabah_select').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById('show_kode').value = selectedOption.getAttribute('data-kode');
            document.getElementById('show_nama').value = selectedOption.getAttribute('data-nama');
        });

        function editPenilaian(data) {
            document.getElementById('editPenilaianForm').action = `/penilaian/${data.penilaian.id}`;
            document.getElementById('edit_nasabah_id').value = data.id;
            document.getElementById('edit_show_kode').value = data.kode;
            document.getElementById('edit_show_nama').value = data.nama;

            @foreach ($kriterias as $kriteria)
                if (data.penilaian.nilai && data.penilaian.nilai['{{ $kriteria->kode }}']) {
                    document.getElementById('edit_kriteria_{{ $kriteria->kode }}').value =
                        data.penilaian.nilai['{{ $kriteria->kode }}'];
                }
            @endforeach

            $('#editPenilaianModal').modal('show');
        }

        function setNasabahId(id) {
            document.getElementById('nasabah_select').value = id;
        }
    </script>
@endpush
