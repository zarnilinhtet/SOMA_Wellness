@include('master.header')
@include('master.sidebar')
@include('master.nav')

{{-- DataTables Buttons CSS for Excel Export --}}
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

<div class="container">
    <div class="page-inner">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">Manage Instructor Reports</h4>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="basic-datatables">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Instructor Name</th>
                                <th>Total Classes</th>
                                <th>Total Fee</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($instructors as $instructor)
                                {{-- array offset on null error မတက်အောင် $instructor ကို အရင်စစ်ပေးပါတယ် --}}
                                @if($instructor)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{-- Clickable instructor name triggers the interactive modal --}}
                                            <a href="javascript:void(0)" class="fw-bold text-primary show-instructor-details"
                                                data-id="{{ $instructor['id'] ?? '' }}" 
                                                data-name="{{ $instructor['name'] ?? 'Unknown' }}"
                                                style="text-decoration: none; cursor: pointer;">
                                                {{ $instructor['name'] ?? 'Unknown' }}
                                            </a>
                                        </td>
                                        <td>{{ $instructor['total_classes'] ?? 0 }}</td>
                                        <td>{{ number_format((float)($instructor['total_fee'] ?? 0)) }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="instructorPackagesModal" tabindex="-1" aria-labelledby="instructorPackagesModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="instructorPackagesModalLabel">
                    Summary For <span id="modalinstructorName"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 550px; overflow-y: auto;">
                {{-- Loading state spinner --}}
                <div id="loadingSpinner" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                {{-- Scrollable dynamic transaction card container --}}
                <div id="packagesContainer" class="d-none">
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@include('master.footer')

<style>
    /* Gives a subtle highlight effect on hover for interactive rows */
    .show-instructor-details:hover {
        opacity: 0.8;
        text-decoration: underline !important;
    }

    /* Custom scrollbar styling for the scrollable modal body */
    .modal-body::-webkit-scrollbar {
        width: 6px;
    }

    .modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .modal-body::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .modal-body::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Excel Button Custom Style */
    .btn-excel {
        background-color: #107c41 !important;
        color: white !important;
        border: none;
        border-radius: 5px;
        padding: 5px 15px;
        font-weight: 500;
    }
    .btn-excel:hover {
        background-color: #0b5e31 !important;
    }
</style>

{{-- DataTables Export Plugin Scripts --}}
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<script>
    $(document).ready(function () {
        // 1. Initialize main page DataTable with Excel Export
        if ($('#basic-datatables').length) {
            $('#basic-datatables').DataTable({
                "order": [[0, "desc"]],
                "dom": '<"row mb-3"<"col-md-6"B><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
                "buttons": [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel me-1"></i> Export Instructors Summary',
                        className: 'btn btn-excel btn-sm',
                        title: 'Instructor Summary Report'
                    }
                ]
            });
        }

        // 2. Handle interactive click event on instructor Name
        $(document).on('click', '.show-instructor-details', function () {
            let userId = $(this).data('id');
            let userName = $(this).data('name');

            $('#modalinstructorName').text(userName);
            $('#instructorPackagesModal').modal('show');
            $('#loadingSpinner').removeClass('d-none');
            $('#packagesContainer').addClass('d-none').empty();

            // 3. Fetch transaction data asynchronously
            $.ajax({
                url: '{{ route("instructor.packages.report", ":id") }}'.replace(':id', userId),
                type: 'GET',
                success: function (response) {
                    $('#loadingSpinner').addClass('d-none');
                    if (response && response.length > 0) {
                        let html = `
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle w-100" id="modal-datatables">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>Class Name</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Time</th>
                                        <th>Total Clients</th>
                                        <th>Total Fee</th>
                                    </tr>
                                </thead>
                                <tbody>`;

                        response.forEach(function (res, index) {
                            html += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${res.class_name ? res.class_name : 'N/A'}</td>
                                <td>${res.start_date || '-'}</td>
                                <td>${res.end_date || '-'}</td>
                                <td>${res.time || '-'}</td>
                                <td>${res.total_clients || '0'}</td>
                                <td>${res.total_fee ? Number(res.total_fee).toLocaleString() : '0'}</td>
                            </tr>`;
                        });

                        html += `</tbody></table></div>`;

                        // Inject HTML
                        $('#packagesContainer').html(html).removeClass('d-none');

                        // Initialize DataTables inside the modal with Excel Export
                        if ($('#modal-datatables').length) {
                            $('#modal-datatables').DataTable({
                                "order": [[0, "desc"]],
                                "destroy": true, // Ensures it resets properly if modal is closed and reopened
                                "dom": '<"row mb-3"<"col-md-6"B><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
                                "buttons": [
                                    {
                                        extend: 'excelHtml5',
                                        text: '<i class="fas fa-file-excel me-1"></i> Export Instructor Details',
                                        className: 'btn btn-excel btn-sm',
                                        title: userName + ' - Classes Report'
                                    }
                                ]
                            });
                        }
                    } else {
                        $('#packagesContainer').html(`
                            <div class="alert alert-warning text-center my-3">
                                <i class="fas fa-exclamation-circle me-2"></i> No data found for this instructor.
                            </div>
                        `).removeClass('d-none');
                    }
                },
                error: function () {
                    $('#loadingSpinner').addClass('d-none');
                    $('#packagesContainer').html(`
                        <div class="alert alert-danger text-center my-3" role="alert">
                            <i class="fas fa-times-circle me-2"></i> Failed to load data. Please try again.
                        </div>
                    `).removeClass('d-none');
                }
            });
        });
    });
</script>