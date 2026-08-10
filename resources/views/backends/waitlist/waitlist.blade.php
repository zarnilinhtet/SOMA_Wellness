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

        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">

            <div class="card-body p-4">

                <div class="table-responsive">

                    <table class="table table-hover align-middle" id="transaction-datatables"
                        style="min-width: 1000px;">

                        <thead>

                            <tr class="table-light">

                                <th>No.</th>

                                <th>User</th>

                                <th>Booked Course/Class</th>

                                <th>Status</th>

                                <th class="text-center">Action</th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach ($bookings as $book)

                                <tr>

                                    <td>
                                        {{ $loop->iteration }}
                                    </td>

                                    <td>

                                        <span class="fw-bold d-block text-dark">

                                            {{ $book->bookingUser->name }}

                                        </span>

                                        <small class="text-muted">

                                            {{ $book->bookingUser->phone }}

                                        </small>

                                    </td>

                                    <td>

                                        <span class="fw-bold">

                                            {{ $book->class->class_name }}

                                        </span>

                                    </td>

                                    <td>

                                        {{-- CONFIRMED --}}
                                        @if($book->status === 'confirmed')

                                            <span class="badge badge-confirmed px-3 py-2 rounded-pill">

                                                <i class="fas fa-check-circle me-1"></i>

                                                Confirmed

                                            </span>

                                            {{-- WAITLIST --}}
                                        @elseif($book->status === 'waitlisted')

                                            <span class="badge badge-waitlisted px-3 py-2 rounded-pill">

                                                <i class="fas fa-clock me-1"></i>

                                                Waitlisted

                                            </span>

                                            {{-- REJECTED --}}
                                        @elseif($book->status === 'rejected')

                                            <span class="badge badge-rejected px-3 py-2 rounded-pill">

                                                <i class="fas fa-times-circle me-1"></i>

                                                Rejected

                                            </span>

                                            @if($book->rejection_reason)

                                                <div class="mt-2 small text-danger">

                                                    "{{ $book->rejection_reason }}"

                                                </div>

                                            @endif

                                            {{-- CANCELLED --}}
                                        @elseif($book->status === 'cancelled')

                                            <span class="badge badge-pending px-3 py-2 rounded-pill">

                                                <i class="fas fa-ban me-1"></i>

                                                Cancelled

                                            </span>

                                            <span class="small text-danger mt-1">

                                                By {{ $book->byWho }}

                                            </span>

                                            @if($book->cancellation_reason)

                                                <div class="mt-2 small text-muted">

                                                    "{{ $book->cancellation_reason }}"

                                                </div>

                                            @endif

                                        @endif

                                    </td>

                                    {{-- ACTION --}}
                                    <td class="text-center">

                                        @if($book->status === 'waitlisted')

                                            <div class="action-control-group">

                                                {{-- APPROVE --}}
                                                <form action="{{ route('bookings.update-status', $book->id) }}" method="POST"
                                                    class="d-inline">

                                                    @csrf
                                                    @method('PATCH')

                                                    <input type="hidden" name="status" value="confirmed">

                                                    <button type="submit" class="btn-action-pill action-approve">

                                                        <i class="fa fa-check"></i>

                                                        Approve

                                                    </button>

                                                </form>

                                                {{-- CANCEL --}}
                                                <button type="button" class="btn-action-pill action-reject "
                                                    onclick="cancelBooking({{ $book->id }})">
                                                    <i class="fa fa-times"></i>
                                                    Cancel
                                                </button>

                                            </div>

                                        @elseif($book->status === 'confirmed')

                                            <div class="action-control-group">

                                                <button type="button" class="btn-action-pill action-reject "
                                                    onclick="cancelBooking({{ $book->id }})">
                                                    <i class="fa fa-times"></i>
                                                    Cancel
                                                </button>

                                            </div>

                                        @else

                                            <span class="text-muted small">

                                                Processed

                                            </span>

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

{{-- SHARED CANCEL MODAL --}}
<!-- <div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <form id="cancelForm" method="POST" class="modal-content border-0 shadow">

            @csrf
            @method('PATCH')

            <input type="hidden" name="status" value="cancelled">

            <div class="modal-header bg-danger text-white">

                <h5 class="modal-title fw-bold">

                    <i class="fas fa-ban me-2"></i>

                    Cancel Booking

                </h5>

                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>

            </div>

            <div class="modal-body">

                <label class="fw-bold mb-2">

                    Reason for Cancellation

                </label>

                <textarea name="cancellation_reason" class="form-control" rows="4" required
                    placeholder="Please provide cancellation reason..."></textarea>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">

                    Close

                </button>

                <button type="submit" class="btn btn-danger">

                    Submit Cancellation

                </button>

            </div>

        </form>

    </div>

</div> -->

@include('master.footer')

<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

    $(document).ready(function () {

        // DATATABLE
        if ($('#transaction-datatables').length) {

            $('#transaction-datatables').DataTable({
                order: [[0, "asc"]],
                pageLength: 10,
                responsive: true
            });

        }

        // DYNAMIC MODAL
        $(document).on('click', '.openCancelModal', function () {

            let bookingId = $(this).data('id');

            let actionUrl = "{{ url('/bookings/status') }}/" + bookingId;

            $('#cancelForm').attr('action', actionUrl);

        });

    });

    function cancelBooking(classId) {
        console.log('Cancel class with ID:', classId);
        Swal.fire({
            title: 'Cancel Booking',
            text: 'Are you sure you want to cancel this booking?',
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