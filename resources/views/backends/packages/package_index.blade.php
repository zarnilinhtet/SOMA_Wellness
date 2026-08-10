@include('master.header')
@include('master.sidebar')
@include('master.nav')
{{-- Summernote & Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

<div class="container">
    <div class="page-inner">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">Manage packages</h4>

            {{-- Permission: package_register --}}
            @if(auth()->user()->hasPermission('package_register'))
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addpackageModal">
                    <i class="fas fa-plus"></i> New Package
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

       <div class="card border-0 shadow-sm mt-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center mb-3">
            <i class="bi bi-info-circle-fill text-primary fs-4 me-2"></i>
            <h5 class="mb-0 fw-bold">Important Notes</h5>
        </div>

        <div class="d-flex gap-3 p-3 mb-3 rounded-3 bg-light">
            <div>
                <span class="badge bg-info text-dark mb-2">
                    <i class="bi bi-calendar-check me-1"></i>
                    Duration
                </span>
                <p class="mb-0 text-muted">
                    If the user has join at least one class by using package,
                    the package remains valid for the specified duration.
                </p>
            </div>
        </div>

        <div class="d-flex gap-3 p-3 mb-3 rounded-3 bg-light">
            <div>
                <span class="badge bg-warning text-dark mb-2">
                    <i class="bi bi-hourglass-split me-1"></i>
                    Fixed Expiration Duration
                </span>
                <p class="mb-0 text-muted">
                    If the user has not joined any class by using package,
                    the package will expire when the fixed expiration duration ends.
                </p>
            </div>
        </div>

        <div class="d-flex gap-3 p-3 rounded-3 bg-light">
            <div>
                <span class="badge bg-success mb-2">
                    <i class="bi bi-star-fill me-1"></i>
                    Loyal Point Duration
                </span>
                <p class="mb-0 text-muted">
                    The number of days after which the loyalty points will expire.
                </p>
            </div>
        </div>
    </div>
</div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="basic-datatables">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th>Duration (days)</th>
                                <th>Fixed Expired Duration (days)</th>
                                <th>Class Count</th>
                                <th>Loyal Point</th>
                                <th>Loyal Point Duration (days)</th>
                                <th>Status</th>

                                {{-- Action Header --}}
                                @if(auth()->user()->hasPermission('package_view') || auth()->user()->hasPermission('package_edit') || auth()->user()->hasPermission('package_delete'))
                                    <th class="text-center">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($packages as $package)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-bold">{{ $package->name }}</td>
                                    <td>{{ $package->category->name ?? '-' }}</td>
                                    <td>{!! Str::limit(strip_tags($package->description), 50) !!}</td>
                                    <td>{{ $package->price ?? '-' }}</td>
                                    <td><span class="badge bg-secondary">{{ $package->duration ?? '-' }} days</span></td>
                                    <td><span class="badge bg-secondary">{{ $package->fix_duration ?? '-' }} days</span></td>
                                    <td>{{ $package->class_count ?? '-' }}</td>
                                    <td>{{ $package->loyal_point ?? '-' }}</td>
                                    <td><span class="badge bg-secondary">{{ $package->loyal_duration ?? '-' }} days</span></td>
                                    <td>
                                        <span class="badge {{ $package->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $package->status }}
                                        </span>
                                    </td>
                                    
                                    {{-- Action Buttons --}}
                                    @if(auth()->user()->hasPermission('package_view') || auth()->user()->hasPermission('package_edit') || auth()->user()->hasPermission('package_delete'))
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-4">

                                                {{-- View Button --}}
                                                @if(auth()->user()->hasPermission('package_view'))
                                                    <button type="button" class="btn btn-link btn-info p-0"
                                                        data-bs-toggle="modal" data-bs-target="#viewpackageModal{{ $package->id }}"
                                                        title="View">
                                                        <i class="fa fa-eye fs-5"></i>
                                                    </button>
                                                @endif

                                                {{-- Edit Button --}}
                                                @if(auth()->user()->hasPermission('package_edit'))
                                                    <button type="button" class="btn btn-link btn-primary p-0"
                                                        data-bs-toggle="modal" data-bs-target="#editpackageModal{{ $package->id }}"
                                                        title="Edit">
                                                        <i class="fa fa-edit fs-5"></i>
                                                    </button>
                                                @endif

                                                {{-- Delete Form --}}
                                                @if(auth()->user()->hasPermission('package_delete'))
                                                    <form action="{{ route('packages.destroy', $package->id) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link btn-danger p-0"
                                                            onclick="return confirm('Are you sure you want to delete {{ $package->name }}?')"
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
{{-- VIEW MODALS --}}
{{-- ========================================== --}}
@if(auth()->user()->hasPermission('package_view'))
    @foreach ($packages as $package)
        <div class="modal fade" id="viewpackageModal{{ $package->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="fw-bold">View Package ({{ $package->name }})</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th width="35%">Package Name</th>
                                    <td>{{ $package->name }}</td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td>{{ $package->category->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Price</th>
                                    <td>{{ $package->price ? number_format($package->price) : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Duration</th>
                                    <td>{{ $package->duration ?? '-' }} Days</td>
                                </tr>
                                <tr>
                                    <th>Fixed Expired Duration</th>
                                    <td>{{ $package->fix_duration ?? '-' }} Days</td>
                                </tr>
                                <tr>
                                    <th>Class Count</th>
                                    <td>{{ $package->class_count ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Loyal Point</th>
                                    <td>{{ $package->loyal_point ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Loyal Point Duration</th>
                                    <td>{{ $package->loyal_duration ?? '-' }} Days</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge {{ $package->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $package->status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td>{!! $package->description ?? '-' !!}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif

{{-- ========================================== --}}
{{-- EDIT MODALS --}}
{{-- ========================================== --}}
@if(auth()->user()->hasPermission('package_edit'))
    @foreach ($packages as $package)
        <div class="modal fade" id="editpackageModal{{ $package->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form action="{{ route('packages.update', $package->id) }}" method="POST" class="modal-content">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="fw-bold">Edit package ({{ $package->name }})</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">package Name <span class="text-danger">*</span></label>
                                <input type="text" value="{{ $package->name }}" name="name" class="form-control"
                                    placeholder="soma" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Price <span class="text-danger">*</span></label>
                                {{-- required ကို ဖြုတ်ထားပါသည် --}}
                                <input type="number" value="{{ $package->price }}" name="price" class="form-control"
                                    placeholder="10,000">
                            </div>


                            <div class="col-12 mb-3">
                                <label class="fw-bold">Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-select">
                                    <option value="">Choose Type</option>

                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ $package->category->id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="fw-bold">Description </label>
                                <textarea name="description"
                                    class="form-control summernote">{{ $package->description }}</textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Duration ( Days ) <span class="text-danger">*</span></label>
                                <input type="number" name="duration" value="{{ $package->duration }}" class="form-control"
                                    placeholder="12">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Fixed Expired Duration ( Days ) <span class="text-danger">*</span></label>
                                <input type="number" name="fix_duration" value="{{ $package->fix_duration }}" class="form-control"
                                    placeholder="12">
                            </div>


                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Class Count <span class="text-danger">*</span></label>
                                <input type="number" name="class_count" value="{{ $package->class_count }}" class="form-control"
                                    placeholder="12">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Loyal Point <span class="text-danger">*</span></label>
                                <input type="text" name="loyal_point" value="{{ $package->loyal_point }}" class="form-control"
                                    placeholder="100">
                            </div>

                               <div class="col-md-6 mb-3">
                                <label class="fw-bold">Loyal Point Duration ( Days ) <span class="text-danger">*</span></label>
                                <input type="number" name="loyal_duration" value="{{ $package->loyal_duration }}" class="form-control"
                                    placeholder="12">
                            </div>

                            <div class="col-12 mb-3">
                                <label class="fw-bold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select">
                                    <option value="">Choose Status</option>

                                    <option value="active" {{ $package->status == 'active' ? 'selected' : '' }}>
                                        Active
                                    </option>

                                    <option value="inActive" {{ $package->status == 'inActive' ? 'selected' : '' }}>
                                        Inactive
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update package</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endif

{{-- ========================================== --}}
{{-- ADD MODAL --}}
{{-- ========================================== --}}
@if(auth()->user()->hasPermission('package_register'))
    <div class="modal fade" id="addpackageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('packages.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="fw-bold">Register New package</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">package Name <span class="text-danger">*</span></label>
                            <input type="text" value="{{ old('name') }}" name="name" class="form-control" placeholder="soma" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Price <span class="text-danger">*</span></label>
                            {{-- required ကို ဖြုတ်ထားပါသည် --}}
                            <input type="number" value="{{ old('price') }}" name="price" class="form-control" placeholder="10,000">
                        </div>

                        <div class="col-12 mb-3">
                            <label class="fw-bold">Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select">
                                <option selected>Choose Type</option>

                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('type') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="fw-bold">Description</label>
                            <textarea name="description" class="form-control summernote">{{ old('description') }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold"> Duration ( Days ) <span class="text-danger">*</span></label>
                            <input type="number" value="{{ old('duration') }}" name="duration" class="form-control" placeholder="12">
                        </div>

                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Fixed Expired Duration ( Days ) <span class="text-danger">*</span></label>
                                <input type="number" value="{{ old('fix_duration') }}" name="fix_duration"  class="form-control"
                                    placeholder="12">
                            </div>

                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Class Count <span class="text-danger">*</span></label>
                            <input type="number" value="{{ old('class_count') }}" name="class_count" class="form-control" placeholder="10">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Loyal Point <span class="text-danger">*</span></label>
                            <input type="text" value="{{ old('loyal_point') }}" name="loyal_point" class="form-control" placeholder="100">
                        </div>

                           <div class="col-md-6 mb-3">
                                <label class="fw-bold">Loyal Point Duration ( Days ) <span class="text-danger">*</span></label>
                                <input type="number" value="{{ old('loyal_duration') }}" name="loyal_duration" class="form-control"
                                    placeholder="12">
                            </div>

                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" aria-label="Default select example">
                                <option selected>Choose Status</option>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inActive" {{ old('status') == 'inActive' ? 'selected' : '' }}>InActive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save package</button>
                </div>
            </form>
        </div>
    </div>
@endif

@include('master.footer')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
    $(document).ready(function () {
        // Initialize DataTable
        if ($('#basic-datatables').length) {
            $('#basic-datatables').DataTable({
                "order": [[0, "desc"]] // အသစ်ထည့်ထားတာတွေကို အပေါ်ဆုံးမှာ ပြရန်
            });
        }
    });


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
</script>