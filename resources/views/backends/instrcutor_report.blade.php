@include('master.header')

{{-- Summernote, Select2, DataTables & FontAwesome CSS --}}
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<style>
    /* Select2 Customization */
    .select2-container .select2-selection--single {
        height: 38px;
        border: 1px solid #ebedf2;
        padding: 5px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        top: 5px;
    }

    /* View Modal Clean UI Customization */
    .view-modal-header {
        background-color: #f8f9fa;
        border-bottom: 2px solid #e9ecef;
    }

    .info-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        margin-bottom: 3px;
        display: block;
    }

    .info-value {
        font-size: 1.05rem;
        font-weight: 500;
        color: #212529;
    }

    .detail-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        height: 100%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    }
</style>

@include('master.sidebar')
@include('master.nav')

<div class="container">
    <div class="page-inner">
        <div class="align-items-center mb-4">
            <h4 class="fw-bold mb-4">Reports</h4>
            <div class="d-flex gap-3">
                <div class="p-3 shadow-sm w-100">
                    <h6 class="text-muted small text-uppercase fw-bold mb-1">Total Classes</h6>
                    <h2 class="fw-bolder text-primary mb-0">{{ $totalClasses }}</h2>
                </div>
                <div class="p-3 shadow-sm w-100">
                    <h6 class="text-muted small text-uppercase fw-bold mb-1">Total Fee</h6>
                    <h2 class="fw-bolder text-primary mb-0">{{ $grandTotalFees }}</h2>
                </div>
            </div>

            @if(auth()->user()->hasPermission('schedule_register'))
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClassScheduleModal">
                    <i class="fas fa-plus"></i> New Schedule
                </button>
            @endif
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
                    <table class="table table-hover align-middle" id="basic-datatables"
                        style="width: 100%; min-width: 1200px;">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Class Name</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Clients</th>
                                <th>Total Fees</th>
                                <!-- <th>Instructor</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reports as $class)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="fw-bold d-block">{{ $class['class_name'] }}</span></td>
                                    <td class="text-center">
                                        <span class="d-block text-primary" style="font-size: 0.9rem;">
                                            {{ \Carbon\Carbon::parse($class['start_date'])->format('d M Y') }} <br>
                                            <span class="text-muted text-center d-block">to</span>
                                            {{ \Carbon\Carbon::parse($class['end_date'])->format('d M Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-dark fw-bold">
                                            {{ $class['time'] }}
                                        </small>
                                    </td>
                                    <td>{{ $class['total_clients'] }}</td>
                                    <td>{{ $class['total_fee'] }}</td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('master.footer')

{{-- JS Dependencies --}}
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        if ($('#basic-datatables').length) {
            $('#basic-datatables').DataTable({
                "order": [[0, "desc"]],
                "paging": true,
                "ordering": true,
                "info": true
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

        // Initialize Select2 for multiple dropdowns
        $('.modal').on('shown.bs.modal', function () {
            $(this).find('.select2-dropdown').select2({
                dropdownParent: $(this),
                placeholder: "Search & Select...",
                allowClear: true
            });
        });
    });

    // Live Image Preview
    function previewNewImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById(previewId).src = e.target.result;
                let addContainer = document.getElementById(previewId.replace('add_preview', 'add_preview_container'));
                if (addContainer) addContainer.style.display = 'inline-block';
                let editContainer = document.getElementById(previewId.replace('edit_preview', 'container_image'));
                if (editContainer) editContainer.style.setProperty('display', 'inline-block', 'important');
                if (previewId.includes('edit_preview')) {
                    let parts = previewId.split('_');
                    let hiddenInput = document.getElementById('remove_image_' + parts[2] + '_' + parts[3]);
                    if (hiddenInput) hiddenInput.value = '0';
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Remove Image UI
    window.removeExistingImage = function (imageNum, scheduleId) {
        if (confirm("Are you sure you want to remove this image?")) {
            let hiddenInput = document.getElementById('remove_image_' + imageNum + '_' + scheduleId);
            if (hiddenInput) hiddenInput.value = '1';
            let previewContainer = document.getElementById('container_image_' + imageNum + '_' + scheduleId);
            if (previewContainer) previewContainer.style.setProperty('display', 'none', 'important');
            let fileInput = document.getElementById('file_input_' + imageNum + '_' + scheduleId);
            if (fileInput) fileInput.value = '';
        }
    };
</script>