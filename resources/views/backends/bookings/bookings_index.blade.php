@include('master.header')

<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

<style>
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

    .badge-waitlisted {
        background-color: #f0f9ff;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }

    .badge-waitlisted i {
        color: #0284c7;
    }

    .badge-rejected {
        background-color: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .action-control-group {
        display: inline-flex;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 30px;
        border: 1px solid #e2e8f0;
    }

    .btn-action-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        font-size: 12.5px;
        font-weight: 700;
        border-radius: 20px;
        border: none;
        background: transparent;
        transition: all 0.2s ease;
        text-decoration: none !important;
    }

    .btn-action-pill.action-approve {
        color: #16a34a;
    }

    .btn-action-pill.action-approve:hover {
        background: #dcfce7;
        color: #15803d;
    }

    .btn-action-pill.action-reject {
        color: #dc2626;
    }

    .btn-action-pill.action-reject:hover {
        background: #fee2e2;
        color: #b91c1c;
    }

    /* Status Reason Tooltip Box */
    .reason-box {
        max-width: 220px; /* အရှည်ဆုံးထားမည့် အကျယ် */
        background: #f8f9fa;
        border-left: 3px solid #adb5bd;
        padding: 4px 8px;
        border-radius: 4px;
        cursor: help; /* Hover လုပ်ရင် ပုံစံပြောင်းရန် */
        display: inline-block;
    }

    .reason-box.reject-reason {
        background: #fff5f5;
        border-left-color: #ef4444;
    }

    .reason-box.cancel-reason {
        background: #f1f5f9;
        border-left-color: #64748b;
    }
</style>

@include('master.sidebar')
@include('master.nav')

<div class="container">
    <div class="page-inner">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">User Booking Approvals</h4>
        </div>

        {{-- SUCCESS --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ERRORS --}}
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- FILTER FORM --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-body p-4">
                <form method="GET" action="{{ url()->current() }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Date From</label>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Date To</label>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="waitlisted" {{ request('status') == 'waitlisted' ? 'selected' : '' }}>Waitlisted</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100" style="background-color: #BE9676; border: none;">
                                <i class="fas fa-search me-1"></i> Search
                            </button>
                            <a href="{{ url()->current() }}" class="btn btn-light w-100 border">
                                Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="transaction-datatables" style="min-width: 1000px;">
                        <thead>
                            <tr class="table-light">
                                <th>No.</th>
                                <th>Booking Date</th>
                                <th>User</th>
                                <th>Booked Course/Class</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $book)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <span class="d-block">{{ $book->created_at->format('M d, Y') }}</span>
                                        <small class="text-muted">{{ $book->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-bold d-block text-dark">{{ $book->bookingUser->name ?? 'N/A' }}</span>
                                        <small class="text-muted">{{ $book->bookingUser->phone ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $book->class->class_name ?? 'N/A' }}</span>
                                    </td>
                                    
                                    {{-- NEW STATUS COLUMN --}}
                                    <td>
                                        <div class="d-flex flex-column align-items-start gap-2">
                                            {{-- CONFIRMED --}}
                                            @if($book->status === 'confirmed')
                                                <span class="badge badge-confirmed px-3 py-2 rounded-pill">
                                                    <i class="fas fa-check-circle me-1"></i> Confirmed
                                                </span>
                                    
                                            {{-- WAITLIST --}}
                                            @elseif($book->status === 'waitlisted')
                                                <span class="badge badge-waitlisted px-3 py-2 rounded-pill">
                                                    <i class="fas fa-clock me-1"></i> Waitlisted
                                                </span>
                                    
                                            {{-- REJECTED --}}
                                            @elseif($book->status === 'rejected')
                                                <span class="badge badge-rejected px-3 py-2 rounded-pill">
                                                    <i class="fas fa-times-circle me-1"></i> Rejected
                                                </span>
                                                @if($book->rejection_reason)
                                                    <div class="reason-box reject-reason text-muted small text-truncate w-100" 
                                                         data-bs-toggle="tooltip" 
                                                         data-bs-placement="top" 
                                                         title="{{ $book->rejection_reason }}">
                                                        <i class="fas fa-info-circle me-1 text-danger"></i> {{ $book->rejection_reason }}
                                                    </div>
                                                @endif
                                    
                                            {{-- CANCELLED --}}
                                            @elseif($book->status === 'cancelled')
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge badge-pending px-3 py-2 rounded-pill">
                                                        <i class="fas fa-ban me-1"></i> Cancelled
                                                    </span>
                                                    <span class="small text-danger fw-bold" style="font-size: 11px;">(By {{ $book->byWho }})</span>
                                                </div>
                                                @if($book->cancellation_reason)
                                                    <div class="reason-box cancel-reason text-muted small text-truncate w-100" 
                                                         data-bs-toggle="tooltip" 
                                                         data-bs-placement="top" 
                                                         title="{{ $book->cancellation_reason }}">
                                                        <i class="fas fa-info-circle me-1 text-secondary"></i> {{ $book->cancellation_reason }}
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </td>

                                    {{-- ACTION --}}
                                    <td class="text-center">
                                        @if(auth()->user()->hasRole("Admin"))
                                            @if($book->status === 'waitlisted')
                                                <div class="action-control-group">
                                                    {{-- APPROVE --}}
                                                    <form action="{{ route('bookings.update-status', $book->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="confirmed">
                                                        <button type="submit" class="btn-action-pill action-approve">
                                                            <i class="fa fa-check"></i> Approve
                                                        </button>
                                                    </form>
                                                    {{-- CANCEL --}}
                                                    <button type="button" class="btn-action-pill action-reject" onClick="cancelClass({{ $book->id }})">
                                                        <i class="fa fa-times"></i> Cancel
                                                    </button>
                                                </div>
                                            @elseif($book->status === 'confirmed')
                                                <button type="button" class="btn-action-pill action-reject" onclick="cancelClass({{ $book->id }})">
                                                    <i class="fa fa-times"></i> Cancel
                                                </button>
                                            @else
                                                <span class="text-muted small">Processed</span>
                                            @endif
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

@include('master.footer')

<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        // Initialize Bootstrap Tooltips for truncated text
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // DATATABLE
        if ($('#transaction-datatables').length) {
            $('#transaction-datatables').DataTable({
                order: [[0, "asc"]],
                pageLength: 10,
                responsive: true,
                // Optional: disable DataTables built-in search if you want users to rely entirely on your backend filters
                // searching: false 
            });
        }
    });

    function cancelClass(classId) {
        console.log('Cancel class with ID:', classId);
        Swal.fire({
            title: 'Cancel Class',
            text: 'Are you sure you want to cancel this class?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#BE9676',
            cancelButtonColor: '#999',
            confirmButtonText: 'Yes, Cancel',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `/cancel/booking/${classId}`;
            }
        });
    }
</script>