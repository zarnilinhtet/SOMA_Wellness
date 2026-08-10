@include('master.header')
@include('master.sidebar')
@include('master.nav')

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- DataTables Buttons CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<style>
    body, html, .page-inner {
        overflow-y: auto !important;
    }

    .form-select-beauty {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        color: #475569;
        font-weight: 500;
        background-color: #f8fafc;
        cursor: pointer;
        transition: all 0.3s ease;
    }

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

    .badge-custom {
        padding: 0.45rem 1rem;
        border-radius: 50rem;
        font-size: 0.85rem;
        font-weight: 600;
        letter-spacing: 0.3px;
        display: inline-block;
    }
    .role-admin { background-color: #fee2e2; color: #ef4444; border: 1px solid #fecaca; }
    .role-instructor { background-color: #e0f2fe; color: #3b82f6; border: 1px solid #bae6fd; }
    .role-customer { background-color: #dcfce7; color: #22c55e; border: 1px solid #bbf7d0; }

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

    /* Dropdown Options Hover Effects */
    .dropdown-item {
        padding: 0.5rem 1rem;
        font-weight: 500;
        transition: all 0.2s;
    }
    .dropdown-item:hover {
        background-color: #f8fafc;
        transform: translateX(4px);
    }
</style>

<div class="container">
    <div class="page-inner">

        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
            @if (session('success'))
                <div class="alert alert-dismissible fade show shadow-sm border-0 d-flex align-items-center py-2 px-4 rounded-pill m-0" role="alert" style="background-color: #e0f8e9; color: #155724;">
                    <i class="fas fa-check-circle me-2" style="font-size: 1.2rem; color: #28a745;"></i>
                    <span class="fw-bold">{{ session('success') }}</span>
                </div>
            @endif
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom flex-wrap gap-3">
                <div>
                    <h4 class="card-title mb-0 fw-bold text-dark">
                        <i class="fas fa-gem text-primary me-2"></i> Users With Packages
                    </h4>
                    {{-- <small class="text-muted">အမှန်တကယ် Package ဝယ်ယူထားသော User များကိုသာ ပြသထားပါသည်။</small> --}}
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
                                <th>Actions & Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-bold text-dark">{{ $user->name }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td>{{ $user->age }}</td>
                                    <td>
                                        @if ($user->hasRole('Admin'))
                                            <span class="badge-custom role-admin">Admin</span>
                                        @elseif($user->hasRole('Instructor'))
                                            <span class="badge-custom role-instructor">Instructor</span>
                                        @else
                                            <span class="badge-custom role-customer">Customer</span>
                                        @endif
                                    </td>
                                    <td>
                                        <!-- Dropdown Menu for Actions -->
                                        <div class="dropdown">
                                            <button class="btn btn-beauty btn-sm dropdown-toggle shadow-sm" type="button" id="actionMenu{{ $user->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-cog me-1"></i> Options
                                            </button>
                                            <ul class="dropdown-menu border-0 shadow" aria-labelledby="actionMenu{{ $user->id }}">
                                                
                                                <!-- Full Details -->
                                                <li>
                                                    <a class="dropdown-item text-primary" href="{{ route('user.package.details', $user->id) }}">
                                                        Full Details
                                                    </a>
                                                </li>

                                                @if(!$user->hasRole('Admin'))
                                                    <li><hr class="dropdown-divider"></li>
                                                    
                                                    <!-- Onboarding Info -->
                                                  
                                                    @if (auth()->user()->hasPermission('user_register'))
                                                        <!-- Discount -->
                                                        <li>
                                                            <a class="dropdown-item " href="/give/discount/{{ $user->id }}">
                                                              </i> Give Discount
                                                            </a>
                                                        </li>
                                                        
                                                        <!-- Buy Package -->
                                                        <li>
                                                            <a class="dropdown-item " href="/buy/package/{{ $user->id }}">
                                                                Buy Package
                                                            </a>
                                                        </li>
                                                        
                                                        <!-- Join Class -->
                                                        <li>
                                                            <a class="dropdown-item " href="/join/class/for/user/{{ $user->id }}">
                                                                Join Class
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endif

                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-box-open fs-3 mb-2 d-block"></i>
                                        Package ဝယ်ယူထားသော User မရှိသေးပါ။
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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
                                <label class="form-label text-muted mb-0" style="font-size: 10px; text-transform: uppercase;">Level</label>
                                <div class="fw-bold" id="modal-user-level">Not set</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box border p-2 rounded">
                                <label class="form-label text-muted mb-0" style="font-size: 10px; text-transform: uppercase;">Practices</label>
                                <div id="modal-user-practices" class="pt-1"><span class="text-muted small">None</span></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box border p-2 rounded">
                                <label class="form-label text-muted mb-0" style="font-size: 10px; text-transform: uppercase;">Times</label>
                                <div id="modal-user-times" class="pt-1"><span class="text-muted small">None</span></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box border p-2 rounded">
                                <label class="form-label text-muted mb-0" style="font-size: 10px; text-transform: uppercase;">Considerations</label>
                                <div id="modal-user-considerations" class="pt-1"><span class="text-muted small">None</span></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box border p-2 rounded">
                                <label class="form-label text-muted mb-0" style="font-size: 10px; text-transform: uppercase;">Know Where</label>
                                <div id="modal-user-know-where" class="pt-1"><span class="text-muted small">Not set</span></div>
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

<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        // DataTables Init
        $('#basic-datatables').DataTable({
            "pageLength": 10,
            "columnDefs": [{ "targets": [5], "orderable": false }]
        });

        const users = @json($users);

        // User Onboarding Info JavaScript Logic
        $('#userInfo').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const userId = button.data('bs-user-id');
            const user = users.find(u => u.id == userId);

            const generateBadges = (items) => {
                if (!items || items.length === 0) return '<span class="text-muted small">None</span>';
                let badges = '';
                const arrayItems = Array.isArray(items) ? items : Object.values(items);
                arrayItems.forEach(item => { badges += `<span class="badge bg-light text-dark border me-1 mb-1">${item}</span>`; });
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
                    $(this).find('#modal-user-level').text('Not set');
                    $(this).find('#modal-user-know-where').text('Not set');
                }
            }
        });
    });
</script>