@include('master.header')
@include('master.sidebar')
@include('master.nav')

<div class="container">
    <div class="page-inner">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">Manage Instructors</h4>

            {{-- Permission: instructor_register --}}
            @if(auth()->user()->hasPermission('instructor_register'))
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addInstructorModal">
                    <i class="fas fa-plus"></i> New Instructor
                </button>
            @endif
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Validation Error Message --}}
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
                    <table class="table table-hover align-middle" id="basic-datatables">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Specialty</th>
                                <th>Fee</th>
                                <!-- <th>Total Earnings</th> -->
                                {{-- Action Header --}}
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
                                    <td><span class="badge bg-secondary">{{ $instructor->specialty ?? 'General' }}</span>
                                    </td>
                                    <td>{{ $instructor->fee ?? 0 }}</td>
                                    <!-- <td>{{ $instructor->total_earnings ?? 0 }}</td> -->

                                    {{-- Action Buttons --}}
                                    @if(auth()->user()->hasPermission('instructor_edit') || auth()->user()->hasPermission('instructor_delete'))
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-4">

                                                {{-- Edit Button --}}
                                                @if(auth()->user()->hasPermission('instructor_edit'))
                                                    <button type="button" class="btn btn-link btn-primary p-0"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editInstructorModal{{ $instructor->id }}" title="Edit">
                                                        <i class="fa fa-edit fs-5"></i>
                                                    </button>
                                                @endif

                                                {{-- Delete Form --}}
                                                @if(auth()->user()->hasPermission('instructor_delete'))
                                                    <form action="{{ route('instructors.destroy', $instructor->id) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link btn-danger p-0"
                                                            onclick="return confirm('Are you sure you want to delete {{ $instructor->user->name }}?')"
                                                            title="Delete">
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

{{-- ========================================== --}}
{{-- EDIT MODALS --}}
{{-- ========================================== --}}
@if(auth()->user()->hasPermission('instructor_edit'))
    @foreach ($instructors as $instructor)
        <div class="modal fade" id="editInstructorModal{{ $instructor->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form action="{{ route('instructors.update', $instructor->id) }}" method="POST" class="modal-content">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="fw-bold">Edit Instructor ({{ $instructor->user->name }})</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-start">
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Instructor Name <span class="text-danger">*</span></label>
                                <select name="instructor_id" class="form-select" required>
                                    <option>Select One</option>
                                    @foreach ($instRole as $in)
                                        <option value={{ $in->id }} {{ old('instructor_id') == $in->id ? 'selected' : '' }}>{{ $in->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Specialty (Optional)</label>
                                <input type="text" value="{{ old('specialty') }}" name="specialty" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Fee ( percent % )</label>
                                <input type="number" value="{{ old('fee') }}" name="fee" class="form-control" step="0.01" min="0" max="100">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Instructor</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endif

{{-- ========================================== --}}
{{-- ADD MODAL --}}
{{-- ========================================== --}}
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
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Instructor Name <span class="text-danger">*</span></label>
                            <select name="instructor_id" class="form-select" required>
                                <option>Select One</option>
                                @foreach ($instRole as $in)
                                    <option value={{ $in->id }} {{ old('instructor_id') == $in->id ? 'selected' : '' }}>{{ $in->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Specialty (Optional)</label>
                            <input type="text" value="{{ old('specialty') }}" name="specialty" class="form-control" placeholder="e.g. Hatha Yoga, Vinyasa">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Fee ( percent % )</label>
                            <input type="number" value="{{ old('fee') }}" name="fee" class="form-control" placeholder="e.g. 50" step="0.01" min="0" max="100">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Instructor</button>
                </div>
            </form>
        </div>
    </div>
@endif

@include('master.footer')

<script>
    $(document).ready(function () {
        // Initialize DataTable
        if ($('#basic-datatables').length) {
            $('#basic-datatables').DataTable({
                "order": [[0, "desc"]] // အသစ်ထည့်ထားတာတွေကို အပေါ်ဆုံးမှာ ပြရန်
            });
        }
    });
</script>