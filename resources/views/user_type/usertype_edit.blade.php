@include('master.header')
@include('master.sidebar')
@include('master.nav')

<div class="container">
    <div class="page-inner">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h4 class="card-title fw-bold">Edit User Type: {{ $userType->name }}</h4>
                    </div>

                    <form action="{{ route('user_types.update', $userType->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body bg-white">
                            <div class="form-group mb-4">
                                <label class="fw-bold mb-2">User Type Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg" name="name" value="{{ $userType->name }}" required>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3 px-2 border-top pt-3">
                                <label class="fw-bold mb-0 text-dark fs-5">Assign Permissions</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="selectAllPerms" style="cursor: pointer; width: 40px; height: 20px;">
                                    <label class="form-check-label fw-bold ms-2 text-primary" for="selectAllPerms" style="cursor: pointer;">Select All</label>
                                </div>
                            </div>

                            <div class="row g-3">
                                @php $assignedPerms = $userType->permissions ?? []; @endphp
                                @foreach($permissionGroups as $groupName => $perms)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px; overflow: hidden; background: #f8fafc;">
                                            <div class="card-header border-bottom-0 py-2 d-flex justify-content-between align-items-center" style="background: #e2e8f0;">
                                                <span class="fw-bold text-dark small text-uppercase">{{ $groupName }}</span>
                                                <input class="form-check-input group-select" type="checkbox" data-target="group-{{ \Illuminate\Support\Str::slug($groupName) }}" style="cursor: pointer;">
                                            </div>
                                            <div class="card-body p-3">
                                                @foreach($perms as $perm)
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input perm-checkbox group-{{ \Illuminate\Support\Str::slug($groupName) }}"
                                                               type="checkbox" name="permissions[]" value="{{ $perm }}"
                                                               id="perm_{{ $perm }}"
                                                               {{ in_array($perm, $assignedPerms) ? 'checked' : '' }}
                                                               style="cursor: pointer;">
                                                        <label class="form-check-label text-secondary fw-semibold user-select-none" for="perm_{{ $perm }}" style="cursor: pointer; font-size: 0.85rem;">
                                                            {{ ucwords(str_replace('_', ' ', $perm)) }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="card-footer bg-light d-flex justify-content-end p-3">
                            <a href="{{ route('user_types.index') }}" class="btn btn-outline-secondary me-2 px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Update Role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('master.footer')

<script>
    $(document).ready(function() {
        // Master "Select All" Logic
        $('#selectAllPerms').change(function() {
            let isChecked = $(this).prop('checked');
            $('.perm-checkbox').prop('checked', isChecked);
            $('.group-select').prop('checked', isChecked);
        });

        // Group "Select All" Logic
        $('.group-select').change(function() {
            let isChecked = $(this).prop('checked');
            let targetClass = $(this).data('target');
            $('.' + targetClass).prop('checked', isChecked);
            checkMasterStatus();
        });

        // Individual Checkbox Logic
        $('.perm-checkbox').change(function() {
            checkMasterStatus();
        });

        function checkMasterStatus() {
            let total = $('.perm-checkbox').length;
            let checked = $('.perm-checkbox:checked').length;
            $('#selectAllPerms').prop('checked', total === checked);
        }

        // Run once on load to check if all are already checked
        checkMasterStatus();
    });
</script>
