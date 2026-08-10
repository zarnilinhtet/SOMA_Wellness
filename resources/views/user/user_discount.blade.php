@include('master.header')
@include('master.sidebar')
@include('master.nav')

<!-- External Styles -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
    rel="stylesheet" />

<style>
    .package-item-wrapper {
        align-self: flex-start;
    }

    .package-card {
        transition: all 0.25s ease-in-out;
        border: 1px solid #dee2e6;
        height: auto !important;
    }

    .package-card.selected {
        border-color: #0d6efd;
        box-shadow: 0 0.25rem 0.5rem rgba(13, 110, 253, 0.15);
        background-color: #f8f9fa;
    }

    .package-discount-details {
        display: none;
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="container-fluid py-4 mt-5">
    <div class="row justify-content-center">

        {{-- Flash Alert Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div>
            <div class="card-header bg-white border-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Assign User Package Discounts</h4>
                    <p class="text-muted small mb-0">Set default discounts globally or customize rates and expiration
                        per package.</p>
                </div>
            </div>

            <form action="{{ route('discount.store') }}" method="POST" class="card-body p-4">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">

                <!-- Global Preset Bar -->
                <div class="p-3 bg-light rounded-3 border mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-secondary small text-uppercase">
                            <i class="fas fa-bolt me-1 text-warning"></i> Bulk Fill / Quick Apply to All Packages
                        </span>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="number" step="0.01" min="0" max="100" id="globalDiscount"
                                name="global_discount" value="{{ old('global_discount') }}"
                                class="form-control form-control-sm global-input" placeholder="Default Discount (%)">
                        </div>
                        <div class="col-md-4">
                            <input type="date" id="globalExpDate" name="global_exp_date"
                                value="{{ old('global_exp_date') }}" class="form-control form-control-sm global-input">
                        </div>
                        <div class="col-md-4">
                            <input type="time" id="globalExpTime" name="global_exp_time"
                                value="{{ old('global_exp_time', '23:59') }}"
                                class="form-control form-control-sm global-input">
                        </div>
                    </div>
                </div>

                <!-- Package Selection -->
                <div class="col-md-12 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <label class="fw-bold mb-0">Select & Customize Packages <span
                                class="text-danger">*</span></label>

                        <div class="d-flex align-items-center gap-3">
                            <input type="text" id="packageSearchInput"
                                class="form-control form-control-sm border-secondary shadow-sm"
                                placeholder="Search packages..." style="max-width: 250px;">

                            <div class="form-check mb-0">
                                <input class="form-check-input border-secondary shadow-sm" type="checkbox"
                                    id="selectAllPackages" style="cursor: pointer;">
                                <label class="form-check-label fw-bold text-primary user-select-none"
                                    style="cursor:pointer;" for="selectAllPackages">
                                    Select All
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="border rounded-3 p-3 bg-light shadow-sm" style="max-height: 520px; overflow-y: auto;">
                        <div class="row g-3 align-items-start" id="packageListContainer">
                            @foreach($packages as $package)
                                @php 
                                                                    $oldData = old('package_data.' . $package->id, []);
                                    $isCheck = !empty($oldData['selected']);
                                @endphp
                                <div class="col-lg-6 col-md-12 package-item-wrapper">
                                    <div class="card package-card p-3 rounded-3 bg-white {{ $isCheck ? 'selected' : '' }}">

                                        <!-- Checkbox & Package Info -->
                                        <div class="form-check d-flex align-items-center mb-0">
                                            <input class="form-check-input package-checkbox me-2" type="checkbox"
                                                name="package_data[{{ $package->id }}][selected]" value="1"
                                                id="package_{{ $package->id }}" {{ $isCheck ? 'checked' : '' }}
                                                style="cursor: pointer; transform: scale(1.15);">

                                            <label
                                                class="form-check-label ms-1 user-select-none w-100 package-label-text d-flex justify-content-between align-items-center"
                                                style="cursor:pointer;" for="package_{{ $package->id }}">
                                                <span class="fw-bold text-dark">{{ $package->name }}</span>
                                                @if(isset($package->price))
                                                    <span
                                                        class="badge bg-light text-dark border">${{ number_format($package->price, 2) }}</span>
                                                @endif
                                            </label>
                                        </div>

                                        <!-- Dynamic Custom Inputs -->
                                        <div class="package-discount-details mt-3 pt-3 border-top"
                                            style="{{ $isCheck ? 'display: block;' : '' }}">
                                            <div class="row g-2">
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-semibold text-muted mb-1">Discount
                                                        (%)</label>
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" step="0.01" min="0" max="100"
                                                            name="package_data[{{ $package->id }}][discount_amount]"
                                                            class="form-control pkg-discount-input" placeholder="e.g. 10"
                                                            value="{{ $oldData['discount_amount'] ?? '' }}">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-semibold text-muted mb-1">Exp
                                                        Date</label>
                                                    <input type="date"
                                                        name="package_data[{{ $package->id }}][expiration_date]"
                                                        class="form-control form-control-sm pkg-date-input"
                                                        value="{{ $oldData['expiration_date'] ?? '' }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-semibold text-muted mb-1">Exp
                                                        Time</label>
                                                    <input type="time"
                                                        name="package_data[{{ $package->id }}][expiration_time]"
                                                        class="form-control form-control-sm pkg-time-input"
                                                        value="{{ $oldData['expiration_time'] ?? '23:59' }}">
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @error('package_data')
                        <div class="text-danger mt-1 small"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror

                    @error('packages')
                        <div class="text-danger mt-1 small"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end align-items-center gap-2 mt-4 pt-3 border-top">
                    <button type="reset" class="btn btn-outline-secondary px-4"
                        onclick="window.location.href='/user_register/'">Back</button>
                    <button type="submit" class="btn btn-primary px-5 fw-bold"><i class="fas fa-save me-1"></i> Save
                        Discounts</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JS Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        // Helper to clear global inputs
        function clearGlobalInputs() {
            $('#globalDiscount').val('');
            $('#globalExpDate').val('');
            $('#globalExpTime').val('');
        }

        // Apply global inputs to all currently checked cards
        function syncGlobalToCheckedCards() {
            var gDisc = $('#globalDiscount').val();
            var gDate = $('#globalExpDate').val();
            var gTime = $('#globalExpTime').val();

            $('.package-item-wrapper:visible').each(function () {
                var card = $(this).find('.package-card');
                var checkbox = card.find('.package-checkbox');

                if (checkbox.is(':checked')) {
                    card.find('.pkg-discount-input').val(gDisc);
                    card.find('.pkg-date-input').val(gDate);
                    card.find('.pkg-time-input').val(gTime);
                }
            });
        }

        // 1. Initialize Select2
        $('.select2-user').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Search and Select User --',
            allowClear: true,
            width: '100%'
        });

        // 2. Real-time listener: Whenever ANY global input changes, update checked package cards immediately
        $(document).on('input change', '.global-input', function () {
            syncGlobalToCheckedCards();
        });

        // 3. Individual Package Checkbox Change
        $(document).on('change', '.package-checkbox', function (e, isBulkTrigger) {
            var card = $(this).closest('.package-card');
            var detailsPanel = card.find('.package-discount-details');

            if ($(this).is(':checked')) {
                card.addClass('selected');
                detailsPanel.slideDown(200);

                // Auto-fill from global bar if values exist
                var gDisc = $('#globalDiscount').val();
                var gDate = $('#globalExpDate').val();
                var gTime = $('#globalExpTime').val();

                if (gDisc !== '') card.find('.pkg-discount-input').val(gDisc);
                if (gDate !== '') card.find('.pkg-date-input').val(gDate);
                if (gTime !== '') card.find('.pkg-time-input').val(gTime);
            } else {
                card.removeClass('selected');
                detailsPanel.slideUp(200);
            }

            // Clear global bar if the user manually toggles single cards (not bulk select)
            if (!isBulkTrigger) {
                clearGlobalInputs();
            }

            updateSelectAllState();
        });

        // 4. Clear global bar when user manually edits ANY package card input directly
        $(document).on('input change', '.pkg-discount-input, .pkg-date-input, .pkg-time-input', function () {
            clearGlobalInputs();
        });

        // 5. Initial state load (Restores UI after validation errors)
        $('.package-checkbox:checked').each(function () {
            var card = $(this).closest('.package-card');
            card.addClass('selected');
            card.find('.package-discount-details').show();
        });
        updateSelectAllState();

        // 6. Apply Global Button Click
        $('#btnApplyGlobal').on('click', function () {
            syncGlobalToCheckedCards();
        });

        // 7. Select All Checkbox Handler
        $('#selectAllPackages').on('change', function () {
            var isChecked = $(this).is(':checked');

            // Toggle all visible package checkboxes with bulk trigger flag
            $('.package-item-wrapper:visible .package-checkbox').prop('checked', isChecked).trigger('change', [true]);

            if (isChecked) {
                syncGlobalToCheckedCards();
            } else {
                clearGlobalInputs();
            }
        });

        function updateSelectAllState() {
            var totalVisible = $('.package-item-wrapper:visible .package-checkbox').length;
            var totalChecked = $('.package-item-wrapper:visible .package-checkbox:checked').length;
            $('#selectAllPackages').prop('checked', totalVisible > 0 && totalVisible === totalChecked);
        }

        // 8. Live Search Filter
        $('#packageSearchInput').on('keyup', function () {
            var searchTerm = $(this).val().toLowerCase();
            $('.package-item-wrapper').each(function () {
                var name = $(this).find('.package-label-text').text().toLowerCase();
                $(this).toggle(name.indexOf(searchTerm) > -1);
            });
            updateSelectAllState();
        });
    });
</script>