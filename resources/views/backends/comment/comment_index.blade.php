@include('master.header')
@include('master.sidebar')
@include('master.nav')

<style>
    .action-control-group {
        display: inline-flex;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 30px;
        border: 1px solid #e2e8f0;
    }

    .btn-action-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        font-size: 12.5px;
        font-weight: 700;
        border-radius: 20px;
        border: none;
        background: transparent;
        transition: all 0.2s ease;
        text-decoration: none !important;
    }

    .btn-action-pill.action-approve {
        color: #16a34a;
    }

    .btn-action-pill.action-approve:hover {
        background: #dcfce7;
        color: #15803d;
    }
</style>

<div class="container">
    <div class="page-inner">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">Manage Comments</h4>

            {{-- Permission: instructor_register --}}
            @if(auth()->user()->hasPermission('instructor_register'))
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addInstructorModal">
                    <i class="fas fa-plus me-1"></i> New Comment
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
                <ul class="mb-0 ps-3">
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
                                <th>Content</th>
                                <th>Status</th>
                                <th>Created At</th>
                                @if(auth()->user()->hasPermission('instructor_edit') || auth()->user()->hasPermission('instructor_delete'))
                                    <th class="text-center">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($comments as $comment)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-bold">{{ $comment->user->name }}</td>
                                    <td>{{ $comment->content }}</td>
                                    <td>
                                        @if($comment->admin_approval == 0)
                                            <span class="badge bg-warning text-dark">Waiting for approval</span>
                                        @else
                                            <span class="badge bg-success">Approved</span>
                                        @endif
                                    </td>
                                    <td>{{ $comment->created_at->format('d M Y, h:i A') }}</td>
                                    @if(auth()->user()->hasPermission('instructor_edit') || auth()->user()->hasPermission('instructor_delete'))
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                {{-- Approve Action --}}
                                                @if($comment->admin_approval == 0 && auth()->user()->hasPermission('instructor_edit'))
                                                    <div class="action-control-group">
                                                        <form action="{{ route('comments.approve.update', $comment->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn-action-pill action-approve">
                                                                <i class="fa fa-check"></i> Approve
                                                            </button>
                                                        </form>
                                                    </div>

                                                    {{-- Delete Action --}}
                                                    @if(auth()->user()->hasPermission('instructor_delete'))
                                                        <form action="{{ route('comments.destroy', $comment->id) }}" method="POST"
                                                            class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-link btn-danger p-0 ms-1"
                                                                onclick="return confirm('Are you sure you want to delete this comment?')"
                                                                title="Delete">
                                                                <i class="fa fa-trash fs-5"></i>
                                                            </button>
                                                        </form>
                                                    @endif
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

@include('master.footer')

<script>
    $(document).ready(function () {
        if ($('#basic-datatables').length) {
            $('#basic-datatables').DataTable({
                "order": [[0, "desc"]]
            });
        }
    });
</script>