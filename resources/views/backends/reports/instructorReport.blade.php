<?php echo view('master.header'); ?>
<?php echo view('master.sidebar'); ?>
<?php echo view('master.nav'); ?>

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

<style>
    .page-inner { padding-top: 2rem; padding-bottom: 2rem; }
    .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); }
    .table-custom thead th { background-color: #f8f9fa; color: #495057; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; border-bottom: 2px solid #e9ecef; padding: 15px 8px; vertical-align: middle; white-space: nowrap;}
    .table-custom tbody td { vertical-align: middle; padding: 12px 8px; color: #333; border-bottom: 1px solid #f1f3f5; font-size: 0.9rem;}
    .badge-soft-primary { background-color: #e0eaff; color: #3d6cb9; border: 1px solid #c2d5ff; }
    .badge-soft-warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
    .badge-soft-secondary { background-color: #e9ecef; color: #495057; }
    .btn-excel { background-color: #107c41 !important; color: white !important; border: none; border-radius: 6px; padding: 8px 16px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px; }
    .btn-excel:hover { background-color: #0b5e31 !important; }
</style>

<div class="container">
    <div class="page-inner">
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h4 class="fw-bold mb-1 text-dark"><i class="fas fa-file-invoice-dollar text-primary me-2"></i>Instructor Payroll Reports</h4>
                <p class="text-muted mb-0 small">Overview of instructor salaries and class earnings based on selected date.</p>
            </div>
        </div>

        <!-- Date Filter Card -->
        <div class="card card-custom mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('instructor.report') }}" class="row align-items-end g-3">
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold">Date From</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate ?? \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold">Date To</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate ?? \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-2"></i> Filter Report</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card card-custom">
            <div class="card-body p-0">
                <div class="table-responsive p-3">
                    <table class="table table-custom w-100" id="basic-datatables">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Instructor Name</th>
                                <th>Type</th>
                                <th class="text-center">Total Clients</th>
                                <th class="text-end">Maint. Fee</th>
                                <th class="text-end">Base Salary</th>
                                <th class="text-end">Class Earning</th>
                                <th class="text-end text-success">Grand Total</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $iteration = 1; ?>
                            <?php foreach ($instructors as $instructor): ?>
                                <?php if($instructor): ?>
                                    <tr>
                                        <td class="text-muted"><?= $iteration++ ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded-circle p-2 me-2 text-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    <i class="fas fa-user small"></i>
                                                </div>
                                                <span class="fw-bold" style="white-space: nowrap;"><?= htmlspecialchars($instructor['name'] ?? 'Unknown') ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if(($instructor['type'] ?? 'full_time') == 'full_time'): ?>
                                                <span class="badge badge-soft-primary px-2 py-1 rounded-pill"><i class="fas fa-user-tie me-1"></i> Full Time</span>
                                            <?php else: ?>
                                                <span class="badge badge-soft-warning px-2 py-1 rounded-pill"><i class="fas fa-user-clock me-1"></i> Part Time</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-soft-secondary rounded-pill px-3 py-1">
                                                <i class="fas fa-users me-1 text-muted"></i> <?= $instructor['total_clients'] ?? 0 ?>
                                            </span>
                                        </td>
                                        <td class="text-end text-danger fw-bold"><?= number_format((float)($instructor['maintenance_fees'] ?? 0)) ?></td>
                                        <td class="text-end">
                                            <?php if(($instructor['type'] ?? '') == 'full_time' && ($instructor['base_salary'] ?? 0) > 0): ?>
                                                <span class="text-dark fw-bold"><?= number_format((float)($instructor['base_salary'] ?? 0)) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end text-primary fw-bold"><?= number_format((float)($instructor['class_earnings'] ?? 0)) ?></td>
                                        <td class="text-end">
                                            <div class="fw-bold text-success" style="font-size: 1.05rem;">
                                                <?= number_format((float)($instructor['grand_total'] ?? 0)) ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <!-- Action View Button -->
                                            <a href="{{ route('instructor.report.detail', ['id' => $instructor['id'], 'start_date' => $startDate ?? \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d'), 'end_date' => $endDate ?? \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d')]) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo view('master.footer'); ?>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<script>
    $(document).ready(function () {
        if ($('#basic-datatables').length) {
            $('#basic-datatables').DataTable({
                "order": [[0, "asc"]],
                "pageLength": 25,
                "dom": '<"row mb-4 align-items-center"<"col-md-6 d-flex align-items-center gap-3"lB><"col-md-6"f>>rt<"row mt-3"<"col-md-6"i><"col-md-6"p>>',
                "buttons": [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Export Summary',
                        className: 'btn btn-excel btn-sm',
                        title: 'Instructor_Payroll_Summary_' + new Date().toISOString().split('T')[0]
                    }
                ]
            });
        }
    });
</script>