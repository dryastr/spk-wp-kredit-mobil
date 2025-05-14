<?php

namespace App\Helpers;

use App\Models\Kriteria;

class KriteriaHelper
{
    public static function getKriteriaText($kode, $value)
    {
        $kriteria = Kriteria::where('kode', $kode)->first();

        if (!$kriteria || empty($kriteria->keterangan)) {
            return '';
        }

        // Handle jika keterangan sudah array atau masih JSON
        $options = self::parseKeterangan($kriteria->keterangan);

        return $options[$value] ?? '';
    }

    protected static function parseKeterangan($keterangan)
    {
        if (is_array($keterangan)) {
            return $keterangan;
        }

        if (is_string($keterangan)) {
            $decoded = json_decode($keterangan, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }
}
