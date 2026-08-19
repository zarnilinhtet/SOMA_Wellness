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
        from { transform: translateX(110%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    @keyframes toastProgress {
        from { width: 100%; }
        to { width: 0%; }
    }

    .flatpickr-calendar {
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
        border: 1px solid #eee !important;
        margin: 0 auto;
        border-radius: 12px;
    }

    .card {
        border-radius: 1rem;
    }

    .flatpickr-time {
        display: none !important;
    }

    .flatpickr-day.has-recorded-attendance {
        position: relative;
        background-color: #e8f5e9 !important;
        color: #1b5e20 !important;
        font-weight: bold;
        border: 1px solid #a5d6a7 !important;
    }

    .flatpickr-day.has-recorded-attendance::after {
        content: "✓";
        position: absolute;
        top: 1px;
        right: 4px;
        font-size: 10px;
        font-weight: 900;
        color: #2e7d32;
    }

    /* Custom styles for filter UI */
    .filter-card {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 0.75rem;
    }
    
    .btn-group .btn {
        font-weight: 600;
    }

    /* Fix for Table Dropdown Clipping Issue */
    .table-responsive {
        min-height: 350px;
        padding-bottom: 20px;
        overflow-x: auto;
    }

    /* Unified Dropdown Action Styles */
    .action-dropdown .dropdown-menu {
        border-radius: 1rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15) !important;
        border: 1px solid #f0f0f0;
        padding: 0.5rem;
        z-index: 1050;
    }
    
    .action-dropdown .dropdown-item {
        border-radius: 0.5rem;
        transition: all 0.2s;
    }
    
    .action-dropdown .dropdown-item:hover {
        background-color: #f1f5f9;
        transform: translateX(3px);
    }
</style>

<div class="container py-4">
    <div class="page-inner">
        {{-- Session Alerts --}}
        @if (session('success'))
            <div class="alert alert-dismissible fade show shadow-sm border-0 d-flex align-items-center py-2 px-4 rounded-pill m-0 mb-3"
                role="alert" style="background-color: #e0f8e9; color: #155724;">
                <i class="fas fa-check-circle me-2 fs-5" style="color: #28a745;"></i>
                <span class="fw-bold">{{ session('success') }}</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-dismissible fade show shadow-sm border-0 d-flex align-items-center py-2 px-4 rounded-pill m-0 mb-3"
                role="alert" style="background-color: #f8d7da; color: #721c24;">
                <i class="fas fa-exclamation-circle me-2 fs-5" style="color: #dc3545;"></i>
                <span class="fw-bold">{{ session('error') }}</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="border-0 rounded-4 card shadow-sm">
            <div class="card-header bg-white border-0 pt-4 pb-2 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <h4 class="fw-bold mb-3 mb-md-0 text-dark">
                    <i class="fas fa-calendar-check me-2 text-primary"></i> Class Attendance Management
                </h4>
                {{-- Global Hub Button --}}
                <button class="btn btn-primary btn-sm rounded-pill px-4 py-2 fw-bold shadow-sm" data-bs-toggle="modal"
                    data-bs-target="#adminReviewAttendanceModal">
                    <i class="fas fa-globe me-2"></i> Global Attendance Hub
                </button>
            </div>
            
            <div class="card-body px-4 pt-0">
                {{-- View Mode Controls (Specific Date vs All Classes) --}}
                <div class="filter-card p-3 mb-4 mt-2 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 shadow-sm">
                    <div class="btn-group shadow-sm" role="group" aria-label="Class View Toggle">
                        <input type="radio" class="btn-check" name="classViewMode" id="viewDateMode" autocomplete="off" checked>
                        <label class="btn btn-outline-info px-4" for="viewDateMode">
                            <i class="fas fa-calendar-day me-2"></i>View by Date
                        </label>

                        <input type="radio" class="btn-check" name="classViewMode" id="viewAllMode" autocomplete="off">
                        <label class="btn btn-outline-info px-4" for="viewAllMode">
                            <i class="fas fa-list me-2"></i>All Classes
                        </label>
                    </div>

                    <div id="mainTableDateContainer" class="d-flex align-items-center bg-white p-2 rounded-pill shadow-sm border" style="min-width: 250px;">
                        <span class="px-3 text-muted fw-bold small"><i class="fas fa-calendar-alt"></i> Selected Date:</span>
                        <input type="date" id="mainTableDateFilter" class="form-control form-control-sm border-0 fw-bold text-primary bg-transparent w-auto" value="{{ \Carbon\Carbon::now('Asia/Yangon')->format('Y-m-d') }}">
                    </div>
                </div>

                @php
                    // Pre-fetch today's attendances to include Substitutes in Class Completion column
                    $todayDateBlade = \Carbon\Carbon::now('Asia/Yangon')->format('Y-m-d');
                    $classIds = collect($att)->pluck('id')->toArray();
                    $actualAttendancesToday = \App\Models\Attendance::with('instructor.user')
                        ->whereIn('class_id', $classIds)
                        ->whereDate('attendance_date', $todayDateBlade)
                        ->whereNull('client_id')
                        ->get()
                        ->groupBy('class_id');
                @endphp

                <div class="table-responsive">
                    <table id="branch-table" class="table table-hover align-middle">
                        <thead class="table-light shadow-sm">
                            <tr>
                                <th style="min-width: 50px;">No.</th>
                                <th style="min-width: 150px;">Assigned Instructors</th>
                                <th style="min-width: 180px;">Class Name</th>
                                <th style="min-width: 140px;">Class Date</th>
                                <th style="min-width: 120px;">Class Time</th>
                                <th style="min-width: 150px;" class="text-center">Manage Attendance</th>
                                <th style="min-width: 180px;" class="text-center">Approval Progress</th>
                                <th style="min-width: 180px;">Class Completion</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($att as $a)
                                @php
                                    $yangonNow = \Carbon\Carbon::now('Asia/Yangon');

                                    // Check Data for Javascript Filtering
                                    $rawStartDate = \Carbon\Carbon::parse($a->start_date)->format('Y-m-d');
                                    $rawEndDate = $a->end_date ? \Carbon\Carbon::parse($a->end_date)->format('Y-m-d') : '2099-12-31';

                                    $classEndDateTimeStr = $yangonNow->format('Y-m-d') . ' ' . ($a->end_time ?? '23:59:59');
                                    $classEndDateTime = \Carbon\Carbon::parse($classEndDateTimeStr, 'Asia/Yangon');
                                    $hasEnded = $yangonNow->greaterThanOrEqualTo($classEndDateTime);

                                    $classApproved = \App\Models\Attendance::where('class_id', $a->id)
                                                        ->whereDate('attendance_date', $todayDateBlade)
                                                        ->where('class_approve', 1)
                                                        ->exists();

                                    // Merge Assigned Instructors with Actual Check-ins (Substitutes) for Display
                                    $todaysAttendances = $actualAttendancesToday->get($a->id) ?? collect();
                                    $displayInstructors = [];
                                    $assignedIdsArray = is_string($a->instructor_ids) ? json_decode($a->instructor_ids, true) : ($a->instructor_ids ?? []);

                                    // 1. Add actual checked-in instructors (Assigned or Substitutes)
                                    foreach($todaysAttendances as $attRecord) {
                                        $isAssigned = in_array($attRecord->instructor_id, $assignedIdsArray);
                                        $displayInstructors[$attRecord->instructor_id] = [
                                            'instructor_id' => $attRecord->instructor_id,
                                            'name' => $attRecord->instructor->user->name ?? 'Unknown',
                                            'is_substitute' => !$isAssigned,
                                            'admin_approve' => $attRecord->admin_approve,
                                            'attended' => $attRecord->attended,
                                        ];
                                    }

                                    // 2. Add assigned instructors who HAVEN'T checked in yet
                                    foreach($a->instructor as $assignedInst) {
                                        if(!isset($displayInstructors[$assignedInst['id']])) {
                                            $displayInstructors[$assignedInst['id']] = [
                                                'instructor_id' => $assignedInst['id'],
                                                'name' => $assignedInst['user']['name'] ?? 'Unknown',
                                                'is_substitute' => false,
                                                'admin_approve' => 0,
                                                'attended' => 0,
                                            ];
                                        }
                                    }
                                @endphp

                                <tr class="main-class-row" 
                                    data-start-date="{{ $rawStartDate }}" 
                                    data-end-date="{{ $rawEndDate }}" 
                                    data-days="{{ json_encode($a->days ?? []) }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-bold">
                                        @foreach ($a->instructor as $inst)
                                            <span class="badge bg-info text-white me-1 mb-1 shadow-sm px-2 py-1">
                                                <i class="fas fa-user me-1"></i> {{ $inst['user']['name'] ?? 'N/A' }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <span class="fw-bold d-block text-dark">{{ $a->class_name }}</span>
                                        <div class="mt-1">
                                            @if(!empty($a->days) && is_array($a->days))
                                                @foreach($a->days as $day)
                                                    <span class="badge bg-light text-secondary border me-1 mb-1" style="font-size: 0.7rem;">{{ $day }}</span>
                                                @endforeach
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column" style="font-size: 0.85rem;">
                                            <span class="text-success fw-bold"><i class="fas fa-play-circle me-1"></i> {{ \Carbon\Carbon::parse($a->start_date)->format('d M Y') }}</span>
                                            <span class="text-danger fw-bold mt-1"><i class="fas fa-stop-circle me-1"></i> {{ $a->end_date ? \Carbon\Carbon::parse($a->end_date)->format('d M Y') : 'Ongoing' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-dark fw-bold" style="font-size: 0.85rem; background: #f8f9fa; padding: 6px 10px; border-radius: 8px; border: 1px solid #e9ecef; display: inline-block;">
                                            <i class="far fa-clock text-primary me-1"></i> 
                                            {{ \Carbon\Carbon::parse($a->start_time)->format('h:i A') }} <br>
                                            <span class="text-muted" style="font-size: 0.75rem; margin-left: 18px;">to {{ \Carbon\Carbon::parse($a->end_time)->format('h:i A') }}</span>
                                        </div>
                                    </td>
                                    
                                    <td class="text-center">
                                        <div class="dropdown action-dropdown">
                                            <button class="btn btn-dark btn-sm px-3 dropdown-toggle rounded-pill shadow-sm fw-bold w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-tasks me-1"></i> Manage
                                            </button>
                                            
                                            <ul class="dropdown-menu shadow-lg border-0 p-2 mt-1" style="min-width: 250px;">
                                                <!-- Section: Instructor Check-In -->
                                                <li class="dropdown-header text-uppercase fw-bold text-primary px-3 mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                                    <i class="fas fa-chalkboard-teacher me-1"></i> Instructor Actions
                                                </li>
                                                
                                                <li>
                                                    <a class="dropdown-item py-2 d-flex align-items-center openInstructorModal px-3" href="#"
                                                        data-schedule-id="{{ $a->id }}"
                                                        data-class-days="{{ json_encode($a->days) }}"
                                                        data-start-date="{{ $a->start_date }}"
                                                        data-end-date="{{ $a->end_date }}"
                                                        data-instructor-id="{{ !empty($a->instructor) ? $a->instructor[0]['id'] : '' }}">
                                                        <i class="fas fa-user-check me-2 text-success"></i> 
                                                        <span class="fw-medium">Record / Substitute</span>
                                                    </a>
                                                </li>

                                                <li><hr class="dropdown-divider my-2 mx-2"></li>
                                                
                                                <!-- Section: Student Check-In -->
                                                <li class="dropdown-header text-uppercase fw-bold text-info px-3 mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                                    <i class="fas fa-user-graduate me-1"></i> Student Actions
                                                </li>
                                                <li>
                                                    <a class="dropdown-item py-2 d-flex align-items-center openStudentCheckinModal px-3" style="background-color: #f8f9fa;" href="#" data-class-id="{{ $a->id }}">
                                                        <i class="fas fa-users-cog me-2 text-primary"></i> 
                                                        <span class="fw-bold text-dark">Manage Students</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                    
                                    <td class="text-center">
                                        @if(!auth()->user()->hasRole("Instructor"))
                                            @if($classApproved)
                                                <span class="badge bg-success-subtle text-success border border-success d-block mb-1 px-3 py-2 rounded-pill"><i class="fas fa-check-double me-1"></i> Attendance Approved</span>
                                                <form action="{{ route('attendances.cancelClassApprove', $a->id) }}" method="POST" class="d-inline-block w-100 mt-1">
                                                    @csrf @method('PUT')
                                                    <button type="button" class="btn btn-outline-danger btn-sm w-100 rounded-pill btn-confirm-action" data-message="Are you sure you want to CANCEL this Class Attendance Approval?">
                                                        <i class="fas fa-times me-1"></i> Cancel Approval
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('attendances.classApprove', $a->id) }}" method="POST" class="d-inline-block w-100">
                                                    @csrf @method('PUT')
                                                    <button type="button" class="btn btn-success btn-sm w-100 shadow-sm rounded-pill btn-confirm-action" data-message="Are you sure you want to approve attendance for this class?">
                                                        <i class="fas fa-stamp me-1"></i> Approve Attendance
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            @if($classApproved)
                                                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill border border-success"><i class="fas fa-check-circle me-1"></i> Approved</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill border border-warning"><i class="fas fa-hourglass-half me-1"></i> Pending Admin</span>
                                            @endif
                                        @endif
                                    </td>

                                    <td>
                                        <div class="w-100 bg-white rounded-3 p-2 border shadow-sm">
                                            @foreach ($displayInstructors as $dInst)
                                                @php
                                                    $isAdminApproved = ($dInst['admin_approve'] == 1) && ($dInst['attended'] == 1);
                                                    $isPendingApproval = ($dInst['admin_approve'] == 0) && ($dInst['attended'] == 1);
                                                @endphp

                                                <div class="mb-2 {{ !$loop->last ? 'border-bottom pb-2' : '' }}">
                                                    <div class="mb-1 fw-bold text-dark" style="font-size: 0.85rem;">
                                                        <i class="fas fa-chalkboard-teacher text-primary opacity-50 me-1"></i> 
                                                        {{ $dInst['name'] }}
                                                        @if($dInst['is_substitute'])
                                                            <span class="badge bg-warning text-dark ms-1" style="font-size: 0.65rem;">Substitute</span>
                                                        @endif
                                                    </div>
                                                    
                                                    @if($isAdminApproved)
                                                         <span class="badge bg-success mb-1 px-2 py-1"><i class="fas fa-check-circle me-1"></i> Completed</span>
                                                         
                                                         @if(!auth()->user()->hasRole("Instructor"))
                                                         <form action="{{ route('attendances.cancelAdminApprove', $dInst['instructor_id']) }}" method="POST" class="d-inline-block ms-1">
                                                             @csrf @method('PUT')
                                                             <input type="hidden" name="class_id" value="{{ $a->id }}">
                                                             <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 btn-confirm-action" data-message="Are you sure you want to CANCEL this completion approval?">
                                                                Cancel
                                                             </button>
                                                         </form>
                                                         @endif

                                                    @elseif($isPendingApproval)
                                                        @if($hasEnded)
                                                            @if(!auth()->user()->hasRole("Instructor"))
                                                            <form action="{{ route('attendances.adminApprove', $dInst['instructor_id']) }}" method="POST" class="d-inline-block">
                                                                @csrf @method('PUT')
                                                                <input type="hidden" name="class_id" value="{{ $a->id }}">
                                                                <button type="button" class="btn btn-sm btn-success py-1 px-3 shadow-sm rounded-pill btn-confirm-action" data-message="Are you sure you want to approve this instructor's completion?">
                                                                   Approve Completion
                                                                </button>
                                                            </form>
                                                            @else
                                                                <span class="badge bg-warning text-dark"><i class="fas fa-spinner fa-spin me-1"></i> Wait for Admin</span>
                                                            @endif
                                                        @else
                                                            <span class="badge bg-warning text-dark px-2 py-1" title="Available after {{ $classEndDateTime->format('h:i A') }}">
                                                                <i class="fas fa-clock me-1"></i> Wait for Class End
                                                            </span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-light text-muted border px-2 py-1" style="font-size: 0.75rem;">Not Recorded</span>
                                                    @endif
                                                </div>
                                            @endforeach
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

{{-- ==================== INSTRUCTOR ATTENDANCE MODAL (With Substitute Selection) ==================== --}}
@php
    $allSystemInstructors = \App\Models\Instructor::with('user')->get();
@endphp

<div class="modal fade" id="instructorAttendanceModal" tabindex="-1" aria-labelledby="instructorModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="instructorAttendanceForm" method="POST" action="{{ route('attendances.inTime') }}"
            class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header bg-light border-0 rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="instructorModalTitle"><i class="fas fa-chalkboard-teacher text-primary me-2"></i> Record Instructor Attendance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <input type="hidden" name="class_id" id="instructor_schedule_id">

                {{-- DYNAMIC INSTRUCTOR SELECTION (Assigned vs Substitute) --}}
                <div class="mb-4 text-start p-3 bg-light rounded-3 border">
                    <label class="form-label fw-bold text-dark mb-2"><i class="fas fa-user-tie text-primary me-1"></i> Select Teaching Instructor:</label>
                    <select name="instructor_id" id="instructor_id_input" class="form-select shadow-sm fw-bold" required>
                        <!-- Options populated dynamically via JS -->
                    </select>
                    <small class="text-muted d-block mt-2" style="font-size: 0.8rem;">
                        <i class="fas fa-info-circle me-1 text-info"></i> Default is assigned instructor. Change this if a substitute teacher is taking the class today.
                    </small>
                </div>

                <div id="instructor-inline-picker" class="mx-auto" style="pointer-events: none;"></div>

                <input type="hidden" name="attendance_date" id="instructor_attendance_date" required>
            </div>
            <div class="modal-footer border-0 bg-light rounded-bottom-4">
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-pill shadow-sm fs-6">
                    <i class="fas fa-check-circle me-1"></i> Confirm Attendance
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ==================== ADMIN CLIENT OVERVIEW & CHECK-IN MODAL ==================== --}}
<div class="modal fade" id="adminReviewAttendanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light border-0 rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-user-graduate me-2 text-primary"></i> Student
                    Attendance Manager & Check-In Hub</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light pt-4">
                <div id="flash-alerts" style="position: absolute; top: 20px; right: 20px; z-index: 9999; min-width: 320px; max-width: 420px;">
                </div>
                
                <div class="row g-3 mb-4 bg-white p-4 rounded-4 shadow-sm border">
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted"><i class="fas fa-chalkboard me-1"></i> Selected Class:</label>
                        <select id="auditClassSelect" class="form-select border-primary shadow-sm fw-bold text-dark py-2">
                            <option value="">-- Choose Class Schedule --</option>
                            @foreach($att as $a)
                                @php
                                    $isAdminUser = auth()->user()->hasRole("Admin");
                                    $isInstructorUser = auth()->user()->hasRole("Instructor");
                                    $currentInstructorUserId = auth()->id();
                                    $belongsToInstructor = false;

                                    if ($isInstructorUser) {
                                        // Condition 1: Check if this logged-in instructor is explicitly assigned to this class
                                        if (!empty($a->instructor)) {
                                            $instructors = $a->instructor;
                                            if (is_string($instructors)) {
                                                $instructors = json_decode($instructors, true);
                                            }
                                            if ($instructors instanceof \Illuminate\Support\Collection) {
                                                $instructors = $instructors->toArray();
                                            }
                                            if (is_array($instructors)) {
                                                foreach ($instructors as $insItem) {
                                                    $userId = is_array($insItem) ? ($insItem['user']['id'] ?? ($insItem['user_id'] ?? null)) : ($insItem->user->id ?? null);
                                                    if ($userId == $currentInstructorUserId) {
                                                        $belongsToInstructor = true;
                                                        break;
                                                    }
                                                }
                                            }
                                        }

                                        // Condition 2: NEW ADDITION -> Check if the logged in instructor has Substitute Attendance today for this class
                                        if (!$belongsToInstructor) {
                                            $todaysAttendancesForThisClass = $actualAttendancesToday->get($a->id) ?? collect();
                                            foreach($todaysAttendancesForThisClass as $attRecord) {
                                                $subUserId = $attRecord->instructor->user->id ?? ($attRecord->instructor->user_id ?? null);
                                                if ($subUserId == $currentInstructorUserId) {
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
                                        {{ \Carbon\Carbon::parse($a->start_time)->format('h:i A') }} -
                                        {{ \Carbon\Carbon::parse($a->end_time)->format('h:i A') }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="form-label small fw-bold text-muted"><i class="far fa-calendar-alt me-1"></i> Filter Date:</label>
                        <input type="date" id="auditDateSelect" class="form-control shadow-sm py-2 text-primary fw-bold"
                            value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="form-label small fw-bold text-muted"><i class="fas fa-search me-1"></i> Search Student:</label>
                        <input type="text" id="auditSearchInput" class="form-control shadow-sm py-2"
                            placeholder="Type student name to filter...">
                    </div>
                </div>

                <form id="quickCheckInForm" method="POST" action="{{ route('attendances.ClientinTime') }}">
                    @csrf
                    <input type="hidden" name="class_id" id="quick_class_id">
                    <input type="hidden" name="attendance_date" id="quick_attendance_date">
                    <input type="hidden" name="client_ids[]" id="quick_client_id">
                </form>

                <div class="table-responsive border rounded-4 bg-white shadow-sm"
                    style="max-height: 420px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0" id="auditResultTable">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th class="ps-4 py-3 text-uppercase text-secondary" style="font-size: 0.8rem;">Student Name</th>
                                <th class="text-center py-3 text-uppercase text-secondary" style="font-size: 0.8rem;">Booked Date</th>
                                <th class="text-center py-3 text-uppercase text-secondary" style="font-size: 0.8rem;">Status</th>
                                <th class="text-end pe-4 py-3 text-uppercase text-secondary" style="font-size: 0.8rem;">Action / Check-In</th>
                            </tr>
                        </thead>
                        <tbody id="auditTableBody">
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="fas fa-inbox d-block fs-1 mb-3 text-secondary opacity-50"></i>
                                    Please select a class schedule above to manage student attendance records.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0 bg-white rounded-bottom-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4 shadow-sm fw-bold" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@include('master.footer')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    $(document).ready(function () {
        // ==========================
        // Variables & Init Data
        // ==========================
        let allInstructorRecords = @json($instructorRecord ?? ($record ?? []));
        let allClientRecords = @json($record ?? []);
        @php $clientsMaster = App\Models\Booking::with('bookingUser')->get(); @endphp
        const allClientsMaster = @json($clientsMaster ?? []);
        const allSystemInstructors = @json($allSystemInstructors ?? []);

        const todayStr = flatpickr.formatDate(new Date(), "Y-m-d");
        const dayMap = { 0: 'Sun', 1: 'Mon', 2: 'Tue', 3: 'Wed', 4: 'Thu', 5: 'Fri', 6: 'Sat' };
        const fullDayMap = { 0: 'Sunday', 1: 'Monday', 2: 'Tuesday', 3: 'Wednesday', 4: 'Thursday', 5: 'Friday', 6: 'Saturday' };

        // Helper Functions 
        function isDateOnClassDay(dateStr, classDaysArray) {
            if (!classDaysArray || classDaysArray.length === 0) return true;
            const parts = dateStr.split('-');
            const targetDate = new Date(parts[0], parts[1] - 1, parts[2]);
            const dayNum = targetDate.getDay();
            const shortName = dayMap[dayNum].toLowerCase();
            const fullName = fullDayMap[dayNum].toLowerCase();
            const normalizedDays = classDaysArray.map(d => String(d).toLowerCase().trim());
            return normalizedDays.includes(String(dayNum)) || normalizedDays.includes(shortName) || normalizedDays.includes(fullName);
        }

        // ==========================
        // DataTable & Custom View Filters
        // ==========================
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex, rowData, counter) {
            if (settings.nTable.id !== 'branch-table') return true;
            
            let viewMode = $('input[name="classViewMode"]:checked').attr('id');
            if (viewMode === 'viewAllMode') return true;

            let filterDateStr = $('#mainTableDateFilter').val();
            if (!filterDateStr) return true;

            let $row = $(settings.aoData[dataIndex].nTr);
            let startDate = $row.attr('data-start-date');
            let endDate = $row.attr('data-end-date');
            let daysRaw = $row.attr('data-days');
            
            if (filterDateStr < startDate || filterDateStr > endDate) return false;

            let daysArray = [];
            try { daysArray = JSON.parse(daysRaw); } catch(e) {}
            
            return isDateOnClassDay(filterDateStr, daysArray);
        });

        if ($.fn.DataTable.isDataTable('#branch-table')) {
            $('#branch-table').DataTable().destroy();
        }
        
        let branchTable = $('#branch-table').DataTable({
            order: [], 
            language: {
                emptyTable: "No classes found for the selected view or date."
            }
        });

        $('input[name="classViewMode"], #mainTableDateFilter').on('change', function() {
            if ($('#viewAllMode').is(':checked')) {
                $('#mainTableDateContainer').slideUp('fast'); 
            } else {
                $('#mainTableDateContainer').slideDown('fast'); 
            }
            branchTable.draw(); 
        });

        branchTable.draw();

        // ==========================
        // General Utility Interactions
        // ==========================
        $(document).on('click', '.btn-confirm-action', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            let message = $(this).data('message') || 'Are you sure you want to proceed?';
            if(confirm(message)) { form.submit(); }
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
            const $toast = $(toastHtml).appendTo('#flash-alerts');
            setTimeout(() => { $toast.fadeOut('slow', function () { $(this).remove(); }); }, 4000);
        }

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

        // ==========================
        // Instructor Modal Logic
        // ==========================
        let instructorFp = flatpickr("#instructor-inline-picker", {
            inline: true,
            enableTime: false,
            dateFormat: "Y-m-d",
            defaultDate: todayStr,
            onChange: function (selectedDates, dateStr, instance) {
                if (dateStr !== todayStr) instance.setDate(todayStr, false);
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
                return recClassId === scheduleId && recInstId === instructorId && !rec.client_id;
            });
            const submitBtn = $('#instructorAttendanceForm button[type="submit"]');
            if (isAlreadyRecorded) {
                submitBtn.prop('disabled', true).html('<i class="fas fa-check-circle me-1"></i> Already Recorded');
                submitBtn.removeClass('btn-primary btn-danger').addClass('btn-secondary');
            } else {
                submitBtn.prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i> Confirm Attendance');
                submitBtn.removeClass('btn-secondary btn-danger').addClass('btn-primary');
            }
        }

        $('#instructorAttendanceForm').on('submit', function () { $('#instructor_attendance_date').val(todayStr); });

        // Update status when selected instructor changes
        $('#instructor_id_input').on('change', function() {
            updateInstructorDateStatus(todayStr);
            instructorFp.redraw();
        });

        $(document).on('click', '.openInstructorModal', function (e) {
            e.preventDefault();
            const scheduleId = $(this).data('schedule-id');
            const assignedInstructorId = String($(this).data('instructor-id'));
            const classStartDate = $(this).data('start-date');
            const classEndDate = $(this).data('end-date');
            const classStartTime = $(this).data('start-time');
            const classEndTime = $(this).data('end-time');

            // Populate Dropdown for Instructors (Assigned vs Substitute)
            let optionsHtml = '';
            allSystemInstructors.forEach(inst => {
                let isSelected = (String(inst.id) === assignedInstructorId) ? 'selected' : '';
                let label = (String(inst.id) === assignedInstructorId) ? ' (Assigned)' : ' (Substitute)';
                let name = inst.user ? inst.user.name : 'Unknown';
                optionsHtml += `<option value="${inst.id}" ${isSelected}>${name} ${label}</option>`;
            });
            $('#instructor_id_input').html(optionsHtml);

            const rawDays = $(this).data('class-days') || [];
            let classDays = [];
            if (typeof rawDays === 'string') {
                try { classDays = JSON.parse(rawDays); } catch (err) { classDays = rawDays.split(',').map(d => d.trim()); }
            } else if (Array.isArray(rawDays)) {
                classDays = rawDays;
            }

            $('#instructor_schedule_id').val(scheduleId);
            $('#instructor_attendance_date').val(todayStr);

            const isWithinDateRange = (!classStartDate || todayStr >= classStartDate) && (!classEndDate || todayStr <= classEndDate);
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
                submitBtn.prop('disabled', true).html('<i class="fas fa-ban me-1"></i> Cannot Record: Outside Date Range');
                submitBtn.removeClass('btn-primary btn-secondary').addClass('btn-danger');
            } else if (!isTodayClassDay) {
                submitBtn.prop('disabled', true).html('<i class="fas fa-ban me-1"></i> Cannot Record: Not a Class Day');
                submitBtn.removeClass('btn-primary btn-secondary').addClass('btn-danger');
            } else if (!isTimeValid) {
                submitBtn.prop('disabled', true).html('<i class="fas fa-ban me-1"></i> Cannot Record: Outside 30-Min Window');
                submitBtn.removeClass('btn-primary btn-secondary').addClass('btn-danger');
            } else {
                updateInstructorDateStatus(todayStr);
            }

            $('#instructorAttendanceModal').modal('show');
        });

        // ==========================
        // Open Student Check-In Modal from Unified Dropdown
        // ==========================
        $(document).on('click', '.openStudentCheckinModal', function (e) {
            e.preventDefault();
            const classId = $(this).data('class-id');
            
            let isDateView = $('#viewDateMode').is(':checked');
            let selectedDate = isDateView ? $('#mainTableDateFilter').val() : todayStr;

            $('#auditClassSelect').val(classId).trigger('change');
            $('#auditDateSelect').val(selectedDate).trigger('change');
            
            $('#adminReviewAttendanceModal').modal('show');
        });

        // ==========================
        // Modal Student Attendance Audit Logic
        // ==========================
        function renderAuditTable() {
            const classId = $('#auditClassSelect').val();
            const auditDate = $('#auditDateSelect').val();
            const searchTerm = $('#auditSearchInput').val().toLowerCase().trim();
            const tbody = $('#auditTableBody');
            tbody.empty();

            if (!classId) {
                tbody.html(`
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">
                            <i class="fas fa-inbox d-block fs-1 mb-3 text-secondary opacity-50"></i>
                            Please select a class schedule above to view records.
                        </td>
                    </tr>
                `);
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
            } else if (Array.isArray(rawDays)) { classDays = rawDays; }

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
                let bookedDate = client.booked_date;
                if (bookedDate && bookedDate.includes(' ')) {
                    bookedDate = bookedDate.split(' ')[0];
                }

                if (String(clientClassId) !== String(classId)) return;
                
                if (bookedDate && bookedDate !== auditDate) return;

                const user = client.booking_user || client.user;
                if (!user) return;
                const studentId = String(user.id);
                const studentName = user.name || 'Unknown Student';

                if (searchTerm && !studentName.toLowerCase().includes(searchTerm)) { return; }

                matchCount++;
                const matchingRecord = currentFilteredRecords.find(rec => String(rec.client_id) === studentId);
                const isRecorded = !!matchingRecord;

                const statusBadge = isRecorded
                    ? '<span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill shadow-sm"><i class="fas fa-check-circle me-1"></i> Recorded</span>'
                    : '<span class="badge bg-warning-subtle text-warning border border-warning px-3 py-2 rounded-pill shadow-sm"><i class="fas fa-clock me-1"></i> Not Recorded</span>';

                let actionButton = '';
                if (isRecorded) {
                    let recordDateObj = matchingRecord.created_at ? new Date(matchingRecord.created_at) : new Date();
                    let formattedDateTime = '';
                    if (recordDateObj && !isNaN(recordDateObj)) {
                        let dateStr = recordDateObj.toLocaleDateString([], { day: '2-digit', month: 'short', year: 'numeric' });
                        let timeStr = recordDateObj.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                        formattedDateTime = `${dateStr}, ${timeStr}`;
                    }
                    actionButton = `<span class="text-muted small fw-bold bg-light px-3 py-2 border rounded-pill shadow-sm"><i class="fas fa-history text-secondary me-1"></i> Checked In - ${formattedDateTime}</span>`;
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
                        actionButton = `<button type="button" class="btn btn-primary btn-sm px-4 py-2 rounded-pill fw-bold quickCheckBtn shadow-sm" data-student-id="${studentId}" data-student-name="${studentName}"><i class="fas fa-check me-1"></i> Check In</button>`;
                    }
                }

                const displayDate = bookedDate || auditDate;

                tbody.append(`
                    <tr class="border-bottom align-middle bg-white">
                        <td class="fw-bold text-dark ps-4 py-3">${studentName}</td>
                        <td class="text-center">
                            <span class="badge bg-light text-primary border border-primary-subtle px-3 py-2 rounded-pill shadow-sm">
                                <i class="fas fa-calendar-alt me-1"></i> ${displayDate}
                            </span>
                        </td>
                        <td class="text-center">${statusBadge}</td>
                        <td class="text-end pe-4">${actionButton}</td>
                    </tr>
                `);
            });

            if (matchCount === 0) {
                tbody.html(`
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">
                            <i class="fas fa-search d-block mb-3 text-secondary opacity-50 fs-1"></i>
                            No students booked for this class on the selected date (<strong>${auditDate}</strong>).
                        </td>
                    </tr>
                `);
            }
        }

        $('#auditClassSelect, #auditDateSelect').on('change', function () { renderAuditTable(); });
        $('#auditSearchInput').on('keyup search input', function () { renderAuditTable(); });

        $(document).on('click', '.quickCheckBtn', function (e) {
            e.preventDefault();
            
            const btn = $(this);
            const studentId = btn.data('student-id');
            const studentName = btn.data('student-name') || 'Student';
            const classId = $('#auditClassSelect').val();
            const auditDate = $('#auditDateSelect').val();

            if (!classId || !auditDate) {
                showAuditAlert("Please select both a class schedule and date before checking in.", "danger");
                return;
            }

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Checking in...');

            $.ajax({
                url: $('#quickCheckInForm').attr('action') || "{{ route('attendances.ClientinTime') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    class_id: classId,
                    attendance_date: auditDate,
                    'client_ids[]': [studentId],
                    client_ids: [studentId],
                    client_id: studentId
                },
                success: function (response) {
                    const newRecord = {
                        class_id: classId,
                        attendance_date: auditDate,
                        client_id: studentId,
                        created_at: new Date().toISOString()
                    };
                    allClientRecords.push(newRecord);
                    renderAuditTable();
                    showAuditAlert(`Successfully checked in <strong>${studentName}</strong>!`, 'success');
                },
                error: function (xhr) {
                    btn.prop('disabled', false).html('<i class="fas fa-check me-1"></i> Check In');
                    let errorMsg = 'An error occurred while trying to check in.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
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