@include('master.header')
@include('master.sidebar')
@include('master.nav')

{{-- Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
{{-- DataTables Buttons CSS --}}
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">

<style>
    .category-fee-card { transition: all 0.2s ease-in-out; }
    .category-fee-card.selected { border-color: #0d6efd !important; background-color: #f8f9fa; }
    
    /* Select2 Multi-Select Styling */
    .select2-container .select2-selection--multiple { 
        border: 1px solid #ebedf2; 
        min-height: 38px; 
        border-radius: 5px;
        padding-bottom: 2px;
    }
    
    /* Main Tag Wrapper */
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #f4f5f8; /* Light gray background */
        border: 1px solid #cfd4db; /* Border around the tag */
        color: #1e2b58; /* Dark navy blue text */
        border-radius: 4px;
        padding: 0; /* Remove default padding to allow flexbox layout */
        margin-top: 6px;
        margin-left: 6px;
        display: flex;
        flex-direction: row-reverse; /* Puts the 'x' on the left side of the text */
        align-items: center;
        overflow: hidden;
    }

    /* Tag Text */
    .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
        padding: 3px 10px;
        font-weight: 500;
        cursor: default;
    }

    /* Remove Button ('x') */
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        background-color: transparent;
        color: #888;
        border: none;
        border-right: 1px solid #cfd4db; /* Vertical divider line */
        border-radius: 0;
        padding: 3px 8px;
        margin: 0;
        font-weight: bold;
        position: relative; /* Overrides default Select2 absolute positioning */
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        background-color: #e2e6ea;
        color: #dc3545;
    }

    /* Far right clear all 'x' */
    .select2-container--default .select2-selection--multiple .select2-selection__clear {
        margin-top: 6px;
        margin-right: 10px;
        color: #000;
        font-weight: bold;
    }

    .select2-container { width: 100% !important; }
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

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="basic-datatables">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Name & Details</th>
                                <th>Payment Info</th>
                                <th>Category Rates & Bonuses</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($instructors as $instructor)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-bold">
                                        {{ $instructor->user->name ?? 'Unknown User' }} 
                                        @if($instructor->instructor_code)
                                            <span class="text-muted small ms-1">({{ $instructor->instructor_code }})</span>
                                        @endif
                                        <br>
                                        <span class="badge {{ $instructor->instructor_type == 'full_time' ? 'bg-primary' : 'bg-warning text-dark' }} mt-1 mb-2">
                                            {{ $instructor->instructor_type == 'full_time' ? 'Full Time' : 'Part Time' }}
                                        </span><br>
                                        @php
                                            $specs = is_string($instructor->specialty) ? json_decode($instructor->specialty, true) : ($instructor->specialty ?? []);
                                            $specs = is_array($specs) ? $specs : (!empty($instructor->specialty) ? [$instructor->specialty] : []);
                                        @endphp
                                        @forelse($specs as $spec)
                                            <span class="badge bg-secondary mb-1 d-inline-block">{{ $spec }}</span>
                                        @empty
                                            <span class="text-muted small">No Specialty</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div><strong>Method:</strong> {{ ucfirst($instructor->payment_method) ?? 'N/A' }}</div>
                                            <div><strong>Date:</strong> {{ $instructor->payment_date ?? 'N/A' }}</div>
                                            <div><strong>Maint. Fee:</strong> {{ number_format($instructor->maintenance_fees) }} MMK</div>
                                            <div><strong>Class Fee:</strong> {{ number_format($instructor->class_teaching_fees) }} MMK</div>
                                        </div>
                                    </td>
                                    <td>
                                        @forelse($instructor->categoryFees as $fee)
                                            <div class="badge bg-light text-dark border mb-2 text-start p-2 d-inline-block w-100">
                                                <div class="fw-bold text-primary mb-1">{{ optional($fee->category)->name ?? 'Unknown Category' }}</div>
                                                <div>
                                                    @if($fee->fee_type == 'part_time_percentage')
                                                        Base Rate: <strong>{{ floatval($fee->fee_value) }}%</strong> (of Revenue)
                                                    @elseif($fee->fee_type == 'part_time_tiered')
                                                        Type: <strong>Tiered Fee Rules</strong>
                                                    @else
                                                        Base Rate: <strong>{{ number_format($fee->fee_value) }} MMK</strong>
                                                    @endif
                                                </div>
                                                
                                                @if(!empty($fee->bonuses) && is_array($fee->bonuses))
                                                    <div class="mt-2 pt-1 border-top" style="font-size: 0.75rem;">
                                                        <div class="text-success fw-bold">
                                                            <i class="fas fa-list-ol me-1"></i>
                                                            {{ $fee->fee_type == 'part_time_tiered' ? 'Tier Settings:' : 'Bonus Tiers:' }}
                                                        </div>
                                                        @foreach(collect($fee->bonuses)->sortBy(function($b) { return $b['min_students'] ?? $b['threshold'] ?? 0; }) as $b)
                                                            @php
                                                                $min = $b['min_students'] ?? $b['threshold'] ?? 0;
                                                                $max = $b['max_students'] ?? null;
                                                                $amt = $b['bonus_amount'] ?? $b['amount'] ?? 0;
                                                                $type = $b['bonus_type'] ?? 'fixed';
                                                            @endphp
                                                            <div class="text-muted mt-1">
                                                                - <strong>{{ (int)$min }}</strong> 
                                                                @if(!empty($max))
                                                                    to <strong>{{ (int)$max }}</strong>
                                                                @else
                                                                    and above
                                                                @endif
                                                                Students: 
                                                                <strong>{{ number_format((float)$amt) }} {{ $type == 'per_student' ? 'MMK (Per Student)' : 'MMK (Fixed)' }}</strong>
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
                                            <!-- Payment Modal Button -->
                                            <button type="button" class="btn btn-link btn-success p-0"
                                                data-bs-toggle="modal" data-bs-target="#paymentModal{{ $instructor->id }}" title="Payments">
                                                <i class="fa fa-money-bill-wave fs-5"></i>
                                            </button>
                                            
                                            <!-- Edit Button -->
                                            <button type="button" class="btn btn-link btn-primary p-0"
                                                data-bs-toggle="modal" data-bs-target="#editInstructorModal{{ $instructor->id }}" title="Edit">
                                                <i class="fa fa-edit fs-5"></i>
                                            </button>
                                            
                                            <!-- Delete Button -->
                                            <form action="{{ route('instructors.destroy', $instructor->id) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-link btn-danger p-0" onclick="return confirm('Are you sure you want to delete this instructor?')"><i class="fa fa-trash fs-5"></i></button>
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

{{-- ==================== ADD INSTRUCTOR MODAL ==================== --}}
<div class="modal fade" id="addInstructorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
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
                        <label class="fw-bold form-label w-100">Categories (Specialties)</label>
                        <!-- Added multiple="multiple" attribute explicitly -->
                        <select name="specialty[]" class="form-select select2-dropdown w-100" multiple="multiple">
                            @foreach($categories as $category)
                                <option value="{{ $category->name }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-4 border-bottom pb-4">
                    <div class="col-md-3">
                        <label class="fw-bold form-label">Payment Date</label>
                        <input type="text" name="payment_date" class="form-control" placeholder="e.g. 5th of Month">
                    </div>
                    <div class="col-md-3">
                        <label class="fw-bold form-label">Payment Method</label>
                        <select name="payment_method" class="form-select">
                            <option value="">-- Select --</option>
                            <option value="Cash">Cash</option>
                            <option value="KBZ PAy">KBZ Pay</option>
                            <option value="Wave">Wave Money</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="fw-bold form-label">Maintenance Fees (MMK)</label>
                        <input type="number" step="0.01" name="maintenance_fees" class="form-control" placeholder="0">
                    </div>
                    <div class="col-md-3">
                        <label class="fw-bold form-label">Class Teaching Fees (MMK)</label>
                        <input type="number" step="0.01" name="class_teaching_fees" class="form-control" placeholder="0">
                    </div>
                </div>

                <h6 class="fw-bold mt-2 mb-2">Category Fee Structure</h6>
                
                <div class="alert alert-info p-3 small mb-3 border-0 shadow-sm rounded">
                    <i class="fas fa-info-circle me-1"></i> <strong>ဖြည့်သွင်းနည်း လမ်းညွှန် (Tier / Bonus Rules) :</strong><br>
                    <ul class="mb-0 mt-1 ps-3">
                        <li class="mb-1"><strong>၁ ယောက် မှ ၅ ယောက် (ပုံသေ) :</strong> Min <code>1</code>, Max <code>5</code>, Type <code>Fixed Amount</code>, Amount <code>15000</code> ဟု ထည့်ပါ။</li>
                        <li><strong>၆ ယောက် နှင့် အထက် (တစ်ယောက်ချင်းစီ) :</strong> Min <code>6</code>, <strong>Max ကို ဘာမှမထည့်ဘဲ အလွတ်ထားပါ</strong>၊ Type <code>Per Student</code>, Amount <code>2000</code> ဟု ထည့်ပါ။</li>
                    </ul>
                </div>

                <div class="border rounded p-3 bg-light" style="max-height: 450px; overflow-y: auto;">
                    <div class="row g-3">
                        @foreach($categories as $cat)
                            <div class="col-md-12">
                                <div class="card p-3 border category-fee-card">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input cat-checkbox" type="checkbox" name="category_fees[{{ $cat->id }}][selected]" value="1" id="cat_add_{{ $cat->id }}">
                                        <label class="form-check-label fw-bold" style="cursor:pointer;" for="cat_add_{{ $cat->id }}">{{ $cat->name }}</label>
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
                                            <label class="form-label small text-muted mb-1 bonus-label">Tier / Bonus Rules (အထက်ပါ လမ်းညွှန်အတိုင်းဖြည့်ရန်)</label>
                                            <div class="bonus-container">
                                                <div class="row g-2 bonus-row align-items-center">
                                                    <div class="col-2"><input type="number" name="category_fees[{{ $cat->id }}][bonuses][0][min_students]" class="form-control form-control-sm" placeholder="Min (e.g. 1)"></div>
                                                    <div class="col-2"><input type="number" name="category_fees[{{ $cat->id }}][bonuses][0][max_students]" class="form-control form-control-sm" placeholder="Max (e.g. 5)"></div>
                                                    <div class="col-3">
                                                        <select name="category_fees[{{ $cat->id }}][bonuses][0][bonus_type]" class="form-select form-select-sm">
                                                            <option value="fixed">Fixed Amount</option>
                                                            <option value="per_student">Per Student</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-3"><input type="number" step="0.01" name="category_fees[{{ $cat->id }}][bonuses][0][bonus_amount]" class="form-control form-control-sm" placeholder="Amount (MMK)"></div>
                                                    <div class="col-2"><button type="button" class="btn btn-sm btn-success add-bonus-btn"><i class="fas fa-plus"></i></button></div>
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

@foreach ($instructors as $instructor)
    {{-- ==================== EDIT INSTRUCTOR MODAL ==================== --}}
    <div class="modal fade" id="editInstructorModal{{ $instructor->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <form action="{{ route('instructors.update', $instructor->id) }}" method="POST" class="modal-content">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="fw-bold">Edit Settings ({{ $instructor->user->name ?? 'Unknown' }})</h5>
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
                            <label class="fw-bold form-label w-100">Categories (Specialties)</label>
                            <!-- Added multiple="multiple" attribute explicitly -->
                            <select name="specialty[]" class="form-select select2-dropdown w-100" multiple="multiple">
                                @foreach($categories as $category)
                                    <option value="{{ $category->name }}" {{ in_array($category->name, $editSpecs) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-4 border-bottom pb-4">
                        <div class="col-md-3">
                            <label class="fw-bold form-label">Payment Date</label>
                            <input type="text" name="payment_date" class="form-control" value="{{ $instructor->payment_date }}" placeholder="e.g. 5th of Month">
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold form-label">Payment Method</label>
                            <select name="payment_method" class="form-select">
                                <option value="">-- Select --</option>
                                <option value="Cash" {{ $instructor->payment_method == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="KBZ Pay" {{ $instructor->payment_method == 'KBZ Pay' ? 'selected' : '' }}>KBZ Pay</option>
                                <option value="Wave" {{ $instructor->payment_method == 'Wave' ? 'selected' : '' }}>Wave Money</option>
                                <option value="Bank Transfer" {{ $instructor->payment_method == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold form-label">Maintenance Fees (MMK)</label>
                            <input type="number" step="0.01" name="maintenance_fees" class="form-control" value="{{ (float)$instructor->maintenance_fees }}">
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold form-label">Class Teaching Fees (MMK)</label>
                            <input type="number" step="0.01" name="class_teaching_fees" class="form-control" value="{{ (float)$instructor->class_teaching_fees }}">
                        </div>
                    </div>

                    <h6 class="fw-bold mt-2 mb-2">Category Fee Rates</h6>

                    <div class="alert alert-info p-3 small mb-3 border-0 shadow-sm rounded">
                        <i class="fas fa-info-circle me-1"></i> <strong>ဖြည့်သွင်းနည်း လမ်းညွှန် (Tier / Bonus Rules) :</strong><br>
                        <ul class="mb-0 mt-1 ps-3">
                            <li class="mb-1"><strong>၁ ယောက် မှ ၅ ယောက် (ပုံသေ) :</strong> Min <code>1</code>, Max <code>5</code>, Type <code>Fixed Amount</code>, Amount <code>15000</code> ဟု ထည့်ပါ။</li>
                            <li><strong>၆ ယောက် နှင့် အထက် (တစ်ယောက်ချင်းစီ) :</strong> Min <code>6</code>, <strong>Max ကို ဘာမှမထည့်ဘဲ အလွတ်ထားပါ</strong>၊ Type <code>Per Student</code>, Amount <code>2000</code> ဟု ထည့်ပါ။</li>
                        </ul>
                    </div>

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
                                            <label class="form-check-label fw-bold" style="cursor:pointer;" for="cat_edit_{{ $instructor->id }}_{{ $cat->id }}">{{ $cat->name }}</label>
                                        </div>
                                        <div class="cat-fee-inputs" style="{{ $isCheck ? 'display: block;' : 'display: none;' }}">
                                            <div class="row g-2 mb-3 pb-2 border-bottom">
                                                <div class="col-6">
                                                    <label class="form-label small text-muted mb-1">Fee Type</label>
                                                    <select name="category_fees[{{ $cat->id }}][fee_type]" data-selected="{{ $feeType }}" class="form-select form-select-sm fee-type-select">
                                                        <option value="full_time_fixed" {{ $feeType == 'full_time_fixed' ? 'selected' : '' }}>Fixed Rate (Base Amount)</option>
                                                        <option value="part_time_percentage" {{ $feeType == 'part_time_percentage' ? 'selected' : '' }}>Percentage (%) on Revenue</option>
                                                        <option value="part_time_tiered" {{ $feeType == 'part_time_tiered' ? 'selected' : '' }}>Tiered Fees Rules</option>
                                                    </select>
                                                </div>
                                                <div class="col-6 fee-value-container">
                                                    <label class="form-label small text-muted mb-1 fee-value-label">Base Rate / Percentage</label>
                                                    <input type="number" step="0.01" name="category_fees[{{ $cat->id }}][fee_value]" class="form-control form-control-sm fee-value-input" value="{{ isset($existingFee->fee_value) ? (float)$existingFee->fee_value : '' }}">
                                                </div>
                                            </div>

                                            <div class="bonus-wrapper" data-cat-id="{{ $cat->id }}">
                                                <label class="form-label small text-muted mb-1 bonus-label">Tier / Bonus Rules (အထက်ပါ လမ်းညွှန်အတိုင်းဖြည့်ရန်)</label>
                                                <div class="bonus-container">
                                                    @if(is_array($bonuses) && count($bonuses) > 0)
                                                        @foreach($bonuses as $index => $b)
                                                            @php
                                                                $min = $b['min_students'] ?? $b['threshold'] ?? '';
                                                                $max = $b['max_students'] ?? '';
                                                                $amt = $b['bonus_amount'] ?? $b['amount'] ?? '';
                                                                $type = $b['bonus_type'] ?? 'fixed';
                                                            @endphp
                                                            <div class="row g-2 mt-1 bonus-row align-items-center">
                                                                <div class="col-2"><input type="number" name="category_fees[{{ $cat->id }}][bonuses][{{ $index }}][min_students]" class="form-control form-control-sm" value="{{ $min !== '' ? (int)$min : '' }}" placeholder="Min"></div>
                                                                <div class="col-2"><input type="number" name="category_fees[{{ $cat->id }}][bonuses][{{ $index }}][max_students]" class="form-control form-control-sm" value="{{ $max !== '' ? (int)$max : '' }}" placeholder="Max (Optional)"></div>
                                                                <div class="col-3">
                                                                    <select name="category_fees[{{ $cat->id }}][bonuses][{{ $index }}][bonus_type]" class="form-select form-select-sm">
                                                                        <option value="fixed" {{ $type == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                                                        <option value="per_student" {{ $type == 'per_student' ? 'selected' : '' }}>Per Student</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-3"><input type="number" step="0.01" name="category_fees[{{ $cat->id }}][bonuses][{{ $index }}][bonus_amount]" class="form-control form-control-sm" value="{{ $amt !== '' ? (float)$amt : '' }}" placeholder="Amount (MMK)"></div>
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
                                                            <div class="col-2"><input type="number" name="category_fees[{{ $cat->id }}][bonuses][0][min_students]" class="form-control form-control-sm" placeholder="Min"></div>
                                                            <div class="col-2"><input type="number" name="category_fees[{{ $cat->id }}][bonuses][0][max_students]" class="form-control form-control-sm" placeholder="Max (Optional)"></div>
                                                            <div class="col-3">
                                                                <select name="category_fees[{{ $cat->id }}][bonuses][0][bonus_type]" class="form-select form-select-sm">
                                                                    <option value="fixed">Fixed Amount</option>
                                                                    <option value="per_student">Per Student</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-3"><input type="number" step="0.01" name="category_fees[{{ $cat->id }}][bonuses][0][bonus_amount]" class="form-control form-control-sm" placeholder="Amount (MMK)"></div>
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

    {{-- ==================== PAYMENT MODAL (FULL SCREEN) ==================== --}}
    <div class="modal fade" id="paymentModal{{ $instructor->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header  text-black">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-money-check-alt me-2"></i> Manage Payments for {{ $instructor->user->name ?? 'Unknown' }}
                    </h5>
                    <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light">
                    <div class="container-fluid mt-3">
                        
                        <!-- 1. Add New Payment Form -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white fw-bold"><i class="fas fa-plus-circle me-1"></i> Add New Payment</div>
                            <div class="card-body">
                                <form action="{{ route('instructor-payments.store', $instructor->id) }}" method="POST">
                                    @csrf
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-2">
                                            <label class="form-label small fw-bold">Date <span class="text-danger">*</span></label>
                                            <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-bold">Method</label>
                                            <select name="payment_method" class="form-select">
                                                <option value="">-- Select --</option>
                                                <option value="cash" {{ $instructor->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                                                <option value="kpay" {{ $instructor->payment_method == 'kpay' ? 'selected' : '' }}>KBZ Pay</option>
                                                <option value="wave" {{ $instructor->payment_method == 'wave' ? 'selected' : '' }}>Wave Money</option>
                                                <option value="bank" {{ $instructor->payment_method == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-bold">Amount (MMK) <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" name="amount" class="form-control" required placeholder="0.00">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-bold">Transaction ID</label>
                                            <input type="text" name="transaction_id" class="form-control" placeholder="e.g. 091234...">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold">Remark</label>
                                            <input type="text" name="remark" class="form-control" placeholder="Note...">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="submit" class="btn btn-success w-100"><i class="fas fa-save"></i> Save</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- 2. Payment History Table with Export -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white fw-bold"><i class="fas fa-history me-1"></i> Payment History</div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover w-100 payment-datatables" id="paymentTable{{ $instructor->id }}">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Date</th>
                                                <th>Amount (MMK)</th>
                                                <th>Method</th>
                                                <th>Transaction ID</th>
                                                <th>Remark</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($instructor->payments as $payment)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d-M-Y') }}</td>
                                                <td class="fw-bold text-success">{{ number_format($payment->amount) }}</td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ strtoupper($payment->payment_method ?? 'N/A') }}</span>
                                                </td>
                                                <td>{{ $payment->transaction_id ?? '-' }}</td>
                                                <td>{{ $payment->remark ?? '-' }}</td>
                                                <td class="text-center">
                                                    <form action="{{ route('instructor-payments.destroy', $payment->id) }}" method="POST" class="d-inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this payment record?')">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
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
            </div>
        </div>
    </div>
@endforeach

@include('master.footer')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

{{-- DataTables Buttons JS --}}
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>

<script>
    $(document).ready(function () {
        // Initialize DataTables
        if ($.fn.DataTable) {
            $('#basic-datatables').DataTable();
            $('.payment-datatables').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> Export to Excel', className: 'btn btn-success btn-sm mb-2' }
                ],
                pageLength: 10,
                ordering: false
            });
        }

        // ==========================================
        // ✅ MULTIPLE SELECT2 INITIALIZATION LOGIC
        // ==========================================
        function initializeSelect2(modalElement = $(document.body)) {
            modalElement.find('.select2-dropdown').each(function() {
                $(this).select2({
                    width: '100%',
                    placeholder: "-- Search & Select Categories --",
                    allowClear: true,
                    dropdownParent: modalElement // <-- This is required for Bootstrap Modals
                });
            });
        }

        // Init on page load (if any select2 exists outside modal)
        initializeSelect2();

        // Init/Re-init Select2 whenever a modal is opened
        $('.modal').on('shown.bs.modal', function () {
            // Adjust DataTables inside Modal
            if ($.fn.DataTable) {
                $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
            }
            
            // Destroy and Re-init Select2 specifically for this opened modal
            let $select2 = $(this).find('.select2-dropdown');
            if ($select2.hasClass("select2-hidden-accessible")) {
                $select2.select2('destroy');
            }
            initializeSelect2($(this));
        });

        // ==========================================
        // ✅ CHECKBOX TOGGLE LOGIC (Fixed)
        // ==========================================
        $(document).delegate('.cat-checkbox', 'click', function () {
            let parentWrapper = $(this).closest('.category-fee-card');
            let formInputs = parentWrapper.find('.cat-fee-inputs');

            if (this.checked) {
                parentWrapper.addClass('selected');
                formInputs.stop(true, true).slideDown(250); 
            } else {
                parentWrapper.removeClass('selected');
                formInputs.stop(true, true).slideUp(250); 
            }
        });

        // Add bonus btn logic
        $(document).on('click', '.add-bonus-btn', function() {
            let wrapper = $(this).closest('.bonus-wrapper');
            let catId = wrapper.attr('data-cat-id'); 
            let uniqueIndex = new Date().getTime(); 
            let html = `
                <div class="row g-2 mt-1 bonus-row align-items-center">
                    <div class="col-2"><input type="number" name="category_fees[${catId}][bonuses][${uniqueIndex}][min_students]" class="form-control form-control-sm" placeholder="Min"></div>
                    <div class="col-2"><input type="number" name="category_fees[${catId}][bonuses][${uniqueIndex}][max_students]" class="form-control form-control-sm" placeholder="Max (Optional)"></div>
                    <div class="col-3">
                        <select name="category_fees[${catId}][bonuses][${uniqueIndex}][bonus_type]" class="form-select form-select-sm">
                            <option value="fixed">Fixed Amount</option>
                            <option value="per_student">Per Student</option>
                        </select>
                    </div>
                    <div class="col-3"><input type="number" step="0.01" name="category_fees[${catId}][bonuses][${uniqueIndex}][bonus_amount]" class="form-control form-control-sm" placeholder="Amount (MMK)"></div>
                    <div class="col-2"><button type="button" class="btn btn-sm btn-danger remove-bonus-btn"><i class="fas fa-minus"></i></button></div>
                </div>`;
            wrapper.find('.bonus-container').append(html);
        });

        // Remove bonus btn logic
        $(document).on('click', '.remove-bonus-btn', function() {$(this).closest('.bonus-row').remove();
        });

        // Toggle Instructor Type
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
                                <option value="part_time_tiered" ${existingFeeType === 'part_time_tiered' ? 'selected' : ''}>Tiered Fees Rules</option>`;
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

        // Toggle Fee Type Settings
        $(document).on('change', '.fee-type-select', function() {
            let typeSelect = $(this);
            let card = typeSelect.closest('.category-fee-card');
            let val = typeSelect.val();
            
            typeSelect.attr('data-selected', val);
            
            let feeValueContainer = card.find('.fee-value-container');
            let bonusWrapper = card.find('.bonus-wrapper');
            let feeValueLabel = card.find('.fee-value-label');

            if (val === 'part_time_percentage') {
                feeValueContainer.show();
                feeValueLabel.text('Percentage (%)');
                bonusWrapper.show();
            } else if (val === 'part_time_tiered') {
                feeValueContainer.hide();
                bonusWrapper.show();
            } else if (val === 'full_time_fixed') {
                feeValueContainer.show();
                feeValueLabel.text('Base Amount (MMK)');
                bonusWrapper.show();
            }
        });
    });
</script>