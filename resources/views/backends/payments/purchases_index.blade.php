@include('master.header')

{{-- Summernote CSS for viewing long user notes if necessary --}}
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
{{-- DataTables Buttons CSS --}}
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<style>
    /* Status Badge System Matching Modern UI Theme */
    .badge-pending {
        background-color: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .badge-confirmed {
        background-color: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .badge-rejected {
        background-color: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    /* Modal Layout Overrides */
    .view-modal-header {
        background-color: #f8f9fa;
        border-bottom: 2px solid #e9ecef;
    }

    .info-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        margin-bottom: 3px;
        display: block;
    }

    .info-value {
        font-size: 1.05rem;
        font-weight: 500;
        color: #212529;
    }

    .detail-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        height: 100%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    }

    /* --- Modernized Action Controls UI --- */
    .action-control-group {
        display: inline-flex;
        align-items: center;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 30px;
        border: 1px solid #e2e8f0;
        gap: 2px;
    }

    .btn-action-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 6px 14px;
        font-size: 12.5px;
        font-weight: 700;
        border-radius: 20px;
        border: none;
        background: transparent;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none !important;
    }

    .btn-action-pill.action-inspect {
        color: #0284c7;
    }

    .btn-action-pill.action-inspect:hover {
        background: #e0f2fe;
        color: #0369a1;
    }

    .btn-action-pill.action-approve {
        color: #16a34a;
    }

    .btn-action-pill.action-approve:hover {
        background: #dcfce7;
        color: #15803d;
        transform: translateY(-1px);
    }

    .btn-action-pill.action-reject {
        color: #dc2626;
    }

    .btn-action-pill.action-reject:hover {
        background: #fee2e2;
        color: #b91c1c;
        transform: translateY(-1px);
    }

    .btn-action-pill.action-delete {
        color: #64748b;
    }

    .btn-action-pill.action-delete:hover {
        background: #e2e8f0;
        color: #0f172a;
        transform: translateY(-1px);
    }

    /* Image Hover Zoom */
    .hover-zoom {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        cursor: pointer;
    }

    .hover-zoom:hover {
        transform: scale(1.08);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    }

    .dt-buttons .btn { margin-right: 5px; }
</style>

@include('master.sidebar')
@include('master.nav')

<div class="container">
    <div class="page-inner">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">User Transaction Approvals</h4>
        </div>

        {{-- Filter Form --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-body p-3">
                <form action="{{ url()->current() }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label text-muted small fw-bold mb-1">Date From</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date', $startDate ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted small fw-bold mb-1">Date To</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date', $endDate ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted small fw-bold mb-1">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ url()->current() }}" class="btn btn-light border px-3 ms-2">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Success/Error Alerts --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Transaction Table Card --}}
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="transaction-datatables" style="min-width: 1000px;">
                        <thead>
                            <tr class="table-light">
                                <th>No.</th>
                                <th>User</th>
                                <th>Purchased Course/Class</th>
                                <th>Amount</th>
                                <th>Coins Used</th>
                                <th>User Discount</th>
                                <th>Payment Method</th>
                                <th>Transaction Screenshot</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $transaction)
                                @php
                                    // Normalize image path resolution
                                    $rawImage = $transaction->screenshot ?? $transaction->receipt_image ?? null;
                                    $imageUrl = null;
                                    if ($rawImage) {
                                        if (str_starts_with($rawImage, 'http') || str_starts_with($rawImage, 'uploads/') || str_starts_with($rawImage, 'storage/')) {
                                            $imageUrl = asset($rawImage);
                                        } else {
                                            $imageUrl = asset('storage/' . $rawImage);
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td class="text-muted fw-medium">{{ $loop->iteration }}</td>
                                    <td>
                                        <span class="fw-bold d-block text-dark mb-0" style="font-size: 14.5px;">{{ $transaction->user->name ?? 'N/A' }}</span>
                                        <small class="text-muted font-monospace" style="font-size: 11.5px;">{{ $transaction->user->phone ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <span class="text-dark fw-bold" style="font-size: 14px;">{{ $transaction->package?->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="text-dark fw-bold font-monospace" style="font-size: 14px;">{{ number_format($transaction->amount) }} MMK</span>
                                    </td>
                                    <td>
                                        <span class="text-dark fw-bold font-monospace" style="font-size: 14px;">{{ number_format($transaction->coin_used) }} MMK</span>
                                    </td>
                                    <td>
                                        <span class="text-dark fw-bold font-monospace" style="font-size: 14px;">{{ number_format($transaction->user_discount) }}%</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-secondary border px-2 py-1.5 rounded" style="font-size: 12px; font-weight: 500;">
                                            {{ $transaction->paymentMethod->method ?? $transaction->payment_method ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($imageUrl)
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#viewReceiptModal{{ $transaction->id }}">
                                                <img src="{{ $imageUrl }}" alt="Receipt" class="rounded border object-fit-cover shadow-sm hover-zoom" width="50" height="50">
                                            </a>
                                        @else
                                            <span class="text-muted small fst-italic">No Slip Uploaded</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->pay_status === 'pending')
                                            <span class="badge badge-pending px-3 py-2 rounded-pill text-capitalize" style="font-size: 11px; font-weight: 700;">
                                                <i class="fas fa-clock me-1"></i> Pending
                                            </span>
                                        @elseif($transaction->pay_status === 'confirmed')
                                            <span class="badge badge-confirmed px-3 py-2 rounded-pill text-capitalize" style="font-size: 11px; font-weight: 700;">
                                                <i class="fas fa-check-circle me-1"></i> Confirmed
                                            </span>
                                        @elseif($transaction->pay_status === 'rejected')
                                            <span class="badge badge-rejected px-3 py-2 rounded-pill text-capitalize" style="font-size: 11px; font-weight: 700;">
                                                <i class="fas fa-times-circle me-1"></i> Rejected
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="action-control-group">
                                            {{-- Details View --}}
                                            <button type="button" class="btn-action-pill action-inspect" data-bs-toggle="modal" data-bs-target="#viewTransactionModal{{ $transaction->id }}" title="Inspect Transaction">
                                                <i class="fa fa-eye"></i> View
                                            </button>

                                            @if($transaction->pay_status === 'pending')
                                                {{-- Quick Approve --}}
                                                <form action="{{ route('transactions.update-status', $transaction->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="pay_status" value="confirmed">
                                                    <button type="submit" class="btn-action-pill action-approve" title="Quick Approve">
                                                        <i class="fa fa-check"></i> Approve
                                                    </button>
                                                </form>

                                                {{-- Reject Modal Trigger --}}
                                                <button type="button" class="btn-action-pill action-reject" title="Reject Transaction" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $transaction->id }}">
                                                    <i class="fa fa-times"></i> Reject
                                                </button>
                                            @endif
                                            
                                            {{-- Delete Modal Trigger --}}
                                            <button type="button" class="btn-action-pill action-delete" title="Delete Transaction" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $transaction->id }}">
                                                <i class="fa fa-trash"></i> Delete
                                            </button>
                                        </div>
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

{{-- ======================================================== --}}
{{-- MODALS SECTION --}}
{{-- ======================================================== --}}
@foreach ($transactions as $transaction)
    @php
        $rawImage = $transaction->screenshot ?? $transaction->receipt_image ?? null;
        $imageUrl = null;
        if ($rawImage) {
            if (str_starts_with($rawImage, 'http') || str_starts_with($rawImage, 'uploads/') || str_starts_with($rawImage, 'storage/')) {
                $imageUrl = asset($rawImage);
            } else {
                $imageUrl = asset('storage/' . $rawImage);
            }
        }
    @endphp

    {{-- 1. Full Detail View Modal --}}
    <div class="modal fade" id="viewTransactionModal{{ $transaction->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header view-modal-header py-3 px-4">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-receipt text-primary me-2"></i> Audit Transaction Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="row g-3 mb-4">
                        <div class="col-md-5 text-center">
                            <div class="detail-card py-4 d-flex flex-column align-items-center justify-content-center">
                                @if($imageUrl)
                                    <img src="{{ $imageUrl }}" class="rounded shadow-sm mb-3 border object-fit-contain img-fluid bg-white" style="max-height: 220px;" alt="User Payment Proof">
                                    <a href="{{ $imageUrl }}" download="Transaction_{{ $transaction->transaction_no ?? $transaction->id }}_Slip" class="btn btn-outline-primary btn-sm rounded-pill px-3" target="_blank">
                                        <i class="fa fa-download me-1"></i> View / Download Slip
                                    </a>
                                @else
                                    <div class="bg-light rounded d-inline-flex align-items-center justify-content-center text-muted border mb-3" style="width: 100px; height: 100px;">
                                        <i class="fas fa-file-invoice-dollar fs-1"></i>
                                    </div>
                                    <span class="text-muted small">No screenshot attached</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-7">
                            <div class="row g-3 h-100">
                                <div class="col-sm-12">
                                    <div class="detail-card">
                                        <span class="info-label"><i class="fas fa-user me-1"></i> Applicant</span>
                                        <span class="info-value text-dark">{{ $transaction->user->name ?? 'N/A' }}</span>
                                        <small class="d-block text-muted">{{ $transaction->user->phone ?? 'N/A' }}</small>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="detail-card">
                                        <span class="info-label"><i class="fas fa-graduation-cap me-1"></i> Requested Target Class</span>
                                        <span class="info-value text-primary fs-6">{{ $transaction->package->name ?? 'No Package' }}</span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="detail-card">
                                        <span class="info-label"><i class="fas fa-coins text-warning me-1"></i> Declared Amount</span>
                                        <span class="info-value text-success font-monospace">{{ number_format($transaction->amount) }} MMK</span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="detail-card">
                                        <span class="info-label"><i class="fas fa-university me-1"></i> Remitted Via</span>
                                        <span class="info-value text-secondary">{{ $transaction->paymentMethod->method ?? $transaction->payment_method ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="detail-card">
                                <span class="info-label"><i class="fas fa-hashtag me-1"></i> User-Submitted Transaction ID</span>
                                <span class="info-value mt-2 text-dark font-monospace mb-2 d-block">{{ $transaction->transaction_no ?? 'N/A' }}</span>

                                <span class="info-label"><i class="fas fa-phone me-1"></i> User-Submitted Phone Number</span>
                                <span class="info-value text-dark font-monospace">{{ $transaction->phone ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-card">
                                <span class="info-label"><i class="fas fa-clock me-1"></i> Submission Time</span>
                                <span class="info-value text-muted small">{{ $transaction->created_at ? $transaction->created_at->format('Y-M-d H:i A') : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    @if($transaction->user_note)
                        <div class="detail-card mb-3">
                            <span class="fw-bold fs-6 border-bottom d-block pb-2 mb-2">User Notes / References</span>
                            <div class="text-muted small" style="line-height: 1.6;">
                                {{ $transaction->user_note }}
                            </div>
                        </div>
                    @endif

                    @if($transaction->pay_status === 'rejected' && $transaction->rejection_reason)
                        <div class="detail-card border-danger bg-white">
                            <span class="fw-bold fs-6 border-bottom d-block pb-2 mb-2 text-danger">
                                <i class="fas fa-exclamation-triangle me-1"></i> Rejection Reason History
                            </span>
                            <div class="text-danger small fst-italic">
                                "{{ $transaction->rejection_reason }}"
                            </div>
                        </div>
                    @endif
                </div>

                <div class="modal-footer bg-white py-3">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                    @if($transaction->pay_status === 'pending')
                        <form action="{{ route('transactions.update-status', $transaction->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="pay_status" value="confirmed">
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fas fa-check-circle me-1"></i> Confirm Transaction
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Receipt Image Popup Modal --}}
    @if($imageUrl)
        <div class="modal fade" id="viewReceiptModal{{ $transaction->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content bg-transparent border-0 text-center">
                    <div class="modal-body p-0">
                        <img src="{{ $imageUrl }}" class="img-fluid rounded shadow-lg bg-white" style="max-height: 85vh; width: auto;" alt="Full Receipt">
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-light btn-sm rounded-pill px-4" data-bs-dismiss="modal">Close Preview</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- 3. Reject Modal --}}
    <div class="modal fade" id="rejectModal{{ $transaction->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('transactions.update-status', $transaction->id) }}" method="POST" class="modal-content">
                @csrf
                @method('PATCH')
                <input type="hidden" name="pay_status" value="rejected">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold"><i class="fas fa-ban me-2"></i> Reject Transaction</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="fw-bold mb-2">Reason for Rejection <span class="text-danger">*</span></label>
                    <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="e.g. Screenshot blurry, Transaction ID matches nothing, Incorrect amount remitted."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Submit Rejection</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 4. Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteModal{{ $transaction->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" class="modal-content">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold"><i class="fas fa-trash me-2"></i> Delete Transaction</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-exclamation-circle text-warning mb-3" style="font-size: 3rem;"></i>
                    <h5 class="fw-bold">Are you sure?</h5>
                    <p class="text-muted">Do you really want to delete this transaction record? This process cannot be undone and the uploaded proof will be deleted.</p>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark px-4">Yes, Delete</button>
                </div>
            </form>
        </div>
    </div>
@endforeach

@include('master.footer')

{{-- Plugin Initializer Dependencies --}}
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<!-- DataTables Buttons & Excel/PDF/CSV Export Libraries -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<script>
    $(document).ready(function () {
        if ($('#transaction-datatables').length) {
            $('#transaction-datatables').DataTable({
                "order": [[0, "asc"]],
                "pageLength": 10,
                "responsive": true,
                "dom": 'Bfrtip',
                "buttons": [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Export to Excel',
                        className: 'btn btn-success btn-sm mb-3',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 8]
                        }
                    },
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fas fa-file-csv"></i> Export to CSV',
                        className: 'btn btn-info btn-sm mb-3 text-white',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 8]
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf"></i> Export to PDF',
                        className: 'btn btn-danger btn-sm mb-3',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 8]
                        }
                    }
                ]
            });
        }
    });
</script>