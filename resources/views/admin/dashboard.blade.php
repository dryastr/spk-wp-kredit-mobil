@extends('layouts.main')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Nasabah</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalNasabah }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Layak</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalLayak }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Tidak Layak</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTidakLayak }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Rata-rata Skor</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($averageScore, 4) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">5 Nasabah Terbaik</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="topApplicantsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Distribusi Kelayakan</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="eligibilityChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Layak
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> Tidak Layak
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Evaluasi Terbaru</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Nasabah</th>
                                    <th>Vektor S</th>
                                    <th>Vektor V</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentEvaluations as $key => $evaluation)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $evaluation->nasabah->nama }}</td>
                                        <td>{{ number_format($evaluation->vektor_s, 4) }}</td>
                                        <td>{{ number_format($evaluation->vektor_v, 4) }}</td>
                                        <td>
                                            @if ($evaluation->layak)
                                                <span class="badge badge-success">Layak</span>
                                            @else
                                                <span class="badge badge-warning">Tidak Layak</span>
                                            @endif
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
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        var ctx = document.getElementById("topApplicantsChart");
        var topApplicantsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach ($topApplicants as $applicant)
                        "{{ $applicant->nasabah->nama }}",
                    @endforeach
                ],
                datasets: [{
                    label: "Skor V",
                    backgroundColor: "#4e73df",
                    hoverBackgroundColor: "#2e59d9",
                    borderColor: "#4e73df",
                    data: [
                        @foreach ($topApplicants as $applicant)
                            {{ $applicant->vektor_v }},
                        @endforeach
                    ],
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        var ctx2 = document.getElementById("eligibilityChart");
        var eligibilityChart = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ["Layak", "Tidak Layak"],
                datasets: [{
                    data: [{{ $eligibilityDistribution['Layak'] }},
                        {{ $eligibilityDistribution['Tidak Layak'] }}
                    ],
                    backgroundColor: ['#1cc88a', '#f6c23e'],
                    hoverBackgroundColor: ['#17a673', '#dda20a'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                cutoutPercentage: 80,
            },
        });
    </script>
@endpush
