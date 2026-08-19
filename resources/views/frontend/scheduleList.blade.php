@extends('layouts.link')

@section('content')
    <!-- Added Google Fonts for Fahkwang -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fahkwang:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <style>
        /* --- Brand Variables & Fonts --- */
        :root {
            --soma-primary: #BE9676; /* Perfect Beige */
            --soma-secondary: #8D7E71; /* Desert Taupe */
            --soma-bg: #FFF7E9; /* Soft Cream */
        }

        body, h1, h2, h3, h4, h5, h6, p, span, a, div, button, input, select, textarea, table, th, td, label, ul, li {
            font-family: 'Fahkwang', sans-serif !important;
        }

        body {
            background-color: var(--soma-bg) !important;
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
            color: var(--soma-secondary);
            border-color: rgba(141, 126, 113, 0.3);
            font-size: 0.9rem;
            border-radius: 50rem;
            padding: 0.25rem 1.5rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }

        .btn-book:hover {
            background-color: var(--soma-bg);
            border-color: var(--soma-primary);
            color: var(--soma-secondary);
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
            box-shadow: 0 2px 4px rgba(141, 126, 113, 0.1);
        }

        .avatar-group .avatar-circle:first-child {
            margin-left: 0;
        }

        .avatar-more {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: var(--soma-bg);
            color: var(--soma-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
            border: 2px solid #fff;
            margin-left: -10px;
            z-index: 10;
            box-shadow: 0 2px 4px rgba(141, 126, 113, 0.1);
        }

        .filter-card {
            max-width: 1100px;
            margin: 0 auto 1.5rem auto;
            background-color: #fff;
            border-radius: 8px;
            border: 1px solid rgba(190, 150, 118, 0.3) !important;
        }

        /* DataTables Custom Theme Integration */
        .schedule-container .card {
            border: 1px solid rgba(190, 150, 118, 0.3) !important;
            border-radius: 12px;
            background: #ffffff;
        }

        .table thead th {
            background-color: var(--soma-secondary) !important;
            color: #ffffff !important;
            font-size: 13px;
            font-weight: 700;
            padding: 16px 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
        }

        .table tbody td {
            padding: 16px 14px;
            vertical-align: middle;
            border-bottom: 1px solid rgba(190, 150, 118, 0.2);
            color: var(--soma-secondary);
            font-size: 14px;
            font-weight: 500;
        }

        .table tbody tr:hover td {
            background-color: rgba(190, 150, 118, 0.08);
        }

        /* Buttons matching SOMA palette */
        .btn-primary {
            background-color: var(--soma-primary) !important;
            border-color: var(--soma-primary) !important;
            color: #ffffff !important;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: var(--soma-secondary) !important;
            border-color: var(--soma-secondary) !important;
        }

        .btn-outline-secondary {
            color: var(--soma-secondary) !important;
            border-color: rgba(141, 126, 113, 0.4) !important;
            background-color: transparent;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-outline-secondary:hover {
            background-color: var(--soma-secondary) !important;
            color: #ffffff !important;
            border-color: var(--soma-secondary) !important;
        }

        /* Badge and text styling overrides */
        .text-primary {
            color: var(--soma-primary) !important;
        }

        .text-dark {
            color: var(--soma-secondary) !important;
        }

        .badge.bg-light {
            background-color: var(--soma-bg) !important;
            color: var(--soma-secondary) !important;
            border: 1px solid rgba(190, 150, 118, 0.3);
        }

        /* DataTables Controls Layout & Inputs */
        div.dataTables_wrapper div.dataTables_length select,
        div.dataTables_wrapper div.dataTables_filter input {
            border: 1px solid rgba(190, 150, 118, 0.4);
            border-radius: 8px;
            padding: 6px 12px;
            outline: none;
            color: var(--soma-secondary);
        }

        div.dataTables_wrapper div.dataTables_filter input:focus {
            border-color: var(--soma-primary);
            box-shadow: 0 0 0 3px rgba(190, 150, 118, 0.1);
        }

        /* Pagination Styling */
        div.dataTables_wrapper .dataTables_paginate .paginate_button { 
            color: var(--soma-secondary) !important; 
            border-radius: 6px; 
            background: #F8F8F8 !important; 
            border: 1px solid rgba(141, 126, 113, 0.2) !important;
            margin: 0 3px;
        }
        div.dataTables_wrapper .dataTables_paginate .paginate_button:hover { 
            background: rgba(190, 150, 118, 0.1) !important; 
            border: 1px solid transparent !important;
            color: var(--soma-secondary) !important;
        }
        div.dataTables_wrapper .dataTables_paginate .paginate_button.current, 
        div.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover { 
            background: var(--soma-secondary) !important; 
            color: white !important; 
            border: 1px solid var(--soma-secondary) !important; 
        }
        div.dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        div.dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            background: #F8F8F8 !important;
            color: rgba(141, 126, 113, 0.5) !important;
            border: 1px solid rgba(141, 126, 113, 0.1) !important;
        }
    </style>

    <div class="container">
        <div class="page-inner">

            <!-- Top Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                <h4 class="fw-bold mb-0" style="color: var(--soma-secondary);">
                    <i class="fas fa-calendar-check me-2" style="color: var(--soma-primary);"></i> Class Schedule List
                </h4>
                <a href="{{ route('home.page') }}" class="btn btn-outline-secondary shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>

            <!-- Date Filter Card -->
            <div class="filter-card shadow-sm p-3">
                <form action="{{ route('schedule.page') ?? '#' }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="from_date" class="form-label fw-bold mb-1" style="color: var(--soma-secondary);">From Date</label>
                        <input type="date" class="form-control" id="from_date" name="from_date" value="{{ $fromDate }}" style="border-color: rgba(190, 150, 118, 0.4); border-radius: 8px;">
                    </div>
                    <div class="col-md-4">
                        <label for="to_date" class="form-label fw-bold mb-1" style="color: var(--soma-secondary);">To Date</label>
                        <input type="date" class="form-control" id="to_date" name="to_date" value="{{ $toDate }}" style="border-color: rgba(190, 150, 118, 0.4); border-radius: 8px;">
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
                <div class="card shadow-sm overflow-hidden p-3">
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
                                            <td class="fw-bold" style="color: var(--soma-secondary);">{{ $counter++ }}</td>

                                            <!-- Date & Time -->
                                            <td>
                                                <div class="fw-bold mb-1" style="font-size: 0.85rem; color: var(--soma-primary);">
                                                    <i class="far fa-calendar-alt me-1"></i> {{ $date }}
                                                </div>
                                                <div class="fw-semibold" style="font-size: 0.9rem; color: var(--soma-secondary);">
                                                    {{ $start->format('g:i a') }}
                                                </div>
                                                <div class="text-muted small">
                                                    {{ $duration }} min
                                                </div>
                                            </td>

                                            <!-- Class Name -->
                                            <td class="fw-bold fs-6" style="color: var(--soma-secondary);">
                                                {{ $schedule->class_name }}
                                            </td>

                                            <!-- Days -->
                                            <td class="fw-bold fs-6">
                                                @foreach ($schedule->days as $day)
                                                    <span class="badge bg-light me-1">{{ $day }}</span>
                                                @endforeach
                                            </td>

                                            <!-- Instructors -->
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    @if($schedule->instructors && $schedule->instructors->count() > 0)
                                                        <div class="avatar-group">
                                                            @foreach($schedule->instructors as $instructor)
                                                                <span style="color: var(--soma-secondary); font-weight: 600;">{{ $instructor->user->name ?? 'Instructor' }}</span>
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
                                                    <span class="fw-semibold {{ $spotsLeft <= 0 ? 'text-danger' : '' }}"
                                                        style="font-size: 0.9rem; color: {{ $spotsLeft > 0 ? 'var(--soma-primary)' : '' }};">
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
                        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                            <div class="modal-header bg-light border-bottom">
                                <div>
                                    <h5 class="modal-title fw-bold" style="color: var(--soma-secondary);" id="instructorModalLabel{{ $schedule->id }}">
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
                                            $instructorAvatar = $instructor->user->profile_photo_url ?? "https://ui-avatars.com/api/?name=" . urlencode($instructorName) . "&background=8D7E71&color=fff";
                                        @endphp
                                        <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="{{ $instructorAvatar }}" alt="{{ $instructorName }}"
                                                    class="rounded-circle object-fit-cover shadow-sm" style="width: 45px; height: 45px;">
                                                <div>
                                                    <h6 class="mb-0 fw-bold" style="color: var(--soma-secondary);">{{ $instructorName }}</h6>
                                                    <small class="text-muted">{{ $instructorEmail }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="modal-footer bg-light border-top py-2">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="background-color: var(--soma-secondary); border: none;">Close</button>
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
                        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                            <div class="modal-header bg-light border-bottom">
                                <div>
                                    <h5 class="modal-title fw-bold" style="color: var(--soma-secondary);" id="bookingModalLabel{{ $schedule->id }}">
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
                                            $studentAvatar = $booking->user->profile_photo_url ?? "https://ui-avatars.com/api/?name=" . urlencode($studentName) . "&background=8D7E71&color=fff";
                                        @endphp
                                        <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="{{ $studentAvatar }}" alt="{{ $studentName }}"
                                                    class="rounded-circle object-fit-cover shadow-sm" style="width: 45px; height: 45px;">
                                                <div>
                                                    <h6 class="mb-0 fw-bold" style="color: var(--soma-secondary);">{{ $studentName }}</h6>
                                                    <small class="text-muted">{{ $studentEmail }}</small>
                                                </div>
                                            </div>
                                            <div>
                                                <span
                                                    class="badge {{ strtolower($booking->status) == 'active' ? '' : 'bg-secondary' }}" style="{{ strtolower($booking->status) == 'active' ? 'background-color: var(--soma-primary);' : '' }}">
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
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="background-color: var(--soma-secondary); border: none;">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endforeach

    <!-- DataTables Scripts & Initializer -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if ($('#basic-datatables').length) {
                $('#basic-datatables').DataTable({
                    "order": [[0, "desc"]],
                    "paging": true,
                    "ordering": true,
                    "info": true,
                    "language": {
                        "search": "Search Schedule:",
                        "lengthMenu": "Show _MENU_ entries",
                        "emptyTable": "No schedules found."
                    }
                });
            }
        });
    </script>
@endsection