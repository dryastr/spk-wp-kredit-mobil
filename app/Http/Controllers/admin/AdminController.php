<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\HasilWP;
use App\Models\Nasabah;
use App\Models\Kriteria;
use App\Models\Penilaian;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $totalNasabah = Nasabah::count();
        $totalLayak = HasilWP::where('layak', true)->count();
        $totalTidakLayak = HasilWP::where('layak', false)->count();
        $averageScore = HasilWP::avg('vektor_v');

        $topApplicants = HasilWP::with('nasabah')
            ->orderBy('vektor_v', 'desc')
            ->take(5)
            ->get();

        $eligibilityDistribution = [
            'Layak' => $totalLayak,
            'Tidak Layak' => $totalTidakLayak
        ];

        $recentEvaluations = HasilWP::with('nasabah')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalNasabah',
            'totalLayak',
            'totalTidakLayak',
            'averageScore',
            'topApplicants',
            'eligibilityDistribution',
            'recentEvaluations'
        ));
    }
}
