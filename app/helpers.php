<?php

use App\Models\Kriteria;

if (!function_exists('getKriteriaText')) {
    /**
     * Mendapatkan teks keterangan kriteria berdasarkan kode dan value
     *
     * @param string $kode Kode kriteria
     * @param mixed $value Nilai yang dicari
     * @return string
     */
    function getKriteriaText($kode, $value)
    {
        $kriteria = Kriteria::where('kode', $kode)->first();

        if (!$kriteria || !$kriteria->keterangan) {
            return '';
        }

        $options = json_decode($kriteria->keterangan, true);
        return $options[$value] ?? '';
    }
}
