@include('master.header')

{{-- Summernote, Select2 & DataTables Buttons CSS --}}
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<style>
    /* Select2 Customization */
    .select2-container .select2-selection--single { height: 38px; border: 1px solid #ebedf2; padding: 5px; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { top: 5px; }

    /* View Modal Clean UI Customization */
    .view-modal-header { background-color: #f8f9fa; border-bottom: 2px solid #e9ecef; }
    .info-label { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d; margin-bottom: 3px; display: block; }
    .info-value { font-size: 1.05rem; font-weight: 500; color: #212529; }
    .detail-card { background: #fff; border: 1px solid #e9ecef; border-radius: 8px; padding: 15px; height: 100%; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    
    /* DataTable Buttons Customization */
    .dt-buttons .btn { margin-right: 5px; }
</style>

@include('master.sidebar')
@include('master.nav')

<div class="container">
<div class="page-inner">
    <div class="d-flex justify-content-between align-items-center mb-4">
        
        <!-- Page Title with Icon -->
        <h4 class="fw-bold mb-0 text-dark">
            <i class="fas fa-calendar-alt me-2 text-primary"></i> Manage Class Schedules
        </h4>

        <!-- Action Buttons Group -->
        @if(auth()->user()->hasPermission('schedule_register'))
            <div class="d-flex gap-2">
                <!-- Class Schedule List Button -->
                <a href="{{ route('class_schedules_list') }}" class="btn btn-outline-secondary shadow-sm">
                    <i class="fas fa-list me-1"></i> Schedule List
                </a>

                <!-- New Schedule Modal Button -->
                <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addClassScheduleModal">
                    <i class="fas fa-plus-circle me-1"></i> New Schedule
                </button>
            </div>
        @endif

    </div>
</div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="basic-datatables" style="min-width: 1200px;">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Image</th>
                                <th>Class Name</th>
                                <th>Category</th>
                                <th>Date (Start - End)</th>
                                <th>Days of Week</th>
                                <th>Time</th>
                                <th>Instructors</th>
                                <th>Capacity</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($schedules as $schedule)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @if($schedule->image_1)
                                            <img src="{{ asset($schedule->image_1) }}" alt="Class Image" class="rounded object-fit-cover" width="50" height="50">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted" style="width: 50px; height: 50px; font-size: 10px;">No Img</div>
                                        @endif
                                    </td>
                                    <td><span class="fw-bold d-block">{{ Str::limit($schedule->class_name, 20) }}</span></td>
                                    
                                    {{-- Category Column --}}
                                    <td><span class="badge bg-secondary">{{ $schedule->category->name ?? 'N/A' }}</span></td>
                                    <td>
                                        <span class="d-block text-primary" style="font-size: 0.9rem;">
                                            {{ \Carbon\Carbon::parse($schedule->start_date)->format('d M Y') }} <br>
                                            <span class="text-muted text-center d-block">to</span>
                                            {{ $schedule->end_date ? \Carbon\Carbon::parse($schedule->end_date)->format('d M Y') : 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $days = is_string($schedule->days) ? json_decode($schedule->days, true) : ($schedule->days ?? []);
                                        @endphp

                                        @foreach ($days as $day)
                                            <span class="badge bg-info text-white me-1">{{ $day }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <small class="text-dark fw-bold">
                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} - 
                                            {{ $schedule->end_time ? \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') : 'N/A' }}
                                        </small>
                                    </td>
                                    <td>
                                        @if(!empty($schedule->instructor) && is_array($schedule->instructor))
                                            @foreach ($schedule->instructor as $inst)
                                                <span class="badge bg-info text-white me-1">
                                                    {{ $inst['user']['name'] ?? 'N/A' }}
                                                </span>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>{{ $schedule->capacity }}</td>
                                    <td>
                                        <span class="badge {{ strtolower($schedule->status) == 'book' ? 'bg-primary' : (strtolower($schedule->status) == 'completed' ? 'bg-success' : 'bg-danger') }}">
                                            {{ ucfirst($schedule->status) }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-4">
                                            <button type="button" class="btn btn-link btn-info p-0" data-bs-toggle="modal" data-bs-target="#viewScheduleModal{{ $schedule->id }}" title="View Details">
                                                <i class="fa fa-eye fs-5"></i>
                                            </button>

                                            @if(auth()->user()->hasPermission('schedule_edit'))
                                                <button type="button" class="btn btn-link btn-primary p-0" data-bs-toggle="modal" data-bs-target="#editScheduleModal{{ $schedule->id }}" title="Edit">
                                                    <i class="fa fa-edit fs-5"></i>
                                                </button>
                                            @endif

                                            @if(auth()->user()->hasPermission('schedule_delete'))
                                                <form action="{{ route('class_schedules.destroy', $schedule->id) }}" method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-link btn-danger p-0" onclick="return confirm('Are you sure you want to delete this schedule?')" title="Delete">
                                                        <i class="fa fa-trash fs-5"></i>
                                                    </button>
                                                </form>
                                            @endif

                                             @if(auth()->user()->hasPermission('schedule_delete') && \Carbon\Carbon::parse($schedule->start_date . ' ' . ($schedule->start_time ?? '00:00:00'))->subHours(48)->isFuture())
                                             <form action="{{ route('class_schedules.cancel', $schedule->id) }}" method="POST" class="d-inline">
                                                @csrf @method('PUT')
                                                <button type="submit" class="btn btn-link btn-success p-0" title="Cancel">
                                                    Cancel
                                                </button>
                                            </form>
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

{{-- ========================================== --}}
{{-- VIEW MODALS --}}
{{-- ========================================== --}}
@foreach ($schedules as $schedule)
<div class="modal fade" id="viewScheduleModal{{ $schedule->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            
            <div class="modal-header view-modal-header py-3 px-4">
                <h5 class="fw-bold mb-0 text-dark">
                    {{ $schedule->class_name }} <span class="badge bg-secondary fs-6 ms-2">{{ $schedule->category->name ?? 'N/A' }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4 bg-light">
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="detail-card">
                            <span class="info-label"><i class="fas fa-user-tie me-1"></i> Instructor</span>
                            <span class="info-value">
                                @foreach ($schedule->instructor as $inst)
                                    <span class="badge bg-info text-white">{{ $inst['user']['name'] ?? 'N/A' }}</span>
                                @endforeach
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="detail-card">
                            <span class="info-label"><i class="fas fa-calendar-day me-1"></i> Date Range</span>
                            <span class="info-value d-block" style="font-size: 0.95rem;">
                                {{ \Carbon\Carbon::parse($schedule->start_date)->format('d M Y') }} <br>to<br> 
                                {{ $schedule->end_date ? \Carbon\Carbon::parse($schedule->end_date)->format('d M Y') : 'N/A' }}
                            </span>
                        </div>
                    </div>

                    
                    <div class="col-md-6 col-lg-3">
                        <div class="detail-card">
                            <span class="info-label"><i class="fas fa-clock me-1"></i> Time</span>
                            <span class="info-value text-nowrap">
                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} <br>-<br> 
                                {{ $schedule->end_time ? \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') : 'N/A' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="detail-card">
                            <span class="info-label"><i class="fas fa-users me-1"></i> Capacity</span>
                            <span class="info-value">{{ $schedule->capacity }} Persons</span>
                        </div>
                    </div>
                </div>

                <div class="detail-card mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <span class="fw-bold fs-6">Class Description</span>
                        <span class="badge {{ strtolower($schedule->status) == 'book' ? 'bg-primary' : (strtolower($schedule->status) == 'completed' ? 'bg-success' : 'bg-danger') }} px-3 py-2">
                            Status: {{ ucfirst($schedule->status) }}
                        </span>
                    </div>
                    <div class="text-muted" style="line-height: 1.6;">
                        {!! $schedule->description ?? '<i>No additional details provided for this class.</i>' !!}
                    </div>
                </div>

                @if($schedule->image_1 || $schedule->image_2)
                    <h6 class="fw-bold mb-3 ms-1 text-secondary">Attached Images</h6>
                    <div class="row g-3">
                        @if($schedule->image_1)
                            <div class="col-sm-6">
                                <div class="card border-0 shadow-sm">
                                    <img src="{{ asset($schedule->image_1) }}" class="card-img-top object-fit-cover" style="height: 200px;">
                                    <div class="card-body p-2 text-center">
                                        <a href="{{ asset($schedule->image_1) }}" download="Image_1_{{ $schedule->class_name }}" class="btn btn-outline-success btn-sm w-100">
                                            <i class="fa fa-download me-1"></i> Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($schedule->image_2)
                            <div class="col-sm-6">
                                <div class="card border-0 shadow-sm">
                                    <img src="{{ asset($schedule->image_2) }}" class="card-img-top object-fit-cover" style="height: 200px;">
                                    <div class="card-body p-2 text-center">
                                        <a href="{{ asset($schedule->image_2) }}" download="Image_2_{{ $schedule->class_name }}" class="btn btn-outline-success btn-sm w-100">
                                            <i class="fa fa-download me-1"></i> Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

            </div>
            
            <div class="modal-footer bg-white py-2">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- ========================================== --}}
{{-- EDIT MODALS --}}
{{-- ========================================== --}}
@if(auth()->user()->hasPermission('schedule_edit'))
    @foreach ($schedules as $schedule)
        <div class="modal fade" id="editScheduleModal{{ $schedule->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <form action="{{ route('class_schedules.update', $schedule->id) }}" method="POST" enctype="multipart/form-data" class="modal-content">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="fw-bold">Edit Class Schedule</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-start">
                        <div class="row g-3">
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold">Class Name <span class="text-danger">*</span></label>
                                <input type="text" name="class_name" placeholder="Enter Class Name" class="form-control" value="{{ $schedule->class_name }}" required>
                            </div>

                            {{-- Category Dropdown --}}
                            <div class="col-md-4 mb-3">
                                <label class="fw-bold d-block">Category <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select select2-dropdown" style="width: 100%;" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $schedule->category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="fw-bold d-block">Instructor <span class="text-danger">*</span></label>
                                <select name="instructor_ids[]" multiple class="form-select select2-dropdown" style="width: 100%;" required>
                                    @foreach($instructors as $instructor)
                                        <option value="{{ $instructor->id }}" {{ in_array($instructor->id, $schedule->instructor_ids) ? 'selected' : '' }}>
                                            {{ $instructor->user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="fw-bold">Start Date </label>
                                <input type="date" name="start_date" class="form-control" value="{{ $schedule->start_date }}" >
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="fw-bold">End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{ $schedule->end_date }}" >
                            </div>

                            <div class="col-md-6 mb-3">
                                @php
                                    $daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                                    
                                    // Safely decode JSON string to array if it's not casted on the model
                                    $selectedDays = is_string($schedule->days) 
                                        ? json_decode($schedule->days, true) 
                                        : ($schedule->days ?? []);
                                @endphp

                                <label class="fw-bold d-block">Days of Week <span class="text-danger">*</span></label>
                                <select name="days[]" multiple class="form-select select2-dropdown" style="width: 100%;" required>
                                    @foreach($daysOfWeek as $day)
                                        <option value="{{ $day }}" 
                                            {{ in_array($day, (array)$selectedDays) ? 'selected' : '' }}>
                                            {{ $day }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Time (Start - End)</label>
                                <div class="d-flex gap-2">
                                    <input type="time" name="start_time" class="form-control" value="{{ $schedule->start_time }}" required>
                                    <input type="time" name="end_time" class="form-control" value="{{ $schedule->end_time }}" >
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Capacity</label>
                                <input type="number" name="capacity" class="form-control" value="{{ $schedule->capacity }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="book" {{ strtolower($schedule->status) == 'book' ? 'selected' : '' }}>Book</option>
                                    <option value="completed" {{ strtolower($schedule->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="fw-bold">Description</label>
                                <textarea name="description" class="form-control summernote">{{ $schedule->description }}</textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Image 1</label>
                                <input type="file" name="image_1" id="file_input_1_{{ $schedule->id }}" class="form-control" accept="image/*" onchange="previewNewImage(this, 'edit_preview_1_{{ $schedule->id }}')">
                                <input type="hidden" name="remove_image_1" id="remove_image_1_{{ $schedule->id }}" value="0">
                                
                                <div class="mt-2 position-relative d-inline-block" id="container_image_1_{{ $schedule->id }}" style="{{ !$schedule->image_1 ? 'display:none;' : '' }}">
                                    <img src="{{ $schedule->image_1 ? asset($schedule->image_1) : '' }}" id="edit_preview_1_{{ $schedule->id }}" class="img-thumbnail object-fit-cover" width="120" height="120">
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" onclick="removeExistingImage('1', '{{ $schedule->id }}')" title="Remove Image">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Image 2</label>
                                <input type="file" name="image_2" id="file_input_2_{{ $schedule->id }}" class="form-control" accept="image/*" onchange="previewNewImage(this, 'edit_preview_2_{{ $schedule->id }}')">
                                <input type="hidden" name="remove_image_2" id="remove_image_2_{{ $schedule->id }}" value="0">
                                
                                <div class="mt-2 position-relative d-inline-block" id="container_image_2_{{ $schedule->id }}" style="{{ !$schedule->image_2 ? 'display:none;' : '' }}">
                                    <img src="{{ $schedule->image_2 ? asset($schedule->image_2) : '' }}" id="edit_preview_2_{{ $schedule->id }}" class="img-thumbnail object-fit-cover" width="120" height="120">
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" onclick="removeExistingImage('2', '{{ $schedule->id }}')" title="Remove Image">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endif

{{-- ========================================== --}}
{{-- ADD MODAL --}}
{{-- ========================================== --}}
@if(auth()->user()->hasPermission('schedule_register'))
<div class="modal fade" id="addClassScheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form action="{{ route('class_schedules.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="fw-bold">Register New Class Schedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold">Class Name <span class="text-danger">*</span></label>
                        <input type="text" value="{{ old('class_name') }}" placeholder="Enter Class Name" name="class_name" class="form-control" required>
                    </div>

                    {{-- Category Dropdown --}}
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold d-block">Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select select2-dropdown" style="width: 100%;" required>
                            <option value="" selected disabled>Select Category...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="fw-bold d-block">Instructor <span class="text-danger">*</span></label>
                        <select name="instructor_ids[]" multiple class="form-select select2-dropdown" style="width: 100%;" required>
                            @foreach($instructors as $instructor)
                                <option value="{{ $instructor->id }}" {{ in_array($instructor->id, old('instructor_ids', [])) ? 'selected' : '' }}>
                                    {{ $instructor->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="fw-bold">Start Date </label>
                        <input type="date" value="{{ old('start_date') }}" name="start_date" class="form-control" required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="fw-bold">End Date </label>
                        <input type="date" value="{{ old('end_date') }}" name="end_date" class="form-control" >
                    </div>

                       <div class="col-md-4 mb-3">
                        @php
                        $daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                        @endphp
                        <label class="fw-bold d-block">Days of Week <span class="text-danger">*</span></label>
                        <select name="days[]" multiple class="form-select select2-dropdown" style="width: 100%;" required>
                            @foreach($daysOfWeek as $day)
                                <option value="{{ $day }}" {{ in_array($day, old('days', [])) ? 'selected' : '' }}>
                                    {{ $day }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Time (Start - End)</label>
                        <div class="d-flex gap-2">
                            <input type="time" value="{{ old('start_time') }}" name="start_time" class="form-control" required>
                            <input type="time" value="{{ old('end_time') }}" name="end_time" class="form-control" >
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Capacity</label>
                        <input type="number" value="{{ old('capacity') }}" name="capacity" class="form-control" value="15" required min="1">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="book" {{ old('status', 'book') == 'book' ? 'selected' : '' }}>Book</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="fw-bold">Description</label>
                        <textarea name="description" class="form-control summernote">{{ old('description') }}</textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Image 1 (Optional)</label>
                        <input type="file" name="image_1" class="form-control" accept="image/*" onchange="previewNewImage(this, 'add_preview_1')">
                        <div class="mt-2" style="display:none;" id="add_preview_container_1">
                            <img src="" id="add_preview_1" class="img-thumbnail object-fit-cover" width="120" height="120">
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Image 2 (Optional)</label>
                        <input type="file" name="image_2" class="form-control" accept="image/*" onchange="previewNewImage(this, 'add_preview_2')">
                        <div class="mt-2" style="display:none;" id="add_preview_container_2">
                            <img src="" id="add_preview_2" class="img-thumbnail object-fit-cover" width="120" height="120">
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Schedule</button>
            </div>
        </form>
    </div>
</div>
@endif

@include('master.footer')

{{-- Plugins Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- DataTables Buttons & Excel Export Libraries -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- DataTables PDF Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<!-- DataTables HTML5 buttons -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<script>
    $(document).ready(function() {
        if($('#basic-datatables').length) {
            $('#basic-datatables').DataTable({
                "order": [[ 0, "desc" ]],
                "dom": 'Bfrtip',
                "buttons": [
                    {
                        // Collection ကိုသုံးပြီး Dropdown ပုံစံပြောင်းခြင်း
                        extend: 'collection',
                        text: '<i class="fas fa-file-export me-1"></i> Export Data',
                        className: 'btn btn-success text-white btn-sm mb-3',
                        buttons: [
                            {
                                extend: 'excelHtml5',
                                text: '<i class="fas fa-file-excel text-success me-2"></i> Export to Excel',
                                exportOptions: {
                                    columns: [0, 2, 3, 4, 5, 6, 7, 8, 9] 
                                }
                            },
                            {
                                extend: 'csvHtml5',
                                text: '<i class="fas fa-file-csv text-info me-2"></i> Export to CSV',
                                exportOptions: {
                                    columns: [0, 2, 3, 4, 5, 6, 7, 8, 9] 
                                }
                            },
                            {
                                extend: 'pdfHtml5',
                                text: '<i class="fas fa-file-pdf text-danger me-2"></i> Export to PDF',
                                orientation: 'landscape',
                                pageSize: 'A4',
                                exportOptions: {
                                    columns: [0, 2, 3, 4, 5, 6, 7, 8, 9] 
                                }
                            }
                        ]
                    }
                ]
            });
        }

        $('.summernote').summernote({
            placeholder: 'Write class details...',
            tabsize: 2,
            height: 150,
            dialogsInBody: true, 
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview']]
            ]
        });

        // Initialize Select2 for multiple dropdowns with closeOnSelect fix
        $('.modal').on('shown.bs.modal', function () {
            $(this).find('.select2-dropdown').each(function() {
                // multiple attribute ပါ/မပါ စစ်ဆေးပါမည်
                let isMultiple = $(this).prop('multiple'); 
                
                $(this).select2({
                    dropdownParent: $(this).closest('.modal'),
                    placeholder: "Search & Select...",
                    allowClear: true,
                    // Multiple ဖြစ်ရင် Dropdown ပြန်မပိတ်ပါ (ဆက်တိုက်ရွေးနိုင်ရန်)
                    closeOnSelect: !isMultiple 
                });
            });
        });
    });

    // Live Image Preview
    function previewNewImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
                let addContainer = document.getElementById(previewId.replace('add_preview', 'add_preview_container'));
                if(addContainer) addContainer.style.display = 'inline-block';
                let editContainer = document.getElementById(previewId.replace('edit_preview', 'container_image'));
                if(editContainer) editContainer.style.setProperty('display', 'inline-block', 'important');
                if(previewId.includes('edit_preview')) {
                    let parts = previewId.split('_'); 
                    let hiddenInput = document.getElementById('remove_image_' + parts[2] + '_' + parts[3]);
                    if(hiddenInput) hiddenInput.value = '0';
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Remove Image UI
    window.removeExistingImage = function(imageNum, scheduleId) {
        if(confirm("Are you sure you want to remove this image?")) {
            let hiddenInput = document.getElementById('remove_image_' + imageNum + '_' + scheduleId);
            if(hiddenInput) hiddenInput.value = '1';
            let previewContainer = document.getElementById('container_image_' + imageNum + '_' + scheduleId);
            if(previewContainer) previewContainer.style.setProperty('display', 'none', 'important');
            let fileInput = document.getElementById('file_input_' + imageNum + '_' + scheduleId);
            if(fileInput) fileInput.value = '';
        }
    };
</script>