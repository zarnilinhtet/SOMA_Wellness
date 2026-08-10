@include('master.header')
@include('master.sidebar')
@include('master.nav')

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
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0 text-dark">
                <i class="fas fa-calendar-check me-2 text-primary"></i> Class Schedule List
            </h4>
        </div>

        <div class="filter-card border border-light-subtle shadow-sm p-3">
            <form action="{{ route('class_schedules_list') ?? '#' }}" method="GET" class="row g-3 align-items-end">
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
                    <a href="{{ route('class_schedules_list') ?? '#' }}" class="btn btn-outline-secondary shadow-sm">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="schedule-container mt-0">
            <div class="card border border-light-subtle shadow-sm overflow-hidden rounded-0">
                
                @forelse($groupedSchedules as $date => $schedules)
                    <div class="bg-light px-4 py-3 border-bottom border-light-subtle">
                        <h6 class="text-secondary fw-semibold mb-0" style="font-size: 0.95rem; letter-spacing: 0.5px;">
                            {{ $date }}
                        </h6>
                    </div>

                    <div class="d-flex align-items-center px-4 py-2 border-bottom border-light-subtle bg-white text-muted" style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                        <div style="width: 100px;">Time</div>
                        <div class="flex-grow-1 px-3">Class Name</div>
                        <div style="width: 180px;">Instructors</div>
                        <div style="width: 150px;">Bookings</div>
                        <div style="width: 120px;">Availability</div>
                        <div style="width: 100px;" class="text-center">Action</div>
                    </div>

                    <div class="list-group list-group-flush">
                        @foreach($schedules as $schedule)
                            @php
                                $start = \Carbon\Carbon::parse($schedule->start_time);
                                $end = \Carbon\Carbon::parse($schedule->end_time);
                                $duration = $start->diffInMinutes($end);
                                
                                $isCompleted = strtolower($schedule->status) === 'completed';
                                $isClosed = strtolower($schedule->status) === 'closed' || strtolower($schedule->status) === 'cancelled';
                                $isFaded = $isCompleted || $isClosed;
                            @endphp

                            <div class="list-group-item list-group-item-action d-flex align-items-center px-4 py-4 border-bottom border-light-subtle {{ $isFaded ? 'faded-row' : '' }}">
                                
                                <div class="d-flex flex-column" style="width: 100px;">
                                    <span class="text-dark fw-semibold" style="font-size: 0.95rem;">
                                        {{ $start->format('g:i a') }}
                                    </span>
                                    <span class="text-secondary small mt-1">
                                        {{ $duration }} min
                                    </span>
                                </div>
                                
                                <div class="flex-grow-1 px-3">
                                    <span class="text-dark fw-bold" style="font-size: 1rem;">
                                        {{ $schedule->class_name }}
                                    </span>
                                </div>
                                
                                <div class="d-flex align-items-center gap-2" style="width: 180px;">
                                    @if($schedule->instructors && $schedule->instructors->count() > 0)
                                        <div class="avatar-group" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#instructorModal{{ $schedule->id }}" title="View all instructors">
                                            @foreach($schedule->instructors->take(3) as $instructor)
                                                @php 
                                                    $instructorName = $instructor->user->name ?? 'Instructor';
                                                    $avatarBg = $isFaded ? 'e9ecef' : '343a40';
                                                    $avatarColor = $isFaded ? '6c757d' : 'fff';
                                                    $avatarUrl = $instructor->user->profile_photo_url ?? "https://ui-avatars.com/api/?name=".urlencode($instructorName)."&background={$avatarBg}&color={$avatarColor}";
                                                @endphp
                                                <img src="{{ $avatarUrl }}" class="avatar-circle border-white" title="{{ $instructorName }}">
                                            @endforeach
                                            @if($schedule->instructors->count() > 3)
                                                <div class="avatar-more">+{{ $schedule->instructors->count() - 3 }}</div>
                                            @endif
                                        </div>
                                        <button class="btn btn-sm text-primary p-0 fw-bold border-0 bg-transparent" data-bs-toggle="modal" data-bs-target="#instructorModal{{ $schedule->id }}" style="font-size: 0.8rem;">View</button>
                                    @else
                                        <span class="text-secondary small fst-italic">TBA</span>
                                    @endif
                                </div>

                                <div class="d-flex align-items-center gap-2" style="width: 150px;">
                                    @if(isset($schedule->bookings) && $schedule->bookings->count() > 0)
                                        <div class="avatar-group" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#bookingModal{{ $schedule->id }}" title="View all bookings">
                                            @foreach($schedule->bookings->take(3) as $booking)
                                                @php
                                                    $studentName = $booking->user->name ?? 'Unknown Student';
                                                    $studentAvatar = $booking->user->profile_photo_url ?? "https://ui-avatars.com/api/?name=".urlencode($studentName)."&background=random";
                                                @endphp
                                                <img src="{{ $studentAvatar }}" class="avatar-circle border-white" title="{{ $studentName }}">
                                            @endforeach
                                            @if($schedule->bookings->count() > 3)
                                                <div class="avatar-more">+{{ $schedule->bookings->count() - 3 }}</div>
                                            @endif
                                        </div>
                                        <button class="btn btn-sm text-primary p-0 fw-bold border-0 bg-transparent" data-bs-toggle="modal" data-bs-target="#bookingModal{{ $schedule->id }}" style="font-size: 0.8rem;">View</button>
                                    @else
                                        <span class="text-secondary small fst-italic">No bookings</span>
                                    @endif
                                </div>
                                
                                <div class="text-start" style="width: 120px;">
                                    @if(!$isFaded)
                                        @php
                                            $spotsTaken = $schedule->bookings ? $schedule->bookings->count() : 0;
                                            $spotsLeft = $schedule->capacity - $spotsTaken;
                                        @endphp
                                        <span class="text-secondary fw-semibold {{ $spotsLeft <= 0 ? 'text-danger' : '' }}" style="font-size: 0.95rem;">
                                            {{ $spotsLeft > 0 ? $spotsLeft . ' spots left' : 'Full' }}
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- 6. Action / Status Button -->
                                <div class="d-flex flex-column align-items-center gap-1" style="width: 100px;">
                                    @if($isCompleted)
                                        <span class="fw-semibold status-red">Completed</span>
                                    @elseif($isClosed)
                                        <span class="fw-semibold status-red">Closed</span>
                                    @elseif(isset($spotsLeft) && $spotsLeft <= 0)
                                        <span class="fw-semibold text-secondary">Waitlist</span>
                                    @else
                                        <a href="#" class="btn-book">Book</a>
                                    @endif

                                    <!-- Attendance Log Viewer Button -->
                                    {{-- <button class="btn btn-sm btn-outline-primary w-100 mt-1" style="border-radius: 50rem; font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#attendanceModal{{ $schedule->id }}">
                                        <i class="fas fa-user-check"></i> Attended
                                    </button> --}}
                                </div>
                                
                            </div>
                        @endforeach
                    </div>
                @empty
                    <div class="p-5 text-center text-secondary fw-medium">
                        No class schedules found between {{ \Carbon\Carbon::parse($fromDate)->format('M d, Y') }} and {{ \Carbon\Carbon::parse($toDate)->format('M d, Y') }}.
                    </div>
                @endforelse

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
            <div class="modal fade" id="instructorModal{{ $schedule->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-light border-bottom">
                            <div>
                                <h5 class="modal-title fw-bold text-dark">Class Instructors</h5>
                                <div class="text-muted small mt-1">{{ $schedule->class_name }} • {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i a') }}</div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach($schedule->instructors as $instructor)
                                    @php
                                        $instructorName = $instructor->user->name ?? 'Unknown Instructor';
                                        $instructorEmail = $instructor->user->email ?? 'No email provided';
                                        $instructorAvatar = $instructor->user->profile_photo_url ?? "https://ui-avatars.com/api/?name=".urlencode($instructorName)."&background=343a40&color=fff";
                                    @endphp
                                    <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ $instructorAvatar }}" alt="{{ $instructorName }}" class="rounded-circle object-fit-cover shadow-sm" style="width: 45px; height: 45px;">
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
            <div class="modal fade" id="bookingModal{{ $schedule->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-light border-bottom">
                            <div>
                                <h5 class="modal-title fw-bold text-dark">Class Bookings</h5>
                                <div class="text-muted small mt-1">{{ $schedule->class_name }} • {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i a') }}</div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach($schedule->bookings as $booking)
                                    @php
                                        $studentName = $booking->user->name ?? 'Unknown Student';
                                        $studentEmail = $booking->user->email ?? 'No email provided';
                                        $studentAvatar = $booking->user->profile_photo_url ?? "https://ui-avatars.com/api/?name=".urlencode($studentName)."&background=random";
                                    @endphp
                                    <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ $studentAvatar }}" alt="{{ $studentName }}" class="rounded-circle object-fit-cover shadow-sm" style="width: 45px; height: 45px;">
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark">{{ $studentName }}</h6>
                                                <small class="text-muted">{{ $studentEmail }}</small>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="badge {{ strtolower($booking->status) == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ ucfirst($booking->status ?? 'Active') }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-top py-2">
                            <span class="text-muted small me-auto">Total Bookings: <strong>{{ $schedule->bookings->count() }}</strong></span>
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endforeach


<!-- ============================================== -->
<!-- ATTENDANCE MODALS GENERATION (UPDATED FOR ACCORDION) -->
<!-- ============================================== -->
@foreach($groupedSchedules as $date => $schedules)
    @foreach($schedules as $schedule)
        <div class="modal fade" id="attendanceModal{{ $schedule->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-light border-bottom">
                        <div>
                            <h5 class="modal-title fw-bold text-dark">Check-In Records</h5>
                            <div class="text-muted small mt-1">
                                {{ $schedule->class_name }} • {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i a') }}
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body p-0">
                        @if(isset($schedule->attendances) && $schedule->attendances->count() > 0)
                            @php
                                // Group checked-in users by date they attended
                                $groupedAtt = $schedule->attendances->groupBy(function($item) {
                                    return \Carbon\Carbon::parse($item->attendance_date)->format('M d, Y');
                                })->sortKeysDesc();
                            @endphp

                            <div class="accordion accordion-flush" id="accordionAttendance{{ $schedule->id }}">
                                @foreach($groupedAtt as $attDate => $dayAttendances)
                                    @php
                                        // Filter unique people per date
                                        $uniqueAttendances = $dayAttendances->unique(function ($item) {
                                            return $item->client_id ? 'client_'.$item->client_id : 'instructor_'.$item->instructor_id;
                                        });
                                        $collapseId = 'collapse_' . $schedule->id . '_' . str_replace([' ', ','], '', $attDate);
                                    @endphp

                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }} bg-light fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}">
                                                <i class="far fa-calendar-alt me-2 text-primary"></i> {{ $attDate }} 
                                                <span class="badge bg-success ms-auto">{{ $uniqueAttendances->count() }} Checked-In</span>
                                            </button>
                                        </h2>
                                        <div id="{{ $collapseId }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#accordionAttendance{{ $schedule->id }}">
                                            <div class="list-group list-group-flush">
                                                @foreach($uniqueAttendances as $att)
                                                    @php
                                                        if ($att->client_id && isset($att->client_user)) {
                                                            $name = $att->client_user->name ?? 'Unknown Student';
                                                            $email = $att->client_user->email ?? '';
                                                            $role = 'Student';
                                                            $avatar = $att->client_user->profile_photo_url ?? "https://ui-avatars.com/api/?name=".urlencode($name)."&background=random";
                                                        } else {
                                                            $name = $att->instructor->user->name ?? 'Unknown Instructor';
                                                            $email = $att->instructor->user->email ?? '';
                                                            $role = 'Instructor';
                                                            $avatar = $att->instructor->user->profile_photo_url ?? "https://ui-avatars.com/api/?name=".urlencode($name)."&background=343a40&color=fff";
                                                        }
                                                    @endphp

                                                    <div class="list-group-item d-flex justify-content-between align-items-center p-3 border-0 border-bottom">
                                                        <div class="d-flex align-items-center gap-3">
                                                            <img src="{{ $avatar }}" alt="{{ $name }}" class="rounded-circle object-fit-cover shadow-sm" style="width: 45px; height: 45px;">
                                                            <div>
                                                                <h6 class="mb-0 fw-bold text-dark">{{ $name }}</h6>
                                                                <small class="text-muted">{{ $email }}</small>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <span class="badge {{ $role == 'Instructor' ? 'bg-primary' : 'bg-success' }}">
                                                                {{ $role }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-5 text-center text-muted">
                                <i class="fas fa-user-times fs-2 mb-3 text-secondary opacity-50"></i>
                                <p class="mb-0">No one has checked in to this class yet.</p>
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer bg-light border-top py-2">
                        @php 
                            $totalCount = isset($schedule->attendances) ? $schedule->attendances->unique(function ($item) {
                                return $item->attendance_date . '_' . ($item->client_id ? 'client_'.$item->client_id : 'instructor_'.$item->instructor_id);
                            })->count() : 0; 
                        @endphp
                        <span class="text-muted small me-auto">Total Check-ins (All Dates): <strong>{{ $totalCount }}</strong></span>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endforeach

@include('master.footer')