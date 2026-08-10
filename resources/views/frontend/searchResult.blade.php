<div id="classResults">
    <div class="container py-4 mb-5">
        <div class="row g-4">
            @forelse($classes as $class)
                <div class="col-md-12 col-lg-4"> {{-- Changed to col-lg-6 for a beautiful wide-card landscape split --}}
                    <div class="cool-class-card-with-img">

                        {{-- Data Content Side --}}
                        <div class="card-content-side">

                            {{-- Top Row Header: Name & Status Pill --}}
                            <div class="card-top-header">
                                <div class="title-meta-group">
                                    <h4 class="class-main-title">{{ $class->class_name }}</h4>
                                    <div class="badge-status-active-text  mt-1">{{ $class->category->name }}</div>
                                    @foreach ($class->instructor as $inst)
                                        <small class="text-muted"><i class="fas fa-user"></i>
                                            {{ $inst['user']['name'] }}</small>
                                    @endforeach
                                </div>

                                <div class="header-action-badge">
                                    @php
                                        $cancelledBooking = $bookings->where('selected_class_id', $class->id)->where('status', 'cancelled')->first();
                                    @endphp
                                    @if ($class->status == 'ongoing')
                                        @auth
                                            @if($bookings->where('selected_class_id', $class->id)->where('status', 'confirmed')->first())
                                                <span class="status-pill pill-approved">Joined</span>
                                            @elseif($bookings->where('selected_class_id', $class->id)->where('status', 'waitlisted')->first())
                                                <span class="status-pill pill-waitlist">Waitlisted</span>
                                            @elseif($bookings->where('selected_class_id', $class->id)->where('status', 'cancelled')->first())
                                                <span class="status-pill pill-cancelled">Cancelled</span>
                                            @else
                                                @php
                                                    $totalCapacity = $class->capacity;
                                                    $bookedSlots = $bookingsAll->where('selected_class_id', $class->id)->where('status', 'confirmed')->count();
                                                    $remainingSlots = max(0, $totalCapacity - $bookedSlots);
                                                @endphp
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
                                        <span class="badge-status-active-text">Completed</span>
                                    @elseif ($class->status == 'cancelled')
                                        <span class="badge-status-active-text">Cancelled </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Horizontal Meta Track Trackline --}}
                            <div class="metadata-track-line">
                                <div class="meta-item">
                                    <i class="far fa-calendar-alt text-muted me-1"></i>
                                    <span>{{ \Carbon\Carbon::parse($class->start_date)->format('d M Y') }} -
                                        {{ \Carbon\Carbon::parse($class->end_date)->format('d M Y') }}</span>
                                </div>
                                <div class="meta-item">
                                    <i class="far fa-clock text-muted me-1"></i>
                                    <span>{{ \Carbon\Carbon::parse($class->start_time)->format('h:i A') }} -
                                        {{ \Carbon\Carbon::parse($class->end_time)->format('h:i A') }}</span>
                                </div>
                            </div>

                            <div class="mt-2">
                                @php
                                    // Safely parse $class->days whether it's a JSON string or an array
                                    $days = is_string($class->days) ? json_decode($class->days, true) : ($class->days ?? []);
                                @endphp

                                @if(!empty($days))
                                    @foreach($days as $day)
                                        <span class="badge bg-secondary text-white me-1">{{ $day }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted small">No scheduled days</span>
                                @endif
                            </div>

                            <hr class="card-divider-line">

                            {{-- Mini Matrix Occupancy Output --}}
                            <div class="mini-data-matrix">
                                <div class="matrix-cell">
                                    <div class="cell-label">STUDIO OCCUPANCY</div>
                                    <div class="cell-value">
                                        @php
                                            $totalCapacity = $class->capacity;
                                            $bookedSlots = $bookingsAll->where('selected_class_id', $class->id)->where('status', 'confirmed')->count();
                                            if ($bookedSlots > $totalCapacity) {
                                                $bookedSlots = $totalCapacity; // Cap booked slots to total capacity
                                            }
                                            $remainingSlots = max(0, $totalCapacity - $bookedSlots);
                                        @endphp
                                        {{ $bookedSlots }} / {{ $totalCapacity }} Slots
                                    </div>
                                </div>
                                <div class="matrix-cell text-end">
                                    <div class="cell-label">AVAILABILITY</div>
                                    <div class="cell-value">
                                        @if($remainingSlots == 0)
                                            <span class="text-secondary small fw-bold">Waitlist</span>
                                        @else
                                            <span class="text-success small fw-bold">{{ $remainingSlots }} Left</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Bottom Action Buttons Tray --}}
                            <div class="card-action-footer-tray">
                                <button class="btn-action-secondary"
                                    onclick='window.location.href="{{ route('class.details', $class->id) }}"'>
                                    Details
                                </button>

                                @auth
                                    @php
                                        $todayDate = \Carbon\Carbon::now()->format('Y-m-d');
                                        $now = \Carbon\Carbon::now();

                                        // 1. Instructor Attendance Check
                                        $instructorUserIds = $class->instructor_ids ?? [];

                                        // Parse class end datetime to see if class is over
                                        $classEndTime = \Carbon\Carbon::parse($todayDate . ' ' . ($class->end_time ?? $class->time_to ?? '23:59:59'));
                                        $isClassOver = $now->greaterThan($classEndTime);

                                        // Instructor attendance status: checked in today AND class is not over
                                        $isInstructorCheckedInToday = false;
                                        if (!$isClassOver) {
                                            $isInstructorCheckedInToday = \App\Models\Attendance::where('class_id', $class->id)
                                                ->whereIn('instructor_id', $instructorUserIds)
                                                ->whereDate('attendance_date', $todayDate)
                                                ->where('attended', 1)
                                                ->exists();
                                        }

                                        // 2. Client Booking Checks
                                        $confirmedBooking = $bookings->where('selected_class_id', $class->id)
                                            ->where('status', 'confirmed')
                                            ->first();

                                        $cancelledBooking = $bookings->where('selected_class_id', $class->id)
                                            ->where('status', 'cancelled')
                                            ->first();

                                        $hasAnyBooking = $bookings->where('selected_class_id', $class->id)
                                            ->whereIn('status', ['confirmed', 'waitlisted'])
                                            ->first();

                                        // 3. Cancellation Window Check
                                        $classStart = \Carbon\Carbon::parse($class->start_date . ' ' . $class->start_time);
                                        $canCancel = now()->diffInHours($classStart, false) >= 48;
                                    @endphp
                                    @if(!$confirmedBooking && $class->status == 'ongoing' && !$cancelledBooking && !$isCurrentUserInstructor)
                                        @if($remainingSlots > 0)
                                            {{-- Show Join button if not confirmed and slots are available --}}
                                            <button onClick="joinClass({{ $class->id }})" class="btn-action-primary">
                                                Join <i class="fas fa-arrow-right ms-1"></i>
                                            </button>
                                        @elseif(!$hasAnyBooking)
                                            {{-- Show Waitlist button if no slots and user is not already on a list --}}
                                            <button onClick="joinClass({{ $class->id }})" class="btn-action-wait">
                                                WaitList <i class="fas fa-arrow-right ms-1"></i>
                                            </button>
                                        @endif
                                    @elseif($confirmedBooking && $class->status == 'ongoing' && $canCancel)
                                        {{-- Show Cancel button if user has a confirmed booking --}}
                                        <button onClick="cancelClass({{ $confirmedBooking->id }})" class="btn-action-cancel">
                                            Cancel <i class="fas fa-times ms-1"></i>
                                        </button>
                                    @elseif($isInstructorCheckedInToday && !$isClassOver)
                                        {{-- Show Teaching button ONLY if logged-in user is an instructor AND checked in today AND
                                        class is not over --}}
                                        <div class="btn-action-teaching text-center">
                                            Teaching...
                                        </div>
                                    @endif
                                @endauth
                            </div>
                        </div> {{-- End content-side --}}
                    </div> {{-- End cool-class-card-with-img --}}
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <p class="text-muted fs-5">No classes found matching your criteria.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Pagination Layout Control --}}
    @if (method_exists($classes, 'lastPage') && $classes->lastPage() > 0)
        <div class="soma-pagination-wrap">
            <ul class="soma-pagination">
                @if ($classes->onFirstPage())
                    <li class="disabled"><span><i class="fas fa-chevron-left"></i></span></li>
                @else
                    <li><a href="{{ $classes->previousPageUrl() . '#classResults' }}"><i class="fas fa-chevron-left"></i></a>
                    </li>
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