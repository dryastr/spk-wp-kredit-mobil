<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class KriteriaController extends Controller
{
    public function index()
    {
        $lastKriteria = Kriteria::orderBy('kode', 'desc')->first();
        $nextCode = 'C1';

        if ($lastKriteria) {
            $lastNumber = (int) substr($lastKriteria->kode, 1);
            $nextCode = 'C' . ($lastNumber + 1);
        }

        $kriterias = Kriteria::orderBy('kode')->get();
        return view('admin.kriteria.index', compact('kriterias', 'nextCode'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'bobot' => str_replace(',', '.', $request->bobot)
        ]);

        $validator = Validator::make($request->all(), [
            'kode' => 'required|unique:kriterias|max:10',
            'nama' => 'required|max:100',
            'bobot' => 'required|numeric|min:0.01|max:1',
            'keterangan_keys' => 'sometimes|array',
            'keterangan_values' => 'sometimes|array'
        ]);

        if ($validator->fails()) {
            // return redirect()->back()->withErrors($validator) $keterangan = []->withInput();
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $keterangan = [];
        if ($request->has('keterangan_keys')) {
            foreach ($request->keterangan_keys as $index => $key) {
                if (!empty($key)) {
                    $value = $request->keterangan_values[$index] ?? '';
                    $keterangan[$key] = $value;
                }
            }
        }

        $keteranganJson = null;
        if (!empty($keterangan)) {
            $formattedKeterangan = [];
            foreach ($keterangan as $key => $value) {
                $formattedKeterangan[$key] = "{$value} ({$key})";
            }

            $keteranganJson = json_encode($formattedKeterangan, JSON_UNESCAPED_SLASHES);
            $keteranganJson = str_replace('":', '": ', $keteranganJson);
        }


        $currentBobot = round((float) $request->bobot, 2);
        $totalBobot = round(Kriteria::sum('bobot') + $currentBobot, 2);

        if ($totalBobot > 1.0) {
            $currentTotal = Kriteria::sum('bobot');
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'bobot' => "Total bobot tidak boleh melebihi 1.0. Total saat ini: {$currentTotal}."
                ]);
        }

        $kriteria = Kriteria::create([
            'kode' => $request->kode,
            'nama' => $request->nama,
            'bobot' => $currentBobot,
            'keterangan' => $formattedKeterangan
        ]);

        return redirect()->route('kriteria.index')
            ->with('success', 'Kriteria berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $kriteria = Kriteria::findOrFail($id);

        $oldBobot = $kriteria->bobot;

        $request->merge([
            'bobot' => str_replace(',', '.', $request->bobot)
        ]);

        $validator = Validator::make($request->all(), [
            'kode' => 'required|max:10',
            'nama' => 'required|max:100',
            'bobot' => 'required|numeric|min:0.01|max:1',
            'keterangan_keys' => 'sometimes|array',
            'keterangan_values' => 'sometimes|array'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $currentBobot = round((float) $request->bobot, 2);
        $totalBobot = round(Kriteria::sum('bobot') - $oldBobot + $currentBobot, 2);

        if ($totalBobot > 1.0) {
            $currentTotal = round(Kriteria::sum('bobot') - $oldBobot, 2);
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'bobot' => "Total bobot tidak boleh melebihi 1.0. Total saat ini: {$currentTotal}."
                ]);
        }

        $keterangan = [];
        if ($request->has('keterangan_keys')) {
            foreach ($request->keterangan_keys as $index => $key) {
                if (!empty($key)) {
                    $value = $request->keterangan_values[$index] ?? '';
                    $keterangan[$key] = "{$value} ({$key})";
                }
            }
        }

        $bobotDifference = $oldBobot - $currentBobot;
        Log::info("Perubahan bobot Kriteria ID {$kriteria->id}: Sebelumnya {$oldBobot}, Sekarang {$currentBobot}, Selisih: {$bobotDifference}");

        $updateStatus = $kriteria->update([
            'kode' => $request->kode,
            'nama' => $request->nama,
            'bobot' => $currentBobot,
            'keterangan' => !empty($keterangan) ? $keterangan : null
        ]);

        if ($updateStatus) {
            Log::info("Kriteria ID {$kriteria->id} berhasil diupdate dengan bobot {$currentBobot}");
        } else {
            Log::warning("GAGAL update Kriteria ID {$kriteria->id}");
        }

        return redirect()->route('kriteria.index')
            ->with('success', 'Kriteria berhasil diperbarui');
    }


    public function destroy(Kriteria $kriteria)
    {
        $kriteria->delete();
        return redirect()->route('kriteria.index')
            ->with('success', 'Kriteria berhasil dihapus');
    }
}
