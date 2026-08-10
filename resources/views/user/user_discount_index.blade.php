@include('master.header')
@include('master.sidebar')
@include('master.nav')

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- DataTables Buttons CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<div class="container-fluid py-4 mt-3">

    <!-- Card Container for Inline Table -->
    <div class="card border-0 shadow-sm mt-5">
        <div class="card-header bg-white py-3 d-flex gap-3 align-items-center">
            <!-- Back Button -->
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="history.back()">
                <i class="fas fa-arrow-left me-1"></i> Back
            </button>
            <h5 class="card-title fw-bold mb-0">
                Packages for <span class="text-primary">{{ $user->name }}</span>
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="basic-datatables" class="table table-striped table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%">No.</th>
                            <th>Package Name</th>
                            <th>Discount</th>
                            <th>Expire Date</th>
                            <th>Expire Time</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($userPackages as $pkg)
                            @php
                                $expiration = \Carbon\Carbon::parse($pkg->expiration_date . ' ' . ($pkg->expiration_time ?? '23:59:59'));
                                $isExpired = $expiration->isPast();
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $pkg->package_name ?? $pkg->package->name ?? 'N/A' }}</td>
                                <td>{{ $pkg->discount_amount ? $pkg->discount_amount . '%' : '0%' }}</td>
                                <td>{{ $pkg->expiration_date ?? 'N/A' }}</td>
                                <td>{{ $pkg->expiration_time ?? 'N/A' }}</td>
                                <td>
                                    @if($isExpired)
                                        <span class="badge bg-danger">Expired</span>
                                    @else
                                        <span class="badge bg-success">Active</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1 justify-content-end align-items-center">
                                        <!-- Edit Trigger Button -->
                                        <button type="button" class="btn btn-info btn-sm text-white" data-bs-toggle="modal"
                                            data-bs-target="#editPackageModal-{{ $pkg->user_id }}-{{ $pkg->package_id }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>

                                        <!-- Delete Form -->
                                        <form action="/user/{{ $pkg->user_id }}/package/{{ $pkg->package_id }}/remove"
                                            method="POST" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">
                                    No packages available for this user.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Package Modals -->
    @foreach ($userPackages as $pkg)
        <div class="modal fade" id="editPackageModal-{{ $pkg->user_id }}-{{ $pkg->package_id }}" tabindex="-1"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <form
                        action="{{ route('discount.update', ['userId' => $pkg->user_id, 'packageId' => $pkg->package_id]) }}"
                        method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">
                                Edit Discount: {{ $pkg->package->name ?? 'Package' }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Discount Amount (%)</label>
                                <input type="number" step="0.01" min="0" max="100" name="discount_amount"
                                    class="form-control" value="{{ old('discount_amount', $pkg->discount_amount) }}"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Expiration Date</label>
                                <input type="date" name="expiration_date" class="form-control"
                                    value="{{ old('expiration_date', $pkg->expiration_date) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Expiration Time</label>
                                <input type="time" name="expiration_time" class="form-control"
                                    value="{{ old('expiration_time', $pkg->expiration_time) }}">
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

</div>

@include('master.footer')

<script>
    $(document).ready(function () {
        $('#basic-datatables').DataTable({
            "pageLength": 10,
            "info": true,
            "order": [[3, 'asc']], // Orders by Expire Date (Index 3)
            "columnDefs": [
                { "targets": [6], "orderable": false } // Disables sorting on Actions column (Index 6)
            ],
            "buttons": [
                {
                    extend: 'excelHtml5',
                    title: 'User_Packages_Export',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5], // Exports all columns except Actions
                        modifier: {
                            search: 'applied'
                        }
                    }
                }
            ]
        });
    });
</script>