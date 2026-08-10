<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor & Class Attendance Record Calendar</title>
    <style>
        .fc .fc-button-primary {
            background-color: #2c3e50 !important;
            border-color: #2c3e50 !important;
            color: #ffffff !important;
        }

        .fc .fc-button-primary:hover {
            background-color: #34495e !important;
            border-color: #34495e !important;
        }

        .fc .fc-button-primary:not(:disabled).fc-button-active,
        .fc .fc-button-primary:not(:disabled):active {
            background-color: #1a252f !important;
            border-color: #1a252f !important;
        }

        .fc-list-event-title {
            color: #2c3e50 !important;
            font-weight: bold;
        }

        .fc-list-event-time {
            color: #333333 !important;
            font-weight: 600 !important;
        }

        .fc .fc-col-header-cell-cushion,
        .fc .fc-daygrid-day-number {
            color: #000000 !important;
            text-decoration: none !important;
            font-weight: 600 !important;
        }

        .fc .fc-list-event-dot {
            vertical-align: middle;
            display: inline-block;
            margin-top: 3px;
        }

        .fc .fc-list-event-graphic {
            vertical-align: middle;
        }

        .fc .fc-list-day-cushion,
        .fc .fc-list-day-text,
        .fc .fc-list-day-side-text {
            color: #000000 !important;
            text-decoration: none !important;
        }

        .fc a:hover {
            text-decoration: none !important;
        }

        .fc-daygrid-day:hover {
            background-color: #f8f9fa !important;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
        }

        .fc-daygrid-day.disabled-class-day {
            background-color: #e9ecef !important;
            opacity: 0.6;
            cursor: not-allowed;
        }

        .fc-daygrid-day.disabled-class-day .fc-daygrid-day-frame {
            pointer-events: none;
        }

        .modal-content {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            border-bottom: 1px solid #edf2f7;
            background-color: #f8fafc;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }

        .avatar-placeholder {
            width: 40px;
            height: 40px;
            background-color: #e2e8f0;
            color: #4a5568;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: bold;
            font-size: 14px;
        }

        #calendar {
            height: 600px !important;
            min-height: 600px;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css' rel='stylesheet' />
</head>

<body>

    <div class="m-4">
        <h3 class="text-center mb-4">Instructor & Class Attendance Record Calendar</h3>

        <div class="row mb-4 d-flex justify-content-between align-items-center">
            <a href="attendances" style="cursor: pointer; display: inline-block; font-size:18px"
                class="col-md-2 align-self-center">
                <i class="fas fa-chevron-left"></i> Back
            </a>

            <div class="d-flex gap-4 col-md-8 justify-content-end">
                <div class="form-group col-md-5">
                    <label class="fw-bold small text-muted mb-1">Filter by Class</label>
                    <select class="form-control form-select" id="classFilter" name="class_id">
                        <option value="">All Classes</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}"
                                data-start="{{ \Carbon\Carbon::parse($class->start_date)->format('Y-m-d') }}"
                                data-end="{{ \Carbon\Carbon::parse($class->end_date)->format('Y-m-d') }}">
                                {{ $class->class_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @role('Instructor')
                @php
                    $instructor = App\Models\Instructor::where('instructor_id',auth()->user()->id)->first();
                @endphp
                    <input type="hidden" id="instructorFilter" name="instructor_id" value="{{ $instructor->id ?? '' }}">
                @else
                    <div class="form-group col-md-5">
                        <label class="fw-bold small text-muted mb-1">Filter by Instructor</label>
                        <select class="form-control form-select" id="instructorFilter" name="instructor_id">
                            <option value="">All Instructors</option>
                            @foreach ($instructors as $instructor)
                                <option value="{{ $instructor->id }}">{{ $instructor->user->name ?? 'Unknown' }}</option>
                            @endforeach
                        </select>
                    </div>
                @endrole
            </div>
        </div>

        <div id="calendar"></div>
    </div>

    <div class="modal fade" id="attendanceDetailModal" tabindex="-1" aria-labelledby="attendanceDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="modal-title fw-bold text-dark" id="attendanceDetailModalLabel">Attendance Details</h5>
                        <small class="text-muted" id="modalSubTitle">Class: All | Instructor: All | Date: --</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 450px; overflow-y: auto;">

                    <div id="modalLoader" class="text-center my-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>

                    <div id="modalTableContainer" class="d-none">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-secondary small text-uppercase">
                                <tr>
                                    <th>Instructor</th>
                                    <th>Class</th>
                                    <th>Attendance Date</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Recorded At</th>
                                    <!-- <th class="text-center">Payment Status</th>
                                    <th>Actions</th> -->
                                </tr>
                            </thead>
                            <tbody id="attendanceModalBody">
                            </tbody>
                        </table>
                    </div>

                    <div id="modalEmptyState" class="text-center my-5 d-none">
                        <i class="fas fa-calendar-times text-muted fa-3x mb-3"></i>
                        <p class="text-secondary fw-semibold">No attendance logs found matching these criteria for this date.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('master.footer')

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');
        var classFilter = document.getElementById('classFilter');
        var instructorFilter = document.getElementById('instructorFilter');
        var detailModal = new bootstrap.Modal(document.getElementById('attendanceDetailModal'));

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
            },
            buttonText: {
                today: 'today',
                month: 'month',
                week: 'week',
                day: 'day',
                list: 'list'
            },
            eventTimeFormat: {
                hour: 'numeric',
                minute: '2-digit',
                meridiem: 'short'
            },
            displayEventTime: true,
            datesSet: function (info) {
                applyClassDateRestrictions();
            },
            dateClick: function (info) {
                if (info.dayEl.classList.contains('disabled-class-day')) {
                    return;
                }

                let selectedDate = info.dateStr;
                let today = new Date();
                today.setHours(0, 0, 0, 0);
                let clickedDateObj = new Date(selectedDate + 'T00:00:00');

                if (clickedDateObj > today) {
                    alert("Action Denied: You cannot view or record attendance for tomorrow/future dates.");
                    return;
                }

                let classId = classFilter.value;
                let instructorId = instructorFilter ? instructorFilter.value : ''; 
                let classText = classFilter.selectedIndex > 0 ? classFilter.options[classFilter.selectedIndex].text : 'All';
                
                let instructorText = 'All';
                if (instructorFilter && instructorFilter.tagName === 'SELECT' && instructorFilter.selectedIndex > 0) {
                    instructorText = instructorFilter.options[instructorFilter.selectedIndex].text;
                } else if (instructorFilter && instructorFilter.tagName === 'INPUT') {
                    instructorText = 'Auth Instructor';
                }

                document.getElementById('modalSubTitle').innerText = `Class: ${classText} | Instructor: ${instructorText} | Date: ${selectedDate}`;
                detailModal.show();

                document.getElementById('modalLoader').classList.remove('d-none');
                document.getElementById('modalTableContainer').classList.add('d-none');
                document.getElementById('modalEmptyState').classList.add('d-none');

                let detailUrl = `{{ url("/attendance/day-details") }}?date=${selectedDate}&class_id=${classId}&instructor_id=${instructorId}`;

                fetch(detailUrl)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('modalLoader').classList.add('d-none');
                        let tbody = document.getElementById('attendanceModalBody');
                        tbody.innerHTML = '';

                       if (data && data.length > 0) {
                            data.forEach(record => {
                                let initials = (record.instructor_name || 'A').split(" ").map((n) => n[0]).join("").substring(0, 2).toUpperCase();
                                let badgeClass = record.is_paid === 'Paid' ? 'info' : 'warning';
                                
                                let actionFormHtml = '';
                                if ((record.is_paid === 0 || record.is_paid === 'Unpaid') && !document.body.dataset.isInstructor) {
                                    let updateUrl = `{{ url('/instructors') }}/${record.instructor_id}/earnings`;
                                    let csrfToken = '{{ csrf_token() }}';
                                    
                                    actionFormHtml = `
                                        <form action="${updateUrl}" method="POST">
                                            <input type="hidden" name="_token" value="${csrfToken}">
                                            <input type="hidden" name="_method" value="PUT">
                                            <input type="hidden" name="status" value="paid">
                                            <input type="hidden" name="attendance_id" value="${record.attendance_id}">
                                            <button type="submit" class="btn btn-link p-0 border-0 bg-transparent"
                                                onclick="return confirm('Are you sure you want to mark this attendance as Paid?')"
                                                title="Mark as Paid">
                                                <span class="badge bg-info px-3 py-2 rounded-pill">Mark as Paid</span>
                                            </button>
                                        </form>
                                    `;
                                }

                                let row = `
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-placeholder">${initials}</div>
                                            <div>
                                                <div class="fw-bold text-dark">${record.instructor_name || 'N/A'}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-semibold text-secondary">${record.class_name || 'N/A'}</td>
                                    <td class="fw-semibold text-secondary">${record.attendance_date}</td>
                                    <td class="text-center">
                                        <span class="badge bg-success px-3 py-2 rounded-pill">Attended</span>
                                    </td>
                                    <td class="text-center">${record.recorded_at || 'N/A'}</td>
                                </tr>
                                `;
                                    // <td class="text-center">
                                    //     <span class="badge bg-${badgeClass} px-3 py-2 rounded-pill">
                                    //         ${record.is_paid}
                                    //     </span>
                                    // </td>
                                    // <td class="text-center">
                                    //     ${actionFormHtml}
                                    // </td>
                                tbody.insertAdjacentHTML('beforeend', row);
                            });
                            document.getElementById('modalTableContainer').classList.remove('d-none');
                        } else {
                            document.getElementById('modalEmptyState').classList.remove('d-none');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching modal details:', error);
                        document.getElementById('modalLoader').classList.add('d-none');
                        document.getElementById('modalEmptyState').classList.remove('d-none');
                    });
            },
            events: function (info, successCallback, failureCallback) {
                let baseUrl = '{{ url("/attendance/data") }}';
                let classId = classFilter.value;
                let instructorId = instructorFilter ? instructorFilter.value : '';
                let fetchUrl = `${baseUrl}?start=${info.startStr}&end=${info.endStr}&class_id=${classId}&instructor_id=${instructorId}`;

                fetch(fetchUrl)
                    .then(response => response.json())
                    .then(data => successCallback(data))
                    .catch(error => {
                        console.error('Error fetching calendar data:', error);
                        failureCallback(error);
                    });
            },
            eventDisplay: 'block'
        });

        calendar.render();

        classFilter.addEventListener('change', function () {
            calendar.refetchEvents();
            applyClassDateRestrictions();
        });

        if (instructorFilter && instructorFilter.tagName === 'SELECT') {
            instructorFilter.addEventListener('change', function () {
                calendar.refetchEvents();
            });
        }

        function applyClassDateRestrictions() {
            document.querySelectorAll('.fc-daygrid-day').forEach(cell => {
                cell.classList.remove('disabled-class-day');
            });

            let selectedOption = classFilter.options[classFilter.selectedIndex];
            let classStart = selectedOption.getAttribute('data-start');
            let classEnd = selectedOption.getAttribute('data-end');

            if (classStart && classEnd) {
                document.querySelectorAll('.fc-daygrid-day').forEach(cell => {
                    let cellDate = cell.getAttribute('data-date');
                    if (cellDate && (cellDate < classStart || cellDate > classEnd)) {
                        cell.classList.add('disabled-class-day');
                    }
                });
            }
        }
    });
    </script>
</body>

</html>