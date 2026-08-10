@include('master.header')

{{-- Summernote CSS & Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

@include('master.sidebar')
@include('master.nav')

<style>
    /* Select2 Customization for Bootstrap 5 & Modal */
    .select2-container--default .select2-selection--single {
        height: 42px;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px;
        padding-left: 12px;
        color: #495057;
        font-weight: 500;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
        right: 10px;
    }
    /* Ensure Dropdown is visible over modals */
    .select2-container {
        z-index: 100000;
        width: 100% !important; /* Force width */
    }
    /* Style for Optgroup in Select2 */
    .select2-results__group {
        background-color: #f8f9fa;
        color: #0d6efd;
        font-weight: 700;
        padding: 8px 12px;
        border-bottom: 1px solid #e9ecef;
    }
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

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Class Attendance Management</h4>
            <button class="btn btn-dark btn-sm rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal"
                data-bs-target="#adminReviewAttendanceModal">
                <i class="fas fa-clipboard-check me-2"></i> Client Attendance Overview & Check-In
            </button>
        </div>

        {{-- CLASS DATE FILTER FORM --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-body p-4">
                <form method="GET" action="{{ url()->current() }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Active Date From</label>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date', \Carbon\Carbon::today()->toDateString()) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Active Date To</label>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date', \Carbon\Carbon::today()->toDateString()) }}">
                        </div>
                        <div class="col-md-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100" style="background-color: #BE9676; border: none;">
                                <i class="fas fa-filter me-1"></i> Filter Classes
                            </button>
                            <a href="{{ url()->current() }}" class="btn btn-light w-100 border">
                                Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="border-0 rounded-4 card">
            <div class="card-body mt-3">
                {{-- WRAPPED TABLE IN TABLE-RESPONSIVE FOR MOBILE SCROLLING --}}
                <div class="table-responsive">
                    <table id="branch-table" class="table table-hover align-middle text-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>No.</th>
                                <th>Status</th>
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

                                    // Check if class is completed for long-term organization
                                    $isClassCompleted = $now->greaterThan($endDate);
                                    $isWithinDateRange = $now->between($startDate, $endDate);

                                    // 1. Create Carbon instances for start and end times
                                    $startTime = \Carbon\Carbon::today()->setTimeFromTimeString($a->start_time);
                                    $endTime = \Carbon\Carbon::today()->setTimeFromTimeString($a->end_time);

                                    // 2. Apply 15-minute buffers safely (၁၅ မိနစ်ကြိုဖွင့်ရန်)
                                    $startTimeWithBuffer = $startTime->copy()->subMinutes(15);
                                    $endTimeWithBuffer = $endTime;

                                    // 3. Format everything strictly in 24-hour time strings
                                    $currentTimeOnly = $now->format('H:i:s');
                                    $startTimeStr = $startTimeWithBuffer->format('H:i:s');
                                    $endTimeStr = $endTimeWithBuffer->format('H:i:s');

                                    // 4. Compare strings directly
                                    $isWithinTimeBuffer = ($currentTimeOnly >= $startTimeStr && $currentTimeOnly <= $endTimeStr);
                                    
                                    // Admin ဖြစ်ပါက အချိန်ကျော်လည်း Check-in လုပ်ခွင့်ပေးရန်
                                    $isAdminUserRole = auth()->user()->hasRole("Admin");
                                    if ($isAdminUserRole) {
                                        $isClickable = true; // Admin bypass time and date
                                    } else {
                                        $isClickable = $isWithinDateRange && $isWithinTimeBuffer;
                                    }
                                @endphp

                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    {{-- CLASS STATUS FOR LONG TERM ORGANIZATION --}}
                                    <td>
                                        @if($isClassCompleted)
                                            <span class="badge bg-secondary">Completed</span>
                                        @else
                                            <span class="badge bg-success">Active</span>
                                        @endif
                                    </td>

                                    <td class="fw-bold">
                                        @foreach ($a->instructor as $inst)
                                            <span class="badge bg-info text-white me-1 mb-1">
                                                {{ $inst['user']['name'] ?? 'N/A' }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <span class="fw-bold d-block">{{ $a->class_name }}</span>
                                        <span class="badge bg-secondary" style="font-size: 10px;">{{ $a->category->name ?? 'Class' }}</span>
                                    </td>
                                    
                                    {{-- DATE COLUMN --}}
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-dark" style="font-size: 0.9rem;">
                                                <i class="fas fa-calendar text-primary me-1"></i> {{ \Carbon\Carbon::parse($a->start_date)->format('d M Y') }}
                                            </span>
                                            <span class="text-muted small mt-1">
                                                <i class="fas fa-arrow-right text-secondary me-1" style="font-size: 10px;"></i> {{ \Carbon\Carbon::parse($a->end_date)->format('d M Y') }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- DAYS COLUMN --}}
                                    <td>
                                        @if(!empty($a->days) && is_array($a->days))
                                            @foreach($a->days as $day)
                                                <span class="badge bg-secondary text-white me-1 mb-1">{{ $day }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted small">No scheduled days</span>
                                        @endif
                                    </td>

                                    {{-- TIME COLUMN --}}
                                    <td>
                                        <div class="badge bg-light text-dark border px-2 py-2 d-inline-flex align-items-center gap-1 shadow-sm">
                                            <i class="fas fa-clock text-warning"></i>
                                            <span class="fw-bold">{{ \Carbon\Carbon::parse($a->start_time)->format('h:i A') }}</span>
                                            <span class="text-muted mx-1">-</span>
                                            <span class="fw-bold">{{ \Carbon\Carbon::parse($a->end_time)->format('h:i A') }}</span>
                                        </div>
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
                                                @if($isClickable)
                                                    <div class="dropdown">
                                                        <button class="btn btn-primary btn-sm px-3 dropdown-toggle rounded-pill shadow-sm"
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
                                                @else
                                                    <button class="btn btn-secondary btn-sm px-3 rounded-pill shadow-sm" type="button" disabled
                                                        title="Available 15 mins before start time and up to end time.">
                                                        Locked Time
                                                    </button>
                                                @endif
                                            @else
                                                <div class="dropdown">
                                                    <button class="btn btn-primary btn-sm px-3 dropdown-toggle rounded-pill shadow-sm"
                                                        type="button" data-bs-toggle="dropdown" aria-expanded="false" {{ $isClickable ? '' : 'disabled' }}>
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
                                        <div class="w-100">
                                            @if(auth()->user()->hasRole("Instructor"))
                                                @php
                                                    $attdItem = collect($a->instructor)->filter(function ($instructor) {
                                                        return $instructor['instructor_id'] === auth()->id();
                                                    })->first();

                                                    $isAdminApproved = ($attdItem['attendance']['admin_approve'] ?? 0) == 1 && ($attdItem['attendance']['attended'] ?? 0) == 1;
                                                    $isPendingApproval = ($attdItem['attendance']['admin_approve'] ?? 0) == 0 && ($attdItem['attendance']['attended'] ?? 0) == 1;
                                                @endphp

                                                <div class="mb-2">
                                                    {{ $attdItem['user']['name'] ?? 'N/A' }}

                                                    @if($isAdminApproved)
                                                        Recorded: <span class="badge bg-success">Approved</span>
                                                    @elseif($isPendingApproval)
                                                        Recorded: <span type="submit" class="badge bg-warning">Waiting For admin Approve</span>
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

                <div id="instructor-inline-picker" class="mx-auto" style="pointer-events: none;"></div>
                <input type="hidden" name="attendance_date" id="instructor_attendance_date" required>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-pill">Confirm Instructor
                    Attendance</button>
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
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label small fw-bold">Select Class:</label>
                        {{-- Select2 Dropdown --}}
                        <select id="auditClassSelect" class="form-select shadow-sm select2" style="width: 100%;">
                            <option value="">-- Choose Class Schedule --</option>
                            
                            {{-- GROUP CLASSES BY CATEGORY FOR ORGANIZED LONG-TERM STORAGE UI --}}
                            @php
                                $groupedClasses = collect($att)->groupBy(function($item) {
                                    return $item->category->name ?? 'Uncategorized Classes';
                                });
                            @endphp

                            @foreach($groupedClasses as $categoryName => $classesInCategory)
                                <optgroup label="{{ $categoryName }}">
                                    @foreach($classesInCategory as $a)
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

                                            $rawDays = is_string($a->days) ? json_decode($a->days, true) : $a->days;
                                            $formattedDays = is_array($rawDays) ? implode(', ', $rawDays) : 'N/A';
                                            
                                            $timeString = \Carbon\Carbon::parse($a->start_time)->format('h:i A') . ' to ' . \Carbon\Carbon::parse($a->end_time)->format('h:i A');
                                            $dateString = \Carbon\Carbon::parse($a->start_date)->format('d M Y') . ' - ' . \Carbon\Carbon::parse($a->end_date)->format('d M Y');
                                            
                                            // Determine if class is completed to append visual marker
                                            $isCompleted = \Carbon\Carbon::now()->greaterThan(\Carbon\Carbon::parse($a->end_date)->endOfDay());
                                            $statusText = $isCompleted ? '[Completed]' : '[Active]';
                                        @endphp

                                        @if($isAdminUser || ($isInstructorUser && $belongsToInstructor))
                                            <option value="{{ $a->id }}" data-name="{{ $a->class_name }}"
                                                data-start="{{ $a->start_date }}" data-end="{{ $a->end_date }}"
                                                data-start-time="{{ $a->start_time }}" data-end-time="{{ $a->end_time }}"
                                                data-days='@json($a->days)'>
                                                {{ $statusText }} {{ $a->class_name }} | 📅 {{ $formattedDays }} | 🕒 {{ $timeString }} | 🗓️ {{ $dateString }}
                                            </option>
                                        @endif
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Select Date:</label>
                        <input type="date" id="auditDateSelect" class="form-control shadow-sm"
                            value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Filter Status:</label>
                        <select id="auditStatusFilter" class="form-select shadow-sm">
                            <option value="all">All Students</option>
                            <option value="recorded">Checked-In Only</option>
                            <option value="not_recorded">Not Checked-In</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Search Name:</label>
                        <input type="text" id="auditSearchInput" class="form-control shadow-sm"
                            placeholder="Type student name...">
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
                    <table class="table table-hover align-middle mb-0 text-nowrap" id="auditResultTable">
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
            <div class="modal-footer border-0 d-flex justify-content-between">
                <div class="text-muted small" id="auditSummaryText"></div>
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@include('master.footer')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
{{-- Select2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
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
</style>

<script>
    $(document).ready(function () {
        const isAdminUser = {{ auth()->user()->hasRole('Admin') ? 'true' : 'false' }};

        // Initialize Select2 for the Class Selection Dropdown
        $('#auditClassSelect').select2({
            dropdownParent: $('#adminReviewAttendanceModal'), // Ensure it stays inside the modal
            placeholder: "-- Choose Class Schedule --",
            allowClear: true,
            width: '100%' // Ensure full width
        });

        $('#auditClassSelect').on('select2:select select2:unselect', function (e) {
            renderAuditTable();
        });

        // Initialize DataTable with logic for long-term storage
        if ($.fn.DataTable.isDataTable('#branch-table')) {
            $('#branch-table').DataTable().destroy();
        }
        $('#branch-table').DataTable({
            responsive: true,
            scrollX: true, 
            order: [[1, "desc"]], // Default order by Status (Active first)
        });

        const allInstructorRecords = @json($instructorRecord ?? ($record ?? []));
        const allClientRecords = @json($record ?? []);
        
        @php
            $clientsMaster = App\Models\Booking::with('user')->where('status', 'confirmed')->get();
        @endphp
        const allClientsMaster = @json($clientsMaster ?? []);

        const todayStr = flatpickr.formatDate(new Date(), "Y-m-d");

        const dayMap = { 0: 'Sun', 1: 'Mon', 2: 'Tue', 3: 'Wed', 4: 'Thu', 5: 'Fri', 6: 'Sat' };
        const fullDayMap = { 0: 'Sunday', 1: 'Monday', 2: 'Tuesday', 3: 'Wednesday', 4: 'Thursday', 5: 'Friday', 6: 'Saturday' };

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

        let instructorFp = flatpickr("#instructor-inline-picker", {
            inline: true,
            enableTime: false,
            dateFormat: "Y-m-d",
            defaultDate: todayStr,
            onChange: function (selectedDates, dateStr, instance) {
                if (!isAdminUser && dateStr !== todayStr) {
                    instance.setDate(todayStr, false);
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
            if(!isAdminUser) {
                $('#instructor_attendance_date').val(todayStr);
            } else {
                $('#instructor_attendance_date').val(instructorFp.selectedDates[0] ? flatpickr.formatDate(instructorFp.selectedDates[0], "Y-m-d") : todayStr);
            }
        });

        $(document).on('click', '.openInstructorModal', function (e) {
            e.preventDefault();

            const scheduleId = $(this).data('schedule-id');
            const instructorId = $(this).data('instructor-id');
            const instructorName = $(this).data('instructor-name');
            const classStartDate = $(this).data('start-date');
            const classEndDate = $(this).data('end-date');

            const rawDays = $(this).data('class-days') || [];
            let classDays = [];
            if (typeof rawDays === 'string') {
                try { classDays = JSON.parse(rawDays); } catch (err) { classDays = rawDays.split(',').map(d => d.trim()); }
            } else if (Array.isArray(rawDays)) {
                classDays = rawDays;
            }

            $('#instructor_schedule_id').val(scheduleId);
            $('#instructor_id_input').val(instructorId);
            $('#selectedInstructorNameText').text(instructorName);
            $('#instructor_attendance_date').val(todayStr);

            const isWithinDateRange = (!classStartDate || todayStr >= classStartDate) && (!classEndDate || todayStr <= classEndDate);
            const isTodayClassDay = isDateOnClassDay(todayStr, classDays);

            instructorFp.set('enable', [
                function (date) {
                    const dStr = flatpickr.formatDate(date, "Y-m-d");
                    if (!isAdminUser && dStr !== todayStr) return false;
                    if (classStartDate && dStr < classStartDate) return false;
                    if (classEndDate && dStr > classEndDate) return false;
                    return isDateOnClassDay(dStr, classDays);
                }
            ]);

            instructorFp.setDate(todayStr, true);

            const submitBtn = $('#instructorAttendanceForm button[type="submit"]');
            
            if (!isAdminUser && !isWithinDateRange) {
                submitBtn.prop('disabled', true).text('Cannot Record: Outside Class Date Range');
                submitBtn.removeClass('btn-primary btn-secondary').addClass('btn-danger');
            } else if (!isAdminUser && !isTodayClassDay) {
                submitBtn.prop('disabled', true).text('Cannot Record: Today is not a class day');
                submitBtn.removeClass('btn-primary btn-secondary').addClass('btn-danger');
            } else {
                updateInstructorDateStatus(todayStr);
            }
            $('#instructorAttendanceModal').modal('show');
        });

        function renderAuditTable() {
            const classId = $('#auditClassSelect').val();
            const auditDate = $('#auditDateSelect').val();
            const filterStatus = $('#auditStatusFilter').val(); 
            const searchTerm = $('#auditSearchInput').val().toLowerCase().trim();
            const tbody = $('#auditTableBody');
            
            tbody.empty();
            $('#auditSummaryText').text('');

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
                try { classDays = JSON.parse(rawDays); } catch (err) { classDays = rawDays.split(',').map(d => d.trim()); }
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
            if (!isAdminUser) {
                if (auditDate !== todayStr) {
                    isTimeValid = false; 
                } else if (classStartTime && classEndTime) {
                    const now = new Date();
                    const currentTime = `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}:${String(now.getSeconds()).padStart(2, '0')}`;

                    let [stH, stM, stS] = classStartTime.split(':').map(Number);
                    let startD = new Date();
                    startD.setHours(stH, stM, stS || 0);
                    startD.setMinutes(startD.getMinutes() - 15);
                    const startWithBuffer = `${String(startD.getHours()).padStart(2, '0')}:${String(startD.getMinutes()).padStart(2, '0')}:00`;

                    isTimeValid = (currentTime >= startWithBuffer && currentTime <= classEndTime);
                }
            }

            const canCheckIn = isAdminUser ? true : (isWithinClassRange && isDayValid && isTimeValid);

            const currentFilteredRecords = allClientRecords.filter(rec =>
                String(rec.class_id) === String(classId) &&
                rec.attendance_date === auditDate &&
                rec.client_id
            );
            
            let matchCount = 0;
            let totalCheckedIn = 0;
            let totalBooked = 0;

            allClientsMaster.forEach(client => {
                const clientClassId = client.selected_class_id;
                if (String(clientClassId) !== String(classId)) return;

                const user = client.user;
                if (!user) return;

                totalBooked++;
                const studentId = String(user.id);
                const studentName = user.name || 'Unknown Student';

                if (searchTerm && !studentName.toLowerCase().includes(searchTerm)) return;

                const matchingRecord = currentFilteredRecords.find(rec => String(rec.client_id) === studentId);
                const isRecorded = !!matchingRecord;
                
                if (isRecorded) totalCheckedIn++;

                if (filterStatus === 'recorded' && !isRecorded) return;
                if (filterStatus === 'not_recorded' && isRecorded) return;

                matchCount++;

                const statusBadge = isRecorded
                    ? '<span class="badge bg-success-subtle text-success border border-success px-2 py-1"><i class="fas fa-check me-1"></i> Recorded</span>'
                    : '<span class="badge bg-warning-subtle text-warning border border-warning px-2 py-1"><i class="fas fa-clock me-1"></i> Not Recorded</span>';

                let actionButton = '';
                if (isRecorded) {
                    let recordDateObj = matchingRecord.created_at ? new Date(matchingRecord.created_at) : null;
                    let formattedDateTime = '';
                    if (recordDateObj && !isNaN(recordDateObj)) {
                        let dateStr = recordDateObj.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
                        let timeStr = recordDateObj.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
                        formattedDateTime = `<span class="fw-bold text-dark">${dateStr}</span> at <span class="fw-bold text-dark">${timeStr}</span>`;
                    }
                    actionButton = `<span class="text-muted small"><i class="fas fa-history text-secondary me-1"></i> Checked In - ${formattedDateTime}</span>`;
                } else {
                    if (!canCheckIn) {
                        if (!isDayValid) {
                            actionButton = `<span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1">Not a Class Day</span>`;
                        } else {
                            actionButton = `<span class="badge bg-light text-muted border px-2 py-1">Outside Class Date/Time</span>`;
                        }
                    } else {
                        actionButton = `<button type="button" class="btn btn-success btn-sm px-3 rounded-pill quickCheckBtn shadow-sm" data-student-id="${studentId}">Check In</button>`;
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

            $('#auditSummaryText').html(`<strong>${totalCheckedIn}</strong> students checked in out of <strong>${totalBooked}</strong> bookings`);

            if (matchCount === 0) {
                tbody.html('<tr><td colspan="3" class="text-center text-muted py-4">No matching students found for selected filter.</td></tr>');
            }
        }

        $('#auditDateSelect, #auditStatusFilter').on('change', function () {
            renderAuditTable();
        });

        $('#auditSearchInput').on('keyup search input', function () {
            renderAuditTable();
        });

        $(document).on('click', '.quickCheckBtn', function () {
            const studentId = $(this).data('student-id');
            const classId = $('#auditClassSelect').val();
            const auditDate = $('#auditDateSelect').val();

            if (!classId || !auditDate) {
                alert("Please select both a class and a date.");
                return;
            }

            $('#quick_class_id').val(classId);
            $('#quick_attendance_date').val(auditDate);
            $('#quick_client_id').val(studentId);

            $('#quickCheckInForm').submit();
        });
    });
</script>