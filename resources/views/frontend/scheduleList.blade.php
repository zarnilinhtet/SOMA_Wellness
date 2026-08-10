@extends('layouts.link')

@section('content')
    <style>
        body {
            background-color: #f8f9fa;
        }

        .schedule-container {
            max-width: 1100px;
            margin: 1rem auto 2rem auto;
        }

        .faded-row {
            opacity: 0.5;
        }

        .status-red {
            color: #ff8a80;
            font-size: 0.95rem;
        }

        .btn-book {
            color: #495057;
            border-color: #dee2e6;
            font-size: 0.9rem;
            border-radius: 50rem;
            padding: 0.25rem 1.5rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }

        .btn-book:hover {
            background-color: #f8f9fa;
            border-color: #ced4da;
            color: #495057;
        }

        .avatar-circle {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            object-fit: cover;
        }

        .avatar-group {
            display: flex;
            align-items: center;
            transition: transform 0.2s ease;
        }

        .avatar-group:hover {
            transform: scale(1.05);
        }

        .avatar-group .avatar-circle {
            width: 32px;
            height: 32px;
            border: 2px solid #fff;
            margin-left: -10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .avatar-group .avatar-circle:first-child {
            margin-left: 0;
        }

        .avatar-more {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
            border: 2px solid #fff;
            margin-left: -10px;
            z-index: 10;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .filter-card {
            max-width: 1100px;
            margin: 0 auto 1.5rem auto;
            background-color: #fff;
            border-radius: 8px;
        }
    </style>

    <div class="container">
        <div class="page-inner">

            <!-- Top Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                <h4 class="fw-bold mb-0 text-dark">
                    <i class="fas fa-calendar-check me-2 text-primary"></i> Class Schedule List
                </h4>
                <a href="{{ route('home.page') }}" class="btn btn-outline-secondary shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>

            <!-- Date Filter Card -->
            <div class="filter-card border border-light-subtle shadow-sm p-3">
                <form action="{{ route('schedule.page') ?? '#' }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="from_date" class="form-label fw-bold text-secondary mb-1">From Date</label>
                        <input type="date" class="form-control" id="from_date" name="from_date" value="{{ $fromDate }}">
                    </div>
                    <div class="col-md-4">
                        <label for="to_date" class="form-label fw-bold text-secondary mb-1">To Date</label>
                        <input type="date" class="form-control" id="to_date" name="to_date" value="{{ $toDate }}">
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary shadow-sm flex-grow-1">
                            <i class="fas fa-search me-1"></i> Search
                        </button>
                        <a href="{{ route('schedule.page') ?? '#' }}" class="btn btn-outline-secondary shadow-sm">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- DataTables Schedule Container -->
            <div class="schedule-container mt-0">
                <div class="card border border-light-subtle shadow-sm overflow-hidden p-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="basic-datatables">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Date & Time</th>
                                    <th>Class Name</th>
                                    <th>Days</th>
                                    <th>Instructors</th>
                                    <th>Availability</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $counter = 1; @endphp
                                @forelse($groupedSchedules as $date => $schedules)
                                    @foreach($schedules as $schedule)
                                        @php
                                            $start = \Carbon\Carbon::parse($schedule->start_time);
                                            $end = \Carbon\Carbon::parse($schedule->end_time);
                                            $duration = $start->diffInMinutes($end);

                                            $isCompleted = strtolower($schedule->status) === 'completed';
                                            $isClosed = strtolower($schedule->status) === 'closed' || strtolower($schedule->status) === 'cancelled';
                                            $isFaded = $isCompleted || $isClosed;

                                            $spotsTaken = $schedule->bookings ? $schedule->bookings->count() : 0;
                                            $spotsLeft = $schedule->capacity - $spotsTaken;
                                        @endphp
                                        <tr class="{{ $isFaded ? 'faded-row' : '' }}">
                                            <!-- Index -->
                                            <td>{{ $counter++ }}</td>

                                            <!-- Date & Time -->
                                            <td>
                                                <div class="fw-bold text-primary mb-1" style="font-size: 0.85rem;">
                                                    <i class="far fa-calendar-alt me-1"></i> {{ $date }}
                                                </div>
                                                <div class="text-dark fw-semibold" style="font-size: 0.9rem;">
                                                    {{ $start->format('g:i a') }}
                                                </div>
                                                <div class="text-muted small">
                                                    {{ $duration }} min
                                                </div>
                                            </td>

                                            <!-- Class Name -->
                                            <td class="fw-bold text-dark fs-6">
                                                {{ $schedule->class_name }}
                                            </td>

                                            <!-- Days -->
                                            <td class="fw-bold text fs-6">
                                                @foreach ($schedule->days as $day)
                                                    <span class="badge bg-light text-dark me-1">{{ $day }}</span>
                                                @endforeach
                                            </td>

                                            <!-- Instructors -->
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    @if($schedule->instructors && $schedule->instructors->count() > 0)
                                                        <div class="avatar-group">
                                                            @foreach($schedule->instructors as $instructor)
                                                                <span>{{ $instructor->user->name ?? 'Instructor' }}</span>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span class="text-secondary small fst-italic">TBA</span>
                                                    @endif
                                                </div>
                                            </td>

                                            <!-- Availability -->
                                            <td>
                                                @if(!$isFaded)
                                                    <span class="fw-semibold {{ $spotsLeft <= 0 ? 'text-danger' : 'text-secondary' }}"
                                                        style="font-size: 0.9rem;">
                                                        {{ $spotsLeft > 0 ? $spotsLeft . ' spots left' : 'Full' }}
                                                    </span>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @empty
                                    <!-- Handled gracefully by DataTables empty Table message -->
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ============================================== -->
    <!-- INSTRUCTOR MODALS GENERATION -->
    <!-- ============================================== -->
    @foreach($groupedSchedules as $date => $schedules)
        @foreach($schedules as $schedule)
            @if($schedule->instructors && $schedule->instructors->count() > 0)
                <div class="modal fade" id="instructorModal{{ $schedule->id }}" tabindex="-1"
                    aria-labelledby="instructorModalLabel{{ $schedule->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header bg-light border-bottom">
                                <div>
                                    <h5 class="modal-title fw-bold text-dark" id="instructorModalLabel{{ $schedule->id }}">
                                        Class Instructors
                                    </h5>
                                    <div class="text-muted small mt-1">
                                        {{ $schedule->class_name }} •
                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i a') }}
                                    </div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-0">
                                <div class="list-group list-group-flush">
                                    @foreach($schedule->instructors as $instructor)
                                        @php
                                            $instructorName = $instructor->user->name ?? 'Unknown Instructor';
                                            $instructorEmail = $instructor->user->email ?? 'No email provided';
                                            $instructorAvatar = $instructor->user->profile_photo_url ?? "https://ui-avatars.com/api/?name=" . urlencode($instructorName) . "&background=343a40&color=fff";
                                        @endphp
                                        <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="{{ $instructorAvatar }}" alt="{{ $instructorName }}"
                                                    class="rounded-circle object-fit-cover shadow-sm" style="width: 45px; height: 45px;">
                                                <div>
                                                    <h6 class="mb-0 fw-bold text-dark">{{ $instructorName }}</h6>
                                                    <small class="text-muted">{{ $instructorEmail }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="modal-footer bg-light border-top py-2">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endforeach

    <!-- ============================================== -->
    <!-- BOOKING MODALS GENERATION -->
    <!-- ============================================== -->
    @foreach($groupedSchedules as $date => $schedules)
        @foreach($schedules as $schedule)
            @if(isset($schedule->bookings) && $schedule->bookings->count() > 0)
                <div class="modal fade" id="bookingModal{{ $schedule->id }}" tabindex="-1"
                    aria-labelledby="bookingModalLabel{{ $schedule->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header bg-light border-bottom">
                                <div>
                                    <h5 class="modal-title fw-bold text-dark" id="bookingModalLabel{{ $schedule->id }}">
                                        Class Bookings
                                    </h5>
                                    <div class="text-muted small mt-1">
                                        {{ $schedule->class_name }} •
                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i a') }}
                                    </div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-0">
                                <div class="list-group list-group-flush">
                                    @foreach($schedule->bookings as $booking)
                                        @php
                                            $studentName = $booking->user->name ?? 'Unknown Student';
                                            $studentEmail = $booking->user->email ?? 'No email provided';
                                            $studentAvatar = $booking->user->profile_photo_url ?? "https://ui-avatars.com/api/?name=" . urlencode($studentName) . "&background=random";
                                        @endphp
                                        <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="{{ $studentAvatar }}" alt="{{ $studentName }}"
                                                    class="rounded-circle object-fit-cover shadow-sm" style="width: 45px; height: 45px;">
                                                <div>
                                                    <h6 class="mb-0 fw-bold text-dark">{{ $studentName }}</h6>
                                                    <small class="text-muted">{{ $studentEmail }}</small>
                                                </div>
                                            </div>
                                            <div>
                                                <span
                                                    class="badge {{ strtolower($booking->status) == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ ucfirst($booking->status ?? 'Active') }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="modal-footer bg-light border-top py-2">
                                <span class="text-muted small me-auto">
                                    Total Bookings: <strong>{{ $schedule->bookings->count() }}</strong>
                                </span>
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endforeach

    <!-- DataTables Initializer -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if ($('#basic-datatables').length) {
                $('#basic-datatables').DataTable({
                    "order": [[0, "desc"]],
                    "paging": true,
                    "ordering": true,
                    "info": true
                });
            }
        });
    </script>
@endsection