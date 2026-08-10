@include('master.header')
@include('master.sidebar')
@include('master.nav')

<div class="container">
    <div class="page-inner">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h4 class="card-title fw-bold">Edit User</h4>
                    </div>

                    <form action="{{ route('user_register.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label class="fw-bold">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" value="{{ $user->name }}"
                                        required>
                                </div>

                                <div class="col-md-6 form-group mb-3">
                                    <label class="fw-bold">Phone <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="phone" value="{{ $user->phone }}"
                                        required>
                                </div>

                                <div class="col-md-6 form-group mb-3">
                                    <label class="fw-bold">Age <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="age" value="{{ $user->age }}"
                                        required>
                                </div>

                                {{-- <div class="col-md-12 form-group mb-4">
                                    <label class="fw-bold">User Type (Role) <span class="text-danger">*</span></label>
                                    <select class="form-select form-control" name="user_type_id" required>
                                        <option value="" disabled>-- Select a Role --</option>
                                        @foreach($userTypes as $role)
                                        <option value="{{ $role->id }}" {{ $user->user_type_id == $role->id ? 'selected'
                                            : '' }}>
                                            {{ $role->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div> --}}

                                <hr>
                                <p class="text-muted small mb-2"><i class="fas fa-info-circle"></i> Leave password
                                    fields blank if you do not want to change it.</p>

                                <div class="col-md-6 form-group mb-3">
                                    <label class="fw-bold">New Password</label>
                                    <input type="password" class="form-control" name="password"
                                        placeholder="Leave blank to keep current">
                                </div>

                                <div class="col-md-6 form-group mb-3">
                                    <label class="fw-bold">Confirm New Password</label>
                                    <input type="password" class="form-control" name="password_confirmation"
                                        placeholder="Leave blank to keep current">
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-light d-flex justify-content-end">
                            <a href="{{ route('user_register.index') }}"
                                class="btn btn-outline-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update
                                User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('master.footer')