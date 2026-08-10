@include('master.header')

{{-- Summernote CSS --}}
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

<style>
    /* View Modal Clean UI Customization */
    .view-modal-header { background-color: #f8f9fa; border-bottom: 2px solid #e9ecef; }
    .detail-card { background: #fff; border: 1px solid #e9ecef; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
</style>

@include('master.sidebar')
@include('master.nav')

<div class="container">
    <div class="page-inner">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">Manage Categories</h4>

            {{-- Permission Check (e.g. category_register) --}}
            @if(auth()->user()->hasPermission('category_register'))
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="fas fa-plus"></i> Add Category
                </button>
            @endif
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
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
                    <table class="table table-hover align-middle" id="basic-datatables" style="min-width: 800px;">
                        <thead>
                            <tr>
                                <th width="5%">No.</th>
                                <th width="25%">Category Name</th>
                                <th width="45%">Description</th>
                                <th width="25%" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="fw-bold text-dark fs-6">{{ $category->name }}</span></td>
                                    <td>
                                        <span class="text-muted text-truncate d-inline-block" style="max-width: 400px;">
                                            {{-- Summernote မှ ထွက်လာသော HTML tag များကို ဖျောက်၍ စာသားသက်သက်သာ ပြရန် --}}
                                            {!! strip_tags($category->description) ?: '-' !!}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-4">
                                        

                                            {{-- Edit Button --}}
                                            @if(auth()->user()->hasPermission('category_edit'))
                                                <button type="button" class="btn btn-link btn-primary p-0" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $category->id }}" title="Edit">
                                                    <i class="fa fa-edit fs-5"></i>
                                                </button>
                                            @endif

                                            {{-- Delete Button --}}
                                            @if(auth()->user()->hasPermission('category_delete'))
                                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-link btn-danger p-0" onclick="return confirm('Are you sure you want to delete this category?')" title="Delete">
                                                        <i class="fa fa-trash fs-5"></i>
                                                    </button>
                                                </form>
                                            @endif
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

{{-- ========================================== --}}
{{-- VIEW MODALS (Clean UI) --}}
{{-- ========================================== --}}
@foreach ($categories as $category)
<div class="modal fade" id="viewCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header view-modal-header py-3 px-4">
                <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-tags text-primary me-2"></i> Category Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                
                <div class="detail-card mb-4">
                    <span class="text-muted small text-uppercase fw-bold d-block mb-1">Category Name</span>
                    <span class="fs-5 fw-bold text-primary">{{ $category->name }}</span>
                </div>

                <div class="detail-card">
                    <span class="text-muted small text-uppercase fw-bold d-block border-bottom pb-2 mb-3">Description</span>
                    <div class="text-dark" style="line-height: 1.6;">
                        {!! $category->description ?? '<i class="text-muted">No description provided.</i>' !!}
                    </div>
                </div>

            </div>
            <div class="modal-footer bg-white py-2">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- ========================================== --}}
{{-- EDIT MODALS --}}
{{-- ========================================== --}}
@if(auth()->user()->hasPermission('category_edit'))
    @foreach ($categories as $category)
        <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form action="{{ route('categories.update', $category->id) }}" method="POST" class="modal-content">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="fw-bold">Edit Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-start">
                        <div class="row g-3">
                            <div class="col-12 mb-3">
                                <label class="fw-bold">Category Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="fw-bold">Description</label>
                                <textarea name="description" class="form-control summernote">{{ $category->description }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endif

{{-- ========================================== --}}
{{-- ADD MODAL --}}
{{-- ========================================== --}}
@if(auth()->user()->hasPermission('category_register'))
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('categories.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="fw-bold">Register New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12 mb-3">
                        <label class="fw-bold">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Yoga, Pilates, Zumba" required>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="fw-bold">Description</label>
                        <textarea name="description" class="form-control summernote" placeholder="Enter category details..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Category</button>
            </div>
        </form>
    </div>
</div>
@endif

@include('master.footer')

{{-- Plugins Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<script>
    $(document).ready(function() {
        // DataTables
        if($('#basic-datatables').length) {
            $('#basic-datatables').DataTable({
                "order": [[ 0, "desc" ]]
            });
        }

        // Summernote Initialization
        $('.summernote').summernote({
            placeholder: 'Enter description...',
            tabsize: 2,
            height: 150,
            dialogsInBody: true, 
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['view', ['fullscreen', 'codeview']]
            ]
        });
    });
</script>