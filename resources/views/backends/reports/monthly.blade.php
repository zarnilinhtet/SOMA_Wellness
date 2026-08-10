@include('master.header')
@include('master.sidebar')
@include('master.nav')

@php
    // Predefine an array of attractive theme colors for random assignment
    $randomColors = ['bg-primary', 'bg-success', 'bg-danger', 'bg-warning text-dark', 'bg-info text-dark', 'bg-secondary', 'bg-dark'];
@endphp

<div class="container py-4">
    <div class="page-inner">
        
        {{-- Header & Date Filter Section --}}
        <div class="mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <h4 class="fw-bold mb-1">Comprehensive System Report</h4>
                    <p class="text-muted small mb-0">Track your package sales, revenue progression, channels, and category breakdown.</p>
                </div>
                <div class="bg-white px-4 py-2 rounded-pill shadow-sm border">
                    <span class="text-muted small me-2">Year:</span>
                    <span class="fw-bold text-primary">{{ $currentYear ?? \Carbon\Carbon::now()->year }}</span>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <form action="{{ url()->current() }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label fw-bold text-muted small text-uppercase">Date From</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-calendar-alt text-muted"></i></span>
                            <input type="date" class="form-control border-start-0 ps-0" id="start_date" name="start_date" value="{{ request('start_date') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label fw-bold text-muted small text-uppercase">Date To</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-calendar-alt text-muted"></i></span>
                            <input type="date" class="form-control border-start-0 ps-0" id="end_date" name="end_date" value="{{ request('end_date') }}">
                        </div>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4 py-2 fw-bold rounded-3 shadow-sm flex-grow-1">
                            <i class="fas fa-filter me-2"></i> Apply Filter
                        </button>
                        <a href="{{ url()->current() }}" class="btn btn-light px-4 py-2 fw-bold rounded-3 text-secondary border" title="Clear Filters">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Top Level KPI Cards --}}
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted small text-uppercase fw-bold mb-1">Total Packages Sold</h6>
                            <h2 class="fw-bolder text-primary mb-0">{{ array_sum($packageCounts ?? []) }}</h2>
                        </div>
                        <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center">
                            <i class="fas fa-boxes fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted small text-uppercase fw-bold mb-1">Total Revenue</h6>
                            <h2 class="fw-bolder text-success mb-0">${{ number_format(array_sum($totalRevenues ?? []), 2) }}</h2>
                        </div>
                        <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center">
                            <i class="fas fa-dollar-sign fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment Method Summary Section --}}
        <div class="row g-4 mb-5">
            @forelse ($paymentSummary ?? [] as $payment)
                @php 
                    $randomBg = $randomColors[array_rand($randomColors)]; 
                @endphp
                <div class="col-md-4 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 p-4 payment-card h-100">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-shape {{ $randomBg }} rounded-3 d-flex align-items-center justify-content-center me-3 text-white flex-shrink-0 shadow-sm">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0 small text-uppercase fw-bold">{{ $payment->payment_method }}</h6>
                            </div>
                        </div>

                        <h3 class="fw-bolder text-dark mb-1">
                            {{ number_format($payment->total_amount, 2) }}
                        </h3>
                        <span class="badge bg-primary bg-opacity-10 text-primary small fw-bold px-2 py-1 rounded-pill">Total Received</span>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted h-100 bg-light">
                        <i class="fas fa-receipt fa-2x mb-3 text-secondary"></i>
                        <h6 class="fw-bold">No Payment Data</h6>
                        <p class="small mb-0">Try adjusting your date range to see results.</p>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Charts Section --}}
        <div class="row g-4 mb-5">
            <div class="col-xl-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h6 class="text-muted small text-uppercase fw-bold mb-1">Packages Sold</h6>
                            <h3 class="fw-bolder text-dark mb-0">Volume (Monthly)</h3>
                        </div>
                        <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center">
                            <i class="fas fa-boxes fa-lg"></i>
                        </div>
                    </div>
                    <div class="chart-container" style="position: relative; height: 320px;">
                        <canvas id="packagesChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h6 class="text-muted small text-uppercase fw-bold mb-1">Revenue Earned</h6>
                            <h3 class="fw-bolder text-dark mb-0">Amount (Monthly)</h3>
                        </div>
                        <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center">
                            <i class="fas fa-dollar-sign fa-lg"></i>
                        </div>
                    </div>
                    <div class="chart-container" style="position: relative; height: 320px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-xl-12">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h6 class="text-muted small text-uppercase fw-bold mb-1">Category Analysis</h6>
                            <h3 class="fw-bolder text-dark mb-0">Packages Sold by Category</h3>
                        </div>
                        <div class="icon-shape bg-warning bg-opacity-10 text-warning rounded-3 d-flex align-items-center justify-content-center">
                            <i class="fas fa-tags fa-lg"></i>
                        </div>
                    </div>
                    <div class="chart-container" style="position: relative; height: 320px;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Data Tables & Lists Section --}}
        <div class="row g-4 mb-5">
            {{-- Monthly Breakdown Table --}}
            <div class="col-xl-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 py-4 px-4">
                        <h5 class="fw-bold mb-0">Monthly Breakdown</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4 py-3 text-uppercase x-small text-muted fw-bold border-0">Month</th>
                                        <th class="px-4 py-3 text-uppercase x-small text-muted fw-bold border-0">Packages Sold</th>
                                        <th class="px-4 py-3 text-uppercase x-small text-muted fw-bold border-0">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($months))
                                        @foreach ($months as $index => $month)
                                            <tr>
                                                <td class="px-4 py-3 fw-semibold border-light">{{ $month }}</td>
                                                <td class="px-4 py-3 border-light">{{ $packageCounts[$index] ?? 0 }}</td>
                                                <td class="px-4 py-3 text-success fw-bold border-light">
                                                    {{ number_format($totalRevenues[$index] ?? 0, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">No monthly data available.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- User Acquisition Channels --}}
            <div class="col-xl-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 py-4 px-4">
                        <h5 class="fw-bold mb-1">User Acquisition Channels</h5>
                        <p class="text-muted small mb-0">Where your users are discovering the platform.</p>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 border-white">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3 text-uppercase x-small text-muted fw-bold border-0">Source / Channel</th>
                                        <th class="pe-4 py-3 text-end text-uppercase x-small text-muted fw-bold border-0">Total Users</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    @forelse($reportKnow ?? [] as $item)
                                        <tr>
                                            <td class="ps-4 py-3 fw-bold text-dark border-light">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                                        <i class="fas fa-bullhorn small"></i>
                                                    </div>
                                                    {{ $item->channel }}
                                                </div>
                                            </td>
                                            <td class="pe-4 py-3 text-end border-light">
                                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill font-monospace fs-6 shadow-sm">
                                                    {{ number_format($item->total_users) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center py-5 text-muted border-light">
                                                <i class="fas fa-info-circle fa-2x mb-2 d-block text-light-muted"></i> 
                                                No referral source data found for this period.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Selling Packages Section --}}
        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1">Top Selling Packages</h4>
                    <p class="text-muted small mb-0">Your best performing packages by sales volume.</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="list-group list-group-flush">
                    @if(isset($topPackages) && count($topPackages) > 0)
                        @foreach ($topPackages as $index => $package)
                            @php 
                                $randomPackageBg = $randomColors[array_rand($randomColors)]; 
                            @endphp
                            <div class="list-group-item d-flex align-items-center justify-content-between p-3 py-4 modern-list-item position-relative border-bottom border-light">

                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-shape {{ $randomPackageBg }} rounded-3 text-white d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm">
                                        <i class="fas 
                                            @if($index === 0) fa-trophy text-warning
                                            @elseif($index === 1) fa-medal text-light
                                            @elseif($index === 2) fa-award text-warning
                                            @else fa-box-open @endif fa-lg"></i>
                                    </div>

                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark">{{ $package->name }}</h6>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-4">
                                    <div class="text-end d-none d-md-block">
                                        <span class="text-muted d-block x-small text-uppercase fw-bold">Price</span>
                                        <span class="fw-bolder text-dark">${{ number_format($package->price ?? 0, 2) }}</span>
                                    </div>

                                    <div class="sales-badge bg-light border px-3 py-2 rounded-pill text-secondary fw-bold small flex-shrink-0 shadow-sm">
                                        Sold: <span class="text-dark fs-6">{{ $package->purchases_count ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5 text-muted bg-light">
                            <i class="fas fa-shopping-bag fa-3x mb-3 d-block text-secondary opacity-50"></i> 
                            <h6 class="fw-bold">No sales data found</h6>
                            <p class="small mb-0">Try expanding your date range.</p>
                        </div>
                    @endif
                </div>
            </div>

            @if(isset($topPackages) && method_exists($topPackages, 'hasPages') && $topPackages->hasPages())
                <div class="mt-4 d-flex justify-content-end">
                    {{ $topPackages->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>

    </div>
</div>

@include('master.footer')

<style>
    body {
        background-color: #f8f9fa;
    }

    .x-small {
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    .icon-shape {
        width: 48px;
        height: 48px;
    }

    .modern-list-item {
        transition: all 0.25s ease-in-out;
    }

    .modern-list-item:hover {
        background-color: #f8faff !important;
        transform: translateX(4px);
    }

    .sales-badge {
        min-width: 95px;
        text-align: center;
        transition: all 0.2s;
    }

    .payment-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .payment-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.1) !important;
    }

    .form-control:focus {
        box-shadow: none;
        border-color: #0d6efd;
    }
    
    .input-group-text {
        background-color: #f8f9fa;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const months = @json($months ?? []);
        const packageCounts = @json($packageCounts ?? []);
        const totalRevenues = @json($totalRevenues ?? []);
        const categories = @json($categories ?? []);
        const categoryCounts = @json($categoryCounts ?? []);

        const packagesCanvas = document.getElementById('packagesChart');
        const revenueCanvas = document.getElementById('revenueChart');
        const categoryCanvas = document.getElementById('categoryChart');

        if (!packagesCanvas || !revenueCanvas || !categoryCanvas) {
            console.warn('Chart canvas elements not found or incomplete.');
            return;
        }

        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#fff',
                    titleColor: '#333',
                    bodyColor: '#666',
                    borderColor: '#ddd',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#6c757d' } },
                y: { beginAtZero: true, ticks: { color: '#6c757d' }, grid: { color: 'rgba(0,0,0,0.06)' } }
            }
        };

        // 1. Packages Sold Chart (Bar)
        if(months.length > 0) {
            new Chart(packagesCanvas, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Packages Sold',
                        data: packageCounts,
                        backgroundColor: 'rgba(54, 162, 235, 0.65)',
                        hoverBackgroundColor: 'rgba(54, 162, 235, 0.9)',
                        borderRadius: 10,
                        maxBarThickness: 40
                    }]
                },
                options: chartOptions
            });
        }

        // 2. Revenue Chart (Line / Area)
        if(months.length > 0) {
            const ctx = revenueCanvas.getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 320);
            gradient.addColorStop(0, 'rgba(25, 135, 84, 0.45)');
            gradient.addColorStop(1, 'rgba(25, 135, 84, 0.02)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Revenue',
                        data: totalRevenues,
                        borderColor: '#198754',
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#198754',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: chartOptions
            });
        }

        // 3. Packages Sold by Category Chart (Bar Chart)
        if(categories.length > 0) {
            new Chart(categoryCanvas, {
                type: 'bar',
                data: {
                    labels: categories,
                    datasets: [{
                        label: 'Sold by Category',
                        data: categoryCounts,
                        backgroundColor: 'rgba(255, 193, 7, 0.75)',
                        hoverBackgroundColor: 'rgba(255, 193, 7, 1)',
                        borderRadius: 10,
                        maxBarThickness: 50
                    }]
                },
                options: chartOptions
            });
        }
    });
</script>