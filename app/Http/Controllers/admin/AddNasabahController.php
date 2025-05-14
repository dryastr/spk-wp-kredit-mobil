<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Nasabah;
use Illuminate\Http\Request;

class AddNasabahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $nasabahs = Nasabah::all();
        $kode = $this->generateKode(); 
        return view('admin.nasabah.index', compact('nasabahs', 'kode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|max:100',
            'alamat' => 'required'
        ]);

        $kode = $this->generateKode();

        Nasabah::create(array_merge($validated, ['kode' => $kode]));

        return redirect()->route('nasabah.index')
            ->with('success', 'Nasabah berhasil ditambahkan dengan kode ' . $kode);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Nasabah $nasabah)
    {
        $validated = $request->validate([
            'kode' => 'required|max:10|unique:nasabahs,kode,' . $nasabah->id,
            'nama' => 'required|max:100',
            'alamat' => 'required'
        ]);

        $nasabah->update($validated);

        return redirect()->route('nasabah.index')
            ->with('success', 'Data nasabah berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Nasabah $nasabah)
    {
        $nasabah->delete();

        return redirect()->route('nasabah.index')
            ->with('success', 'Nasabah berhasil dihapus');
    }

    private function generateKode()
    {
        $lastNasabah = Nasabah::orderBy('id', 'desc')->first();
        $lastKode = $lastNasabah ? $lastNasabah->kode : 'A000';

        $number = intval(substr($lastKode, 1)) + 1;
        $newKode = 'A' . str_pad($number, 3, '0', STR_PAD_LEFT);

        return $newKode;
    }
}
