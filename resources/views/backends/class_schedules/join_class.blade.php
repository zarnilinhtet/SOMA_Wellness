@include('master.header')
@include('master.sidebar')
@include('master.nav')

<!-- Include Select2 & Flatpickr CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    /* Custom Soft UI Tweaks */
    .form-control, .form-select, .select2-container--bootstrap-5 .select2-selection {
        border-radius: 0.5rem;
        padding: 0.6rem 1rem;
        border: 1px solid #e0e4e8;
    }
    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        border-color: #86b7fe;
    }
    .soft-card {
        background-color: #f8f9fa;
        border-radius: 0.75rem;
        border: 1px solid #edf1f5;
    }
    .step-badge {
        background-color: #e9ecef;
        color: #495057;
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin-right: 8px;
        font-weight: bold;
    }
</style>

<div class="container py-4">
    <div class="card shadow-sm border-0 rounded-4 mt-2">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 px-md-5">
            <h4 class="fw-bold text-dark mb-0">Join Class for <span class="text-primary">{{ $user->name ?? 'User' }}</span></h4>
            <p class="text-muted small mt-1">Select an available class and schedule dates for the client.</p>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mx-4 mx-md-5 mt-3 rounded-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show mx-4 mx-md-5 mt-3 rounded-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Dynamic Eligibility Alert --}}
        <div id="eligibility_alert" class="alert alert-danger alert-dismissible fade show d-none mx-4 mx-md-5 mt-3 rounded-3" role="alert">
            <i class="bi bi-shield-exclamation me-2"></i> <span id="eligibility_msg"></span>
        </div>

        <div class="card-body px-4 px-md-5 pb-5 pt-3">
            <form action="{{ route('join.class-for-user-submit') }}" id="join_class_form" method="POST">
                @csrf
                <input type="hidden" name="user_id" id="user_id" value="{{ $user->id }}">

                <div class="row g-4">
                    {{-- STEP 1: CLASS SELECTION --}}
                    <div class="col-md-12">
                        <div class="d-flex align-items-center mb-3">
                            <span class="step-badge">1</span>
                            <h6 class="fw-bold mb-0">Select a Class <span class="text-danger">*</span></h6>
                        </div>
                        
                        <select name="class_id" id="class_select" class="form-select" required>
                            <option value="" disabled {{ old('class_id') ? '' : 'selected' }}>Search or Choose Class...</option>
                            @foreach ($classes as $class)
                                @php
                                    if(strtolower($class->status) === 'completed') continue;

                                    $rawStartDate = \Carbon\Carbon::parse($class->start_date ?? now())->format('Y-m-d');
                                    $rawEndDate = \Carbon\Carbon::parse($class->end_date ?? now())->format('Y-m-d');
                                    $formattedDate = \Carbon\Carbon::parse($class->start_date ?? now())->format('d M Y');
                                    $startTime = $class->start_time ? \Carbon\Carbon::parse($class->start_time)->format('h:i A') : '--';
                                    $endTime = $class->end_time ? \Carbon\Carbon::parse($class->end_time)->format('h:i A') : '--';
                                    
                                    $computedStatus = (strtolower($class->status) === 'ongoing' || strtolower($class->status) === 'active') ? 'Open' : ucfirst($class->status);

                                    // Correctly Extract Instructors from JSON array
                                    $instIds = is_string($class->instructor_ids) ? json_decode($class->instructor_ids, true) : ($class->instructor_ids ?? []);
                                    $instIds = is_array($instIds) ? $instIds : [];
                                    $classInstructors = \App\Models\Instructor::with('user')->whereIn('id', $instIds)->get();
                                    $instructorNames = $classInstructors->pluck('user.name')->filter()->implode(', ') ?: 'N/A';

                                    // Correctly Extract Days
                                    $daysArray = is_string($class->days) ? json_decode($class->days, true) : ($class->days ?? []);
                                    $daysForJs = is_array($daysArray) ? implode(' ', $daysArray) : '';
                                    $daysForDisplay = is_array($daysArray) ? implode(', ', $daysArray) : '';
                                @endphp

                                <option value="{{ $class->id }}" 
                                        data-name="{{ $class->class_name ?? $class->name }}"
                                        data-category="{{ $class->category->name ?? 'N/A' }}"
                                        data-instructor="{{ $instructorNames }}"
                                        data-start-date="{{ $formattedDate }}"
                                        data-raw-start-date="{{ $rawStartDate }}"
                                        data-raw-end-date="{{ $rawEndDate }}"
                                        data-end-date="{{ \Carbon\Carbon::parse($class->end_date ?? now())->format('d M Y') }}"
                                        data-time="{{ $startTime }} - {{ $endTime }}"
                                        data-days="{{ $daysForJs }}"
                                        data-capacity="{{ $class->capacity ?? 0 }}"
                                        data-status="{{ $computedStatus }}"
                                        {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->class_name ?? $class->name }} | Inst: {{ $instructorNames }} | {{ $daysForDisplay }} | {{ $formattedDate }} | {{ $startTime }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- CLEAN PREVIEW CARD --}}
                    <div class="col-md-12 d-none" id="class_card_container">
                        <div class="soft-card p-3 p-md-4">
                            <div class="row align-items-center">
                                <div class="col-md-7 border-end-md mb-3 mb-md-0">
                                    <div class="d-flex align-items-center mb-1">
                                        <h5 class="fw-bold text-dark mb-0 me-2" id="card_title">Class Name</h5>
                                        <span class="badge bg-primary rounded-pill px-2 py-1 small" id="card_category">Category</span>
                                    </div>
                                    <p class="text-muted small mb-3"><i class="bi bi-person-video3 me-1"></i> Instructor: <span id="card_instructor" class="fw-medium text-dark">Name</span></p>
                                    <div id="card_days"></div>
                                </div>
                                <div class="col-md-5 ps-md-4">
                                    <div class="mb-2">
                                        <small class="text-muted d-block text-uppercase fw-semibold" style="font-size: 0.75rem;">Schedule</small>
                                        <span class="text-dark fw-medium" id="card_dates"><i class="bi bi-calendar3 me-1"></i> --</span>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted d-block text-uppercase fw-semibold" style="font-size: 0.75rem;">Time</small>
                                        <span class="text-dark fw-medium" id="card_time"><i class="bi bi-clock me-1"></i> --</span>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block text-uppercase fw-semibold" style="font-size: 0.75rem;">Status</small>
                                        <span class="badge bg-info text-white" id="card_badge">OPEN</span>
                                        <span class="text-muted small ms-2" id="card_slots">(0 Slots)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- STEP 2: DATE SELECTION --}}
                    <div class="col-md-12 mt-4 d-none" id="date_picker_container">
                        <div class="d-flex align-items-center mb-3">
                            <span class="step-badge">2</span>
                            <h6 class="fw-bold mb-0">Select Booking Dates <span class="text-danger">*</span></h6>
                        </div>
                        
                        <div class="input-group shadow-sm" style="border-radius: 0.5rem; overflow: hidden;">
                            <span class="input-group-text bg-white border-end-0 text-primary"><i class="bi bi-calendar-check"></i></span>
                            <input type="text" id="booking_dates" name="booking_dates" class="form-control border-start-0 ps-0 bg-white" placeholder="Click to choose dates..." readonly required>
                            <button type="button" class="btn btn-primary px-4" id="select_all_dates">
                                <i class="bi bi-calendar-plus me-1"></i> Book All Days
                            </button>
                        </div>
                        <small class="text-muted mt-2 d-block"><i class="bi bi-info-circle me-1"></i> The calendar will only allow selecting valid class days (e.g., Mon, Wed).</small>
                    </div>

                </div>

                <hr class="my-4 text-muted">

                <div class="text-end">
                    <a href="{{ url()->previous() }}" class="btn btn-light border px-4 me-2 rounded-3 text-secondary">Cancel</a>
                    <button type="submit" id="submit_btn" class="btn btn-primary px-4 rounded-3" disabled>
                        <i class="bi bi-check2-circle me-1"></i> Confirm Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    $(document).ready(function () {
        $('#class_select').select2({
            theme: 'bootstrap-5',
            placeholder: 'Search or Choose Class...',
            allowClear: true,
            width: '100%'
        });

        const userId = $('#user_id').val();
        let flatpickrInstance = null;
        let validDatesArray = [];

        function getValidDates(startDate, endDate, allowedDayInts) {
            let dates = [];
            let current = new Date(startDate);
            let end = new Date(endDate);
            
            while (current <= end) {
                if (allowedDayInts.includes(current.getDay())) {
                    let d = new Date(current);
                    let month = '' + (d.getMonth() + 1);
                    let day = '' + d.getDate();
                    let year = d.getFullYear();
                    if (month.length < 2) month = '0' + month;
                    if (day.length < 2) day = '0' + day;
                    dates.push([year, month, day].join('-'));
                }
                current.setDate(current.getDate() + 1);
            }
            return dates;
        }

        $('#class_select').on('change', function () {
            const selectedOption = $(this).find(':selected');
            const classId = $(this).val();

            if (!classId) {
                resetFields();
                return;
            }

            const name = selectedOption.data('name');
            const category = selectedOption.data('category');
            const instructor = selectedOption.data('instructor');
            const startDate = selectedOption.data('start-date');
            const rawStartDate = selectedOption.data('raw-start-date');
            const rawEndDate = selectedOption.data('raw-end-date');
            const endDate = selectedOption.data('end-date');
            const time = selectedOption.data('time');
            const days = selectedOption.data('days'); 
            const capacity = selectedOption.data('capacity');
            const status = selectedOption.data('status');

            $('#card_title').text(name);
            $('#card_category').text(category);
            $('#card_instructor').text(instructor);
            $('#card_dates').html(`<i class="bi bi-calendar3 me-1"></i> ${startDate} — ${endDate}`);
            $('#card_time').html(`<i class="bi bi-clock me-1"></i> ${time}`);
            $('#card_slots').text(`(Max: ${capacity} Slots)`);

            const $cardBadge = $('#card_badge');
            if(status === 'Open') {
                $cardBadge.removeClass('bg-secondary bg-danger').addClass('bg-success').text('OPEN');
            } else {
                $cardBadge.removeClass('bg-success').addClass('bg-secondary').text(status.toUpperCase());
            }

            let daysHtml = '';
            const dayMap = { 'Sun': 0, 'Mon': 1, 'Tue': 2, 'Wed': 3, 'Thu': 4, 'Fri': 5, 'Sat': 6 };
            let allowedDayInts = [];

            if (typeof days === 'string') {
                days.split(' ').forEach(day => {
                    if (day) {
                        daysHtml += `<span class="badge bg-white text-dark border me-1 px-2 py-1 shadow-sm"><i class="bi bi-calendar-event text-primary me-1"></i>${day}</span>`;
                        if(dayMap[day] !== undefined) allowedDayInts.push(dayMap[day]);
                    }
                });
            }
            $('#card_days').html(daysHtml);
            $('#class_card_container').removeClass('d-none').hide().fadeIn(300);
            $('#date_picker_container').removeClass('d-none').hide().fadeIn(300);

            validDatesArray = getValidDates(rawStartDate, rawEndDate, allowedDayInts);

            if (flatpickrInstance) {
                flatpickrInstance.destroy();
            }

            flatpickrInstance = flatpickr("#booking_dates", {
                mode: "multiple",
                minDate: rawStartDate,
                maxDate: rawEndDate,
                dateFormat: "Y-m-d",
                enable: [
                    function(date) {
                        return allowedDayInts.includes(date.getDay());
                    }
                ],
                onChange: function(selectedDates, dateStr, instance) {
                    if(selectedDates.length > 0) {
                        checkEligibility(classId, userId);
                    } else {
                        disableSubmit();
                    }
                }
            });
        });

        $('#select_all_dates').on('click', function() {
            if(flatpickrInstance && validDatesArray.length > 0) {
                flatpickrInstance.setDate(validDatesArray, true);
                
                // Manually trigger the validation check since setDate won't fire onChange automatically 
                const classId = $('#class_select').val();
                if (classId) {
                    checkEligibility(classId, userId);
                }
            }
        });

        function checkEligibility(classId, userId) {
            $.ajax({
                url: "{{ route('check.class.eligibility') }}",
                type: "GET",
                data: { class_id: classId, user_id: userId },
                success: function (response) {
                    if (response.status) {
                        $('#eligibility_alert').addClass('d-none');
                        $('#submit_btn').prop('disabled', false);
                    } else {
                        showAlert(response.message);
                        disableSubmit();
                    }
                },
                error: function () {
                    showAlert('Failed to check package eligibility. Please try again.');
                    disableSubmit();
                }
            });
        }

        function resetFields() {
            $('#class_card_container').fadeOut(200, function() { $(this).addClass('d-none'); });
            $('#date_picker_container').fadeOut(200, function() { $(this).addClass('d-none'); });
            $('#eligibility_alert').addClass('d-none');
            disableSubmit();
        }

        function disableSubmit() {
            $('#submit_btn').prop('disabled', true);
        }

        function showAlert(msg) {
            $('#eligibility_msg').text(msg);
            $('#eligibility_alert').removeClass('d-none');
        }
    });
</script>