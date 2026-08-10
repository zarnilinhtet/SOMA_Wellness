@include('master.header')
@include('master.sidebar')
@include('master.nav')

<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Categories</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="#">
                        <i class="icon-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Category List</a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>

            </ul>
        </div>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Categories Table</h4>

                        <button type="button" class="btn btn-primary btn-md" id="openModalBtn">
                            <i class="fas fa-plus me-1"></i> Register New
                        </button>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Number</th>
                                        <th>Name</th>
                                        <th style="width: 15%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categories as $category)
                                        <tr>
                                            {{-- $loop->iteration starts at 1 and increases automatically --}}
                                            <td>{{ $loop->iteration }}</td>

                                            <td>{{ $category->category_name }}</td>
                                            <td>
                                                <div class="form-button-action"
                                                    style="display: flex; align-items: center; gap: 5px;">
                                                    {{-- <button type="button" class="btn btn-link btn-primary btn-lg"

                                                        title="Edit Task">
                                                        <i class="fa fa-edit"></i>
                                                    </button> --}}
                                                    <a href="{{ route('categories.edit', $category->id) }}" class="form-button-action" title="Edit Category">
                                                        <i class="fa fa-edit"></i>
                                                    </a>

                                                    <form action="{{ route('categories.destroy', $category->id) }}"
                                                        method="POST" style="margin: 0;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link btn-danger btn-lg"
                                                            onclick="return confirm('Are you sure you want to delete this category?')"
                                                            title="Remove">
                                                            <i class="fa fa-times"></i>
                                                        </button>
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

    </div>
</div>

<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    onclick="$('#addCategoryModal').modal('hide');">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label for="category_name" class="form-label">Category Name <span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('category_name') is-invalid @enderror"
                        id="category_name" name="category_name" value="{{ old('category_name') }}" required>

                    @error('category_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        onclick="$('#addCategoryModal').modal('hide');">Close</button>
                    <button type="submit" class="btn btn-primary">Save Category</button>
                </div>
            </form>

        </div>
    </div>
</div>

@include('master.footer')

<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#basic-datatables').DataTable({
            "pageLength": 10,
        });

        // THE FIX: Move the modal to the document body so it isn't trapped behind the background
        $('#addCategoryModal').appendTo("body");

        // Open Modal
        $('#openModalBtn').on('click', function(e) {
            e.preventDefault();
            $('#addCategoryModal').modal('show');
        });
    });
</script>
