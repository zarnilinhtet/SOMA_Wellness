@extends('layouts.link')
@section('content')

    <!-- Added Google Fonts for Fahkwang -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fahkwang:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <style>
        /* --- Brand Variables --- */
        :root {
            --soma-primary: #BE9676; /* Perfect Beige */
            --soma-secondary: #8D7E71; /* Desert Taupe */
            --soma-bg: #FFF7E9; /* Soft Cream */
            
            /* Aliases for existing variables */
            --soma-cream: var(--soma-bg);
            --soma-taupe: var(--soma-secondary);
            --soma-beige: var(--soma-primary);
        }

        /* --- Global Font Settings --- */
        body, h1, h2, h3, h4, h5, h6, p, span, a, div, button, input, select, table, th, td {
            font-family: 'Fahkwang', sans-serif !important;
        }

        /* --- General Section Styling --- */
        .section-bg { background-color: var(--soma-bg); padding-bottom: 2rem; padding-top: 5rem; }
        .section-title { color: var(--soma-secondary); font-size: 3.5rem; margin-bottom: 1rem; font-weight: 600; }
        .modern-divider { width: 60px; height: 3px; background-color: var(--soma-primary); margin: 4rem auto; border-radius: 3px; }
        
        /* --- Custom Table Theming with #8D7E71 & #BE9676 --- */
        .color-dark { color: var(--soma-secondary) !important; }
        .color-light { color: var(--soma-primary) !important; }

        .schedule-table-card { 
            background: #ffffff; 
            border-radius: 12px; 
            border: 1px solid rgba(190, 150, 118, 0.4); 
            box-shadow: 0 6px 16px rgba(141, 126, 113, 0.12); 
            padding: 0; 
            margin-bottom: 30px; 
            overflow: hidden; 
        }
        
        .schedule-table { margin-bottom: 0; }

        .schedule-table thead th { 
            background-color: var(--soma-secondary); color: #ffffff; font-size: 13px; font-weight: 700; padding: 18px 14px; text-transform: uppercase; letter-spacing: 0.5px; border: none;
        }

        .schedule-table tbody td { 
            padding: 18px 14px; vertical-align: middle; border-bottom: 1px solid rgba(190, 150, 118, 0.25); color: var(--soma-secondary); font-size: 14px; font-weight: 500;
        }

        .schedule-table tbody tr:hover td { background-color: rgba(190, 150, 118, 0.08); }
        .schedule-table tbody tr:last-child td { border-bottom: none; }

        .date-soma-text { color: var(--soma-secondary); font-size: 13px; font-weight: 700; margin-bottom: 4px; display: flex; align-items: center; gap: 6px; }
        .date-soma-text i { color: var(--soma-primary); }
        .time-text { font-weight: 700; color: var(--soma-secondary); font-size: 14px; display: flex; align-items: center; }
        .duration-text { font-size: 12px; color: rgba(141, 126, 113, 0.85); margin-top: 2px; }
        .class-main-title { font-size: 15px; font-weight: 800; color: var(--soma-secondary); margin: 0 0 4px 0; letter-spacing: -0.3px; }
        .badge-status-active-text { color: var(--soma-primary); font-weight: 700; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .day-badge { display: inline-block; padding: 6px 12px; background-color: #ffffff; border: 1px solid var(--soma-primary); border-radius: 6px; font-size: 12px; font-weight: 700; color: var(--soma-secondary); margin-right: 4px; margin-bottom: 4px; }
        .status-pill { font-size: 10px; font-weight: 700; padding: 4px 10px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; margin-top: 4px; }
        .pill-approved { background-color: var(--soma-secondary); color: #ffffff; }
        .pill-waitlist { background-color: var(--soma-primary); color: #ffffff; }
        .pill-cancelled { border: 1px solid var(--soma-secondary); color: var(--soma-secondary); background: transparent; }
        .pill-open { background-color: rgba(190, 150, 118, 0.15); color: var(--soma-secondary); }
        .pill-full { background-color: rgba(141, 126, 113, 0.15); color: var(--soma-secondary); }

        .action-buttons-flex { display: flex; gap: 6px; flex-wrap: wrap; flex-direction: column; } 
        .btn-action-secondary { background: #ffffff; color: var(--soma-secondary); border: 1px solid var(--soma-secondary); border-radius: 6px; padding: 6px 12px; font-size: 12px; font-weight: 600; transition: all 0.2s ease; }
        .btn-action-secondary:hover { background: var(--soma-secondary); color: #ffffff; }
        .btn-soma-primary { background-color: var(--soma-primary); color: #ffffff; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; transition: all 0.2s ease; }
        .btn-soma-primary:hover { background-color: var(--soma-secondary); color: #ffffff; }
        .btn-soma-secondary { background-color: var(--soma-secondary); color: #ffffff; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; transition: all 0.2s ease; }
        .btn-soma-secondary:hover { background-color: var(--soma-primary); color: #ffffff; }
        .btn-soma-danger { border: 1px solid var(--soma-secondary); color: var(--soma-secondary); background: transparent; border-radius: 6px; font-size: 12px; font-weight: 600; transition: all 0.2s ease; }
        .btn-soma-danger:hover { background-color: var(--soma-secondary); color: #ffffff; }
        .btn-soma-teaching { background-color: rgba(190, 150, 118, 0.2); color: var(--soma-secondary); border: 1px solid var(--soma-primary); border-radius: 6px; font-size: 12px; font-weight: 600; cursor: default; }
        
        .table-responsive { min-height: 350px; padding-bottom: 20px; overflow-x: auto; }

        /* --- Filter Area --- */
        .soma-filter-workspace { background: #ffffff; padding: 28px; border-radius: 24px; box-shadow: 0 10px 35px rgba(0, 0, 0, 0.06); border: 1px solid #f1f1f1; margin-bottom: 30px; }
        .filter-header { margin-bottom: 24px; }
        .filter-header h3 { font-size: 28px; font-weight: 700; color: #222; margin-bottom: 6px; }
        .filter-header p { color: #777; font-size: 15px; margin: 0; }
        .input-group { position: relative; }
        .input-group i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #999; font-size: 14px; z-index: 2; }
        .input-group div.from, .input-group div.to { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #999; font-size: 14px; z-index: 2; }
        .input-group input, .input-group select { width: 100%; height: 56px; border-radius: 16px !important; border: 1px solid #e5e7eb; padding: 0 18px 0 46px; font-size: 15px; background: #fafafa; transition: all 0.25s ease; outline: none; appearance: none; }
        .input-group input:focus, .input-group select:focus { border-color: var(--soma-primary); background: #fff; box-shadow: 0 0 0 4px rgba(190, 150, 118, 0.12); }
        .btn-reset { height: 56px; border-radius: 14px; background: #f8f9fa; color: var(--soma-secondary); border: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.25s ease; }
        .btn-reset:hover { background: var(--soma-secondary); color: white; border-color: var(--soma-secondary); }
        
        .gallery-item { position: relative; overflow: hidden; border-radius: 12px; width: 100%; }
        .gallery-wide { aspect-ratio: 16/9; }
        .gallery-square { aspect-ratio: 1/1; }
        .gallery-item img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.7s cubic-bezier(0.2, 1, 0.3, 1); }
        .gallery-item:hover img { transform: scale(1.08); }
        .gallery-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(58, 51, 44, 0.8) 0%, rgba(58, 51, 44, 0) 60%); display: flex; align-items: flex-end; padding: 2rem; opacity: 0; transition: opacity 0.4s ease; }
        .gallery-item:hover .gallery-overlay { opacity: 1; }
        .gallery-overlay h4 { color: var(--soma-bg); margin: 0; font-size: 1.8rem; transform: translateY(20px); transition: transform 0.4s ease; font-weight: 500; }
        .gallery-item:hover .gallery-overlay h4 { transform: translateY(0); }
        
        .soma-pagination-wrap { display: flex; justify-content: center; margin-bottom: 20px; }
        .soma-pagination { display: flex; gap: 8px; list-style: none; padding: 0; margin: 0; }
        .soma-pagination li a, .soma-pagination li span { display: inline-flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; border-radius: 50%; text-decoration: none; font-weight: 600; font-size: 0.85rem; border: 1px solid var(--soma-primary); background: transparent; color: var(--soma-secondary); transition: all 0.3s ease; }
        .soma-pagination li a:hover, .soma-pagination li.active span { background: var(--soma-secondary); color: white; border-color: var(--soma-secondary); }
        .soma-pagination li.disabled span { opacity: 0.4; cursor: not-allowed; border-color: rgba(141, 126, 113, 0.3); color: var(--soma-secondary); }
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
        <div class="soma-filter-workspace">
            <div class="filter-header">
                <h3>Find Your Class</h3>
                <p>Search, filter and discover sessions instantly</p>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12">
                    <div class="input-group">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" name="search" value="{{ request('search') }}" placeholder="Search classes...">
                    </div>
                </div>
            </div>

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
            <div class="schedule-table-card">
                <div class="table-responsive">
                    <table class="table schedule-table w-100">
                        <thead>
                            <tr>
                                <th style="width: 5%;">No.</th>
                                <th style="width: 15%;">Date & Time</th>
                                <th style="width: 15%;">Class Duration</th>
                                <th style="width: 20%;">Class Info</th>
                                <th style="width: 10%;">Day</th>
                                <th style="width: 15%;">Instructors</th>
                                <th style="width: 10%;">Availability</th>
                                <th style="width: 10%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classes as $index => $class)
                                @php
                                    // Controller မှ ပေးပို့လာသော target_date ကို အသုံးပြုမည်
                                    $targetDateStr = $class->target_date ?? \Carbon\Carbon::today('Asia/Yangon')->format('Y-m-d');
                                    $targetDate = \Carbon\Carbon::parse($targetDateStr);
                                    
                                    $displayDayName = $targetDate->format('l'); // ဥပမာ - 'Monday'

                                    $startTime = \Carbon\Carbon::parse($class->start_time);
                                    $endTime = \Carbon\Carbon::parse($class->end_time);
                                    $duration = $startTime->diffInMinutes($endTime);
                                    
                                    $totalCapacity = $class->capacity ?? 0;
                                    
                                    // Target Date ဖြင့်သာ Availability ကို စစ်ဆေးပါမည်
                                    $bookedSlots = $bookingsAll->where('selected_class_id', $class->id)
                                        ->where('status', 'confirmed')
                                        ->filter(function($b) use ($targetDateStr) {
                                            if (!empty($b->booked_date)) {
                                                $bDate = \Carbon\Carbon::parse($b->booked_date)->format('Y-m-d');
                                                return $bDate === $targetDateStr;
                                            }
                                            return false;
                                        })->count();

                                    if ($bookedSlots > $totalCapacity) {
                                        $bookedSlots = $totalCapacity;
                                    }
                                    $remainingSlots = max(0, $totalCapacity - $bookedSlots);

                                    $instructorUserIds = $class->instructor_ids ?? [];
                                    $classEndTimeToday = \Carbon\Carbon::parse($targetDateStr . ' ' . $class->end_time, 'Asia/Yangon');
                                    $isClassOver = \Carbon\Carbon::now('Asia/Yangon')->greaterThan($classEndTimeToday);
                                    
                                    $isInstructorCheckedInToday = false;
                                    if (!$isClassOver && $targetDateStr === \Carbon\Carbon::today('Asia/Yangon')->format('Y-m-d')) {
                                        $isInstructorCheckedInToday = \App\Models\Attendance::where('class_id', $class->id)
                                            ->whereIn('instructor_id', $instructorUserIds)
                                            ->whereDate('attendance_date', $targetDateStr)
                                            ->where('attended', 1)
                                            ->exists();
                                    }

                                    // User ကိုယ်တိုင် ထို Target Date တွင် Booking လုပ်ထားခြင်း ရှိမရှိ စစ်ဆေးပါမည်
                                    $confirmedBooking = collect();
                                    $waitlistedBooking = collect();
                                    $canCancel = false;

                                    if(auth()->check()) {
                                        $confirmedBooking = $bookings->where('selected_class_id', $class->id)
                                            ->where('status', 'confirmed')
                                            ->filter(function($b) use ($targetDateStr) {
                                                if (!empty($b->booked_date)) {
                                                    return \Carbon\Carbon::parse($b->booked_date)->format('Y-m-d') === $targetDateStr;
                                                }
                                                return false;
                                            })->first();

                                        $waitlistedBooking = $bookings->where('selected_class_id', $class->id)
                                            ->where('status', 'waitlisted')
                                            ->filter(function($b) use ($targetDateStr) {
                                                if (!empty($b->booked_date)) {
                                                    return \Carbon\Carbon::parse($b->booked_date)->format('Y-m-d') === $targetDateStr;
                                                }
                                                return false;
                                            })->first();

                                        $classStart = \Carbon\Carbon::parse($targetDateStr . ' ' . $class->start_time, 'Asia/Yangon');
                                        $canCancel = \Carbon\Carbon::now('Asia/Yangon')->diffInHours($classStart, false) >= 48;
                                    }
                                    
                                    $hasAnyBooking = $confirmedBooking || $waitlistedBooking;
                                @endphp
                                
                                <tr>
                                    <td class="color-dark fw-bold">{{ $loop->iteration }}</td>
                                    
                                    <td>
                                        <div class="date-soma-text">
                                            <i class="far fa-calendar-check"></i> 
                                            {{ $targetDate->format('d M Y') }}
                                        </div>
                                        <div class="time-text mt-1">
                                            <i class="far fa-clock me-1 color-light" style="font-size: 0.9rem;"></i> 
                                            <span class="color-dark">{{ $startTime->format('h:i A') }} - {{ $endTime->format('h:i A') }}</span>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="d-flex flex-column" style="font-size: 0.85rem;">
                                            <span class="color-dark fw-bold"><i class="fas fa-play-circle me-1"></i> {{ \Carbon\Carbon::parse($class->start_date)->format('d M Y') }}</span>
                                            <span class="color-light fw-bold mt-1"><i class="fas fa-stop-circle me-1"></i> {{ $class->end_date ? \Carbon\Carbon::parse($class->end_date)->format('d M Y') : 'Ongoing' }}</span>
                                        </div>
                                        <div class="duration-text mt-2"><i class="fas fa-hourglass-half me-1"></i>{{ $duration }} mins / day</div>
                                    </td>
                                    
                                    <td>
                                        <h4 class="class-main-title">{{ $class->class_name }}</h4>
                                        <div class="badge-status-active-text">{{ $class->category->name }}</div>
                                        
                                        @if ($class->status == 'cancelled')
                                            <span class="status-pill pill-cancelled">Cancelled</span>
                                        @else
                                            @auth
                                                @if($confirmedBooking)
                                                    <span class="status-pill pill-approved">Joined</span>
                                                @elseif($waitlistedBooking)
                                                    <span class="status-pill pill-waitlist">Waitlisted</span>
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
                                        @endif
                                    </td>
                                    
                                    <td>
                                        @if($displayDayName)
                                            <span class="day-badge">{{ $displayDayName }}</span>
                                        @else
                                            <span class="color-light small">-</span>
                                        @endif
                                    </td>
                                    
                                    <td class="color-dark small">
                                        @foreach ($class->instructor as $inst)
                                            <div class="mb-1 fw-bold"><i class="fas fa-user-circle me-1 color-light"></i> Tr. {{ $inst['user']['name'] }}</div>
                                        @endforeach
                                    </td>
                                    
                                    <td>
                                        <div class="small color-dark fw-bold mb-1">{{ $bookedSlots }} / {{ $totalCapacity }} Slots</div>
                                        @if($remainingSlots == 0)
                                            <span class="color-dark small fw-bold">Waitlist</span>
                                        @else
                                            <span class="color-light small fw-bold">{{ $remainingSlots }} Left</span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="action-buttons-flex">
                                            <button class="btn-action-secondary w-100" onclick='window.location.href="{{ route('class.details', $class->id) }}"'>
                                                Details
                                            </button>
                                            
                                            @auth
                                                @if($class->status != 'cancelled')
                                                    @if(!$confirmedBooking)
                                                        @if($remainingSlots > 0)
                                                            <button onClick="joinClass({{ $class->id }}, '{{ $targetDateStr }}')" class="btn btn-soma-primary btn-sm px-3 py-1 w-100">
                                                                Join
                                                            </button>
                                                        @elseif(!$hasAnyBooking)
                                                            <button onClick="joinClass({{ $class->id }}, '{{ $targetDateStr }}')" class="btn btn-soma-secondary btn-sm w-100 px-3 py-1">
                                                                WaitList
                                                            </button>
                                                        @endif
                                                    @elseif($confirmedBooking && $canCancel)
                                                        <button onClick="cancelClass({{ $confirmedBooking->id }}, '{{ $targetDateStr }}')" class="btn btn-soma-danger btn-sm px-3 py-1 w-100">
                                                            Cancel
                                                        </button>
                                                    @endif
                                                    
                                                    @if($isInstructorCheckedInToday && !$isClassOver && $targetDateStr === \Carbon\Carbon::today('Asia/Yangon')->format('Y-m-d'))
                                                        <span class="btn btn-soma-teaching btn-sm px-3 py-1 w-100 mt-1">Teaching...</span>
                                                    @endif
                                                @endif
                                            @endauth
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 color-dark">
                                        <i class="fas fa-box-open fs-3 mb-2 d-block color-light"></i>
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

    @if(!empty($allImages))
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
            Swal.fire({ icon: 'success', title: 'Success', text: "{{ session('success') }}", confirmButtonColor: '#8D7E71' });
        </script>
    @endif

    @if(session('warning'))
        <script>
            Swal.fire({ icon: 'warning', title: 'Waiting List / Closed', text: "{{ session('warning') }}", confirmButtonColor: '#8D7E71' });
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

                    const parser = new DOMParser();
                    const doc = parser.parseFromString(data, 'text/html');

                    const newTableContent = doc.querySelector('#classResults');

                    if (newTableContent) {
                        classResults.innerHTML = newTableContent.innerHTML;
                    } else {
                        classResults.innerHTML = data;
                    }
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

        // Updated joinClass and cancelClass with date parameters
        function joinClass(classId, dateStr) {
            @php $isClosed = \App\Models\CloseDate::exists(); @endphp
            @if($isClosed)
                Swal.fire({ icon: 'warning', title: 'Studio Closed', text: 'You cannot join the class because the studio is closed.', confirmButtonColor: '#8D7E71' });
                return;
            @endif

            Swal.fire({
                title: 'Join Class',
                text: 'Are you sure you want to join this class?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#8D7E71',
                cancelButtonColor: '#BE9676',
                confirmButtonText: 'Yes, Join',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `/join/class/${classId}?date=${dateStr}`;
                }
            }); 
        }

        function cancelClass(classId, dateStr) {
            Swal.fire({
                title: 'Cancel Class',
                text: 'Are you sure you want to cancel this class?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#8D7E71',
                cancelButtonColor: '#BE9676',
                confirmButtonText: 'Yes, Cancel',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `/remove/class/${classId}?date=${dateStr}`;
                }
            });
        }
    </script>
@endsection