@include('master.header')
@include('master.sidebar')
@include('master.nav')

<!-- Select2 & DataTables CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<style>
    body, html, .page-inner { overflow-y: auto !important; }
    
    .btn-beauty { background-color: #eef5f4; color: #5c8d89; border: 1px solid #d1e5e3; border-radius: 8px; font-weight: 600; padding: 0.35rem 0.8rem; transition: all 0.3s ease; }
    .btn-beauty:hover { background-color: #5c8d89; color: #ffffff; transform: translateY(-2px); box-shadow: 0 6px 12px rgba(92, 141, 137, 0.2); }

    .badge-custom { padding: 0.45rem 1rem; border-radius: 50rem; font-size: 0.85rem; font-weight: 600; }
    .role-admin { background-color: #fee2e2; color: #ef4444; border: 1px solid #fecaca; }
    .role-instructor { background-color: #e0f2fe; color: #3b82f6; border: 1px solid #bae6fd; }
    .role-customer { background-color: #dcfce7; color: #22c55e; border: 1px solid #bbf7d0; }
    
    .status-active { background-color: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; }
    .status-completed { background-color: #ffedd5; color: #c2410c; border: 1px solid #fed7aa; }
    
    .badge-attended { background-color: #dcfce7; color: #166534; }
    .badge-noshow { background-color: #fee2e2; color: #991b1b; }

    .table-scroll-container { max-height: 65vh; overflow-y: auto; border-bottom: 1px solid #dee2e6; }
    #basic-datatables thead th { position: sticky; top: 0; background: #fff; z-index: 10; }
    
    .count-box { border-radius: 12px; border: 1px solid #e2e8f0; padding: 15px; text-align: center; }
    .count-box.total { background: #f8fafc; }
    .count-box.used { background: #fffbeb; border-color: #fde68a; }
    .count-box.remain { background: #f0fdf4; border-color: #bbf7d0; }
    
    .select2-container .select2-selection--single { height: 38px !important; border: 1px solid #dee2e6; border-radius: 6px; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 38px; color: #495057; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; }
</style>

<div class="container">
    <div class="page-inner">

        <!-- Success Message -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center py-2 px-4 rounded-pill mb-4" role="alert">
                <i class="fas fa-check-circle me-2 fs-5"></i>
                <span class="fw-bold">{{ session('success') }}</span>
                <button type="button" class="btn-close mt-1" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <!-- Header & Filters -->
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                    <h4 class="card-title mb-0 fw-bold text-dark">
                        <i class="fas fa-gem text-primary me-2"></i> Users With Packages
                    </h4>
                </div>
                
                <div class="row g-2 align-items-end">
                    <!-- Status Filter -->
                    <div class="col-md-4">
                        <label class="fw-bold text-muted small mb-1">Package Status</label>
                        <select id="packageStatusFilter" class="form-select shadow-sm" style="height: 38px; border-radius: 6px;">
                            <option value="">All Status</option>
                            <option value="Using Package">Active (Using Package)</option>
                            <option value="Completed">Needs Follow-up (Completed)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table Body -->
            <div class="card-body">
                <div class="table-responsive table-scroll-container">
                    <table id="basic-datatables" class="display table table-striped table-hover align-middle w-100">
                        <thead>
                            <tr>
                                <th style="width: 5%">No.</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Purchased Package(s)</th>
                                <!-- Hidden Column for Class Filter -->
                                <th class="d-none">Classes</th>
                                <th>Package Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-bold text-dark">{{ $user->name }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td>
                                        @if ($user->hasRole('Admin'))
                                            <span class="badge-custom role-admin">Admin</span>
                                        @elseif($user->hasRole('Instructor'))
                                            <span class="badge-custom role-instructor">Instructor</span>
                                        @else
                                            <span class="badge-custom role-customer">Customer</span>
                                        @endif
                                    </td>
                                    
                                    <!-- Purchased Packages Column -->
                                    <td>
                                        <span class="d-none">
                                            @foreach($user->purchases as $purchase)
                                                {{ $purchase->package ? $purchase->package->name : '' }}
                                            @endforeach
                                        </span>

                                        @php
                                            // Modal သို့ ပေးပို့မည့် Package Data များကို JSON ပြောင်းခြင်း (Null ပြဿနာ ဖြေရှင်းပြီး)
                                            $packageData = $user->purchases->map(function($p) {
                                                $totalClasses = $p->total_classes ?? ($p->package->class_count ?? 0);
                                                $remainingClasses = $p->remaining_classes ?? ($p->class_remaining ?? 0);
                                                
                                                return [
                                                    'name' => $p->package ? $p->package->name : 'Unknown Package',
                                                    'total' => $totalClasses,
                                                    'remain' => $remainingClasses,
                                                    'status' => $remainingClasses > 0 ? 'Active' : 'Completed'
                                                ];
                                            })->toJson();
                                        @endphp

                                        @if($user->purchases->count() > 0)
                                            <button class="btn btn-sm btn-outline-secondary view-packages-btn rounded-pill shadow-sm"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#packagesListModal"
                                                    data-name="{{ $user->name }}"
                                                    data-packages="{{ $packageData }}">
                                                <i class="fas fa-box-open me-1 text-primary"></i> View ({{ $user->purchases->count() }})
                                            </button>
                                        @else
                                            <span class="text-muted small">None</span>
                                        @endif
                                    </td>

                                    <!-- Classes (Hidden Search Column) -->
                                    <td class="d-none"></td>

                                    <!-- Package Status Column -->
                                    <td>
                                        @if($user->package_status == 'Using Package')
                                            <span class="badge-custom status-active"><i class="fas fa-check-circle me-1"></i> Using Package</span>
                                        @else
                                            <span class="badge-custom status-completed"><i class="fas fa-exclamation-circle me-1"></i> Completed</span>
                                        @endif
                                    </td>
                                    
                                    <!-- Actions Column -->
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-beauty btn-sm dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-cog me-1"></i> Options
                                            </button>
                                            <ul class="dropdown-menu border-0 shadow">
                                                <li><a class="dropdown-item text-primary" href="{{ route('user.package.details', $user->id) }}">Full Details</a></li>
                                                
                                                <!-- Attendance History Button -->
                                                <li>
                                                    <button class="dropdown-item text-info view-attendance" 
                                                            data-id="{{ $user->id }}" 
                                                            data-name="{{ $user->name }}"
                                                            data-total="{{ $user->total_classes ?? 0 }}"
                                                            data-used="{{ $user->used_classes ?? 0 }}"
                                                            data-remain="{{ $user->remaining_classes ?? 0 }}"
                                                            data-bs-toggle="modal" data-bs-target="#attendanceHistoryModal">
                                                        <i class="fas fa-history me-1"></i> Class History
                                                    </button>
                                                </li>

                                                @if(!$user->hasRole('Admin') && auth()->user()->hasPermission('user_register'))
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item" href="/give/discount/{{ $user->id }}">Give Discount</a></li>
                                                    <li><a class="dropdown-item" href="/buy/package/{{ $user->id }}">Buy Package</a></li>
                                                    <li><a class="dropdown-item" href="/join/class/for/user/{{ $user->id }}">Join Class</a></li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="fas fa-box-open fs-3 mb-2 d-block"></i> Package ဝယ်ယူထားသော User မရှိသေးပါ။
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

{{-- MODAL 1: Purchased Packages List --}}
<div class="modal fade" id="packagesListModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="fas fa-box-open text-primary me-2"></i> Packages for <span id="pkgModalUserName" class="text-info"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive p-3">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Package Name</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Remain</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="pkgModalTableBody">
                            <!-- JS ဖြင့် Data ဝင်လာပါမည် -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL 2: Class Attendance History & Count Number --}}
<div class="modal fade" id="attendanceHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="fas fa-history text-info me-2"></i> Class History: <span id="historyUserName" class="text-primary"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body p-4">
                <!-- Class Count Dashboard -->
                <div class="row g-3 mb-4">
                    <div class="col-4">
                        <div class="count-box total shadow-sm">
                            <span class="d-block text-muted small fw-bold text-uppercase mb-1">Total Classes</span>
                            <span class="fs-4 fw-bolder text-secondary" id="modTotalCount">0</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="count-box used shadow-sm">
                            <span class="d-block text-muted small fw-bold text-uppercase mb-1">Used</span>
                            <span class="fs-4 fw-bolder text-warning" id="modUsedCount">0</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="count-box remain shadow-sm">
                            <span class="d-block text-muted small fw-bold text-uppercase mb-1">Remaining</span>
                            <span class="fs-4 fw-bolder text-success" id="modRemainCount">0</span>
                        </div>
                    </div>
                </div>

                <!-- History Table -->
                <div class="table-responsive rounded border">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Class Name</th>
                                <th>Deduction Status</th>
                            </tr>
                        </thead>
                        <tbody id="attendanceTableBody">
                            <tr><td colspan="4" class="text-center text-muted py-3">Loading history...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@include('master.footer')

<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        // Init Select2 for Package Search Dropdown
        $('.select2-packages').select2({
            placeholder: "Search package...",
            width: '100%',
            allowClear: true
        });

        // Init Select2 for Class Search Dropdown
        $('.select2-classes').select2({
            placeholder: "Search class...",
            width: '100%',
            allowClear: true
        });

        // Init DataTables
        let table = $('#basic-datatables').DataTable({
            "pageLength": 10,
            "columnDefs": [
                { "targets": [7], "orderable": false } 
            ],
            "language": {
                "search": "Search Client Name:" 
            }
        });

        // DataTables Filters
        $('#packageSearchFilter').on('change', function() {
            let pkgName = $(this).val();
            table.column(4).search(pkgName).draw(); 
        });

        $('#classSearchFilter').on('change', function() {
            let className = $(this).val();
            table.column(5).search(className).draw(); 
        });

        $('#packageStatusFilter').on('change', function() {
            let status = $(this).val();
            table.column(6).search(status).draw(); 
        });

        // --------------------------------------------------------
        // 1. Package List Modal Logic
        // --------------------------------------------------------
        $('.view-packages-btn').on('click', function() {
            let userName = $(this).data('name');
            let packages = $(this).data('packages'); 
            
            $('#pkgModalUserName').text(userName);
            let tbody = $('#pkgModalTableBody');
            tbody.empty();
            
            if(packages && packages.length > 0) {
                packages.forEach(function(pkg) {
                    let statusBadge = pkg.status === 'Active' 
                        ? '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Active</span>' 
                        : '<span class="badge bg-warning text-dark"><i class="fas fa-exclamation-circle me-1"></i>Completed</span>';
                        
                    let row = `
                        <tr>
                            <td class="fw-bold text-dark">${pkg.name}</td>
                            <td class="text-center">${pkg.total}</td>
                            <td class="text-center fw-bold ${pkg.remain > 0 ? 'text-success' : 'text-danger'}">${pkg.remain}</td>
                            <td>${statusBadge}</td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            } else {
                tbody.append('<tr><td colspan="4" class="text-center text-muted py-3">No packages found.</td></tr>');
            }
        });

        // --------------------------------------------------------
        // 2. Class Attendance History Modal Logic
        // --------------------------------------------------------
        $('.view-attendance').on('click', function() {
            let userId = $(this).data('id');
            let userName = $(this).data('name');
            let tCount = $(this).data('total');
            let uCount = $(this).data('used');
            let rCount = $(this).data('remain');
            
            // Populate Counts in Modal Dashboard
            $('#historyUserName').text(userName);
            $('#modTotalCount').text(tCount);
            $('#modUsedCount').text(uCount);
            $('#modRemainCount').text(rCount);
            
            $('#attendanceTableBody').html('<tr><td colspan="4" class="text-center text-muted py-4"><div class="spinner-border spinner-border-sm me-2"></div> Loading...</td></tr>');

            // Database မချိတ်ရသေးမီ Testing အတွက် Mockup Data
            setTimeout(() => {
                let mockupData = `
                    <tr>
                        <td>12 Oct 2023</td>
                        <td>09:00 AM</td>
                        <td class="fw-bold text-dark">Yoga Beginner</td>
                        <td><span class="badge badge-custom badge-attended"><i class="fas fa-check me-1"></i> Joined</span></td>
                    </tr>
                    <tr>
                        <td>14 Oct 2023</td>
                        <td>10:00 AM</td>
                        <td class="fw-bold text-dark">Pilates Core</td>
                        <td><span class="badge badge-custom badge-noshow"><i class="fas fa-times me-1"></i> Booked (Class Not Joined)</span></td>
                    </tr>
                `;
                $('#attendanceTableBody').html(mockupData);
            }, 800);
        });
    });
</script>