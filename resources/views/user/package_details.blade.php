@include('master.header')
@include('master.sidebar')
@include('master.nav')

<style>
    .summary-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        transition: transform 0.3s ease;
    }
    .summary-card:hover {
        transform: translateY(-5px);
    }
    .icon-box {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .bg-light-primary { background-color: #e0f2fe; color: #0284c7; }
    .bg-light-success { background-color: #dcfce7; color: #16a34a; }
    .bg-light-warning { background-color: #fef08a; color: #ca8a04; }
    .bg-light-info { background-color: #e0e7ff; color: #4f46e5; }
    
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.8rem;
    }
    .status-active { background-color: #dcfce7; color: #16a34a; }
    .status-completed { background-color: #e0e7ff; color: #4f46e5; }
    .status-expired { background-color: #fee2e2; color: #dc2626; }
</style>

<div class="container">
    <div class="page-inner">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="fas fa-user-circle me-2 text-primary"></i> {{ $user->name ?? 'User' }}'s Package Details
                </h3>
                <span class="text-muted">Contact: {{ $user->phone ?? 'N/A' }} | Role: {{ optional($user->roles)->first()->name ?? 'Customer' }}</span>
            </div>
            <a href="{{ route('user.with_packages') }}" class="btn btn-outline-secondary rounded-pill shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>

        <!-- Summary Cards Section -->
        <div class="row g-4 mb-4">
            <!-- Total Packages -->
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card summary-card h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-box bg-light-primary me-3">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 fw-semibold">Total Packages Bought</p>
                            <h3 class="fw-bold mb-0">{{ $totalPackages }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Allowed Classes -->
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card summary-card h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-box bg-light-info me-3">
                            <i class="fas fa-list-ol"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 fw-semibold">Total Classes Quota</p>
                            <h3 class="fw-bold mb-0">{{ $totalClassesAllowed }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Used Classes -->
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card summary-card h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-box bg-light-warning me-3">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 fw-semibold">Classes Used / Booked</p>
                            <h3 class="fw-bold mb-0">{{ $totalClassesUsed }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Remaining Classes -->
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card summary-card h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="icon-box bg-light-success me-3">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 fw-semibold">Total Remaining Classes</p>
                            <h3 class="fw-bold mb-0 text-success">{{ $totalClassesRemaining }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Table Section -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title fw-bold mb-0"><i class="fas fa-history text-primary me-2"></i> Purchase History & Breakdown</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">No.</th>
                                <th>Package Info</th>
                                <th>Category</th>
                                <th class="text-center">Total Classes</th>
                                <th class="text-center">Used</th>
                                <th class="text-center">Remaining</th>
                                <th>Expiry Dates</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($purchases as $purchase)
                                <tr>
                                    <td class="px-4 fw-bold text-muted">{{ $loop->iteration }}</td>
                                    
                                    <td>
                                        <div class="fw-bold text-dark">{{ $purchase->package->name ?? 'Unknown Package' }}</div>
                                        <small class="text-muted">Purchased on: {{ \Carbon\Carbon::parse($purchase->created_at)->format('d M Y') }}</small>
                                    </td>
                                    
                                    <td>
                                        <span class="badge bg-secondary">{{ $purchase->package->category->name ?? $purchase->package->type ?? 'N/A' }}</span>
                                    </td>
                                    
                                    <td class="text-center fw-bold fs-5">{{ $purchase->package->class_count ?? 0 }}</td>
                                    
                                    <td class="text-center fw-bold fs-5 text-warning">{{ $purchase->used_classes }}</td>
                                    
                                    <td class="text-center fw-bold fs-5 text-success">{{ $purchase->class_remaining }}</td>
                                    
                                    <td>
                                        @if($purchase->expires_at)
                                            <div class="small"><i class="fas fa-calendar-times text-danger"></i> Exp: {{ \Carbon\Carbon::parse($purchase->expires_at)->format('d M Y') }}</div>
                                        @endif
                                        @if($purchase->fix_expires_at)
                                            <div class="small"><i class="fas fa-hourglass-end text-warning"></i> Fix Exp: {{ \Carbon\Carbon::parse($purchase->fix_expires_at)->format('d M Y') }}</div>
                                        @endif
                                        @if(!$purchase->expires_at && !$purchase->fix_expires_at)
                                            <span class="text-muted small">No Expiry Date</span>
                                        @endif
                                    </td>
                                    
                                    <td>
                                        @if($purchase->current_status == 'Active')
                                            <span class="status-badge status-active"><i class="fas fa-check-circle me-1"></i> Active</span>
                                        @elseif($purchase->current_status == 'Completed')
                                            <span class="status-badge status-completed"><i class="fas fa-flag-checkered me-1"></i> Completed</span>
                                        @else
                                            <span class="status-badge status-expired"><i class="fas fa-times-circle me-1"></i> Expired</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="fas fa-box-open fs-1 mb-3 d-block text-light"></i>
                                        <h4>No purchased packages found.</h4>
                                        <p>This user hasn't bought any packages yet or purchases are pending.</p>
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

@include('master.footer')