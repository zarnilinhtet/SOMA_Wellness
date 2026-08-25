@include('master.header')
@include('master.sidebar')
@include('master.nav')

{{-- Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    .category-fee-card.selected { border-color: #0d6efd !important; background-color: #f8f9fa; }
    .select2-container .select2-selection--multiple { border: 1px solid #ebedf2; min-height: 38px; }
</style>

<div class="container">
    <div class="page-inner">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">Manage Instructors</h4>
            @if(auth()->user()->hasPermission('instructor_register'))
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addInstructorModal">
                    <i class="fas fa-plus"></i> New Instructor
                </button>
            @endif
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="basic-datatables">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Name & Type</th>
                                <th>Categories (Specialties)</th>
                                <th>Category Rates & Bonuses</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($instructors as $instructor)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-bold">
                                        {{ $instructor->user->name ?? 'Unknown User' }} <br>
                                        <span class="badge {{ $instructor->instructor_type == 'full_time' ? 'bg-primary' : 'bg-warning text-dark' }} mt-1">
                                            {{ $instructor->instructor_type == 'full_time' ? 'Full Time' : 'Part Time' }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $specs = is_string($instructor->specialty) ? json_decode($instructor->specialty, true) : ($instructor->specialty ?? []);
                                            $specs = is_array($specs) ? $specs : (!empty($instructor->specialty) ? [$instructor->specialty] : []);
                                        @endphp
                                        @forelse($specs as $spec)
                                            <span class="badge bg-secondary mb-1 d-inline-block">{{ $spec }}</span>
                                        @empty
                                            <span class="text-muted small">None</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        @forelse($instructor->categoryFees as $fee)
                                            <div class="badge bg-light text-dark border mb-2 text-start p-2 d-inline-block w-100">
                                                <div class="fw-bold text-primary mb-1">{{ optional($fee->category)->name ?? 'Unknown Category' }}</div>
                                                <div>
                                                    @if($fee->fee_type == 'part_time_percentage')
                                                        Base Rate: <strong>{{ floatval($fee->fee_value) }}%</strong> (of Revenue)
                                                    @elseif($fee->fee_type == 'part_time_tiered')
                                                        Type: <strong>Tiered Flat Fee Per Class</strong>
                                                    @else
                                                        Base Rate: <strong>{{ number_format($fee->fee_value) }} MMK</strong>
                                                    @endif
                                                </div>
                                                
                                                @if(!empty($fee->bonuses) && is_array($fee->bonuses))
                                                    <div class="mt-2 pt-1 border-top" style="font-size: 0.75rem;">
                                                        <div class="text-success fw-bold">
                                                            <i class="fas fa-list-ol me-1"></i>
                                                            {{ $fee->fee_type == 'part_time_tiered' ? 'Tier Settings:' : 'Bonus Tiers (Extra):' }}
                                                        </div>
                                                        @foreach(collect($fee->bonuses)->sortBy('threshold') as $b)
                                                            <div class="text-muted mt-1">
                                                                - <strong>{{ (int)$b['threshold'] }}</strong> Students or more: <strong>{{ $fee->fee_type == 'part_time_percentage' && (float)$b['amount'] <= 100 ? floatval($b['amount']).'%' : number_format((float)$b['amount']).' MMK' }}</strong>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div><br>
                                        @empty
                                            <span class="text-muted small">No rates set</span>
                                        @endforelse
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-3">
                                            <button type="button" class="btn btn-link btn-primary p-0"
                                                data-bs-toggle="modal" data-bs-target="#editInstructorModal{{ $instructor->id }}" title="Edit">
                                                <i class="fa fa-edit fs-5"></i>
                                            </button>
                                            <form action="{{ route('instructors.destroy', $instructor->id) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-link btn-danger p-0" onclick="return confirm('Are you sure?')"><i class="fa fa-trash fs-5"></i></button>
                                            </form>
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

{{-- ADD INSTRUCTOR MODAL --}}
<div class="modal fade" id="addInstructorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('instructors.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="fw-bold">Register New Instructor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="fw-bold form-label">Instructor User <span class="text-danger">*</span></label>
                        <select name="instructor_id" class="form-select" required>
                            <option value="">-- Select --</option>
                            @foreach ($instRole as $in)
                                <option value="{{ $in->id }}">{{ $in->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="fw-bold form-label">Instructor Type <span class="text-danger">*</span></label>
                        <select name="instructor_type" class="form-select instructor-type-select" required>
                            <option value="full_time">Full Time</option>
                            <option value="part_time">Part Time</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="fw-bold form-label">Categories (Specialties)</label>
                        <select name="specialty[]" multiple class="form-select select2-dropdown" style="width: 100%;">
                            @foreach($categories as $category)
                                <option value="{{ $category->name }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <h6 class="fw-bold mt-4 mb-2">Category Fee Structure</h6>
                <div class="border rounded p-3 bg-light" style="max-height: 450px; overflow-y: auto;">
                    <div class="row g-3">
                        @foreach($categories as $cat)
                            <div class="col-md-12">
                                <div class="card p-3 border category-fee-card">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input cat-checkbox" type="checkbox" name="category_fees[{{ $cat->id }}][selected]" value="1" id="cat_add_{{ $cat->id }}">
                                        <label class="form-check-label fw-bold" for="cat_add_{{ $cat->id }}">{{ $cat->name }}</label>
                                    </div>
                                    <div class="cat-fee-inputs" style="display: none;">
                                        <div class="row g-2 mb-3 pb-2 border-bottom">
                                            <div class="col-6">
                                                <label class="form-label small text-muted mb-1">Fee Type</label>
                                                <select name="category_fees[{{ $cat->id }}][fee_type]" class="form-select form-select-sm fee-type-select">
                                                    <option value="full_time_fixed">Fixed Rate (Full Time Base)</option>
                                                </select>
                                            </div>
                                            <div class="col-6 fee-value-container">
                                                <label class="form-label small text-muted mb-1 fee-value-label">Base Amount (MMK)</label>
                                                <input type="number" step="0.01" name="category_fees[{{ $cat->id }}][fee_value]" class="form-control form-control-sm fee-value-input" placeholder="Amount / %">
                                            </div>
                                        </div>

                                        <div class="bonus-wrapper" data-cat-id="{{ $cat->id }}">
                                            <label class="form-label small text-muted mb-1 bonus-label">Bonus Tiers (Optional Bonus)</label>
                                            <div class="bonus-container">
                                                <div class="row g-2 bonus-row align-items-center">
                                                    <div class="col-5">
                                                        <input type="number" name="category_fees[{{ $cat->id }}][bonuses][0][threshold]" class="form-control form-control-sm" placeholder="Student Target (e.g. 5)">
                                                    </div>
                                                    <div class="col-5">
                                                        <input type="number" step="0.01" name="category_fees[{{ $cat->id }}][bonuses][0][amount]" class="form-control form-control-sm" placeholder="Amount (MMK)">
                                                    </div>
                                                    <div class="col-2">
                                                        <button type="button" class="btn btn-sm btn-success add-bonus-btn"><i class="fas fa-plus"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary fw-bold">Save Instructor</button>
            </div>
        </form>
    </div>
</div>

{{-- EDIT INSTRUCTOR MODALS --}}
@foreach ($instructors as $instructor)
    <div class="modal fade" id="editInstructorModal{{ $instructor->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('instructors.update', $instructor->id) }}" method="POST" class="modal-content">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="fw-bold">Edit Fee Rates ({{ $instructor->user->name ?? 'Unknown' }})</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="fw-bold form-label">Instructor User <span class="text-danger">*</span></label>
                            <select name="instructor_id" class="form-select" required>
                                @foreach ($instRole as $in)
                                    <option value="{{ $in->id }}" {{ $instructor->instructor_id == $in->id ? 'selected' : '' }}>{{ $in->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold form-label">Instructor Type <span class="text-danger">*</span></label>
                            <select name="instructor_type" class="form-select instructor-type-select" required>
                                <option value="full_time" {{ ($instructor->instructor_type ?? 'full_time') == 'full_time' ? 'selected' : '' }}>Full Time</option>
                                <option value="part_time" {{ $instructor->instructor_type == 'part_time' ? 'selected' : '' }}>Part Time</option>
                            </select>
                        </div>
                        
                        @php
                            $editSpecs = is_string($instructor->specialty) ? json_decode($instructor->specialty, true) : ($instructor->specialty ?? []);
                            $editSpecs = is_array($editSpecs) ? $editSpecs : (!empty($instructor->specialty) ? [$instructor->specialty] : []);
                        @endphp
                        <div class="col-md-4">
                            <label class="fw-bold form-label">Categories (Specialties)</label>
                            <select name="specialty[]" multiple class="form-select select2-dropdown" style="width: 100%;">
                                @foreach($categories as $category)
                                    <option value="{{ $category->name }}" {{ in_array($category->name, $editSpecs) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <h6 class="fw-bold mt-4 mb-2">Category Fee Rates</h6>
                    <div class="border rounded p-3 bg-light" style="max-height: 450px; overflow-y: auto;">
                        <div class="row g-3">
                            @foreach($categories as $cat)
                                @php 
                                    $existingFee = $instructor->categoryFees->firstWhere('category_id', $cat->id);
                                    $isCheck = !empty($existingFee);
                                    $bonuses = $existingFee ? ($existingFee->bonuses ?? []) : [];
                                    $feeType = $existingFee->fee_type ?? 'full_time_fixed';
                                @endphp
                                <div class="col-md-12">
                                    <div class="card p-3 border category-fee-card {{ $isCheck ? 'selected' : '' }}">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input cat-checkbox" type="checkbox" name="category_fees[{{ $cat->id }}][selected]" value="1" id="cat_edit_{{ $instructor->id }}_{{ $cat->id }}" {{ $isCheck ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="cat_edit_{{ $instructor->id }}_{{ $cat->id }}">{{ $cat->name }}</label>
                                        </div>
                                        <div class="cat-fee-inputs" style="{{ $isCheck ? 'display: block;' : 'display: none;' }}">
                                            <div class="row g-2 mb-3 pb-2 border-bottom">
                                                <div class="col-6">
                                                    <label class="form-label small text-muted mb-1">Fee Type</label>
                                                    <select name="category_fees[{{ $cat->id }}][fee_type]" data-selected="{{ $feeType }}" class="form-select form-select-sm fee-type-select">
                                                        <option value="full_time_fixed" {{ $feeType == 'full_time_fixed' ? 'selected' : '' }}>Fixed Rate (Base Amount)</option>
                                                        <option value="part_time_percentage" {{ $feeType == 'part_time_percentage' ? 'selected' : '' }}>Percentage (%) on Revenue</option>
                                                        <option value="part_time_tiered" {{ $feeType == 'part_time_tiered' ? 'selected' : '' }}>Tiered Fees Per Class Count</option>
                                                    </select>
                                                </div>
                                                <div class="col-6 fee-value-container">
                                                    <label class="form-label small text-muted mb-1 fee-value-label">Base Rate / Percentage</label>
                                                    <input type="number" step="0.01" name="category_fees[{{ $cat->id }}][fee_value]" class="form-control form-control-sm fee-value-input" value="{{ isset($existingFee->fee_value) ? (float)$existingFee->fee_value : '' }}">
                                                </div>
                                            </div>

                                            <div class="bonus-wrapper" data-cat-id="{{ $cat->id }}">
                                                <label class="form-label small text-muted mb-1 bonus-label">Bonus Tiers / Tiered Settings</label>
                                                <div class="bonus-container">
                                                    @if(is_array($bonuses) && count($bonuses) > 0)
                                                        @foreach($bonuses as $index => $b)
                                                            <div class="row g-2 mt-1 bonus-row align-items-center">
                                                                <div class="col-5">
                                                                    <input type="number" name="category_fees[{{ $cat->id }}][bonuses][{{ $index }}][threshold]" class="form-control form-control-sm" value="{{ isset($b['threshold']) ? (float)$b['threshold'] : '' }}" placeholder="Target">
                                                                </div>
                                                                <div class="col-5">
                                                                    <input type="number" step="0.01" name="category_fees[{{ $cat->id }}][bonuses][{{ $index }}][amount]" class="form-control form-control-sm" value="{{ isset($b['amount']) ? (float)$b['amount'] : '' }}" placeholder="Amount (MMK)">
                                                                </div>
                                                                <div class="col-2">
                                                                    @if($index == 0)
                                                                        <button type="button" class="btn btn-sm btn-success add-bonus-btn"><i class="fas fa-plus"></i></button>
                                                                    @else
                                                                        <button type="button" class="btn btn-sm btn-danger remove-bonus-btn"><i class="fas fa-minus"></i></button>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="row g-2 mt-1 bonus-row align-items-center">
                                                            <div class="col-5"><input type="number" name="category_fees[{{ $cat->id }}][bonuses][0][threshold]" class="form-control form-control-sm" placeholder="Target"></div>
                                                            <div class="col-5"><input type="number" step="0.01" name="category_fees[{{ $cat->id }}][bonuses][0][amount]" class="form-control form-control-sm" placeholder="Amount"></div>
                                                            <div class="col-2"><button type="button" class="btn btn-sm btn-success add-bonus-btn"><i class="fas fa-plus"></i></button></div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Update</button>
                </div>
            </form>
        </div>
    </div>
@endforeach

@include('master.footer')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        // Init Select2 for Multiple
        $('.select2-dropdown').each(function() {
            let isMultiple = $(this).prop('multiple');
            $(this).select2({
                dropdownParent: $(this).closest('.modal').length ? $(this).closest('.modal') : $(document.body),
                placeholder: "-- Search & Select Categories --",
                allowClear: true,
                closeOnSelect: !isMultiple
            });
        });

        $(document).on('change', '.cat-checkbox', function () {
            var card = $(this).closest('.category-fee-card');
            if ($(this).is(':checked')) {
                card.addClass('selected');
                card.find('.cat-fee-inputs').slideDown(200);
            } else {
                card.removeClass('selected');
                card.find('.cat-fee-inputs').slideUp(200);
            }
        });

        $(document).on('click', '.add-bonus-btn', function() {
            let wrapper = $(this).closest('.bonus-wrapper');
            let catId = wrapper.data('cat-id');
            let uniqueIndex = new Date().getTime(); 
            let html = `
                <div class="row g-2 mt-1 bonus-row align-items-center">
                    <div class="col-5"><input type="number" name="category_fees[${catId}][bonuses][${uniqueIndex}][threshold]" class="form-control form-control-sm" placeholder="Target"></div>
                    <div class="col-5"><input type="number" step="0.01" name="category_fees[${catId}][bonuses][${uniqueIndex}][amount]" class="form-control form-control-sm" placeholder="Amount (MMK)"></div>
                    <div class="col-2"><button type="button" class="btn btn-sm btn-danger remove-bonus-btn"><i class="fas fa-minus"></i></button></div>
                </div>`;
            wrapper.find('.bonus-container').append(html);
        });

        $(document).on('click', '.remove-bonus-btn', function() {
            $(this).closest('.bonus-row').remove();
        });

        function toggleInstructorTypeOptions(modal) {
            let type = modal.find('.instructor-type-select').val();
            modal.find('.category-fee-card').each(function() {
                let card = $(this);
                let feeTypeSelect = card.find('.fee-type-select');
                let existingFeeType = feeTypeSelect.attr('data-selected') || 'full_time_fixed';
                if (type === 'full_time') {
                    feeTypeSelect.html('<option value="full_time_fixed" selected>Fixed Rate (Base Amount)</option>');
                } else {
                    let html = `<option value="part_time_percentage" ${existingFeeType === 'part_time_percentage' ? 'selected' : ''}>Percentage (%) on Revenue</option>
                                <option value="part_time_tiered" ${existingFeeType === 'part_time_tiered' ? 'selected' : ''}>Tiered Fees (Per Class Count)</option>`;
                    feeTypeSelect.html(html);
                }
                feeTypeSelect.trigger('change');
            });
        }

        $('.modal').on('show.bs.modal', function () {
            if($(this).find('.instructor-type-select').length > 0) {
                toggleInstructorTypeOptions($(this));
            }
        });

        $(document).on('change', '.instructor-type-select', function() {
            toggleInstructorTypeOptions($(this).closest('.modal'));
        });

        $(document).on('change', '.fee-type-select', function() {
            let typeSelect = $(this);
            let card = typeSelect.closest('.category-fee-card');
            let val = typeSelect.val();
            
            typeSelect.attr('data-selected', val);
            
            let feeValueContainer = card.find('.fee-value-container');
            let bonusWrapper = card.find('.bonus-wrapper');
            let feeValueLabel = card.find('.fee-value-label');
            let bonusLabel = card.find('.bonus-label');

            if (val === 'part_time_percentage') {
                feeValueContainer.show();
                feeValueLabel.text('Percentage (%)');
                bonusWrapper.show();
                bonusLabel.text('Bonus Tiers (Extra Bonus Amount if Reached)');
            } else if (val === 'part_time_tiered') {
                feeValueContainer.hide();
                bonusWrapper.show();
                bonusLabel.text('Tiered Settings (e.g. >= 1 Student: 30000, >= 4 Students: 40000)');
            } else if (val === 'full_time_fixed') {
                feeValueContainer.show();
                feeValueLabel.text('Base Amount (MMK)');
                bonusWrapper.show();
                bonusLabel.text('Bonus Tiers (Extra Bonus Amount if Reached)');
            }
        });
    });
</script>