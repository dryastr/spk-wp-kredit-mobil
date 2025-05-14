<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\Nasabah;
use App\Models\Penilaian;
use Illuminate\Http\Request;

class PenilaianWPController extends Controller
{
    public function index()
    {
        $nasabahsForm = Nasabah::with('penilaian')->get();
        $nasabahs = Nasabah::has('penilaian')->with('penilaian')->get();
        $kriterias = Kriteria::orderBy('kode')->get();

        return view('admin.penilaian.index', compact('nasabahs', 'kriterias', 'nasabahsForm'));
    }

    public function store(Request $request)
    {
        $kriterias = Kriteria::all();

        $rules = ['nasabah_id' => 'required|exists:nasabahs,id'];
        $inputData = ['nasabah_id' => $request->nasabah_id];
        $nilai = [];

        foreach ($kriterias as $kriteria) {
            $fieldName = strtolower($kriteria->kode);
            $rules[$fieldName] = 'required|numeric|min:1';

            $nilai[$kriteria->kode] = $request->input($fieldName);
        }

        $request->validate($rules);

        if (Penilaian::where('nasabah_id', $request->nasabah_id)->exists()) {
            return redirect()->back()->with('error', 'Penilaian untuk nasabah ini sudah ada.');
        }

        Penilaian::create([
            'nasabah_id' => $request->nasabah_id,
            'nilai' => $nilai
        ]);

        return redirect()->route('penilaian.index')->with('success', 'Penilaian berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $kriterias = Kriteria::all();

        $rules = ['nasabah_id' => 'required|exists:nasabahs,id'];
        $nilai = [];

        foreach ($kriterias as $kriteria) {
            $fieldName = strtolower($kriteria->kode);
            $rules[$fieldName] = 'required|numeric|min:1';
            $nilai[$kriteria->kode] = $request->input($fieldName);
        }

        $request->validate($rules);

        $penilaian = Penilaian::findOrFail($id);
        $penilaian->update([
            'nasabah_id' => $request->nasabah_id,
            'nilai' => $nilai
        ]);

        return redirect()->route('penilaian.index')->with('success', 'Penilaian berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $penilaian = Penilaian::findOrFail($id);
        $penilaian->delete();

        return redirect()->route('penilaian.index')->with('success', 'Penilaian berhasil dihapus.');
    }
}
