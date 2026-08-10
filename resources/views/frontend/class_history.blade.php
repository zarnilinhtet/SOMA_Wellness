@extends('layouts.link')
@section('content')

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <style>
        .section-bg {
            background-color: var(--soma-cream, #FFF7E9);
            padding-bottom: 2rem;
            padding-top: 5rem;
        }

        .section-title {
            font-family: 'Cormorant Garamond', serif;
            color: var(--soma-taupe, #8D7E71);
            font-size: 3.5rem;
            margin-bottom: 1rem;
        }

        .history-table-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #eef2f5;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
            padding: 24px;
            margin-bottom: 40px;
            margin-top: -30px;
            position: relative;
            z-index: 10;
        }

        .history-table thead th {
            border-bottom: 2px solid #f1f5f9;
            border-top: none;
            color: #475569;
            font-size: 13px;
            font-weight: 700;
            padding: 16px 12px;
            background-color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .history-table tbody td {
            padding: 16px 12px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-size: 14px;
        }

        .date-soma-text {
            color: var(--soma-beige, #BE9676);
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .time-text {
            font-weight: 700;
            color: #1e293b;
            font-size: 14px;
        }

        .class-main-title {
            font-size: 15px;
            font-weight: 700;
            color: #1a1f2c;
            margin: 0 0 4px 0;
        }

        .badge-category {
            color: var(--soma-beige, #BE9676);
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Status Badges */
        .status-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
        }

        .bg-confirmed { background-color: #e6f9f3; color: #10b981; border: 1px solid #a7f3d0; }
        .bg-waitlisted { background-color: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
        .bg-cancelled { background-color: #fee2e2; color: #ef4444; border: 1px solid #fecaca; }
        
        .class-status-upcoming { color: #3b82f6; font-size: 12px; font-weight: 600; }
        .class-status-completed { color: #64748b; font-size: 12px; font-weight: 600; }

        /* DataTables Customization */
        div.dataTables_wrapper div.dataTables_length select,
        div.dataTables_wrapper div.dataTables_filter input {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 6px 12px;
            outline: none;
        }
        div.dataTables_wrapper div.dataTables_filter input:focus {
            border-color: var(--soma-beige, #BE9676);
            box-shadow: 0 0 0 3px rgba(190, 150, 118, 0.1);
        }
    </style>

    <div class="section-bg">
        <div class="container text-center">
            <h1 class="section-title">My Class History</h1>
            <p class="text-muted fs-5 lh-lg mx-auto" style="max-width: 600px;">
                Review all your past attendances, upcoming classes, and waitlist statuses here.
            </p>
        </div>
    </div>

    <div class="container">
        <div class="history-table-card">
            <div class="table-responsive">
                <table id="historyDataTable" class="table history-table w-100">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No.</th>
                            <th style="width: 20%;"> Date & Time</th>
                            <th style="width: 25%;">Class Info</th>
                            <th style="width: 20%;">Instructor</th>
                            <th style="width: 15%;">Booking Status</th>
                            <th style="width: 15%;">Class Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $index => $booking)
                            @php
                                $class = $booking->classSchedule;
                                
                                // Safe checks in case a class was deleted from DB but booking remains
                                if(!$class) continue;

                                $startTime = \Carbon\Carbon::parse($class->start_time);
                                $endTime = \Carbon\Carbon::parse($class->end_time);
                                $duration = $startTime->diffInMinutes($endTime);
                                
                                // Determine if class is in the past or future
                                $classDateTime = \Carbon\Carbon::parse($class->start_date . ' ' . $class->start_time);
                                $isCompleted = now()->greaterThan($classDateTime);
                            @endphp
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                
                                <!-- Date & Time -->
                                <td>
                                    <div class="date-soma-text">
                                        <i class="far fa-calendar-alt"></i> 
                                        {{ \Carbon\Carbon::parse($class->start_date)->format('l, d M Y') }}
                                    </div>
                                    <div class="time-text">{{ $startTime->format('g:i A') }} - {{ $endTime->format('g:i A') }}</div>
                                    <div class="text-muted small mt-1"><i class="far fa-clock"></i> {{ $duration }} min</div>
                                </td>
                                
                                <!-- Class Info -->
                                <td>
                                    <h4 class="class-main-title">{{ $class->class_name }}</h4>
                                    <div class="badge-category">{{ $class->category->name ?? 'Uncategorized' }}</div>
                                    <div class="text-muted small mt-1">Booked on: {{ $booking->created_at->format('d M Y, g:i A') }}</div>
                                </td>
                                
                                <!-- Instructor -->
                                <td class="text-muted small">
                                    @if(isset($class->instructorList) && $class->instructorList->count() > 0)
                                        @foreach ($class->instructorList as $inst)
                                            <div class="mb-1">
                                                <i class="fas fa-user-circle" style="color: var(--soma-beige);"></i> 
                                                Tr. {{ $inst->user->name ?? 'Unknown' }}
                                            </div>
                                        @endforeach
                                    @else
                                        <span>-</span>
                                    @endif
                                </td>
                                
                                <!-- Booking Status -->
                                <td>
                                    @if($booking->status == 'confirmed')
                                        <span class="status-badge bg-confirmed"><i class="fas fa-check-circle me-1"></i> Confirmed</span>
                                    @elseif($booking->status == 'waitlisted')
                                        <span class="status-badge bg-waitlisted"><i class="fas fa-hourglass-half me-1"></i> Waitlisted</span>
                                    @elseif($booking->status == 'cancelled')
                                        <span class="status-badge bg-cancelled"><i class="fas fa-times-circle me-1"></i> Cancelled</span>
                                    @else
                                        <span class="status-badge bg-secondary text-white">{{ ucfirst($booking->status) }}</span>
                                    @endif
                                </td>

                                <!-- Class Status (Upcoming vs Completed) -->
                                <td>
                                    @if(strtolower($class->status) == 'cancelled')
                                        <span class="class-status-completed text-danger"><i class="fas fa-ban me-1"></i> Class Cancelled</span>
                                    @elseif($isCompleted)
                                        <span class="class-status-completed"><i class="fas fa-history me-1"></i> Completed</span>
                                    @else
                                        <span class="class-status-upcoming"><i class="fas fa-calendar-day me-1"></i> Upcoming</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <!-- Empty state is handled by DataTables automatically -->
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scripts for DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#historyDataTable').DataTable({
                "pageLength": 10,
                "lengthMenu": [10, 25, 50, 100],
                "order": [], // Default array order (latest first as per Controller)
                "language": {
                    "search": "Search History:",
                    "lengthMenu": "Show _MENU_ entries",
                    "emptyTable": "You haven't booked any classes yet."
                },
                "columnDefs": [
                    { "orderable": false, "targets": [4, 5] } // Disable sorting on status columns
                ],
                "dom": "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 d-flex justify-content-end'f>>" +
                       "<'row'<'col-sm-12'tr>>" +
                       "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
            });
        });
    </script>

@endsection