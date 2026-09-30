@include('master.header')
@include('master.sidebar')
@include('master.nav')

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<style>
    body, html, .page-inner { overflow-y: auto !important; }
    
    .badge-custom { padding: 0.45rem 1rem; border-radius: 50rem; font-size: 0.85rem; font-weight: 600; }
    .status-danger { background-color: #fee2e2; color: #ef4444; border: 1px solid #fecaca; }
    
    .table-scroll-container { max-height: 65vh; overflow-y: auto; border-bottom: 1px solid #dee2e6; }
    #absent-datatables thead th { position: sticky; top: 0; background: #f8fafc; z-index: 10; border-bottom: 2px solid #e2e8f0;}
    
    .filter-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 20px; }
</style>

<div class="container">
    <div class="page-inner">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark mb-0">
                <i class="fas fa-user-times text-danger me-2"></i> Absent Users
            </h4>
            <a href="{{ route('user.with_packages') }}" class="btn btn-outline-primary btn-sm rounded-pill shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to All Packages
            </a>
        </div>

        <!-- Date Filter Form -->
        <div class="filter-card shadow-sm mb-4">
            <form method="GET" action="{{ route('user.absent') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small"><i class="fas fa-calendar-alt me-1"></i> Last Attended From Date</label>
                        <input type="date" name="start_date" class="form-control form-control-lg shadow-sm" value="{{ $startDate ?? '' }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small"><i class="fas fa-calendar-check me-1"></i> Last Attended To Date</label>
                        <input type="date" name="end_date" class="form-control form-control-lg shadow-sm" value="{{ $endDate ?? '' }}" required>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm w-100 fw-bold">
                            <i class="fas fa-search me-1"></i> Filter
                        </button>
                        <a href="{{ route('user.absent') }}" class="btn btn-light btn-lg border shadow-sm" title="Reset Filters (Default 3 Days)">
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
                    <table id="absent-datatables" class="display table table-hover align-middle w-100 mb-0">
                        <thead>
                            <tr>
                                <th style="width: 5%">No.</th>
                                <th>Client Name</th>
                                <th>Phone</th>
                                <th>Active Package(s)</th>
                                <th>Last Attended Date</th>
                                <th class="text-center">Days Absent</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Pure PHP syntax with correct spacing to prevent parsing errors -->
                            <?php if (!empty($absentUsers) && count($absentUsers) > 0): ?>
                                <?php $iteration = 1; ?>
                                <?php foreach ($absentUsers as$user): ?>
                                    <tr>
                                        <td class="text-center text-muted"><?= $iteration++ ?></td>
                                        
                                        <!-- User Info -->
                                        <td class="fw-bold text-dark">
                                            <?= htmlspecialchars($user->name ?? '') ?>
                                        </td>
                                        <td>
                                            <a href="tel:<?= htmlspecialchars($user->phone ?? '') ?>" class="text-decoration-none text-primary">
                                                <?= htmlspecialchars($user->phone ?? '') ?>
                                            </a>
                                        </td>
                                        
                                        <!-- Active Packages -->
                                        <td>
                                            <?php if(isset($user->purchases) && count($user->purchases) > 0): ?>
                                                <?php foreach($user->purchases as$purchase): ?>
                                                    <span class="badge bg-secondary mb-1">
                                                        <?= htmlspecialchars($purchase->package->name ?? 'Unknown') ?> 
                                                        (Rem: <?= htmlspecialchars($purchase->class_remaining ?? '0') ?>)
                                                    </span><br>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <!-- Last Attended -->
                                        <td class="text-nowrap fw-bold text-secondary">
                                            <?= htmlspecialchars($user->last_attendance_display ?? '') ?>
                                        </td>

                                        <!-- Days Absent -->
                                        <td class="text-center fw-bolder fs-5 text-danger">
                                            <?= htmlspecialchars($user->absent_days ?? '0') ?> Days
                                        </td>
                                        
                                        <!-- Status -->
                                        <td>
                                            <span class="badge-custom status-danger">
                                                <i class="fas fa-exclamation-triangle me-1"></i> Absent
                                            </span>
                                        </td>
                                        
                                        <!-- Actions (Modal Trigger Button) -->
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-info shadow-sm rounded-pill view-attendance" 
                                                    data-id="<?= htmlspecialchars($user->id) ?>" 
                                                    data-name="<?= htmlspecialchars($user->name) ?>"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#attendanceHistoryModal" 
                                                    title="View Attendance History">
                                                <i class="fas fa-history"></i> View
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="fas fa-check-circle fs-1 text-success mb-3 d-block"></i>
                                        <h5 class="fw-bold">No Absent Users Found</h5>
                                        <p class="small mb-0">ရွေးချယ်ထားသော ရက်စွဲအတွင်း ပျက်နေသော User များ မရှိပါ။</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Box for Attendance History -->
<div class="modal fade" id="attendanceHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="fas fa-user-times text-danger me-2"></i> Attendance Record: <span id="historyUserName" class="text-primary"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body p-4">
                <div class="table-responsive rounded border">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Class Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="attendanceTableBody">
                            <tr><td colspan="4" class="text-center text-muted py-3">Loading records...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="modal-footer bg-light">
                <!-- User ၏ Full Details (Package များအားလုံးကြည့်ရန်) Button -->
              
                <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@include('master.footer')

<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script>
    $(document).ready(function () {$('#absent-datatables').DataTable({
            "pageLength": 15,
            "order": [], // Keeps controller sorting
            "columnDefs": [
                { "targets": [7], "orderable": false }
            ],
            "language": {
                "search": "Search Client:",
                "info": "Showing _START_ to _END_ of _TOTAL_ absent records"
            }
        });

        // AJAX logic to open Modal and fetch Attendance Data
        $('.view-attendance').on('click', function() {
            let userId = $(this).data('id');
            let userName = $(this).data('name');$('#historyUserName').text(userName);
            
            // Construct URL correctly in JS string concatenation
            let detailsUrl = "{{ url('/user/package/details') }}/" + userId;
            $('#fullDetailsBtn').attr('href', detailsUrl); 
            
            let tbody = $('#attendanceTableBody');
            tbody.html('<tr><td colspan="4" class="text-center text-muted py-4"><div class="spinner-border spinner-border-sm me-2 text-primary"></div> Fetching records...</td></tr>');

            let ajaxUrl = "{{ url('/user') }}/" + userId + "/attendance-history";

            $.ajax({
                url: ajaxUrl,
                type: 'GET',
                success: function(response) {
                    tbody.empty();
                    
                    if(response.data && response.data.length > 0) {
                        response.data.forEach(function(item) {
                            let statusText = item.status ? item.status.toLowerCase() : 'attended';
                            let statusBadge = '';
                            
                            if(statusText === 'attended' || statusText === 'joined') {
                                statusBadge = `<span class="badge bg-success"><i class="fas fa-check me-1"></i> Attended</span>`;
                            } else {
                                statusBadge = `<span class="badge bg-danger"><i class="fas fa-times me-1"></i> ${item.status}</span>`;
                            }

                            let row = `
                                <tr>
                                    <td class="text-nowrap">${item.date}</td>
                                    <td class="text-nowrap">${item.time}</td>
                                    <td class="fw-bold text-dark">${item.class_name}</td>
                                    <td>${statusBadge}</td>
                                </tr>
                            `;
                            tbody.append(row);
                        });
                    } else {
                        tbody.html('<tr><td colspan="4" class="text-center text-muted py-4"><i class="fas fa-info-circle fs-4 d-block mb-2 text-secondary"></i> ဤ User သည် အတန်းတက်ရောက်ထားသော မှတ်တမ်း မရှိသေးပါ။</td></tr>');
                    }
                },
                error: function(xhr) {
                    let errorMsg = "Error loading history. Please try again.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    tbody.html(`<tr>
                        <td colspan="4" class="text-center py-4 text-danger">
                            <i class="fas fa-exclamation-triangle fs-3 mb-2 d-block"></i>
                            <strong>Error Occurred!</strong><br><small>${errorMsg}</small>
                        </td>
                    </tr>`);
                }
            });
        });
    });
</script>