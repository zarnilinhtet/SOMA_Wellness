@include('master.header')
@include('master.sidebar')
@include('master.nav')

<style>
    .category-fee-card.selected {
        border-color: #0d6efd !important;
        background-color: #f8f9fa;
    }
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

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
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
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Specialty</th>
                                <th>Category Rates</th>
                                @if(auth()->user()->hasPermission('instructor_edit') || auth()->user()->hasPermission('instructor_delete'))
                                    <th class="text-center">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($instructors as $instructor)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-bold">{{ $instructor->user->name }}</td>
                                    <td>{{ $instructor->user->phone ?? '-' }}</td>
                                    <td><span class="badge bg-secondary">{{ $instructor->specialty ?? 'General' }}</span></td>
                                    <td>
                                        @forelse($instructor->categoryFees as $fee)
                                            <span class="badge bg-light text-dark border mb-1">
                                                {{ $fee->category->name }}: 
                                                <strong>
                                                    {{ $fee->fee_type == 'percentage' ? number_format($fee->fee_value) . '%' : number_format($fee->fee_value) . ' MMK' }}
                                                </strong>
                                            </span><br>
                                        @empty
                                            <span class="text-muted small">No rates set</span>
                                        @endforelse
                                    </td>
                                    @if(auth()->user()->hasPermission('instructor_edit') || auth()->user()->hasPermission('instructor_delete'))
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-3">
                                                @if(auth()->user()->hasPermission('instructor_edit'))
                                                    <button type="button" class="btn btn-link btn-primary p-0"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editInstructorModal{{ $instructor->id }}" title="Edit">
                                                        <i class="fa fa-edit fs-5"></i>
                                                    </button>
                                                @endif
                                                @if(auth()->user()->hasPermission('instructor_delete'))
                                                    <form action="{{ route('instructors.destroy', $instructor->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link btn-danger p-0"
                                                            onclick="return confirm('Are you sure you want to delete {{ $instructor->user->name }}?')" title="Delete">
                                                            <i class="fa fa-trash fs-5"></i>
                                                        </button>
                                                    </form>
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

{{-- ADD INSTRUCTOR MODAL --}}
@if(auth()->user()->hasPermission('instructor_register'))
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
                        <div class="col-md-6">
                            <label class="fw-bold form-label">Instructor User <span class="text-danger">*</span></label>
                            <select name="instructor_id" class="form-select" required>
                                <option value="">-- Select Instructor User --</option>
                                @foreach ($instRole as $in)
                                    <option value="{{ $in->id }}">{{ $in->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold form-label">Specialty (Optional)</label>
                            <input type="text" name="specialty" class="form-control" placeholder="e.g. Pilates, Yoga">
                        </div>
                    </div>

                    <h6 class="fw-bold mt-4 mb-2">Category Fee Structure</h6>
                    <div class="border rounded p-3 bg-light" style="max-height: 380px; overflow-y: auto;">
                        <div class="row g-3">
                            @foreach($categories as $cat)
                                <div class="col-md-6">
                                    <div class="card p-3 border category-fee-card">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input cat-checkbox" type="checkbox"
                                                name="category_fees[{{ $cat->id }}][selected]" value="1"
                                                id="cat_add_{{ $cat->id }}">
                                            <label class="form-check-label fw-bold" for="cat_add_{{ $cat->id }}">
                                                {{ $cat->name }}
                                            </label>
                                        </div>
                                        <div class="cat-fee-inputs" style="display: none;">
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <select name="category_fees[{{ $cat->id }}][fee_type]" class="form-select form-select-sm">
                                                        <option value="fixed">Fixed Rate (MMK)</option>
                                                        <option value="percentage">Percentage (%)</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <input type="number" step="0.01" name="category_fees[{{ $cat->id }}][fee_value]"
                                                        class="form-control form-control-sm" placeholder="e.g. 300000 or 30">
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
@endif

{{-- EDIT INSTRUCTOR MODALS --}}
@if(auth()->user()->hasPermission('instructor_edit'))
    @foreach ($instructors as $instructor)
        <div class="modal fade" id="editInstructorModal{{ $instructor->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form action="{{ route('instructors.update', $instructor->id) }}" method="POST" class="modal-content">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="fw-bold">Edit Fee Rates ({{ $instructor->user->name }})</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="fw-bold form-label">Instructor User <span class="text-danger">*</span></label>
                                <select name="instructor_id" class="form-select" required>
                                    @foreach ($instRole as $in)
                                        <option value="{{ $in->id }}" {{ $instructor->instructor_id == $in->id ? 'selected' : '' }}>
                                            {{ $in->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold form-label">Specialty</label>
                                <input type="text" value="{{ $instructor->specialty }}" name="specialty" class="form-control">
                            </div>
                        </div>

                        <h6 class="fw-bold mt-4 mb-2">Category Fee Rates</h6>
                        <div class="border rounded p-3 bg-light" style="max-height: 380px; overflow-y: auto;">
                            <div class="row g-3">
                                @foreach($categories as $cat)
                                    @php 
                                        $existingFee = $instructor->categoryFees->firstWhere('category_id', $cat->id);
                                        $isCheck = !empty($existingFee);
                                    @endphp
                                    <div class="col-md-6">
                                        <div class="card p-3 border category-fee-card {{ $isCheck ? 'selected' : '' }}">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input cat-checkbox" type="checkbox"
                                                    name="category_fees[{{ $cat->id }}][selected]" value="1"
                                                    id="cat_edit_{{ $instructor->id }}_{{ $cat->id }}" {{ $isCheck ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold" for="cat_edit_{{ $instructor->id }}_{{ $cat->id }}">
                                                    {{ $cat->name }}
                                                </label>
                                            </div>
                                            <div class="cat-fee-inputs" style="{{ $isCheck ? 'display: block;' : 'display: none;' }}">
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <select name="category_fees[{{ $cat->id }}][fee_type]" class="form-select form-select-sm">
                                                            <option value="fixed" {{ ($existingFee->fee_type ?? '') == 'fixed' ? 'selected' : '' }}>Fixed Rate (MMK)</option>
                                                            <option value="percentage" {{ ($existingFee->fee_type ?? '') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="number" step="0.01" name="category_fees[{{ $cat->id }}][fee_value]"
                                                            class="form-control form-control-sm" value="{{ $existingFee->fee_value ?? '' }}" placeholder="Rate">
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
                        <button type="submit" class="btn btn-primary fw-bold">Update Instructor</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endif

@include('master.footer')

<script>
    $(document).ready(function () {
        // Toggle Fee Inputs based on checkbox selection
        $(document).on('change', '.cat-checkbox', function () {
            var card = $(this).closest('.category-fee-card');
            var inputs = card.find('.cat-fee-inputs');

            if ($(this).is(':checked')) {
                card.addClass('selected');
                inputs.slideDown(200);
            } else {
                card.removeClass('selected');
                inputs.slideUp(200);
            }
        });
    });
</script>