<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\HasilWP;
use App\Models\Kriteria;
use App\Models\Nasabah;
use App\Models\Penilaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HasilWPController extends Controller
{
    public function index()
    {
        try {
            $hasResults = HasilWP::exists();
            $nasabahs = Nasabah::with(['penilaian', 'hasilWP'])->get();

            $results = [];
            if (!$hasResults && $nasabahs->isNotEmpty()) {
                $results = $this->calculateWP($nasabahs);
            }

            return view('admin.hasil-wp.index', [
                'nasabahs' => $nasabahs,
                'results' => $results,
                'hasResults' => $hasResults,
                'kriterias' => Kriteria::orderBy('kode')->get()
            ]);
        } catch (\Exception $e) {
            \Log::error("HasilWPController@index error: " . $e->getMessage());

            return view('admin.hasil-wp.index', [
                'nasabahs' => collect(),
                'results' => [],
                'hasResults' => false,
                'kriterias' => Kriteria::orderBy('kode')->get(),
                'error' => $e->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            $nasabahs = Nasabah::with('penilaian')->get();
            $results = $this->calculateWP($nasabahs);

            \DB::transaction(function () use ($results) {
                foreach ($results as $result) {
                    HasilWP::updateOrCreate(
                        ['nasabah_id' => $result['nasabah_id']],
                        [
                            'vektor_s' => $result['vektor_s'],
                            'vektor_v' => $result['vektor_v'],
                            'layak' => $result['layak']
                        ]
                    );
                }
            });

            return redirect()->route('hasil-wp.index')->with('success', 'Hasil perhitungan berhasil disimpan!');
        } catch (\Exception $e) {
            Log::error('Error saving WP results: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan hasil: ' . $e->getMessage());
        }
    }

    private function calculateWP($nasabahs)
    {
        $kriterias = Kriteria::orderBy('kode')->get();
        if ($kriterias->isEmpty()) {
            throw new \Exception("Tidak ada kriteria yang terdaftar");
        }

        $totalWeight = $kriterias->sum('bobot');
        if (abs($totalWeight - 1.0) > 0.0001) {
            throw new \Exception("Total bobot kriteria harus sama dengan 1. Total saat ini: {$totalWeight}");
        }

        $results = [];
        $vectorS = [];
        $invalidEntries = [];

        foreach ($nasabahs as $nasabah) {
            $penilaian = $nasabah->penilaian;
            if (!$penilaian) {
                $invalidEntries[] = "Nasabah {$nasabah->nama} tidak memiliki data penilaian";
                continue;
            }

            $nilaiData = is_array($penilaian->nilai) ? $penilaian->nilai : json_decode($penilaian->nilai, true);
            if (!is_array($nilaiData)) {
                $invalidEntries[] = "Nasabah {$nasabah->nama}: Data nilai tidak valid (JSON)";
                continue;
            }

            $s = 1.0;
            $invalidCriteria = [];
            $detailS = [];

            foreach ($kriterias as $kriteria) {
                $kodeKriteria = $kriteria->kode;
                $nilai = $nilaiData[$kodeKriteria] ?? null;

                if ($nilai === null) {
                    $invalidCriteria[] = "Kriteria {$kodeKriteria} tidak ada";
                    continue;
                }

                if ($nilai <= 0) {
                    $invalidCriteria[] = "Nilai {$kodeKriteria} harus > 0 (got: {$nilai})";
                    continue;
                }

                $nilaiPangkat = pow((float)$nilai, (float)$kriteria->bobot);
                $detailS[$kodeKriteria] = [
                    'nilai' => $nilai,
                    'bobot' => $kriteria->bobot,
                    'hasil' => $nilaiPangkat
                ];

                $s *= $nilaiPangkat;
            }

            if (!empty($invalidCriteria)) {
                $invalidEntries[] = "Nasabah {$nasabah->nama}: " . implode(', ', $invalidCriteria);
                continue;
            }

            $vectorS[$nasabah->id] = [
                'value' => $s,
                'detail' => $detailS,
                'nasabah' => $nasabah
            ];
        }

        if (empty($vectorS)) {
            $errorMsg = "Tidak ada data penilaian yang valid untuk dihitung.\n";
            if (!empty($invalidEntries)) {
                $errorMsg .= implode("\n", $invalidEntries);
            }
            throw new \Exception($errorMsg);
        }

        $totalS = 0;
        foreach ($vectorS as $data) {
            $totalS += $data['value'];
        }

        // $totalS = 0;
        // $vectorSList = [];
        // $totalS = array_sum(array_column($vectorS, 'value')); // Hitung total vector S

        // foreach ($vectorS as $data) {
        //     $detail = [];
        //     foreach ($data['detail'] as $kode => $info) {
        //         $detail[] = [
        //             'kriteria' => $kode,
        //             'nilai' => $info['nilai'],
        //             'bobot' => $info['bobot'],
        //             'nilai^bobot' => $info['hasil']
        //         ];
        //     }

        //     $vector_s = $data['value'];
        //     $vector_v = $vector_s / $totalS;

        //     $vectorSList[] = [
        //         'nama' => $data['nasabah']->nama,
        //         'vector_s' => $vector_s,
        //         'vektor_v' => $vector_v,
        //         'detail_kriteria' => $detail
        //     ];
        // }

        // dd($vectorSList);

        // dd($totalS);

        foreach ($vectorS as $nasabahId => $data) {
            $s = $data['value'];
            $nasabah = $data['nasabah'];

            $v = $s / $totalS;

            $layak = $v >= 0.09;

            $results[] = [
                'nasabah_id' => $nasabah->id,
                'kode' => $nasabah->kode,
                'nama' => $nasabah->nama,
                'vektor_s' => $s,
                'vektor_v' => $v,
                'layak' => $layak,
                'keterangan' => $layak ? 'Layak' : 'Tidak Layak'
            ];
        }

        usort($results, function ($a, $b) {
            return $b['vektor_v'] <=> $a['vektor_v'];
        });

        return $results;
    }
}
