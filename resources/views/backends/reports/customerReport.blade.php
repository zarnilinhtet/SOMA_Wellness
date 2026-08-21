@include('master.header')
@include('master.sidebar')
@include('master.nav')

{{-- DataTables Buttons CSS --}}
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

<div class="container">
    <div class="page-inner">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">Manage Customer Reports</h4>
        </div>

        {{-- Filter Section --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ url()->current() }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="start_date" class="form-label fw-bold text-muted small text-uppercase">Date From</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label fw-bold text-muted small text-uppercase">Date To</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="category_id" class="form-label fw-bold text-muted small text-uppercase">Category</label>
                        <select class="form-select" id="category_id" name="category_id">
                            <option value="">-- All Categories --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary fw-bold flex-grow-1"><i class="fas fa-filter me-1"></i> Filter</button>
                        <a href="{{ url()->current() }}" class="btn btn-light border text-secondary"><i class="fas fa-redo"></i></a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="basic-datatables">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Customer Name</th>
                                <th>Total Package</th>
                                <th>Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customers as $customer)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{-- Clickable customer name triggers the interactive modal --}}
                                        <a href="javascript:void(0)" class="fw-bold text-primary show-customer-details"
                                            data-id="{{ $customer->id }}" data-name="{{ $customer->name }}"
                                            style="text-decoration: none; cursor: pointer;">
                                            {{ $customer->name }}
                                        </a>
                                    </td>
                                    <td>{{ $customer->filtered_total_packages }}</td>
                                    <td>
                                       
                                            {{ number_format($customer->filtered_total_amount) }}
                                       
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

<div class="modal fade" id="customerPackagesModal" tabindex="-1" aria-labelledby="customerPackagesModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="customerPackagesModalLabel">
                    Packages for <span id="modalCustomerName"></span>
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
    .show-customer-details:hover {
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
                        text: '<i class="fas fa-file-excel me-1"></i> Export Filtered Report',
                        className: 'btn btn-excel btn-sm',
                        title: 'Customer Summary Report'
                    }
                ]
            });
        }

        // 2. Handle interactive click event on Customer Name
        $(document).on('click', '.show-customer-details', function () {
            let userId = $(this).data('id');
            let userName = $(this).data('name');
            
            // Get current filter values to pass them to the modal AJAX request
            let filterStartDate = $('#start_date').val() || '';
            let filterEndDate = $('#end_date').val() || '';
            let filterCategory = $('#category_id').val() || '';

            $('#modalCustomerName').text(userName);
            $('#customerPackagesModal').modal('show');
            $('#loadingSpinner').removeClass('d-none');
            $('#packagesContainer').addClass('d-none').empty();

            // 3. Fetch transaction data asynchronously (Appending filter query params)
            let ajaxUrl = '{{ route("customer.packages.report", ":id") }}'.replace(':id', userId) + 
                          `?start_date=${filterStartDate}&end_date=${filterEndDate}&category_id=${filterCategory}`;

            $.ajax({
                url: ajaxUrl,
                type: 'GET',
                success: function (response) {
                    $('#loadingSpinner').addClass('d-none');

                    if (response.packages && response.packages.purchases && response.packages.purchases.length > 0) {
                        let html = `
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle w-100" id="modal-datatables">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>Package Name</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>`;

                        response.packages.purchases.forEach(function (purchase, index) {
                            html += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${purchase.package ? purchase.package.name : 'N/A'}</td>
                                <td>${Number(purchase.amount).toLocaleString()}</td>
                                <td>${purchase.payment_method}</td>
                                <td><span class="badge bg-info">${purchase.pay_status}</span></td>
                                <td>${new Date(purchase.created_at).toLocaleDateString()}</td>
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
                                        text: '<i class="fas fa-file-excel me-1"></i> Export User Details',
                                        className: 'btn btn-excel btn-sm',
                                        title: userName + ' - Packages Report'
                                    }
                                ]
                            });
                        }
                    } else {
                        $('#packagesContainer').html(`
                            <div class="alert alert-warning text-center my-3">
                                <i class="fas fa-exclamation-circle me-2"></i> No transactions found for the selected filters.
                            </div>
                        `).removeClass('d-none');
                    }
                },
                error: function () {
                    $('#loadingSpinner').addClass('d-none');
                    $('#packagesContainer').html(`
                        <div class="alert alert-danger text-center my-3" role="alert">
                            <i class="fas fa-times-circle me-2"></i> Failed to load transaction data. Please try again.
                        </div>
                    `).removeClass('d-none');
                }
            });
        });
    });
</script>