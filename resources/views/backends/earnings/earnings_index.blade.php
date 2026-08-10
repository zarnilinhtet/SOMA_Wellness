@include('master.header')
@include('master.sidebar')
@include('master.nav')

<div class="container">
    <div class="page-inner">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">Manage Earnings</h4>
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

        {{-- Date Filters with Reset Button --}}
        <div class="row mb-3 align-items-end">
            <div class="col-md-3">
                <label for="filter-from" class="form-label fw-bold">From Date</label>
                <input type="date" id="filter-from" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="filter-to" class="form-label fw-bold">To Date</label>
                <input type="date" id="filter-to" class="form-control">
            </div>
            <div class="col-md-auto d-flex align-items-end">
                <button type="button" id="filter-reset"
                    class="btn btn-outline-secondary btn-sm d-flex align-items-center justify-content-center px-3"
                    style="height: 35px;">
                    <i class="fas fa-undo me-1"></i> 
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="basic-datatables">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Instructor Name</th>
                                <th>Class Name</th>
                                <th>Date - Time</th>
                                <th>Status</th>
                                <th>Fee</th>
                                {{-- Action Header --}}
                                @if(auth()->user()->hasPermission('instructor_edit') || auth()->user()->hasPermission('instructor_delete'))
                                    <th class="text-center">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($earnings as $earning)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-bold">{{ $earning->instructor->user->name }}</td>
                                    <td>{{ $earning->class->class_name ?? '-' }}</td>
                                    <td>{{ $earning->created_at ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $earning->is_paid === 1 ? 'success' : 'warning' }}">
                                            {{ $earning->is_paid === 1 ? 'Paid' : 'Pending' }}
                                        </span>
                                    </td>
                                    <td>{{ $earning->instructor->fee ?? 0 }}</td>
                                    <td>
                                        @if($earning->is_paid === 0 && auth()->user()->hasRole("Admin"))
                                            <form action="{{ route('instructors.earnings.update', $earning->instructor->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status"
                                                    value="{{ $earning->is_paid === 1 ? 'unpaid' : 'paid' }}">
                                                <input type="hidden" name="attendance_id" value="{{ $earning->id }}">
                                                <button type="submit" class="btn btn-link p-0"
                                                    onclick="return confirm('Are you sure you want to change the status of {{ $earning->instructor->user->name }} to {{ $earning->is_paid === 1 ? 'Unpaid' : 'Paid' }}?')"
                                                    title="Change Status">
                                                    <span class="badge bg-info">
                                                        Mark as Paid
                                                    </span>
                                                </button>
                                            </form>
                                        @endif
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

@include('master.footer')

<script>
    $(document).ready(function () {
        // Custom filtering function which will search data in column 3 (Date - Time)
        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            var min = $('#filter-from').val();
            var max = $('#filter-to').val();

            // Column index 3 corresponds to 'Date - Time'
            var dateStr = data[3].trim().substring(0, 10);

            if (!dateStr || dateStr === '-') {
                return (!min && !max);
            }

            var rowDate = new Date(dateStr).getTime();
            var minDate = min ? new Date(min).getTime() : null;
            var maxDate = max ? new Date(max).getTime() : null;

            if (
                (!minDate && !maxDate) ||
                (!minDate && rowDate <= maxDate) ||
                (minDate <= rowDate && !maxDate) ||
                (minDate <= rowDate && rowDate <= maxDate)
            ) {
                return true;
            }
            return false;
        });

        // Initialize DataTable
        if ($('#basic-datatables').length) {
            var table = $('#basic-datatables').DataTable({
                "order": [[0, "desc"]] // အသစ်ထည့်ထားတာတွေကို အပေါ်ဆုံးမှာ ပြရန်
            });

            // Trigger redraw on date change instantly
            $('#filter-from, #filter-to').on('change', function () {
                table.draw();
            });

            // Reset Filter Button Action
            $('#filter-reset').on('click', function () {
                $('#filter-from').val('');
                $('#filter-to').val('');
                table.draw();
            });
        }
    });
</script>