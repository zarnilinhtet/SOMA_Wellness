@extends('layouts.link')
@section('content')

    <!-- Added Google Fonts for Fahkwang -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fahkwang:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <style>
        /* --- Brand Variables --- */
        :root {
            --soma-primary: #BE9676; /* Perfect Beige */
            --soma-secondary: #8D7E71; /* Desert Taupe */
            --soma-bg: #FFF7E9; /* Soft Cream */
        }

        /* --- Global Font Settings --- */
        body, h1, h2, h3, h4, h5, h6, p, span, a, div, button, input, textarea, table, th, td {
            font-family: 'Fahkwang', sans-serif !important;
        }

        /* --- General Section Styling --- */
        .section-bg { background-color: var(--soma-bg); padding-bottom: 2rem; padding-top: 5rem; }
        .section-title { color: var(--soma-secondary); font-size: 3.5rem; margin-bottom: 1rem; font-weight: 600; }
        
        /* --- Custom Table Theming with #8D7E71 & #BE9676 --- */
        .color-dark { color: var(--soma-secondary) !important; }
        .color-light { color: var(--soma-primary) !important; }

        .schedule-table-card { 
            background: #ffffff; 
            border-radius: 12px; 
            border: 1px solid rgba(190, 150, 118, 0.4); 
            box-shadow: 0 6px 16px rgba(141, 126, 113, 0.12); 
            padding: 24px 0 0 0; /* Adjusted padding to accommodate filter buttons */
            margin-bottom: 40px; 
            margin-top: -30px;
            position: relative;
            z-index: 10;
            overflow: hidden; 
        }
        
        .schedule-table { margin-bottom: 0; }

        .schedule-table thead th { 
            background-color: var(--soma-secondary); 
            color: #ffffff; 
            font-size: 13px; 
            font-weight: 700; 
            padding: 18px 14px; 
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
            border: none;
        }

        .schedule-table tbody td { 
            padding: 18px 14px; 
            vertical-align: middle; 
            border-bottom: 1px solid rgba(190, 150, 118, 0.25); 
            color: var(--soma-secondary); 
            font-size: 14px; 
            font-weight: 500;
        }

        .schedule-table tbody tr:hover td {
            background-color: rgba(190, 150, 118, 0.08); 
        }

        .schedule-table tbody tr:last-child td { border-bottom: none; }

        /* Typography & Icons */
        .date-soma-text { color: var(--soma-secondary); font-size: 13px; font-weight: 700; margin-bottom: 4px; display: flex; align-items: center; gap: 6px; }
        .date-soma-text i { color: var(--soma-primary); }
        
        .time-text { font-weight: 700; color: var(--soma-secondary); font-size: 14px; display: flex; align-items: center; }
        .duration-text { font-size: 12px; color: rgba(141, 126, 113, 0.85); margin-top: 2px; }
        .class-main-title { font-size: 15px; font-weight: 700; color: var(--soma-secondary); margin: 0 0 4px 0; letter-spacing: -0.3px; }
        .badge-status-active-text { color: var(--soma-primary); font-weight: 700; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }

        /* Status Pills */
        .status-pill { font-size: 10px; font-weight: 700; padding: 4px 10px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; margin-top: 4px; }
        .pill-approved { background-color: var(--soma-secondary); color: #ffffff; }
        .pill-waitlist { background-color: var(--soma-primary); color: #ffffff; }
        .pill-cancelled { border: 1px solid var(--soma-secondary); color: var(--soma-secondary); background: transparent; }
        
        /* Filter Toggle Buttons */
        .btn-outline-custom {
            color: var(--soma-secondary);
            border-color: rgba(141, 126, 113, 0.3);
            background-color: #ffffff;
            font-weight: 600;
            padding: 8px 24px;
            transition: all 0.3s ease;
        }
        .btn-check:checked + .btn-outline-custom {
            background-color: var(--soma-secondary);
            color: #ffffff;
            border-color: var(--soma-secondary);
        }
        .btn-outline-custom:hover {
            background-color: rgba(141, 126, 113, 0.1);
            color: var(--soma-secondary);
        }
        .btn-check:checked + .btn-outline-custom:hover {
            background-color: var(--soma-secondary);
            color: #ffffff;
        }

        /* DataTables Customization for Theme */
        div.dataTables_wrapper { padding: 0 24px 24px 24px; }
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
        
        /* Pagination Colors */
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

    <div class="section-bg">
        <div class="container text-center">
            <h1 class="section-title">My Class History</h1>
            <p class="fs-5 lh-lg mx-auto" style="color: rgba(141, 126, 113, 0.85); max-width: 600px;">
                Review all your past attendances, upcoming classes, and waitlist statuses here.
            </p>
        </div>
    </div>

    <div class="container">
        <div class="schedule-table-card">
            
            <!-- Custom Filter Toggle Buttons -->
            <div class="d-flex justify-content-center mb-4 px-4">
                <div class="btn-group shadow-sm" role="group" aria-label="Class Status Filter">
                    <input type="radio" class="btn-check status-filter" name="statusFilter" id="filterAll" value="" checked>
                    <label class="btn btn-outline-custom" for="filterAll">All</label>

                    <input type="radio" class="btn-check status-filter" name="statusFilter" id="filterUpcoming" value="Upcoming">
                    <label class="btn btn-outline-custom" for="filterUpcoming">Upcoming</label>

                    <input type="radio" class="btn-check status-filter" name="statusFilter" id="filterCompleted" value="Completed">
                    <label class="btn btn-outline-custom" for="filterCompleted">Completed</label>
                </div>
            </div>

            <div class="table-responsive" style="overflow-x: auto;">
                <table id="historyDataTable" class="table schedule-table w-100">
                    <thead>
                        <tr>
                            <th style="width: 5%;">No.</th>
                            <th style="width: 15%;">Date & Time</th>
                            <th style="width: 15%;">Class Duration</th>
                            <th style="width: 20%;">Class Info</th>
                            <th style="width: 15%;">Instructor</th>
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
                                
                                // History Date: Use booked_date if exists, otherwise fallback to class start_date
                                $displayDateStr = $booking->booked_date ? \Carbon\Carbon::parse($booking->booked_date)->format('Y-m-d') : \Carbon\Carbon::parse($class->start_date)->format('Y-m-d');
                                $displayDate = \Carbon\Carbon::parse($displayDateStr);
                                
                                // Determine if class is in the past or future
                                $classDateTime = \Carbon\Carbon::parse($displayDateStr . ' ' . $class->start_time, 'Asia/Yangon');
                                $isCompleted = now('Asia/Yangon')->greaterThan($classDateTime);
                            @endphp
                            <tr>
                                <td class="color-dark fw-bold">{{ $loop->iteration }}</td>
                                
                                <!-- Date & Time -->
                                <td>
                                    <div class="date-soma-text">
                                        <i class="far fa-calendar-check"></i> 
                                        {{ $displayDate->format('d M Y') }}
                                    </div>
                                    <div class="time-text mt-1">
                                        <i class="far fa-clock me-1 color-light" style="font-size: 0.9rem;"></i> 
                                        <span class="color-dark">{{ $startTime->format('h:i A') }} - {{ $endTime->format('h:i A') }}</span>
                                    </div>
                                </td>

                                <!-- Class Duration -->
                                <td>
                                    <div class="d-flex flex-column" style="font-size: 0.85rem;">
                                        <span class="color-dark fw-bold"><i class="fas fa-play-circle me-1"></i> {{ \Carbon\Carbon::parse($class->start_date)->format('d M Y') }}</span>
                                        <span class="color-light fw-bold mt-1"><i class="fas fa-stop-circle me-1"></i> {{ $class->end_date ? \Carbon\Carbon::parse($class->end_date)->format('d M Y') : 'Ongoing' }}</span>
                                    </div>
                                    <div class="duration-text mt-2"><i class="fas fa-hourglass-half me-1"></i>{{ $duration }} mins / day</div>
                                </td>
                                
                                <!-- Class Info -->
                                <td>
                                    <h4 class="class-main-title">{{ $class->class_name }}</h4>
                                    <div class="badge-status-active-text">{{ $class->category->name ?? 'Uncategorized' }}</div>
                                    <div class="duration-text mt-2">
                                        <i class="fas fa-receipt me-1 color-light"></i> Booked: {{ $booking->created_at->format('d M Y, h:i A') }}
                                    </div>
                                </td>
                                
                                <!-- Instructor -->
                                <td class="color-dark small">
                                    @if(isset($class->instructorList) && $class->instructorList->count() > 0)
                                        @foreach ($class->instructorList as $inst)
                                            <div class="mb-1 fw-bold">
                                                <i class="fas fa-user-circle me-1 color-light"></i> 
                                                Tr. {{ $inst->user->name ?? 'Unknown' }}
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="color-light">-</span>
                                    @endif
                                </td>
                                
                                <!-- Booking Status -->
                                <td>
                                    @if($booking->status == 'confirmed')
                                        <span class="status-pill pill-approved"><i class="fas fa-check-circle me-1"></i> Confirmed</span>
                                    @elseif($booking->status == 'waitlisted')
                                        <span class="status-pill pill-waitlist"><i class="fas fa-hourglass-half me-1"></i> Waitlisted</span>
                                    @elseif($booking->status == 'cancelled')
                                        <span class="status-pill pill-cancelled"><i class="fas fa-times-circle me-1"></i> Cancelled</span>
                                    @else
                                        <span class="status-pill pill-open">{{ ucfirst($booking->status) }}</span>
                                    @endif
                                </td>

                                <!-- Class Status (Upcoming vs Completed) -->
                                <td>
                                    @if(strtolower($class->status) == 'cancelled')
                                        <div class="fw-bold color-dark" style="font-size: 12px;"><i class="fas fa-ban me-1 color-light"></i> Class Cancelled</div>
                                    @elseif($isCompleted)
                                        <div class="fw-bold color-dark" style="font-size: 12px;"><i class="fas fa-history me-1 color-light"></i> Completed</div>
                                    @else
                                        <div class="fw-bold color-light" style="font-size: 12px;"><i class="fas fa-calendar-day me-1"></i> Upcoming</div>
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
            var table = $('#historyDataTable').DataTable({
                "pageLength": 10,
                "lengthMenu": [10, 25, 50, 100],
                "order": [], // Default array order (latest first as per Controller)
                "language": {
                    "search": "Search History:",
                    "lengthMenu": "Show _MENU_ entries",
                    "emptyTable": "You haven't booked any classes yet."
                },
                "columnDefs": [
                    { "orderable": false, "targets": [5, 6] } // Disable sorting on status columns
                ],
                "dom": "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 d-flex justify-content-end'f>>" +
                       "<'row'<'col-sm-12'tr>>" +
                       "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
            });

            // Toggle Filter Logic (All / Upcoming / Completed)
            $('.status-filter').on('change', function() {
                var filterValue = $(this).val();
                
                // Column 6 (index 6) is the "Class Status" column
                if (filterValue) {
                    // Exact match search using regex to prevent partial match overlaps
                    table.column(6).search(filterValue).draw();
                } else {
                    // Clear search when 'All' is selected
                    table.column(6).search('').draw();
                }
            });
        });
    </script>

@endsection