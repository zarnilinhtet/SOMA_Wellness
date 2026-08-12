@extends('layouts.link')
@section('content')

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <style>
        /* --- General Section Styling --- */
        .section-bg {
            background-color: var(--soma-cream);
            padding-bottom: 2rem;
            padding-top: 5rem;
        }

        .section-title {
            font-family: 'Cormorant Garamond', serif;
            color: var(--soma-taupe);
            font-size: 3.5rem;
            margin-bottom: 1rem;
        }
        
        /* --- Divider --- */
        .modern-divider {
            width: 60px;
            height: 3px;
            background-color: var(--soma-beige);
            margin: 4rem auto;
            border-radius: 3px;
        }

        /* --- Custom Table Styling (Matching Screenshot) --- */
        .schedule-table-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #eef2f5;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
            padding: 20px;
            margin-bottom: 30px;
        }

        .schedule-table thead th {
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

        .schedule-table tbody td {
            padding: 16px 12px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-size: 14px;
        }

        .date-soma-text {
            color: #BE9676; /* Changed from Blue to Soma Color */
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

        .duration-text {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        .class-main-title {
            font-size: 15px;
            font-weight: 700;
            color: #1a1f2c;
            margin: 0 0 4px 0;
            letter-spacing: -0.3px;
        }

        .badge-status-active-text {
            color: #BE9676;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .day-badge {
            display: inline-block;
            padding: 4px 10px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            margin-right: 4px;
            margin-bottom: 4px;
        }

        /* Status Pill */
        .status-pill {
            font-size: 10px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
            margin-top: 4px;
        }
        .pill-approved { background-color: #e6f9f3; color: #10b981; }
        .pill-waitlist { background-color: #fef3c7; color: #d97706; }
        .pill-cancelled { background-color: #fee2e2; color: #ef4444; }
        .pill-open { background-color: #e0f2fe; color: #0369a1; }
        .pill-full { background-color: #f3f4f6; color: #6b7280; }

        /* Action Buttons from Original Card */
        .action-buttons-flex {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }
        .btn-action-primary, .btn-action-wait, .btn-action-cancel, .btn-action-secondary {
            border: none;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            transition: background 0.15s ease;
            white-space: nowrap;
        }

        .btn-soma-secondary {
                background-color: var(--soma-taupe);
                color: var(--text-light);
                border: none;
        }

        .btn-soma-secondary:hover {
                background-color: var(--text-dark);
                color: var(--text-light);
        }

        .btn-soma-danger {
                background-color: #f87171;
                color: var(--text-light);
                border: none;
        }

        .btn-soma-danger:hover {
                background-color: #ef4444;
                color: var(--text-light);
        }

        .btn-soma-primary {
                background-color: var(--soma-taupe);
                color: var(--text-light);
                border: none;
        }

        .btn-soma-primary:hover {
                background-color: var(--text-dark);
                color: var(--text-light);
        }

           .btn-soma-teaching {
                background-color: #897af7;
                color: var(--text-light);
                border: none;
            }

            .btn-soma-teaching:hover {
                background-color: #7a6ae0;
                color: var(--text-light);
            }
        .btn-action-primary { background: #BE9676; color: #ffffff; }
        .btn-action-primary:hover { background: #a67c5c; }
        .btn-action-wait { background: #d1c8b9; color: #334155; }
        .btn-action-wait:hover { background: #b7a78c; }
        .btn-action-cancel { background: #f87171; color: #ffffff; }
        .btn-action-cancel:hover { background: #ef4444; }
        .btn-action-secondary { background: #ffffff; color: #475569; border: 1px solid #e2e8f0; }
        .btn-action-secondary:hover { background: #f8fafc; }

        /* --- Original Filter Area Styling --- */
        .soma-filter-workspace {
            background: #ffffff;
            padding: 28px;
            border-radius: 24px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.06);
            border: 1px solid #f1f1f1;
            margin-bottom: 30px;
        }
        .filter-header { margin-bottom: 24px; }
        .filter-header h3 { font-size: 28px; font-weight: 700; color: #222; margin-bottom: 6px; }
        .filter-header p { color: #777; font-size: 15px; margin: 0; }
        
        .input-group { position: relative; }
        .input-group i {
            position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #999; font-size: 14px; z-index: 2;
        }
        .input-group div.from, .input-group div.to {
            position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #999; font-size: 14px; z-index: 2;
        }
        .input-group input, .input-group select {
            width: 100%; height: 56px; border-radius: 16px !important; border: 1px solid #e5e7eb; padding: 0 18px 0 46px; font-size: 15px; background: #fafafa; transition: all 0.25s ease; outline: none; appearance: none;
        }
        .input-group input:focus, .input-group select:focus {
            border-color: #c9a46c; background: #fff; box-shadow: 0 0 0 4px rgba(201, 164, 108, 0.12);
        }
        .btn-reset {
            height: 56px; border-radius: 14px; background: var(--soma-cream); color: #444; border: none; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.25s ease;
        }
        .btn-reset:hover { background: #e5e7eb; }

        @media (max-width:768px) {
            .soma-filter-workspace { padding: 20px; border-radius: 20px; }
            .filter-header h3 { font-size: 22px; }
            .input-group input, .input-group select { height: 52px; font-size: 14px; }
        }

        /* --- Yoga Gallery --- */
        .gallery-item { position: relative; overflow: hidden; border-radius: 12px; width: 100%; }
        .gallery-wide { aspect-ratio: 16/9; }
        .gallery-square { aspect-ratio: 1/1; }
        .gallery-item img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.7s cubic-bezier(0.2, 1, 0.3, 1); }
        .gallery-item:hover img { transform: scale(1.08); }
        .gallery-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(58, 51, 44, 0.8) 0%, rgba(58, 51, 44, 0) 60%); display: flex; align-items: flex-end; padding: 2rem; opacity: 0; transition: opacity 0.4s ease; }
        .gallery-item:hover .gallery-overlay { opacity: 1; }
        .gallery-overlay h4 { color: var(--soma-cream); margin: 0; font-family: 'Cormorant Garamond', serif; font-size: 1.8rem; transform: translateY(20px); transition: transform 0.4s ease; }
        .gallery-item:hover .gallery-overlay h4 { transform: translateY(0); }
        
        /* Pagination */
        .soma-pagination-wrap { display: flex; justify-content: center; }
        .soma-pagination { display: flex; gap: 8px; list-style: none; padding: 0; margin: 0; }
        .soma-pagination li a, .soma-pagination li span { display: inline-flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; border-radius: 50%; text-decoration: none; font-weight: 500; font-size: 0.85rem; border: 1px solid rgba(190, 150, 118, 0.2); background: transparent; color: var(--soma-taupe); transition: all 0.3s ease; }
        .soma-pagination li a:hover, .soma-pagination li.active span { background: var(--soma-beige); color: white; border-color: var(--soma-beige); }
        .soma-pagination li.disabled span { opacity: 0.3; cursor: not-allowed; }
    </style>

    <div class="section-bg">
        <div class="container text-center">
            <h1 class="section-title">Featured Classes</h1>
            <p class="text-muted fs-5 lh-lg mx-auto" style="max-width: 700px;">
                Discover our signature sessions designed to help you build strength, find your center, and elevate your mind.
            </p>
        </div>
    </div>

    <div class="container mt-4">
        <!-- LUXURY FILTER ENGINE MATRIX -->
        <div class="soma-filter-workspace">
            <div class="filter-header">
                <h3>Find Your Class</h3>
                <p>Search, filter and discover sessions instantly</p>
            </div>

            {{-- SEARCH - Full Width --}}
            <div class="row g-3 mb-3">
                <div class="col-12">
                    <div class="input-group">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                            placeholder="Search classes...">
                    </div>
                </div>
            </div>

            {{-- 5-COLUMN GRID --}}
            <div class="row g-3">
                <div class="col-md">
                    <div class="input-group">
                       <div class="from">from</div> 
                        <input class="from" type="date" name="from_date" value="{{ request('from_date') }}" title="From Date">
                    </div>
                </div>
                <div class="col-md">
                    <div class="input-group">
                       <div class="to">To</div> 
                        <input class="to" type="date" name="to_date" value="{{ request('to_date') }}" title="To Date">
                    </div>
                </div>
                <div class="col-md">
                    <div class="input-group">
                        <i class="fas fa-layer-group"></i>
                        <select name="category">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md">
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <select name="instructor">
                            <option value="">All Instructors</option>
                            @foreach($instructors as $instructor)
                                <option value="{{ $instructor->id }}" {{ request('instructor') == $instructor->id ? 'selected' : '' }}>
                                    {{ $instructor->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-1">
                    <a href="{{ url()->current() }}" id="btn-reset" class="btn-reset w-100">
                        <i class="fa fa-refresh" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>

        <div id="classResults">
            <!-- NEW TABLE LIST UI -->
            <div class="schedule-table-card">
                <div class="table-responsive">
                    <table class="table schedule-table table-hover w-100">
                        <thead>
                            <tr>
                                <th style="width: 5%;">No.</th>
                                <th style="width: 15%;">Date & Time</th>
                                <th style="width: 25%;">Class Info</th>
                                <th style="width: 10%;">Days</th>
                                <th style="width: 15%;">Instructors</th>
                                <th style="width: 15%;">Availability</th>
                                <th style="width: 15%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classes as $index => $class)
                                @php
                                    $startTime = \Carbon\Carbon::parse($class->start_time);
                                    $endTime = \Carbon\Carbon::parse($class->end_time);
                                    $duration = $startTime->diffInMinutes($endTime);
                                    // Occupancy Logic
                                    $totalCapacity = $class->capacity ?? 0;
                                    $bookedSlots = $bookingsAll->where('selected_class_id', $class->id)->where('status', 'confirmed')->count();
                                    if ($bookedSlots > $totalCapacity) {
                                        $bookedSlots = $totalCapacity;
                                    }
                                    $remainingSlots = max(0, $totalCapacity - $bookedSlots);

                                    // Days Array Parse
                                    $days = is_string($class->days) ? json_decode($class->days, true) : ($class->days ?? []);

                                    // Instructor Check-in & Over logic
                                    $todayDate = \Carbon\Carbon::now()->format('Y-m-d');
                                    $now = \Carbon\Carbon::now();
                                    $instructorUserIds = $class->instructor_ids ?? [];
                                    $classEndTime = \Carbon\Carbon::parse($todayDate . ' ' . ($class->end_time ?? $class->time_to ?? '23:59:59'));
                                    $isClassOver = $now->greaterThan($classEndTime);

                                    $isInstructorCheckedInToday = false;
                                    if (!$isClassOver) {
                                        $isInstructorCheckedInToday = \App\Models\Attendance::where('class_id', $class->id)
                                            ->whereIn('instructor_id', $instructorUserIds)
                                            ->whereDate('attendance_date', $todayDate)
                                            ->where('attended', 1)
                                            ->exists();
                                    }

                                    // Booking states
                                    $confirmedBooking = $bookings->where('selected_class_id', $class->id)->where('status', 'confirmed')->first();
                                    $cancelledBooking = $bookings->where('selected_class_id', $class->id)->where('status', 'cancelled')->first();
                                    $hasAnyBooking = $bookings->where('selected_class_id', $class->id)->whereIn('status', ['confirmed', 'waitlisted'])->first();

                                    // Cancellation Window
                                    $classStart = \Carbon\Carbon::parse($class->start_date . ' ' . $class->start_time);
                                    $canCancel = now()->diffInHours($classStart, false) >= 48;

                                    // Duration Calculation
                                    $duration = \Carbon\Carbon::parse($class->start_time)->diffInMinutes(\Carbon\Carbon::parse($class->end_time));
                                @endphp
                                <tr>
                                    <td class="text-muted">{{ $loop->iteration }}</td>
                                    
                                    <td>
                                        <div class="date-soma-text">
                                            <i class="far fa-calendar-alt"></i> 
                                            {{ \Carbon\Carbon::parse($class->start_date)->format('l, F j') }}
                                        </div>
                                        <div class="time-text">{{ $startTime->format('g:i a') }}</div>
                                        <div class="duration-text">{{ $duration }} min</div>
                                    </td>
                                    
                                    <td>
                                        <h4 class="class-main-title">{{ $class->class_name }}</h4>
                                        <div class="badge-status-active-text">{{ $class->category->name }}</div>
                                        
                                        <!-- Status Badge Logic from Original Card -->
                                        @if ($class->status == 'book')
                                            @auth
                                                @if($confirmedBooking)
                                                    <span class="status-pill pill-approved">Joined</span>
                                                @elseif($bookings->where('selected_class_id', $class->id)->where('status', 'waitlisted')->first())
                                                    <span class="status-pill pill-waitlist">Waitlisted</span>
                                                @elseif($cancelledBooking)
                                                    <span class="status-pill pill-cancelled">Cancelled</span>
                                                @else
                                                    @if($remainingSlots == 0)
                                                        <span class="status-pill pill-full">Full</span>
                                                    @else
                                                        <span class="status-pill pill-open">Open</span>
                                                    @endif
                                                @endif
                                            @else
                                                <span class="status-pill pill-open">Available</span>
                                            @endauth
                                        @elseif ($class->status == 'completed')
                                            <span class="status-pill pill-full">Completed</span>
                                        @elseif ($class->status == 'cancelled')
                                            <span class="status-pill pill-cancelled">Cancelled</span>
                                        @endif
                                    </td>
                                    
                                    <td>
                                        @if(!empty($days))
                                            @foreach($days as $day)
                                                <span class="day-badge">{{ $day }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    
                                    <td class="text-muted small">
                                        @foreach ($class->instructor as $inst)
                                            <div class="mb-1"><i class="fas fa-user-circle me-1"></i> Tr. {{ $inst['user']['name'] }}</div>
                                        @endforeach
                                    </td>
                                    
                                    <td>
                                        <div class="small text-muted fw-bold mb-1">{{ $bookedSlots }} / {{ $totalCapacity }} Slots</div>
                                        @if($remainingSlots == 0)
                                            <span class="text-secondary small fw-bold">Waitlist</span>
                                        @else
                                            <span class="text-success small fw-bold">{{ $remainingSlots }} Left</span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="action-buttons-flex">
                                            <button class="btn-action-secondary w-100 mb-1" onclick='window.location.href="{{ route('class.details', $class->id) }}"'>
                                                Details
                                            </button>
                                            
                                              @auth
                                                @if(!$confirmedBooking && $class->status == 'book' && !$cancelledBooking)
                                                    @if($isInstructorCheckedInToday && !$isClassOver)
                                                        <span class="btn btn-soma-teaching btn-sm rounded-3 px-3 py-1 w-100">Teaching...</span>
                                                    @else
                                                        @if($remainingSlots > 0)
                                                            <button onClick="joinClass({{ $class->id }})" class="btn btn-soma-primary btn-sm rounded-3 px-3 py-1 w-100">
                                                                Join
                                                            </button>
                                                        @elseif(!$hasAnyBooking)
                                                            <button onClick="joinClass({{ $class->id }})" class="btn btn-soma-secondary btn-sm  w-100 rounded-3 px-3 py-1">
                                                                WaitList
                                                            </button>
                                                        @endif
                                                    @endif
                                                @elseif($confirmedBooking && $class->status == 'book' && $canCancel)
                                                    <button onClick="cancelClass({{ $confirmedBooking->id }})" class="btn btn-soma-danger btn-sm rounded-3 px-3 py-1 w-100">
                                                        Cancel
                                                    </button>
                                                @elseif($isInstructorCheckedInToday && !$isClassOver)
                                                    <span class="btn btn-soma-teaching btn-sm rounded-3 px-3 py-1 w-100">Teaching...</span>
                                                @endif
                                            @endauth
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-box-open fs-3 mb-2 d-block"></i>
                                        No classes found matching your criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Layout Control --}}
                @if (method_exists($classes, 'lastPage') && $classes->lastPage() > 0)
                    <div class="soma-pagination-wrap mt-4">
                        <ul class="soma-pagination">
                            @if ($classes->onFirstPage())
                                <li class="disabled"><span><i class="fas fa-chevron-left"></i></span></li>
                            @else
                                <li><a href="{{ $classes->previousPageUrl() . '#classResults' }}"><i class="fas fa-chevron-left"></i></a></li>
                            @endif

                            @foreach ($classes->getUrlRange(1, $classes->lastPage()) as $page => $url)
                                @if ($page == $classes->currentPage())
                                    <li class="active"><span>{{ $page }}</span></li>
                                @else
                                    <li><a href="{{ $url . '#classResults' }}">{{ $page }}</a></li>
                                @endif
                            @endforeach

                            @if ($classes->hasMorePages())
                                <li><a href="{{ $classes->nextPageUrl() . '#classResults' }}"><i class="fas fa-chevron-right"></i></a></li>
                            @else
                                <li class="disabled"><span><i class="fas fa-chevron-right"></i></span></li>
                            @endif
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modern-divider"></div>

    @if(!$allImages)
    <div class="container text-center mb-5">
        <h1 class="section-title">Yoga Gallery</h1>
        <p class="text-muted">Moments of peace, captured in our sanctuary.</p>
    </div>

    <div class="container py-4 mb-5">
        <div class="row g-4">
            @foreach($allImages as $index => $item)
                @if($index % 3 === 0)
                    <div class="col-md-6">
                        <div class="row g-4">
                @endif

                @php $position = $index % 3; @endphp

                @if($position === 0)
                    <div class="col-12">
                        <div class="gallery-item gallery-wide">
                            <img src="{{ asset($item->image) }}" alt="{{ $item->title ?? 'Yoga Image' }}">
                            <div class="gallery-overlay">
                                <h4>{{ $item->title ?? 'SOMA Studio' }}</h4>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col-6">
                        <div class="gallery-item gallery-square">
                            <img src="{{ asset($item->image) }}" alt="{{ $item->title ?? 'Yoga Image' }}">
                            <div class="gallery-overlay">
                                <h4>{{ $item->title ?? 'SOMA Studio' }}</h4>
                            </div>
                        </div>
                    </div>
                @endif

                @if($index % 3 === 2 || $loop->last)
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('success'))
        <script>
            Swal.fire({ icon: 'success', title: 'Success', text: "{{ session('success') }}", confirmButtonColor: '#BE9676' });
        </script>
    @endif

    @if(session('warning'))
        <script>
            Swal.fire({ icon: 'warning', title: 'Waiting List / Closed', text: "{{ session('warning') }}", confirmButtonColor: '#BE9676' });
        </script>
    @endif

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const searchInput = document.getElementById("searchInput");
            const category = document.querySelector("select[name='category']");
            const instructor = document.querySelector("select[name='instructor']");
            const fromDate = document.querySelector("input[name='from_date']");
            const toDate = document.querySelector("input[name='to_date']");
            const classResults = document.getElementById("classResults");
            const btnReset = document.getElementById("btn-reset");

            let timeout;

            async function fetchData() {
                let search = searchInput.value;
                let cat = category.value;
                let inst = instructor.value;
                let from = fromDate.value;
                let to = toDate.value;

                try {
                    const response = await fetch(`/classes/search?search=${search}&category=${cat}&instructor=${inst}&from_date=${from}&to_date=${to}`);
                    const data = await response.text();
                    classResults.innerHTML = data;
                } catch (error) {
                    console.log("Search Error:", error);
                }
            }

            searchInput.addEventListener("keyup", function () {
                clearTimeout(timeout);
                timeout = setTimeout(() => { fetchData(); }, 300);
            });

            category.addEventListener("change", fetchData);
            instructor.addEventListener("change", fetchData);
            fromDate.addEventListener("change", fetchData);
            toDate.addEventListener("change", fetchData);

            btnReset.addEventListener("click", function (e) {
                e.preventDefault();
                searchInput.value = '';
                category.value = '';
                instructor.value = '';
                fromDate.value = '';
                toDate.value = '';
                fetchData();
            });
        });

        function joinClass(classId) {
            @php $isClosed = \App\Models\CloseDate::exists(); @endphp
            @if($isClosed)
                Swal.fire({ icon: 'warning', title: 'Studio Closed', text: 'You cannot join the class because the studio is closed.', confirmButtonColor: '#BE9676' });
                return;
            @endif

            Swal.fire({
                title: 'Join Class',
                text: 'Are you sure you want to join this class?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#BE9676',
                cancelButtonColor: '#999',
                confirmButtonText: 'Yes, Join',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `/join/class/${classId}`;
                }
            }); 
        }

        function cancelClass(classId) {
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
                    window.location.href = `/remove/class/${classId}`;
                }
            });
        }
    </script>
@endsection