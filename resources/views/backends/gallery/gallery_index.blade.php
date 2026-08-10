@include('master.header')
@include('master.sidebar')
@include('master.nav')

<div class="container">
    <div class="page-inner">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">Manage Gallery Images</h4>

            {{-- Permission: gallery_register --}}
            @if(auth()->user()->hasPermission('gallery_register'))
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGalleryModal">
                    <i class="fas fa-plus"></i> Upload Image
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
                                <th>Image Preview</th>
                                <th>Title</th>
                                <th>Created At</th>
                                @if(auth()->user()->hasPermission('gallery_edit') || auth()->user()->hasPermission('gallery_delete'))
                                    <th class="text-center">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($galleries as $gallery)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <img src="{{ asset($gallery->image) }}" alt="Gallery Image" class="img-thumbnail"
                                            style="width: 60px; height: 60px; object-fit: cover;">
                                    </td>
                                    <td>{{ $gallery->title }}</td>
                                    <td>{{ $gallery->created_at ? $gallery->created_at->format('M d, Y') : '-' }}</td>

                                    {{-- Action Buttons --}}
                                    @if(auth()->user()->hasPermission('gallery_edit') || auth()->user()->hasPermission('gallery_delete'))
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-3">

                                                {{-- Edit Button --}}
                                                @if(auth()->user()->hasPermission('gallery_edit'))
                                                    <button type="button" class="btn btn-link btn-primary p-0"
                                                        data-bs-toggle="modal" data-bs-target="#editGalleryModal{{ $gallery->id }}"
                                                        title="Edit">
                                                        <i class="fa fa-edit fs-5"></i>
                                                    </button>
                                                @endif

                                                {{-- Delete Form --}}
                                                @if(auth()->user()->hasPermission('gallery_delete'))
                                                    <form action="{{ route('gallery.destroy', $gallery->id) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link btn-danger p-0"
                                                            onclick="return confirm('Are you sure you want to delete this image?')"
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
@if(auth()->user()->hasPermission('gallery_edit'))
    @foreach ($galleries as $gallery)
        <div class="modal fade" id="editGalleryModal{{ $gallery->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('gallery.update', $gallery->id) }}" method="POST" enctype="multipart/form-data"
                    class="modal-content">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="fw-bold">Edit Image #{{ $gallery->id }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-start">
                        <div class="row g-3">
                            {{-- Current Image Preview --}}
                            <div class="col-md-12 text-center mb-2">
                                <label class="fw-bold d-block text-start mb-2">Current Image</label>
                                <img src="{{ asset($gallery->image) }}" alt="Current Photo" class="img-thumbnail rounded"
                                    style="max-height: 180px; object-fit: cover;">
                            </div>

                            {{-- Replace Image Input --}}
                            <div class="col-md-12 mb-3">
                                <label class="fw-bold mb-1">Replace Image (Optional)</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <small class="text-muted">Leave empty if you don't want to change the image.</small>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="fw-bold mb-1">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" value="{{ $gallery->title }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Image</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endif

{{-- ========================================== --}}
{{-- ADD MODAL --}}
{{-- ========================================== --}}
@if(auth()->user()->hasPermission('gallery_register'))
    <div class="modal fade" id="addGalleryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('gallery.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="fw-bold">Upload New Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12 mb-3">
                            <label class="fw-bold mb-1">Choose Image <span class="text-danger">*</span></label>
                            <input type="file" value="{{ old('image') }}" name="image" class="form-control" accept="image/*" required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="fw-bold mb-1">Title <span class="text-danger">*</span></label>
                            <input type="text" value="{{ old('title') }}" name="title" class="form-control" placeholder="Enter image title" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Image</button>
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