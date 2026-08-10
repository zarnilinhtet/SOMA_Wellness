@include('master.header')

{{-- Summernote CSS --}}
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

<style>
    /* View Modal Clean UI Customization */
    .view-modal-header { background-color: #f8f9fa; border-bottom: 2px solid #e9ecef; }
    .info-label { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d; margin-bottom: 3px; display: block; }
    .info-value { font-size: 1.05rem; font-weight: 500; color: #212529; }
    .detail-card { background: #fff; border: 1px solid #e9ecef; border-radius: 8px; padding: 15px; height: 100%; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
</style>

@include('master.sidebar')
@include('master.nav')

<div class="container">
    <div class="page-inner">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">Manage Payments</h4>

            {{-- Permission Check (e.g. payment_register) --}}
            @if(auth()->user()->hasPermission('payment_register'))
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                    <i class="fas fa-plus"></i> Add Payment Account
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
                                <th>No.</th>
                                <th>QR / Logo</th>
                                <th>Payment Method</th>
                                <th>Account Name</th>
                                <th>Phone / Account No.</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @if($payment->image)
                                            <img src="{{ asset($payment->image) }}" alt="QR" class="rounded border object-fit-cover" width="60" height="60">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted border" style="width: 60px; height: 60px; font-size: 10px;">No Img</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary px-3 py-2">{{ $payment->method }}</span>
                                    </td>
                                    <td><span class="fw-bold d-block text-dark">{{ $payment->name }}</span></td>
                                    <td>{{ $payment->account_info }}</td>

                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-4">
                                            {{-- View Button --}}
                                            <button type="button" class="btn btn-link btn-info p-0" data-bs-toggle="modal" data-bs-target="#viewPaymentModal{{ $payment->id }}" title="View Details">
                                                <i class="fa fa-eye fs-5"></i>
                                            </button>

                                            {{-- Edit Button --}}
                                            @if(auth()->user()->hasPermission('payment_edit'))
                                                <button type="button" class="btn btn-link btn-primary p-0" data-bs-toggle="modal" data-bs-target="#editPaymentModal{{ $payment->id }}" title="Edit">
                                                    <i class="fa fa-edit fs-5"></i>
                                                </button>
                                            @endif

                                            {{-- Delete Button --}}
                                            @if(auth()->user()->hasPermission('payment_delete'))
                                                <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-link btn-danger p-0" onclick="return confirm('Are you sure you want to delete this payment account?')" title="Delete">
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
{{-- VIEW MODALS (Clean & User-Friendly UI) --}}
{{-- ========================================== --}}
@foreach ($payments as $payment)
<div class="modal fade" id="viewPaymentModal{{ $payment->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header view-modal-header py-3 px-4">
                <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-money-check-alt text-primary me-2"></i> Payment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                
                <div class="row g-3 mb-4">
                    <div class="col-md-4 text-center">
                        <div class="detail-card py-4 d-flex flex-column align-items-center justify-content-center">
                            @if($payment->image)
                                <img src="{{ asset($payment->image) }}" class="rounded shadow-sm mb-3 border object-fit-contain" style="width: 150px; height: 150px;" alt="QR Code">
                                <a href="{{ asset($payment->image) }}" download="{{ $payment->method }}_{{ $payment->name }}_QR" class="btn btn-outline-success btn-sm rounded-pill px-3">
                                    <i class="fa fa-download me-1"></i> Download QR
                                </a>
                            @else
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center text-muted border mb-3" style="width: 100px; height: 100px;">
                                    <i class="fas fa-image fs-1"></i>
                                </div>
                                <span class="text-muted small">No QR / Logo Uploaded</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="row g-3 h-100">
                            <div class="col-sm-12">
                                <div class="detail-card">
                                    <span class="info-label"><i class="fas fa-university me-1"></i> Payment Method</span>
                                    <span class="info-value text-primary fs-5">{{ $payment->method }}</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="detail-card">
                                    <span class="info-label"><i class="fas fa-user text-success me-1"></i> Account Name</span>
                                    <span class="info-value">{{ $payment->name }}</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="detail-card">
                                    <span class="info-label"><i class="fas fa-phone-alt text-danger me-1"></i> Account No / Phone</span>
                                    <span class="info-value">{{ $payment->account_info }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="detail-card">
                    <span class="fw-bold fs-6 border-bottom d-block pb-2 mb-3">Instructions / Description</span>
                    <div class="text-muted" style="line-height: 1.6;">
                        {!! $payment->description ?? '<i>No description provided.</i>' !!}
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
@if(auth()->user()->hasPermission('payment_edit'))
    @foreach ($payments as $payment)
        <div class="modal fade" id="editPaymentModal{{ $payment->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form action="{{ route('payments.update', $payment->id) }}" method="POST" enctype="multipart/form-data" class="modal-content">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="fw-bold">Edit Payment Account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-start">
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Payment Method <span class="text-danger">*</span></label>
                                <input type="text" name="method" class="form-control" value="{{ $payment->method }}" placeholder="e.g. KBZ Pay, Wave Money, CB Bank" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Account Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ $payment->name }}" placeholder="e.g. U Ba" required>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="fw-bold">Phone Number / Account No. <span class="text-danger">*</span></label>
                                <input type="text" name="account_info" class="form-control" value="{{ $payment->account_info }}" required>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="fw-bold">Description / Instructions</label>
                                <textarea name="description" class="form-control summernote">{{ $payment->description }}</textarea>
                            </div>

                            {{-- Edit Image --}}
                            <div class="col-md-12 mb-3">
                                <label class="fw-bold">QR Code Image</label>
                                <input type="file" name="image" id="file_input_{{ $payment->id }}" class="form-control" accept="image/*" onchange="previewNewImage(this, 'edit_preview_{{ $payment->id }}')">
                                <input type="hidden" name="remove_image" id="remove_image_{{ $payment->id }}" value="0">
                                
                                {{-- Preview Container --}}
                                <div class="mt-2 position-relative d-inline-block" id="container_image_{{ $payment->id }}" style="{{ !$payment->image ? 'display:none;' : '' }}">
                                    <img src="{{ $payment->image ? asset($payment->image) : '' }}" id="edit_preview_{{ $payment->id }}" class="img-thumbnail object-fit-cover shadow-sm" width="150" height="150">
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" onclick="removeExistingImage('{{ $payment->id }}')" title="Remove Image">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Payment</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endif

{{-- ========================================== --}}
{{-- ADD MODAL --}}
{{-- ========================================== --}}
@if(auth()->user()->hasPermission('payment_register'))
<div class="modal fade" id="addPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="fw-bold">Register New Payment Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Payment Method <span class="text-danger">*</span></label>
                        <input type="text" name="method" class="form-control" placeholder="e.g. KBZ Pay, Wave Money, CB Bank" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Account Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Daw Aye Aye" required>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="fw-bold">Phone Number / Account No. <span class="text-danger">*</span></label>
                        <input type="text" name="account_info" class="form-control" placeholder="e.g. 09123456789" required>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="fw-bold">Description / Instructions</label>
                        <textarea name="description" class="form-control summernote"></textarea>
                    </div>

                    {{-- Add Modal Image --}}
                    <div class="col-md-12 mb-3">
                        <label class="fw-bold">QR Code / Logo Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*" onchange="previewNewImage(this, 'add_preview')">
                        <div class="mt-2" style="display:none;" id="add_preview_container">
                            <img src="" id="add_preview" class="img-thumbnail object-fit-cover shadow-sm" width="150" height="150">
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Payment</button>
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
        if($('#basic-datatables').length) {
            $('#basic-datatables').DataTable({
                "order": [[ 0, "desc" ]]
            });
        }

        $('.summernote').summernote({
            placeholder: 'Enter payment instructions or details...',
            tabsize: 2,
            height: 120,
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
    });

    // Live Image Preview Function
    function previewNewImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
                
                let addContainer = document.getElementById(previewId + '_container');
                if(addContainer) addContainer.style.display = 'inline-block';
                
                let editContainerId = previewId.replace('edit_preview', 'container_image');
                let editContainer = document.getElementById(editContainerId);
                if(editContainer) editContainer.style.setProperty('display', 'inline-block', 'important');

                if(previewId.includes('edit_preview')) {
                    let idOnly = previewId.split('_')[2]; 
                    let hiddenInput = document.getElementById('remove_image_' + idOnly);
                    if(hiddenInput) hiddenInput.value = '0';
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Remove Existing Image Function (For Edit Modal)
    window.removeExistingImage = function(paymentId) {
        if(confirm("Are you sure you want to remove this image?")) {
            
            // Backend ကို ဖျက်ရန် အသိပေးမည်
            let hiddenInput = document.getElementById('remove_image_' + paymentId);
            if(hiddenInput) hiddenInput.value = '1';

            // UI မှ ချက်ချင်း ဖျောက်မည်
            let previewContainer = document.getElementById('container_image_' + paymentId);
            if(previewContainer) previewContainer.style.setProperty('display', 'none', 'important');
            
            // File Input အဟောင်းကို ရှင်းမည်
            let fileInput = document.getElementById('file_input_' + paymentId);
            if(fileInput) fileInput.value = '';
        }
    };
</script>