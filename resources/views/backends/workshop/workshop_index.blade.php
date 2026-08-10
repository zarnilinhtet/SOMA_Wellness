@include('master.header')
@include('master.sidebar')
@include('master.nav')

<div class="container">
    <div class="page-inner">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">Manage Workshops</h4>

            {{-- Permission: workshop_register --}}
            @if(auth()->user()->hasPermission('workshop_register'))
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addWorkshopModal">
                    <i class="fas fa-plus"></i> New Workshop
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
                                <th>Workshop Name</th>
                                <th>Description</th>
                                <th>Image</th>
                                @if(auth()->user()->hasPermission('workshop_edit') || auth()->user()->hasPermission('workshop_delete'))
                                    <th class="text-center">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($workshops as $workshop)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-bold">{{ $workshop->workshop_name ?? '-' }}</td>
                                    <td>{{ Str::limit($workshop->description, 50) ?? '-' }}</td>
                                    <td>
                                        @if($workshop->image)
                                            <img src="{{ asset($workshop->image) }}" alt="Workshop Image" class="img-thumbnail" style="max-width: 80px; max-height: 80px; object-fit: cover;">
                                        @else
                                            <span class="text-muted">No Image</span>
                                        @endif
                                    </td>

                                    {{-- Action Buttons --}}
                                    @if(auth()->user()->hasPermission('workshop_edit') || auth()->user()->hasPermission('workshop_delete'))
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-3">

                                                {{-- Edit Button --}}
                                                @if(auth()->user()->hasPermission('workshop_edit'))
                                                    <button type="button" class="btn btn-link btn-primary p-0"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editWorkshopModal{{ $workshop->id }}" title="Edit">
                                                        <i class="fa fa-edit fs-5"></i>
                                                    </button>
                                                @endif

                                                {{-- Delete Form --}}
                                                @if(auth()->user()->hasPermission('workshop_delete'))
                                                    <form action="{{ route('workshops.destroy', $workshop->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link btn-danger p-0"
                                                            onclick="return confirm('Are you sure you want to delete {{ $workshop->workshop_name ?? 'this workshop' }}?')"
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
@if(auth()->user()->hasPermission('workshop_edit'))
    @foreach ($workshops as $workshop)
        <div class="modal fade" id="editWorkshopModal{{ $workshop->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form action="{{ route('workshops.update', $workshop->id) }}" method="POST" enctype="multipart/form-data" class="modal-content">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="fw-bold">Edit Workshop ({{ $workshop->workshop_name ?? '-' }})</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-start">
                        <div class="row g-3">
                            <div class="col-md-12 mb-3">
                                <label class="fw-bold">Workshop Name <span class="text-danger">*</span></label>
                                <input type="text" name="workshop_name" class="form-control" value="{{ old('workshop_name', $workshop->workshop_name) }}" required>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="fw-bold">Description</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $workshop->description) }}</textarea>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="fw-bold">Workshop Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <small class="text-muted">Leave empty if you don't want to change the current image.</small>

                                @if($workshop->image)
                                    <div class="mt-2">
                                        <label class="d-block text-muted small">Current Image:</label>
                                        <img src="{{ asset($workshop->image) }}" alt="Current Image" class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Workshop</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endif

{{-- ========================================== --}}
{{-- ADD MODAL --}}
{{-- ========================================== --}}
@if(auth()->user()->hasPermission('workshop_register'))
    <div class="modal fade" id="addWorkshopModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('workshops.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="fw-bold">Register New Workshop</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12 mb-3">
                            <label class="fw-bold">Workshop Name <span class="text-danger">*</span></label>
                            <input type="text" value="{{ old('workshop_name') }}" name="workshop_name" class="form-control" placeholder="Enter workshop title" required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Enter details about the workshop">{{ old('description') }}</textarea>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="fw-bold">Workshop Image</label>
                            <input type="file" name="image" value="{{ old('image') }}" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Workshop</button>
                </div>
            </form>
        </div>
    </div>
@endif

@include('master.footer')

<script>
    $(document).ready(function () {
        if ($('#basic-datatables').length) {
            $('#basic-datatables').DataTable({
                "order": [[0, "asc"]]
            });
        }
    });
</script>