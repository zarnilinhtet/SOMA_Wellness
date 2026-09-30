@include('master.header')
@include('master.sidebar')
@include('master.nav')

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<style>
    body, html, .page-inner { overflow-y: auto !important; }
    
    .badge-custom { padding: 0.45rem 1rem; border-radius: 50rem; font-size: 0.85rem; font-weight: 600; }
    .status-expired { background-color: #fee2e2; color: #ef4444; border: 1px solid #fecaca; }
    .status-warning { background-color: #fef08a; color: #a16207; border: 1px solid #fde047; }
    .status-safe { background-color: #dcfce7; color: #22c55e; border: 1px solid #bbf7d0; }

    .table-scroll-container { max-height: 65vh; overflow-y: auto; border-bottom: 1px solid #dee2e6; }
    #expiring-datatables thead th { position: sticky; top: 0; background: #f8fafc; z-index: 10; border-bottom: 2px solid #e2e8f0;}
    
    .filter-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 20px; }
</style>

<div class="container">
    <div class="page-inner">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark mb-0">
                <i class="fas fa-clock text-warning me-2"></i> Expiring Packages List
            </h4>
          
        </div>

        <!-- Date Filter Form -->
        <div class="filter-card shadow-sm">
            <form method="GET" action="{{ route('user.expiring.packages') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small"><i class="fas fa-calendar-alt me-1"></i> Start Date</label>
                        <input type="date" name="start_date" class="form-control form-control-lg shadow-sm" value="{{ $startDate }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small"><i class="fas fa-calendar-check me-1"></i> End Date</label>
                        <input type="date" name="end_date" class="form-control form-control-lg shadow-sm" value="{{ $endDate }}" required>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm w-100 fw-bold">
                            <i class="fas fa-search me-1"></i> Filter
                        </button>
                        <a href="{{ route('user.expiring.packages') }}" class="btn btn-light btn-lg border shadow-sm" title="Reset Filters">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive table-scroll-container">
                    <table id="expiring-datatables" class="display table table-hover align-middle w-100 mb-0">
                        <thead>
                            <tr>
                                <th style="width: 5%">No.</th>
                                <th>Client Name</th>
                                <th>Phone</th>
                                <th>Package Name</th>
                                <th class="text-center">Remaining Classes</th>
                                <th>Expiry Date</th>
                                <th class="text-center">Days Left</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($expiringPackages as $purchase)
                                @php
                                    $daysLeft = $purchase->days_left;
                                    
                                    if ($daysLeft < 0) {
                                        $badgeClass = 'status-expired';
                                        $statusText = 'Expired';
                                        $icon = 'fa-times-circle';
                                    } elseif ($daysLeft <= 7) {
                                        $badgeClass = 'status-warning';
                                        $statusText = 'Expiring Soon';
                                        $icon = 'fa-exclamation-triangle';
                                    } else {
                                        $badgeClass = 'status-safe';
                                        $statusText = 'Active';
                                        $icon = 'fa-check-circle';
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center text-muted">{{ $loop->iteration }}</td>
                                    
                                    <!-- User Info -->
                                    <td class="fw-bold text-dark">
                                        {{ $purchase->user->name ?? 'Unknown User' }}
                                    </td>
                                    <td>
                                        <a href="tel:{{ $purchase->user->phone ?? '' }}" class="text-decoration-none text-primary">
                                            {{ $purchase->user->phone ?? 'N/A' }}
                                        </a>
                                    </td>
                                    
                                    <!-- Package Info -->
                                    <td>
                                        <span class="text-secondary fw-semibold">{{ $purchase->package->name ?? 'Unknown Package' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary rounded-pill px-3 py-2 fs-6">
                                            {{ $purchase->remaining }} Classes
                                        </span>
                                    </td>
                                    
                                    <!-- Date Info -->
                                    <td class="text-nowrap fw-bold text-secondary">
                                        {{ Carbon\Carbon::parse($purchase->actual_expiry_date)->format('d M, Y') }}
                                    </td>
                                    <td class="text-center fw-bolder fs-6 {{ $daysLeft <= 7 ? 'text-danger' : 'text-success' }}">
                                        @if($daysLeft < 0)
                                            Overdue ({{ abs($daysLeft) }} Days)
                                        @else
                                            {{ $daysLeft }} Days
                                        @endif
                                    </td>
                                    
                                    <!-- Status -->
                                    <td>
                                        <span class="badge-custom {{ $badgeClass }}">
                                            <i class="fas {{ $icon }} me-1"></i> {{ $statusText }}
                                        </span>
                                    </td>
                                    
                                    <!-- Actions -->
                                    <td class="text-center">
                                        <a href="{{ route('user.package.details', $purchase->user_id ?? $purchase->registered_id) }}" 
                                           class="btn btn-sm btn-outline-info shadow-sm rounded-pill" title="View Full Details">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="fas fa-check-circle fs-1 text-success mb-3 d-block"></i>
                                        <h5 class="fw-bold">No Expiring Packages Found</h5>
                                        <p class="small mb-0">ရွေးချယ်ထားသော ရက်စွဲအတွင်း သက်တမ်းကုန်ဆုံးမည့် Package များ မရှိပါ။</p>
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

<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script>
    $(document).ready(function () {
        $('#expiring-datatables').DataTable({
            "pageLength": 15,
            "order": [], // Keeps controller sorting (nearest expiry first)
            "columnDefs": [
                { "targets": [8], "orderable": false } // Disable sorting on Action column
            ],
            "language": {
                "search": "Search User/Package:",
                "info": "Showing _START_ to _END_ of _TOTAL_ expiring records"
            }
        });
    });
</script>