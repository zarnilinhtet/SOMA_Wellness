@include('master.header')

{{-- Summernote CSS --}}
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

@include('master.sidebar')
@include('master.nav')
<style>
    /* Animated Toast Styles */
    .custom-toast {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(0, 0, 0, 0.08);
        animation: slideInRight 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }

    .toast-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        width: 100%;
        animation: toastProgress 4s linear forwards;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(110%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes toastProgress {
        from {
            width: 100%;
        }

        to {
            width: 0%;
        }
    }

    .flatpickr-calendar {
        box-shadow: none !important;
        border: 1px solid #eee !important;
        margin: 0 auto;
    }

    .card {
        border-radius: 1rem;
    }

    .flatpickr-time {
        display: none !important;
    }

    /* Style for dates that already have attendance recorded */
    .flatpickr-day.has-recorded-attendance {
        position: relative;
        background-color: #e8f5e9 !important;
        /* Soft green background */
        color: #1b5e20 !important;
        font-weight: bold;
        border: 1px solid #a5d6a7 !important;
    }

    /* Badge Icon in the corner of recorded days (Checkmark or Cross) */
    .flatpickr-day.has-recorded-attendance::after {
        content: "✓";
        /* Change to "✕" if you prefer a cross */
        position: absolute;
        top: 1px;
        right: 4px;
        font-size: 10px;
        font-weight: 900;
        color: #2e7d32;
    }

    /* Optional: Red styling if using Cross "✕" */
    /* 
.flatpickr-day.has-recorded-attendance {
    background-color: #ffebee !important;
    color: #c62828 !important;
    border: 1px solid #ef9a9a !important;
}
.flatpickr-day.has-recorded-attendance::after {
    content: "✕";
    color: #c62828;
} 
*/
</style>

<div class="container py-4">
    <div class="page-inner">
        {{-- Session Success Alert --}}
        @if (session('success'))
            <div class="alert alert-dismissible fade show shadow-sm border-0 d-flex align-items-center py-2 px-4 rounded-pill m-0 mb-3"
                role="alert" style="background-color: #e0f8e9; color: #155724;">
                <i class="fas fa-check-circle me-2" style="font-size: 1.2rem; color: #28a745;"></i>
                <span class="fw-bold">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Session Error Alert --}}
        @if (session('error'))
            <div class="alert alert-dismissible fade show shadow-sm border-0 d-flex align-items-center py-2 px-4 rounded-pill m-0 mb-3"
                role="alert" style="background-color: #f8d7da; color: #721c24;">
                <i class="fas fa-exclamation-circle me-2" style="font-size: 1.2rem; color: #dc3545;"></i>
                <span class="fw-bold">{{ session('error') }}</span>
            </div>
        @endif

        <div class="border-0 rounded-4 card">
            <div class="card-header bg-white border-0 pt-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-2">Class Attendance Management</h4>
                <button class="btn btn-dark btn-sm rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal"
                    data-bs-target="#adminReviewAttendanceModal">
                    <i class="fas fa-clipboard-check me-2"></i> Client Attendance Overview & Check-In
                </button>
            </div>
            <div class="card-body table-responsive p-1" style="max-height: 600px; overflow-y: auto;">
                <table id="branch-table" class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No.</th>
                            <th>Instructor Name</th>
                            <th>Class Name</th>
                            <th>Class Date</th>
                            <th>Class Days</th>
                            <th>Class Time</th>
                            <th class="text-center">Action</th>
                            <th class="w-30">Recorded Approval</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($att as $a)
                            @php
                                $now = \Carbon\Carbon::now();
                                $startDate = \Carbon\Carbon::parse($a->start_date)->startOfDay();
                                $endDate = \Carbon\Carbon::parse($a->end_date)->endOfDay();

                                $isWithinDateRange = $now->between($startDate, $endDate);

                                // 1. Create Carbon instances for start and end times on today's date context
                                $startTime = \Carbon\Carbon::today()->setTimeFromTimeString($a->start_time);
                                $endTime = \Carbon\Carbon::today()->setTimeFromTimeString($a->end_time);

                                // 2. Apply 30-minute buffers safely
                                $startTimeWithBuffer = $startTime->copy()->subMinutes(30);
                                $endTimeWithBuffer = $endTime;

                                // 3. Format everything strictly in 24-hour time strings (H:i:s) for comparison
                                $currentTimeOnly = $now->format('H:i:s');
                                $startTimeStr = $startTimeWithBuffer->format('H:i:s');
                                $endTimeStr = $endTimeWithBuffer->format('H:i:s');

                                // 4. Compare strings directly in 24-hour format
                                $isWithinTimeBuffer = ($currentTimeOnly >= $startTimeStr && $currentTimeOnly <= $endTimeStr);
                                $isClickable = $isWithinDateRange && $isWithinTimeBuffer;

                                \Illuminate\Support\Facades\Log::info("Class ID {$a->id} Debug (24hr):", [
                                    'current_time' => $currentTimeOnly,
                                    'start_buffer' => $startTimeStr,
                                    'end_buffer' => $endTimeStr,
                                    'isWithinTimeBuffer' => $isWithinTimeBuffer,
                                    'isWithinDateRange' => $isWithinDateRange,
                                    'isClickable' => $isClickable
                                ]);
                            @endphp

                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-bold">

                                    @foreach ($a->instructor as $inst)
                                        <span class="badge bg-info text-white me-1 mb-1">
                                            {{ $inst['user']['name'] ?? 'N/A' }}
                                        </span>
                                    @endforeach
                                </td>
                                <td><span class="fw-bold d-block">{{ $a->class_name }}</span></td>
                                <td>
                                    <span class="d-block text-primary" style="font-size: 0.9rem;">
                                        {{ \Carbon\Carbon::parse($a->start_date)->format('d M Y') }} <br>
                                        <span class="text-muted text-center d-block">to</span>
                                        {{ \Carbon\Carbon::parse($a->end_date)->format('d M Y') }}
                                    </span>
                                </td>
                                <td>
                                    <!-- Display class days -->
                                    @if(!empty($a->days) && is_array($a->days))
                                        @foreach($a->days as $day)
                                            <span class="badge bg-secondary text-white me-1 mb-1">{{ $day }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted small">No scheduled days</span>
                                    @endif
                                </td>
                                <td>
                                    <!-- Updated to 24-hour format (H:i) -->
                                    <small class="text-dark fw-bold">
                                        {{ \Carbon\Carbon::parse($a->start_time)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($a->end_time)->format('H:i') }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    @if(!empty($a->instructor) && count($a->instructor) > 0)
                                        @php
                                            $loggedInInstructorModel = App\Models\Instructor::where('instructor_id', auth()->id())->first();
                                            $loggedInInstructorId = $loggedInInstructorModel ? $loggedInInstructorModel->id : null;

                                            // Check if this instructor already recorded attendance for today on this class
                                            $alreadyRecordedToday = false;
                                            if ($loggedInInstructorId && isset($record) && is_iterable($record)) {
                                                foreach ($record as $rec) {
                                                    if (
                                                        (string) $rec->class_id === (string) $a->id &&
                                                        (string) $rec->instructor_id === (string) $loggedInInstructorId &&
                                                        $rec->attendance_date === date('Y-m-d') &&
                                                        !$rec->client_id
                                                    ) {
                                                        $alreadyRecordedToday = true;
                                                        break;
                                                    }
                                                }
                                            }
                                        @endphp

                                        @if (auth()->user()->hasRole("Instructor"))

                                            <div class="dropdown">
                                                <button class="btn btn-primary btn-sm px-3 dropdown-toggle rounded-pill"
                                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Record Instructor
                                                </button>
                                                <ul class="dropdown-menu shadow border-0 p-2">
                                                    <li>
                                                        <a class="dropdown-item rounded openInstructorModal py-2" href="#"
                                                            data-schedule-id="{{ $a->id }}"
                                                            data-class-days="{{ json_encode($a->days) }}"
                                                            data-start-date="{{ $a->start_date }}"
                                                            data-end-date="{{ $a->end_date }}"
                                                            data-instructor-id="{{ $loggedInInstructorId ?? '' }}"
                                                            data-instructor-name="{{ $loggedInInstructorModel->user['name'] ?? 'Instructor' }}">
                                                            <i class="fas fa-user-check me-2 text-primary"></i>
                                                            {{ $loggedInInstructorModel->user['name'] ?? 'N/A' }}
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <!-- @if($isClickable) -->
                                            <!-- @else
                                                                                                                                    <button class="btn btn-secondary btn-sm px-3 rounded-pill" type="button" disabled
                                                                                                                                        title="Available 30 mins before start time and up to 30 mins after end time.">
                                                                                                                                        Locked Time
                                                                                                                                    </button>
                                                                                                                                @endif -->
                                        @else
                                            <!-- {{ $isClickable ? '' : 'disabled' }} -->
                                            <div class="dropdown">
                                                <button class="btn btn-primary btn-sm px-3 dropdown-toggle rounded-pill"
                                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Record Instructor
                                                </button>
                                                <ul class="dropdown-menu shadow border-0 p-2">
                                                    @foreach ($a->instructor as $inst)
                                                        <li>
                                                            <a class="dropdown-item rounded openInstructorModal py-2" href="#"
                                                                data-schedule-id="{{ $a->id }}" data-instructor-id="{{ $inst['id'] }}"
                                                                data-instructor-name="{{ $inst['user']['name'] ?? 'Instructor' }}"
                                                                data-start-date="{{ $a->start_date }}"
                                                                data-end-date="{{ $a->end_date }}"
                                                                data-class-days="{{ json_encode($a->days) }}">
                                                                <i class="fas fa-user-check me-2 text-primary"></i>
                                                                {{ $inst['user']['name'] ?? 'N/A' }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- Unified approval/status section visible to both admin and instructors --}}
                                    <div class="w-100">
                                        @if(auth()->user()->hasRole("Instructor"))
                                            @php
                                                $attdItem = collect($a->instructor)->filter(function ($instructor) {
                                                    return $instructor['instructor_id'] === auth()->id();
                                                })->first();
                                                Illuminate\Support\Facades\Log::info(" Approval Debug for Class ID {$a->id}:", [
                                                    'instructor_id' => auth()->id(),
                                                    'attendance_record' => $attdItem['attendance']['attended'] ?? null,
                                                ]);

                                                $isAdminApproved = ($attdItem['attendance']['admin_approve'] ?? 0) == 1 && ($attdItem['attendance']['attended'] ?? 0) == 1;
                                                $isPendingApproval = ($attdItem['attendance']['admin_approve'] ?? 0) == 0 && ($attdItem['attendance']['attended'] ?? 0) == 1;
                                                Illuminate\Support\Facades\Log::info("Instructor Approval Debug for Class ID {$a->id}:", [
                                                    'instructor_id' => auth()->id(),
                                                    'attendance_record' => $attdItem['attendance']['attended'] ?? null,
                                                    'isAdminApproved' => $isAdminApproved,
                                                    'isPendingApproval' => $isPendingApproval
                                                ]);
                                            @endphp

                                            <div class="mb-2">
                                                {{ $attdItem['user']['name'] ?? 'N/A' }}

                                                @if($isAdminApproved)
                                                    Recorded: <span class="badge bg-success">Approved</span>
                                                @elseif($isPendingApproval)
                                                    Recorded:

                                                    <span type="submit" class="badge bg-warning">
                                                        Waiting For admin Approve</span>

                                                @else
                                                    <span class="badge bg-secondary">Not Recorded</span>
                                                @endif
                                            </div>
                                        @else
                                            @foreach ($a['instructor'] as $attdItem)
                                                @php
                                                    $isAdminApproved = ($attdItem['attendance']['admin_approve'] ?? 0) == 1 && ($attdItem['attendance']['attended'] ?? 0) == 1;
                                                    $isPendingApproval = ($attdItem['attendance']['admin_approve'] ?? 0) == 0 && ($attdItem['attendance']['attended'] ?? 0) == 1;
                                                @endphp

                                                <div class="mb-2">
                                                    {{ $attdItem['user']['name'] ?? 'N/A' }}

                                                    @if($isAdminApproved)
                                                        Recorded: <span class="badge bg-success">Approved</span>
                                                    @elseif($isPendingApproval)
                                                        Recorded:
                                                        <form action="{{ route('attendances.adminApprove', $attdItem['id']) }}"
                                                            method="POST" class="d-inline-block ms-1">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="class_id" value="{{ $a['id'] }}">
                                                            <button type="submit" class="btn btn-sm btn-success">
                                                                Approve
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="badge bg-secondary">Not Recorded</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ==================== INSTRUCTOR ATTENDANCE MODAL ==================== --}}
<div class="modal fade" id="instructorAttendanceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="instructorAttendanceForm" method="POST" action="{{ route('attendances.inTime') }}"
            class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title fw-bold" id="instructorModalTitle">Record Instructor Attendance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <input type="hidden" name="class_id" id="instructor_schedule_id">
                <input type="hidden" name="instructor_id" id="instructor_id_input">

                <div class="alert alert-info py-2 mb-3 text-start small border-0 shadow-sm" id="instructorNameDisplay">
                    <strong>Instructor:</strong> <span id="selectedInstructorNameText"></span>
                </div>

                {{-- Inline Flatpickr Container --}}
                <div id="instructor-inline-picker" class="mx-auto" style="pointer-events: none;"></div>

                <input type="hidden" name="attendance_date" id="instructor_attendance_date" required>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-pill">
                    Confirm Instructor Attendance
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ==================== ADMIN CLIENT OVERVIEW & CHECK-IN MODAL ==================== --}}
<div class="modal fade" id="adminReviewAttendanceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-user-graduate me-2 text-success"></i> Student
                    Attendance Manager & Check-In Hub</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="flash-alerts"
                    style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 320px; max-width: 420px;">
                    {{-- Session Success Alert --}}
                    @if (session('success'))
                        <div
                            class="custom-toast success-toast shadow-lg rounded-4 p-3 mb-3 d-flex align-items-center justify-content-between position-relative overflow-hidden">
                            <div class="d-flex align-items-center">
                                <div class="icon-shape bg-success text-white rounded-circle me-3 d-flex align-items-center justify-content-center"
                                    style="width: 38px; height: 38px; flex-shrink: 0;">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">Success</h6>
                                    <small class="text-muted">{!! session('success') !!}</small>
                                </div>
                            </div>
                            <button type="button" class="btn-close ms-3"
                                onclick="$(this).closest('.custom-toast').fadeOut()" aria-label="Close"></button>
                            <div class="toast-progress bg-success"></div>
                        </div>
                    @endif

                    {{-- Session Error Alert --}}
                    @if (session('error'))
                        <div
                            class="custom-toast error-toast shadow-lg rounded-4 p-3 mb-3 d-flex align-items-center justify-content-between position-relative overflow-hidden">
                            <div class="d-flex align-items-center">
                                <div class="icon-shape bg-danger text-white rounded-circle me-3 d-flex align-items-center justify-content-center"
                                    style="width: 38px; height: 38px; flex-shrink: 0;">
                                    <i class="fas fa-exclamation"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">Error</h6>
                                    <small class="text-muted">{!! session('error') !!}</small>
                                </div>
                            </div>
                            <button type="button" class="btn-close ms-3"
                                onclick="$(this).closest('.custom-toast').fadeOut()" aria-label="Close"></button>
                            <div class="toast-progress bg-danger"></div>
                        </div>
                    @endif
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <label class="form-label small fw-bold">Select Class:</label>
                        <select id="auditClassSelect" class="form-select shadow-sm">
                            <option value="">-- Choose Class Schedule --</option>
                            @foreach($att as $a)
                                @php
                                    $isAdminUser = auth()->user()->hasRole("Admin");
                                    $isInstructorUser = auth()->user()->hasRole("Instructor");
                                    $currentInstructorUserId = auth()->id();
                                    $belongsToInstructor = false;

                                    if ($isInstructorUser && !empty($a->instructor)) {
                                        $instructors = $a->instructor;
                                        if (is_string($instructors)) {
                                            $instructors = json_decode($instructors, true);
                                        }
                                        if ($instructors instanceof \Illuminate\Support\Collection) {
                                            $instructors = $instructors->toArray();
                                        }
                                        if (is_array($instructors)) {
                                            foreach ($instructors as $insItem) {
                                                $insId = is_array($insItem) ? ($insItem['instructor_id'] ?? null) : ($insItem->instructor_id ?? null);
                                                $userId = is_array($insItem) ? ($insItem['user']['id'] ?? ($insItem['user_id'] ?? null)) : ($insItem->user->id ?? null);

                                                if ($insId == $currentInstructorUserId || $userId == $currentInstructorUserId) {
                                                    $belongsToInstructor = true;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                @endphp

                                @if($isAdminUser || ($isInstructorUser && $belongsToInstructor))
                                    <option value="{{ $a->id }}" data-name="{{ $a->class_name }}"
                                        data-start="{{ $a->start_date }}" data-end="{{ $a->end_date }}"
                                        data-start-time="{{ $a->start_time }}" data-end-time="{{ $a->end_time }}"
                                        data-days='@json($a->days)'>
                                        {{ $a->class_name }} ({{ \Carbon\Carbon::parse($a->start_date)->format('d M') }} -
                                        {{ \Carbon\Carbon::parse($a->end_date)->format('d M') }} |
                                        {{ \Carbon\Carbon::parse($a->start_time)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($a->end_time)->format('H:i') }}) |
                                        {{ $a->days ? implode(', ', $a->days) : 'No Days Set' }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Select Date:</label>
                        <input type="date" id="auditDateSelect" class="form-control shadow-sm"
                            value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Search Student Name:</label>
                        <input type="text" id="auditSearchInput" class="form-control shadow-sm"
                            placeholder="Type student name to filter...">
                    </div>
                </div>

                {{-- Hidden Form to Submit Quick Individual Check-ins --}}
                <form id="quickCheckInForm" method="POST" action="{{ route('attendances.ClientinTime') }}">
                    @csrf
                    <input type="hidden" name="class_id" id="quick_class_id">
                    <input type="hidden" name="attendance_date" id="quick_attendance_date">
                    <input type="hidden" name="client_ids[]" id="quick_client_id">
                </form>

                {{-- Student Audit Result Table --}}
                <div class="table-responsive border rounded-3 bg-white shadow-sm"
                    style="max-height: 420px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0" id="auditResultTable">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Student Name</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Action / Quick Check-In</th>
                            </tr>
                        </thead>
                        <tbody id="auditTableBody">
                            <tr>
                                <td colspan="3" class="text-center text-muted py-5">Please select a class schedule above
                                    to manage student attendance records.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@include('master.footer')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    $(document).ready(function () {
        // ----------------------------------------------------
        // 1. Data Initialization & Setup
        // ----------------------------------------------------
        if ($.fn.DataTable.isDataTable('#branch-table')) {
            $('#branch-table').DataTable().destroy();
        }
        $('#branch-table').DataTable();

        // Pass data sets from Blade
        let allInstructorRecords = @json($instructorRecord ?? ($record ?? []));
        let allClientRecords = @json($record ?? []);
        @php
            $clientsMaster = App\Models\Booking::with('bookingUser')->get();
        @endphp
        const allClientsMaster = @json($clientsMaster ?? []);

        const todayStr = flatpickr.formatDate(new Date(), "Y-m-d");

        const dayMap = { 0: 'Sun', 1: 'Mon', 2: 'Tue', 3: 'Wed', 4: 'Thu', 5: 'Fri', 6: 'Sat' };
        const fullDayMap = { 0: 'Sunday', 1: 'Monday', 2: 'Tuesday', 3: 'Wednesday', 4: 'Thursday', 5: 'Friday', 6: 'Saturday' };

        // Helper: Show Feedback Alert Message inside Audit Container
        function showAuditAlert(message, type = 'success') {
            const alertBox = $('#auditAlertContainer');
            if (alertBox.length) {
                alertBox.html(`
                <div class="alert alert-${type} alert-dismissible fade show shadow-sm py-2 px-3 small mb-3" role="alert">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `);
                // Auto dismiss after 4 seconds
                setTimeout(() => {
                    alertBox.find('.alert').fadeOut('slow', function () { $(this).remove(); });
                }, 4000);
            } else {
                alert(message);
            }
        }

        // Helper: Check if a date string falls on a class day
        function isDateOnClassDay(dateStr, classDaysArray) {
            if (!classDaysArray || classDaysArray.length === 0) return true;

            const parts = dateStr.split('-');
            const targetDate = new Date(parts[0], parts[1] - 1, parts[2]);
            const dayNum = targetDate.getDay();

            const shortName = dayMap[dayNum].toLowerCase();
            const fullName = fullDayMap[dayNum].toLowerCase();

            const normalizedDays = classDaysArray.map(d => String(d).toLowerCase().trim());

            return normalizedDays.includes(String(dayNum)) ||
                normalizedDays.includes(shortName) ||
                normalizedDays.includes(fullName);
        }

        // Helper: Check if current time is within 30 minutes before start time and 30 minutes after end time
        function isWithin30MinWindow(startTimeStr, endTimeStr) {
            if (!startTimeStr || !endTimeStr) return true;

            const now = new Date();

            const parseTimeString = (timeStr) => {
                const parts = timeStr.split(':');
                const d = new Date();
                d.setHours(parseInt(parts[0], 10), parseInt(parts[1], 10), parseInt(parts[2] || 0, 10), 0);
                return d;
            };

            const startTime = parseTimeString(startTimeStr);
            const endTime = parseTimeString(endTimeStr);

            const allowedStart = new Date(startTime.getTime() - 30 * 60 * 1000);
            const allowedEnd = new Date(endTime.getTime() + 30 * 60 * 1000);

            return now >= allowedStart && now <= allowedEnd;
        }

        // Helper: Get array of recorded dates for current modal context
        function getRecordedDatesForCurrentContext() {
            const scheduleId = String($('#instructor_schedule_id').val()).trim();
            const instructorId = String($('#instructor_id_input').val()).trim();

            return allInstructorRecords
                .filter(rec => {
                    const recClassId = String(rec.class_id ?? rec.schedule_id ?? '').trim();
                    const recInstId = String(rec.instructor_id ?? '').trim();
                    return recClassId === scheduleId && recInstId === instructorId && !rec.client_id;
                })
                .map(rec => String(rec.attendance_date ?? '').split(' ')[0].trim());
        }

        // ----------------------------------------------------
        // 2. Flatpickr Initialization
        // ----------------------------------------------------
        let instructorFp = flatpickr("#instructor-inline-picker", {
            inline: true,
            enableTime: false,
            dateFormat: "Y-m-d",
            defaultDate: todayStr,
            onChange: function (selectedDates, dateStr, instance) {
                if (dateStr !== todayStr) {
                    instance.setDate(todayStr, false);
                }
            },
            onDayCreate: function (dObj, dStr, fp, dayElem) {
                const dateFormatted = flatpickr.formatDate(dayElem.dateObj, "Y-m-d");
                const recordedDates = getRecordedDatesForCurrentContext();

                if (recordedDates.includes(dateFormatted)) {
                    dayElem.classList.add("has-recorded-attendance");
                    dayElem.title = "Attendance Already Recorded";
                }
            }
        });

        function updateInstructorDateStatus(selectedDateStr) {
            const scheduleId = String($('#instructor_schedule_id').val()).trim();
            const instructorId = String($('#instructor_id_input').val()).trim();

            const isAlreadyRecorded = allInstructorRecords.some(rec => {
                const recClassId = String(rec.class_id ?? rec.schedule_id ?? '').trim();
                const recInstId = String(rec.instructor_id ?? '').trim();
                const recDate = String(rec.attendance_date ?? '').split(' ')[0].trim();

                return recClassId === scheduleId &&
                    recInstId === instructorId &&
                    recDate === selectedDateStr &&
                    !rec.client_id;
            });

            const submitBtn = $('#instructorAttendanceForm button[type="submit"]');
            if (isAlreadyRecorded) {
                submitBtn.prop('disabled', true).text('Already Recorded');
                submitBtn.removeClass('btn-primary btn-danger').addClass('btn-secondary');
            } else {
                submitBtn.prop('disabled', false).text('Confirm Instructor Attendance');
                submitBtn.removeClass('btn-secondary btn-danger').addClass('btn-primary');
            }
        }

        $('#instructorAttendanceForm').on('submit', function (e) {
            $('#instructor_attendance_date').val(todayStr);
        });

        // ----------------------------------------------------
        // 3. Modal Trigger Handler
        // ----------------------------------------------------
        $(document).on('click', '.openInstructorModal', function (e) {
            e.preventDefault();

            const scheduleId = $(this).data('schedule-id');
            const instructorId = $(this).data('instructor-id');
            const instructorName = $(this).data('instructor-name');
            const classStartDate = $(this).data('start-date');
            const classEndDate = $(this).data('end-date');
            const classStartTime = $(this).data('start-time');
            const classEndTime = $(this).data('end-time');

            const rawDays = $(this).data('class-days') || [];
            let classDays = [];
            if (typeof rawDays === 'string') {
                try {
                    classDays = JSON.parse(rawDays);
                } catch (err) {
                    classDays = rawDays.split(',').map(d => d.trim());
                }
            } else if (Array.isArray(rawDays)) {
                classDays = rawDays;
            }

            $('#instructor_schedule_id').val(scheduleId);
            $('#instructor_id_input').val(instructorId);
            $('#selectedInstructorNameText').text(instructorName);
            $('#instructor_attendance_date').val(todayStr);

            const isWithinDateRange = (!classStartDate || todayStr >= classStartDate) &&
                (!classEndDate || todayStr <= classEndDate);

            const isTodayClassDay = isDateOnClassDay(todayStr, classDays);
            const isTimeValid = isWithin30MinWindow(classStartTime, classEndTime);

            instructorFp.set('enable', [
                function (date) {
                    const dStr = flatpickr.formatDate(date, "Y-m-d");
                    if (classStartDate && dStr < classStartDate) return false;
                    if (classEndDate && dStr > classEndDate) return false;
                    return isDateOnClassDay(dStr, classDays);
                }
            ]);

            instructorFp.setDate(todayStr, false);
            instructorFp.redraw();

            const submitBtn = $('#instructorAttendanceForm button[type="submit"]');

            if (!isWithinDateRange) {
                submitBtn.prop('disabled', true).text('Cannot Record: Outside Class Date Range');
                submitBtn.removeClass('btn-primary btn-secondary').addClass('btn-danger');
            } else if (!isTodayClassDay) {
                submitBtn.prop('disabled', true).text('Cannot Record: Today is not a class day');
                submitBtn.removeClass('btn-primary btn-secondary').addClass('btn-danger');
            } else if (!isTimeValid) {
                submitBtn.prop('disabled', true).text('Cannot Record: Outside 30-Min Window');
                submitBtn.removeClass('btn-primary btn-secondary').addClass('btn-danger');
            } else {
                updateInstructorDateStatus(todayStr);
            }

            $('#instructorAttendanceModal').modal('show');
        });

        // ----------------------------------------------------
        // 4. Audit Table Render Logic
        // ----------------------------------------------------
        function renderAuditTable() {
            const classId = $('#auditClassSelect').val();
            const auditDate = $('#auditDateSelect').val();
            const searchTerm = $('#auditSearchInput').val().toLowerCase().trim();
            const tbody = $('#auditTableBody');
            tbody.empty();

            if (!classId) {
                tbody.html('<tr><td colspan="3" class="text-center text-muted py-5">Please select a class schedule above to view records.</td></tr>');
                return;
            }

            const selectedOption = $('#auditClassSelect').find(':selected');
            const classStartDate = selectedOption.data('start');
            const classEndDate = selectedOption.data('end');
            const classStartTime = selectedOption.data('start-time');
            const classEndTime = selectedOption.data('end-time');

            const rawDays = selectedOption.data('days') || [];
            let classDays = [];
            if (typeof rawDays === 'string') {
                try {
                    classDays = JSON.parse(rawDays);
                } catch (err) {
                    classDays = rawDays.split(',').map(d => d.trim());
                }
            } else if (Array.isArray(rawDays)) {
                classDays = rawDays;
            }

            if (classStartDate && classEndDate) {
                $('#auditDateSelect').attr('min', classStartDate).attr('max', classEndDate);
            } else {
                $('#auditDateSelect').removeAttr('min').removeAttr('max');
            }

            let isWithinClassRange = true;
            if (classStartDate && classEndDate && auditDate) {
                isWithinClassRange = (auditDate >= classStartDate && auditDate <= classEndDate);
            }

            let isDayValid = true;
            if (auditDate && classDays.length > 0) {
                isDayValid = isDateOnClassDay(auditDate, classDays);
            }

            let isTimeValid = true;
            if (auditDate === todayStr && classStartTime && classEndTime) {
                isTimeValid = isWithin30MinWindow(classStartTime, classEndTime);
            }

            const canCheckIn = isWithinClassRange && isDayValid && isTimeValid;

            const currentFilteredRecords = allClientRecords.filter(rec =>
                String(rec.class_id) === String(classId) &&
                rec.attendance_date === auditDate &&
                rec.client_id
            );
            let matchCount = 0;

            allClientsMaster.forEach(client => {
                const clientClassId = client.selected_class_id;
                if (String(clientClassId) !== String(classId)) return;

                const user = client.booking_user;
                if (!user) return;

                const studentId = String(user.id);
                const studentName = user.name || 'Unknown Student';

                if (searchTerm && !studentName.toLowerCase().includes(searchTerm)) {
                    return;
                }

                matchCount++;

                const matchingRecord = currentFilteredRecords.find(rec => String(rec.client_id) === studentId);
                const isRecorded = !!matchingRecord;

                const statusBadge = isRecorded
                    ? '<span class="badge bg-success-subtle text-success border border-success px-2 py-1"><i class="fas fa-check me-1"></i> Recorded</span>'
                    : '<span class="badge bg-warning-subtle text-warning border border-warning px-2 py-1"><i class="fas fa-clock me-1"></i> Not Recorded</span>';

                let actionButton = '';
                if (isRecorded) {
                    let recordDateObj = matchingRecord.created_at ? new Date(matchingRecord.created_at) : new Date();
                    let formattedDateTime = '';

                    if (recordDateObj && !isNaN(recordDateObj)) {
                        let dateStr = recordDateObj.toLocaleDateString([], { day: '2-digit', month: 'short', year: 'numeric' });
                        let timeStr = recordDateObj.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                        formattedDateTime = `${dateStr}, ${timeStr}`;
                    }

                    actionButton = `<span class="text-muted small"><i class="fas fa-history text-secondary me-1"></i> Checked In - ${formattedDateTime}</span>`;
                } else {
                    if (!canCheckIn) {
                        if (!isDayValid) {
                            actionButton = `<span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1">Not a Class Day</span>`;
                        } else if (!isTimeValid) {
                            actionButton = `<span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1">Outside 30-Min Window</span>`;
                        } else {
                            actionButton = `<span class="badge bg-light text-muted border px-2 py-1">Outside Class Date/Time</span>`;
                        }
                    } else {
                        actionButton = `<button type="button" class="btn btn-success btn-sm px-3 rounded-pill quickCheckBtn shadow-sm" data-student-id="${studentId}" data-student-name="${studentName}">Check In</button>`;
                    }
                }

                tbody.append(`
            <tr class="border-bottom align-middle">
                <td class="fw-bold text-dark">${studentName}</td>
                <td class="text-center">${statusBadge}</td>
                <td class="text-end">${actionButton}</td>
            </tr>
        `);
            });

            if (matchCount === 0) {
                tbody.html('<tr><td colspan="3" class="text-center text-muted py-4">No matching students found for this class.</td></tr>');
            }
        }

        // ----------------------------------------------------
        // 5. Audit Table Event Handlers & Dynamic Check-In (AJAX)
        // ----------------------------------------------------
        $('#auditClassSelect, #auditDateSelect').on('change', function () {
            renderAuditTable();
        });

        $('#auditSearchInput').on('keyup search input', function () {
            renderAuditTable();
        });

        function showAuditAlert(message, type = 'success') {
            const isSuccess = type === 'success';

            const iconClass = isSuccess ? 'fa-check' : 'fa-exclamation';
            const bgClass = isSuccess ? 'bg-success' : 'bg-danger';
            const title = isSuccess ? 'Success' : 'Error';

            const toastHtml = `
        <div class="custom-toast ${type}-toast shadow-lg rounded-4 p-3 mb-3 d-flex align-items-center justify-content-between position-relative overflow-hidden">
            <div class="d-flex align-items-center">
                <div class="icon-shape ${bgClass} text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; flex-shrink: 0;">
                    <i class="fas ${iconClass}"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">${title}</h6>
                    <small class="text-secondary">${message}</small>
                </div>
            </div>
            <button type="button" class="btn-close ms-3" onclick="$(this).closest('.custom-toast').fadeOut('fast', function(){ $(this).remove(); })"></button>
            <div class="toast-progress ${bgClass}"></div>
        </div>
    `;

            // Append toast inside container
            const $toast = $(toastHtml).appendTo('#flash-alerts');

            // Auto dismiss after 4 seconds
            setTimeout(() => {
                $toast.fadeOut('slow', function () {
                    $(this).remove();
                });
            }, 4000);
        }

        // Quick Check-In via AJAX without closing modal/reloading page
        $(document).on('click', '.quickCheckBtn', function (e) {
            e.preventDefault();

            const btn = $(this);
            // Fixed syntax error: added missing closing parenthesis
            const studentId = btn.data('student-id');
            const studentName = btn.data('student-name') || 'Student';
            const classId = $('#auditClassSelect').val();
            const auditDate = $('#auditDateSelect').val();

            if (!classId || !auditDate) {
                showAuditAlert("Please select both a class schedule and date before checking in.", "danger");
                return;
            }

            // Disable button briefly to prevent double submits
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Checking in...');

            // Perform AJAX Request matching the form route
            $.ajax({
                url: $('#quickCheckInForm').attr('action') || "{{ route('attendances.ClientinTime') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    class_id: classId,
                    attendance_date: auditDate,
                    // Sent as array to satisfy 'client_ids' validation rule in Laravel
                    'client_ids[]': [studentId],
                    client_ids: [studentId],
                    client_id: studentId
                },
                success: function (response) {
                    // Update local memory dataset
                    const newRecord = {
                        class_id: classId,
                        attendance_date: auditDate,
                        client_id: studentId,
                        created_at: new Date().toISOString()
                    };
                    allClientRecords.push(newRecord);

                    // Re-render table locally
                    renderAuditTable();

                    // Display Inline Success Message
                    showAuditAlert(`Successfully checked in <strong>${studentName}</strong>!`, 'success');
                },
                error: function (xhr) {
                    btn.prop('disabled', false).text('Check In');
                    let errorMsg = 'An error occurred while trying to check in.';

                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        // Extract first validation error key & message
                        const firstKey = Object.keys(xhr.responseJSON.errors)[0];
                        errorMsg = xhr.responseJSON.errors[firstKey][0];
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }

                    showAuditAlert(`<strong>Error:</strong> ${errorMsg}`, 'danger');
                }
            });
        });
    });
</script>