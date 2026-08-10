@include('master.header')
@include('master.sidebar')
@include('master.nav')

<div class="container">
    <div class="page-inner">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">User Types & Permissions</h4>

            {{-- Permission: user_type_register --}}
            @if(auth()->user()->hasPermission('user_type_register'))
                <button class="btn btn-primary" id="openModalBtn"><i class="fas fa-plus"></i> New Role</button>
            @endif
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table class="table table-hover align-middle" id="basic-datatables">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Role Name</th>
                            <th>Permissions</th>

                            {{-- Action Header: Only shows if user has Edit or Delete permission --}}
                            @if(auth()->user()->hasPermission('user_type_edit') || auth()->user()->hasPermission('user_type_delete'))
                                <th class="text-center">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($userTypes as $type)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-bold">{{ $type->name }}</td>
                                <td>
                                    @foreach($type->permissions as $perm)
                                        <span class="badge text-black bg-info small mb-1">{{ ucwords(str_replace('_', ' ', $perm)) }}</span>
                                    @endforeach
                                </td>

                                {{-- Action Data Cell --}}
                                @if(auth()->user()->hasPermission('user_type_edit') || auth()->user()->hasPermission('user_type_delete'))
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-4">

                                        {{-- Permission: user_type_edit --}}
                                        @if(auth()->user()->hasPermission('user_type_edit'))
                                            <a href="{{ route('user_types.edit', $type->id) }}" class="btn btn-link btn-primary p-0"><i class="fa fa-edit fs-5"></i></a>
                                        @endif

                                        {{-- Permission: user_type_delete --}}
                                        @if(auth()->user()->hasPermission('user_type_delete'))
                                            <form action="{{ route('user_types.destroy', $type->id) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-link btn-danger p-0" onclick="return confirm('Delete this role?')"><i class="fa fa-trash fs-5"></i></button>
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

{{-- MODAL: Only rendered if user has register permission --}}
@if(auth()->user()->hasPermission('user_type_register'))
<div class="modal fade" id="addRoleModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <form action="{{ route('user_types.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="fw-bold">Register New User Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <label class="fw-bold">User Type Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Manager" required>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-primary"><i class="fas fa-shield-alt"></i> Set Permissions</h6>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="selectAllPerms">
                        <label class="form-check-label fw-bold" for="selectAllPerms">Select All Everything</label>
                    </div>
                </div>

                <div class="row g-3">
                    @foreach($permissionGroups as $groupName => $perms)
                        <div class="col-md-4">
                            <div class="card h-100 border shadow-sm">
                                <div class="card-header py-2 bg-danger text-white d-flex justify-content-between">
                                    <span class="small fw-bold">{{ $groupName }}</span>
                                    <input type="checkbox" class="group-select" data-target="grp-{{ Str::slug($groupName) }}">
                                </div>
                                <div class="card-body p-2">
                                    @foreach($perms as $perm)
                                        <div class="form-check small">
                                            <input class="form-check-input perm-checkbox grp-{{ Str::slug($groupName) }}" type="checkbox" name="permissions[]" value="{{ $perm }}" id="p_{{ $perm }}">
                                            <label class="form-check-label" for="p_{{ $perm }}">
                                                {{ ucwords(str_replace(['_', 'report '], ' ', $perm)) }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger w-100">Save New User Type</button>
            </div>
        </form>
    </div>
</div>
@endif

@include('master.footer')

<script>
    $(document).ready(function() {
        // Only attempt to bind click if the button exists
        if($('#openModalBtn').length) {
            $('#openModalBtn').click(() => $('#addRoleModal').modal('show'));
        }

        // Select All Logic
        $('#selectAllPerms').change(function() {
            let checked = $(this).prop('checked');
            $('.perm-checkbox, .group-select').prop('checked', checked);
        });

        $('.group-select').change(function() {
            let checked = $(this).prop('checked');
            let target = $(this).data('target');
            $('.' + target).prop('checked', checked);
        });
    });
</script>
