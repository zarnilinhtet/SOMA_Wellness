@include('master.header')
@include('master.sidebar')
@include('master.nav')

<!-- Include Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<div class="container py-5">
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-primary text-white py-3 mb-2 d-flex justify-content-between align-items-center" id="form_header">
            <h5 class="fw-bold mb-0" id="header_title">Join Class For {{ $user->name ?? 'User' }}</h5>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mx-4 mt-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show mx-4 mt-3" role="alert">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Dynamic Eligibility Alert --}}
        <div id="eligibility_alert" class="alert alert-danger alert-dismissible fade show d-none mx-4 mt-3" role="alert">
            <span id="eligibility_msg"></span>
        </div>

        <div class="card-body p-4">
            <form action="{{ route('join.class-for-user-submit') }}" id="join_class_form" method="POST">
                @csrf

                <input type="hidden" name="user_id" id="user_id" value="{{ $user->id }}">

                <div class="row g-3">

                    {{-- 1. SEARCHABLE CLASS SELECTION --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Select Class <span class="text-danger">*</span></label>
                        <select name="class_id" id="class_select" class="form-select @error('class_id') is-invalid @enderror" required>
                            <option value="" disabled {{ old('class_id') ? '' : 'selected' }}>Search or Choose Class...</option>
                            
                            @foreach ($classes as $class)
                                @php
                                    // Completed ဖြစ်နေသော အတန်းများကို ကျော်သွားမည် (Dropdown တွင်မပြပါ)
                                    if(strtolower($class->status) === 'completed') {
                                        continue;
                                    }

                                    // Fetch current user booking status for this class
                                    $userBooking = $bookings->where('selected_class_id', $class->id)->first();
                                    $bookingStatus = $userBooking->status ?? null;

                                    // Compute display status
                                    if (strtolower($class->status) === 'ongoing' || strtolower($class->status) === 'active') {
                                        if ($bookingStatus == 'confirmed') {
                                            $computedStatus = 'Joined';
                                        } elseif ($bookingStatus == 'waitlisted') {
                                            $computedStatus = 'Waitlisted';
                                        } elseif ($bookingStatus == 'cancelled') {
                                            $computedStatus = 'Cancelled';
                                        } else {
                                            $totalCapacity = $class->capacity ?? 0;
                                            $bookedSlots = $bookingsAll->where('selected_class_id', $class->id)->where('status', 'confirmed')->count();
                                            $remainingSlots = max(0, $totalCapacity - $bookedSlots);
                                            $computedStatus = ($remainingSlots <= 0) ? 'Full' : 'Open';
                                        }
                                    } else {
                                        $computedStatus = ucfirst($class->status); // e.g. 'Cancelled'
                                    }

                                    // Format Data for Dropdown & Preview
                                    $formattedDate = \Carbon\Carbon::parse($class->start_date ?? now())->format('d M Y');
                                    $startTime = $class->start_time ? \Carbon\Carbon::parse($class->start_time)->format('h:i A') : '--';
                                    $endTime = $class->end_time ? \Carbon\Carbon::parse($class->end_time)->format('h:i A') : '--';
                                    $formattedTime = "{$startTime} - {$endTime}";
                                    $formattedDays = is_array($class->days) ? implode(', ', $class->days) : ($class->days ?? 'N/A');
                                @endphp

                                <option value="{{ $class->id }}" 
                                        data-name="{{ $class->class_name ?? $class->name }}"
                                        data-category="{{ $class->category->name ?? 'N/A' }}"
                                        data-instructor="{{ collect($class->instructor)->pluck('user.name')->filter()->implode(', ') ?: ($class->instructor_code ?? 'CC') }}"
                                        data-start-date="{{ $formattedDate }}"
                                        data-end-date="{{ \Carbon\Carbon::parse($class->end_date ?? now())->format('d M Y') }}"
                                        data-time="{{ $formattedTime }}"
                                        data-days="{{ is_array($class->days) ? implode(' ', $class->days) : ($class->days ?? '') }}"
                                        data-booked="{{ $bookingsAll->where('selected_class_id', $class->id)->where('status', 'confirmed')->count() }}"
                                        data-capacity="{{ $class->capacity ?? 0 }}"
                                        data-status="{{ $computedStatus }}"
                                        {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                    
                                    {{ $class->class_name ?? $class->name }} ({{ $class->category->name ?? 'Category' }}) | 📅 Date: {{ $formattedDate }} | ⏰ Time: {{ $formattedTime }} | 🔄 Days: {{ $formattedDays }} — [{{ $computedStatus }}]
                                    
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. READ-ONLY PREVIEW CARD --}}
                    <div class="col-md-12 mb-3 d-none" id="class_card_container">
                        <div class="card border rounded-3 p-3 bg-light shadow-sm">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="fw-bold text-primary mb-1" id="card_title">Class Name</h5>
                                    <span class="badge bg-primary text-uppercase me-1" id="card_category">CATEGORY</span>
                                    <span class="badge bg-secondary" id="card_instructor"><i class="bi bi-person-fill"></i> CC</span>
                                </div>
                                <span class="badge bg-info text-white text-uppercase px-3 py-2" id="card_badge">OPEN</span>
                            </div>

                            <div class="text-muted small mb-2">
                                <i class="bi bi-calendar3 me-1"></i> <span id="card_dates">--</span>
                                <i class="bi bi-clock me-1 ms-3"></i> <span id="card_time">--</span>
                            </div>

                            <div class="mb-3" id="card_days"></div>

                            <div class="d-flex justify-content-between border-top pt-2 text-secondary small">
                                <div>
                                    <span class="d-block text-uppercase fw-bold text-muted">Studio Occupancy</span>
                                    <span class="fw-bold fs-6 text-dark" id="card_slots">0 / 0 Slots</span>
                                </div>
                                <div class="text-end">
                                    <span class="d-block text-uppercase fw-bold text-muted">Availability</span>
                                    <span class="fw-bold text-dark" id="card_availability">Open</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. READ-ONLY INPUT FIELDS --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Class Category</label>
                        <input type="text" id="class_category_input" class="form-control bg-light" placeholder="Selected Category" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Class Capacity</label>
                        <input type="text" id="class_capacity_input" class="form-control bg-light" placeholder="Selected Capacity" readonly>
                    </div>

                </div>

                <div class="mt-4 text-end">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" id="submit_btn" class="btn btn-primary px-4" disabled>
                        Join Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include jQuery & Select2 JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        // 1. Initialize Searchable Select2
        $('#class_select').select2({
            theme: 'bootstrap-5',
            placeholder: 'Search or Choose Class...',
            allowClear: true,
            width: '100%' // Ensure it takes full width with long text
        });

        const userId = $('#user_id').val();

        // 2. Handle Class Selection
        $('#class_select').on('change', function () {
            const selectedOption = $(this).find(':selected');
            const classId = $(this).val();

            if (!classId) {
                resetFields();
                return;
            }

            // Read Data Attributes
            const name = selectedOption.data('name');
            const category = selectedOption.data('category');
            const instructor = selectedOption.data('instructor');
            const startDate = selectedOption.data('start-date');
            const endDate = selectedOption.data('end-date');
            const time = selectedOption.data('time');
            const days = selectedOption.data('days');
            const booked = selectedOption.data('booked');
            const capacity = selectedOption.data('capacity');
            const status = selectedOption.data('status'); // Joined, Waitlisted, Full, Open, Completed, Cancelled

            // Populate Read-Only Inputs
            $('#class_category_input').val(category);
            $('#class_capacity_input').val(capacity + ' Slots');

            // Populate Preview Card
            $('#card_title').text(name);
            $('#card_category').text(category);
            $('#card_instructor').html('<i class="bi bi-person-fill"></i> ' + instructor);
            $('#card_dates').text(`${startDate} - ${endDate}`);
            $('#card_time').text(time);
            $('#card_slots').text(`${booked} / ${capacity} Slots`);
            $('#card_availability').text(status);

            // Dynamic Styling for Preview Card Badge & Submit Button based on status
            const $cardBadge = $('#card_badge');
            const $submitBtn = $('#submit_btn');
            
            $cardBadge.removeClass('bg-success bg-info bg-warning bg-danger bg-secondary text-white text-dark').text(status.toUpperCase());

            switch (status) {
                case 'Joined':
                    $cardBadge.addClass('bg-success text-white');
                    break;
                case 'Open':
                    $cardBadge.addClass('bg-info text-white');
                    $submitBtn.removeClass('btn-warning text-dark').addClass('btn-primary').text('Join Class');
                    break;
                case 'Full':
                    $cardBadge.addClass('bg-warning text-dark').text('FULL (WAITLIST OPEN)');
                    $submitBtn.removeClass('btn-primary').addClass('btn-warning text-dark').text('Join Waitlist');
                    break;
                case 'Waitlisted':
                    $cardBadge.addClass('bg-warning text-dark');
                    break;
                case 'Cancelled':
                    $cardBadge.addClass('bg-danger text-white');
                    break;
                default: // Completed or other status
                    $cardBadge.addClass('bg-secondary text-white');
                    break;
            }

            // Render Days Badges
            let daysHtml = '';
            if (typeof days === 'string') {
                days.split(' ').forEach(day => {
                    if (day) daysHtml += `<span class="badge bg-dark me-1">${day}</span>`;
                });
            }
            $('#card_days').html(daysHtml);

            // Show Preview Card
            $('#class_card_container').removeClass('d-none');

            // 3. Prevent submission if already joined, on waitlist, completed, or cancelled
            if (status === 'Joined') {
                showAlert(`User has already joined this class.`);
                disableSubmit();
                return;
            } else if (status === 'Waitlisted') {
                showAlert(`User is already on the waitlist for this class waiting for admin approval.`);
                disableSubmit();
                return;
            } else if (status === 'Completed' || status === 'Cancelled') {
                showAlert(`This class is ${status.toLowerCase()} and cannot be joined.`);
                disableSubmit();
                return;
            }

            // 4. Check Active Package Eligibility via AJAX for Open and Full/Waitlist classes
            $.ajax({
                url: "{{ route('check.class.eligibility') }}",
                type: "GET",
                data: {
                    class_id: classId,
                    user_id: userId
                },
                success: function (response) {
                    if (response.status) {
                        $('#eligibility_alert').addClass('d-none');

                        // Enable Join / Waitlist Class Button & update action URL
                        let joinRoute = "{{ route('join.class-for-user-submit') }}";
                        $('#join_class_form').attr('action', joinRoute);
                        $('#submit_btn').prop('disabled', false);
                    } else {
                        showAlert(response.message);
                        disableSubmit();
                    }
                },
                error: function () {
                    showAlert('Failed to check class package eligibility. Please try again.');
                    disableSubmit();
                }
            });
        });

        function resetFields() {
            $('#class_card_container').addClass('d-none');
            $('#class_category_input').val('');
            $('#class_capacity_input').val('');
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

        // Trigger on page load if a class was pre-selected
        if ($('#class_select').val()) {
            $('#class_select').trigger('change');
        }
    });
</script>