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
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                                </div>

                                <div class="col-md-6 form-group mb-3">
                                    <label class="fw-bold">Phone <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="phone" value="{{ old('phone', $user->phone) }}" required>
                                </div>

                                <div class="col-md-6 form-group mb-3">
                                    <label class="fw-bold">Age <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="age" value="{{ old('age', $user->age) }}" required>
                                </div>

                                <div class="col-12">
                                    <hr>
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-info-circle"></i> Password အဟောင်းကို အောက်တွင် မြင်တွေ့နိုင်ပါသည်။ Password အသစ်မပြောင်းလိုပါက New Password အကွက်များကို အလွတ်ထားခဲ့ပါ။
                                    </p>
                                </div>

                                <!-- Password အဟောင်း ပြသမည့် အကွက် -->
                                <div class="col-md-12 form-group mb-3">
                                    <label class="fw-bold text-primary">Current Password (Admin Only View)</label>
                                    <input type="text" class="form-control bg-light text-primary border-primary" 
                                           value="{{ $user->plain_password ? $user->plain_password : 'No plain text record available' }}" 
                                           readonly>
                                </div>

                                <div class="col-md-6 form-group mb-3">
                                    <label class="fw-bold">New Password</label>
                                    <input type="password" class="form-control" name="password" placeholder="Leave blank to keep current">
                                </div>

                                <div class="col-md-6 form-group mb-3">
                                    <label class="fw-bold">Confirm New Password</label>
                                    <input type="password" class="form-control" name="password_confirmation" placeholder="Leave blank to keep current">
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-light d-flex justify-content-end">
                            <a href="{{ route('user_register.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('master.footer')