@include('master.header')
@include('master.sidebar')
@include('master.nav')

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- DataTables Buttons CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<style>
    body,
    html,
    .page-inner {
        overflow-y: auto !important;
    }

    /* Select Box Styling */
    .form-select-beauty {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        color: #475569;
        font-weight: 500;
        background-color: #f8fafc;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .form-select-beauty:focus {
        background-color: #ffffff;
        border-color: #5c8d89;
        box-shadow: 0 0 0 3px rgba(92, 141, 137, 0.15);
    }

    /* Button Styling */
    .btn-beauty {
        background-color: #eef5f4;
        color: #5c8d89;
        border: 1px solid #d1e5e3;
        border-radius: 8px;
        font-weight: 600;
        padding: 0.35rem 0.8rem;
        transition: all 0.3s ease;
    }

    .btn-beauty:hover {
        background-color: #5c8d89;
        color: #ffffff;
        border-color: #5c8d89;
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(92, 141, 137, 0.2);
    }

    /* Badges */
    .badge-custom {
        padding: 0.45rem 1rem;
        border-radius: 50rem;
        font-size: 0.85rem;
        font-weight: 600;
        letter-spacing: 0.3px;
        display: inline-block;
    }

    .role-admin {
        background-color: #fee2e2;
        color: #ef4444;
        border: 1px solid #fecaca;
    }

    .role-instructor {
        background-color: #e0f2fe;
        color: #3b82f6;
        border: 1px solid #bae6fd;
    }

    .role-customer {
        background-color: #dcfce7;
        color: #22c55e;
        border: 1px solid #bbf7d0;
    }

    /* Onboarding Badges */
    .practice-badge {
        display: inline-block;
        background-color: #f1f5f9;
        color: #334155;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.3rem 0.6rem;
        border-radius: 6px;
        margin-right: 4px;
        margin-bottom: 4px;
        border: 1px solid #cbd5e1;
    }

    /* Table Scroll & Sticky Header */
    .table-scroll-container {
        max-height: 65vh;
        overflow-y: auto;
        border-bottom: 1px solid #dee2e6;
    }

    #basic-datatables thead th {
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 10;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }
</style>

<div class="container">
    <div class="page-inner">

        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
            @if (session('success'))
                <div class="alert alert-dismissible fade show shadow-sm border-0 d-flex align-items-center py-2 px-4 rounded-pill m-0"
                    role="alert" style="background-color: #e0f8e9; color: #155724;">
                    <i class="fas fa-check-circle me-2" style="font-size: 1.2rem; color: #28a745;"></i>
                    <span class="fw-bold">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show m-0">
                    {{ session('error') }}
                </div>
            @endif
            @if ($errors->has('error'))
                <div class="alert alert-danger alert-dismissible fade show m-0">
                    {{ $errors->first('error') }}
                </div>
            @endif
        </div>

        <div class="card shadow-sm border-0">
            <div
                class="card-header d-flex justify-content-between align-items-center bg-white border-bottom flex-wrap gap-3">
                <h4 class="card-title mb-0 fw-bold text-dark">User Management</h4>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <!-- Category Dropdown Filter -->
                    <select id="roleFilter" class="form-select form-select-beauty w-auto shadow-sm">
                        <option value="">All Roles</option>
                        <option value="Admin">Admin</option>
                        <option value="Instructor">Instructor</option>
                        <option value="Customer">Customer</option>
                    </select>

                    <!-- Export Dropdown Menu -->
                    <div class="dropdown">
                        <button class="btn btn-success btn-md shadow-sm dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-download me-1"></i> Export
                        </button>
                        <ul class="dropdown-menu border-0 shadow" aria-labelledby="exportDropdown">
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="#" id="exportExcelBtn">
                                    <i class="fas fa-file-excel text-success me-2" style="width: 20px;"></i> Excel
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="#" id="exportCsvBtn">
                                    <i class="fas fa-file-csv text-info me-2" style="width: 20px;"></i> CSV
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="#" id="exportPdfBtn">
                                    <i class="fas fa-file-pdf text-danger me-2" style="width: 20px;"></i> PDF
                                </a>
                            </li>
                        </ul>
                    </div>

                    @if (auth()->user()->hasPermission('user_register'))
                        <button type="button" class="btn btn-primary btn-md shadow-sm" id="openModalBtn"
                            data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="fas fa-plus me-1"></i> Register New User
                        </button>
                    @endif
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive table-scroll-container">
                    <table id="basic-datatables" class="display table table-striped table-hover align-middle w-100">
                        <thead>
                            <tr>
                                <th style="width: 5%">No.</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Age</th>
                                <th>Role</th>
                                <th>Discount Packages</th>
                                <th>Role Update</th>

                                @if (auth()->user()->hasPermission('user_edit') || auth()->user()->hasPermission('user_delete'))
                                    <th class="text-center" style="width: 15%">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-bold text-dark">{{ $user->name }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td>{{ $user->age }}</td>

                                    <td>
                                        @if ($user->role === 'Admin' || $user->hasRole('Admin'))
                                            <span class="badge-custom role-admin">Admin</span>
                                        @elseif($user->role === 'Instructor' || $user->hasRole('Instructor'))
                                            <span class="badge-custom role-instructor">Instructor</span>
                                        @else
                                            <span class="badge-custom role-customer">Customer</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-beauty btn-sm btn-view-packages shadow-sm"
                                            onclick="window.location.href='/user/{{ $user->id }}/package/index'">
                                            <i class="fas fa-box me-1"></i> View
                                        </button>
                                    </td>

                                    <td class="align-middle">
                                        @if($user->hasRole('Admin'))
                                            <span
                                                class="badge-custom role-admin d-inline-flex align-items-center gap-1 shadow-sm">
                                                <i class="fas fa-shield-alt"></i> Admin
                                            </span>
                                        @else
                                            <form action="{{ route('user_register.role_update', $user->id) }}" method="POST"
                                                class="d-flex gap-2 align-items-center mb-0">
                                                @csrf
                                                @method('PUT')

                                                <select name="role"
                                                    class="form-select form-select-sm form-select-beauty w-auto shadow-sm">
                                                    <option value="">Select</option>
                                                    @foreach($userTypes as $type)
                                                        <option value="{{ $type->name }}" {{ $user->hasRole($type->name) ? 'selected' : '' }}>
                                                            {{ $type->name }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <button type="submit"
                                                    class="btn btn-beauty btn-sm shadow-sm d-flex align-items-center gap-1">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>

                                    @if (auth()->user()->hasPermission('user_edit') || auth()->user()->hasPermission('user_delete'))
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2 align-items-center">
                                                @if (auth()->user()->hasPermission('user_edit'))
                                                    <a href="{{ route('user_register.edit', $user->id) }}"
                                                        class="btn btn-link btn-primary p-0" title="Edit User">
                                                        <i class="fa fa-edit fs-5"></i>
                                                    </a>
                                                @endif

                                                @if (auth()->user()->hasPermission('user_delete'))
                                                    @if ($user->email !== 'admin@admin.com')
                                                        <form action="{{ route('user_register.destroy', $user->id) }}" method="POST"
                                                            class="m-0">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-link btn-danger p-0"
                                                                onclick="return confirm('Are you sure you want to delete this user?')">
                                                                <i class="fa fa-times fs-4"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <i class="fa fa-lock text-muted fs-5" title="System Protected"></i>
                                                    @endif
                                                @endif

                                                @if(!$user->hasRole('Admin'))
                                                    <button type="button" class="btn btn-link btn-primary p-0"
                                                        title="User Onboarding Info" data-bs-toggle="modal"
                                                        data-bs-target="#userInfo" data-bs-user-id="{{ $user->id }}">
                                                        <i class="fa fa-eye fs-5"></i>
                                                    </button>

                                                    @if (auth()->user()->hasPermission('user_register'))
                                                        <button type="button" class="btn btn-success btn-sm btn-open-discount shadow-sm"
                                                            onclick="window.location.href='/give/discount/{{ $user->id }}'">
                                                            Discount
                                                        </button>
                                                        <button type="button" class="btn btn-primary btn-sm shadow-sm"
                                                            onclick="window.location.href='/buy/package/{{ $user->id }}'">
                                                            Buy Pkg
                                                        </button>
                                                        <button type="button" class="btn btn-info btn-sm"
                                                            onclick="window.location.href='/join/class/for/user/{{ $user->id }}'">
                                                            Join Class
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: Admin Booking (Package မပါပါ) --}}
@php
    $allClasses = \App\Models\ClassSchedule::where('status', '!=', 'cancelled')->orderBy('start_date', 'desc')->get();
@endphp
<div class="modal fade" id="adminBookClassModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.booking.store') }}" method="POST" class="modal-content border-0 shadow-lg">
            @csrf
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Book Class for <span id="bookingUserName" class="text-primary"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="user_id" id="bookingUserId">

                <div class="mb-3">
                    <label class="fw-bold mb-1">Select Class <span class="text-danger">*</span></label>
                    <select name="selected_class_id" class="form-select form-select-beauty select2-class"
                        style="width: 100%" required>
                        <option value="">Choose a class...</option>
                        @foreach($allClasses as $class)
                            <option value="{{ $class->id }}">
                                {{ $class->class_name }}
                                ({{ \Carbon\Carbon::parse($class->start_date)->format('d M') }}
                                @ {{ \Carbon\Carbon::parse($class->start_time)->format('h:i A') }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-calendar-check me-1"></i> Book Now</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Add User --}}
@if (auth()->user()->hasPermission('user_register'))
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('user_register.store') }}" method="POST" class="modal-content border-0 shadow-lg">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Register New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fw-bold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Enter name" required
                                value="{{ old('name') }}">
                            @error('name') <span style="color: red;">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Phone Number <span class="text-danger">*</span></label>
                            <input type="number" name="phone" class="form-control" placeholder="123456789" required
                                value="{{ old('phone') }}">
                            @error('phone') <span style="color: red;">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Age <span class="text-danger">*</span></label>
                            <input type="date" name="age" class="form-control" required value="{{ old('age') }}">
                            @error('age') <span style="color: red;">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Password <br> <span class="text-danger">* Password Must be at least 8 characters *</span></label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                            @error('password') <span style="color: red;">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Confirm Password <br> <span class="text-danger">* Password Must be at least 8 characters *</span></label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••"
                                required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Register User</button>
                </div>
            </form>
        </div>
    </div>
@endif

<div class="modal fade" id="viewPackagesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    Packages for <span id="pkgUserName" class="text-primary"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="pkgListContainer" class="table-responsive mt-2">
                    <table class="table table-striped table-hover align-middle w-100">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%">No.</th>
                                <th>Package Name</th>
                                <th>Discount</th>
                                <th>Discount Expire Date</th>
                                <th>Discount Expire Time</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="modalPackageTableBody">
                            <!-- Dynamically populated via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: User Info --}}
<div class="modal fade" id="userInfo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="profile-card">
                    <div class="section-title mb-3 fw-bold">Practice Profile</div>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="info-box border p-2 rounded">
                                <label class="form-label text-muted mb-0"
                                    style="font-size: 10px; text-transform: uppercase;">Level</label>
                                <div class="fw-bold" id="modal-user-level">Not set</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box border p-2 rounded">
                                <label class="form-label text-muted mb-0"
                                    style="font-size: 10px; text-transform: uppercase;">Practices</label>
                                <div id="modal-user-practices" class="pt-1"><span class="text-muted small">None</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box border p-2 rounded">
                                <label class="form-label text-muted mb-0"
                                    style="font-size: 10px; text-transform: uppercase;">Times</label>
                                <div id="modal-user-times" class="pt-1"><span class="text-muted small">None</span></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box border p-2 rounded">
                                <label class="form-label text-muted mb-0"
                                    style="font-size: 10px; text-transform: uppercase;">Considerations</label>
                                <div id="modal-user-considerations" class="pt-1"><span
                                        class="text-muted small">None</span></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box border p-2 rounded">
                                <label class="form-label text-muted mb-0"
                                    style="font-size: 10px; text-transform: uppercase;">Know Where</label>
                                <div id="modal-user-know-where" class="pt-1"><span class="text-muted small">Not
                                        set</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@include('master.footer')

<!-- DataTables Buttons and Export Dependencies -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        const users = @json($users);
        
        var table = $('#basic-datatables').DataTable({
            "pageLength": 10,
            "info": true,
            "order": [[4, 'asc']],
            "columnDefs": [
                { "targets": [5, 6, 7], "orderable": false }
            ],
            "buttons": [
                {
                    extend: 'excelHtml5',
                    title: 'Users_Export_Data',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6],
                        modifier: {
                            search: 'applied'
                        }
                    }
                },
                {
                    extend: 'csvHtml5',
                    title: 'Users_Export_Data',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6],
                        modifier: {
                            search: 'applied'
                        }
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Users_Export_Data',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6],
                        modifier: {
                            search: 'applied'
                        }
                    }
                }
            ]
        });

        // Trigger DataTables Exports
        $('#exportExcelBtn').on('click', function (e) {
            e.preventDefault();
            table.button('.buttons-excel').trigger();
        });

        $('#exportCsvBtn').on('click', function (e) {
            e.preventDefault();
            table.button('.buttons-csv').trigger();
        });

        $('#exportPdfBtn').on('click', function (e) {
            e.preventDefault();
            table.button('.buttons-pdf').trigger();
        });

        // Role Category Dropdown Search Trigger
        $('#roleFilter').on('change', function () {
            var selectedRole = $(this).val();
            table.column(4).search(selectedRole).draw();
        });

        // Open Register Modal
        $('#openModalBtn').on('click', function (e) {
            e.preventDefault();
            $('#addUserModal').modal('show');
        });

        // Search Packages in Discount Modal
        $('#packageSearchInput').on('keyup', function () {
            var value = $(this).val().toLowerCase();
            $('.package-item-wrapper').filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // Set User ID & Discount Data
        $(document).on('click', '.btn-open-discount', function () {
            const userId = $(this).attr('data-bs-user-id');
            const discount = $(this).attr('data-bs-discount');
            const expireDate = $(this).attr('data-bs-discount-expire');
            let userPackages = $(this).attr('data-bs-packages');

            $('#discountUserId').val(userId);
            $('#discountInput').val(discount && discount !== 'null' ? discount : '');
            $('#discountExpireInput').val(expireDate && expireDate !== 'null' ? expireDate : '');
            $('#packageSearchInput').val('');
            $('.package-item-wrapper').show();
            $('.package-checkbox').prop('checked', false);
            $('#selectAllPackages').prop('checked', false);

            if (userPackages) {
                try {
                    const parsedPackages = JSON.parse(userPackages);
                    if (Array.isArray(parsedPackages)) {
                        parsedPackages.forEach(function (pkgId) {
                            $('#package_' + pkgId).prop('checked', true);
                        });
                    }
                } catch (e) {
                    console.error("Error parsing packages:", e);
                }
            }
            $('.package-checkbox').first().trigger('change');
        });

        $('#clearDiscountBtn').on('click', function () {
            $('#discountInput').val('');
            $('#discountExpireInput').val('');
            $('.package-checkbox').prop('checked', false);
            $('#selectAllPackages').prop('checked', false);
        });

        // User Information Modal logic
        $('#userInfo').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const userId = button.data('bs-user-id');
            const user = users.find(u => u.id == userId);

            const generateBadges = (items) => {
                if (!items || items.length === 0) return '<span class="text-muted small">None</span>';
                let badges = '';
                const arrayItems = Array.isArray(items) ? items : Object.values(items);
                arrayItems.forEach(item => { badges += `<span class="practice-badge">${item}</span>`; });
                return badges;
            };

            if (user) {
                $(this).find('.modal-title').text('Onboarding Data for: ' + user.name);
                if (user.onboarding) {
                    $(this).find('#modal-user-level').text(user.onboarding.starting_level || 'Not set');
                    $(this).find('#modal-user-practices').html(generateBadges(user.onboarding.included_practices));
                    $(this).find('#modal-user-times').html(generateBadges(user.onboarding.preferred_times));
                    $(this).find('#modal-user-considerations').html(generateBadges(user.onboarding.considerations));
                    $(this).find('#modal-user-know-where').text(user.onboarding.know_where || 'Not set');
                } else {
                    $(this).find('#modal-user-practices, #modal-user-times, #modal-user-considerations, #modal-user-know-where').html('<span class="text-muted small">None</span>');
                }
            }
        });

        // Select2 for Admin Booking Modal Class
        $('.select2-class').select2({
            dropdownParent: $('#adminBookClassModal')
        });

        $(document).on('click', '.btn-admin-book', function () {
            const userId = $(this).data('user-id');
            const userName = $(this).data('user-name');
            $('#bookingUserId').val(userId);
            $('#bookingUserName').text(userName);
            $('.select2-class').val(null).trigger('change');
        });

        @if ($errors->any() && !$errors->has('error'))
            if ($('#addUserModal').length && !$('#discountUserId').val()) {
                $('#addUserModal').modal('show');
            } else if ($('#discountModal').length) {
                $('#discountModal').modal('show');
                $('.package-checkbox').first().trigger('change');
            }
        @endif

        // Auto-dismiss notifications
        setTimeout(function () {
            $('.alert-dismissible').fadeOut('slow', function () {
                $(this).remove();
            });
        }, 4000);
    });
</script>